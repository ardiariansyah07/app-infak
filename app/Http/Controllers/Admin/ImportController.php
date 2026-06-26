<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\KomitmenInfak;
use App\Models\Rayon;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Support\SimpleXlsx;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportController extends Controller
{
    private array $templates = [
        'tahun-ajaran' => ['nama', 'tanggal_mulai', 'tanggal_selesai', 'aktif'],
        'guru' => ['nip', 'nama', 'jenis_kelamin', 'no_hp', 'email', 'alamat', 'aktif'],
        'rayon' => ['nama', 'nip_guru'],
        'rombel' => ['nama', 'tingkat'],
        'siswa' => ['nis', 'nama', 'jenis_kelamin', 'status', 'tahun_ajaran', 'tingkat', 'rombel', 'rayon', 'email_login'],
        'komitmen-infak' => ['nis', 'tahun_ajaran', 'nominal_bulanan', 'mulai_bulan'],
    ];

    public function template(string $master): BinaryFileResponse
    {
        abort_unless(isset($this->templates[$master]), 404);

        $path = SimpleXlsx::template($this->templates[$master], $master);

        return response()->download($path, 'template-'.$master.'.xlsx')->deleteFileAfterSend();
    }

    public function import(Request $request, string $master)
    {
        abort_unless(isset($this->templates[$master]), 404);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:4096'],
        ]);

        $rows = SimpleXlsx::rows($request->file('file')->getRealPath());
        array_shift($rows);

        $count = match ($master) {
            'tahun-ajaran' => $this->tahunAjaran($rows),
            'guru' => $this->guru($rows),
            'rayon' => $this->rayon($rows),
            'rombel' => $this->rombel($rows),
            'siswa' => $this->siswa($rows),
            'komitmen-infak' => $this->komitmenInfak($rows),
        };

        return back()->with('success', $count.' data berhasil diimport');
    }

    private function tahunAjaran(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if (empty($row[0])) {
                continue;
            }

            TahunAjaran::updateOrCreate(
                ['nama' => $row[0]],
                [
                    'tanggal_mulai' => $row[1] ?? null,
                    'tanggal_selesai' => $row[2] ?? null,
                    'aktif' => in_array(strtolower($row[3] ?? ''), ['1', 'ya', 'aktif', 'true']),
                ]
            );
            $count++;
        }

        return $count;
    }

    private function guru(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            Guru::updateOrCreate(
                ['nip' => $row[0]],
                [
                    'nama' => $row[1],
                    'jenis_kelamin' => $row[2] ?: 'L',
                    'no_hp' => $row[3] ?? null,
                    'email' => $row[4] ?? null,
                    'alamat' => $row[5] ?? null,
                    'aktif' => ! in_array(strtolower($row[6] ?? 'aktif'), ['0', 'tidak', 'nonaktif', 'false']),
                ]
            );
            $count++;
        }

        return $count;
    }

    private function rayon(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $guru = Guru::where('nip', $row[1] ?? null)->first();

            if (empty($row[0]) || ! $guru) {
                continue;
            }

            Rayon::updateOrCreate(['nama' => $row[0]], ['guru_id' => $guru->id]);
            $count++;
        }

        return $count;
    }

    private function rombel(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if (empty($row[0])) {
                continue;
            }

            Rombel::updateOrCreate(
                ['nama' => $row[0]],
                ['tingkat' => in_array($row[1] ?? null, ['X', 'XI', 'XII']) ? $row[1] : null]
            );
            $count++;
        }

        return $count;
    }

    private function siswa(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $tahun = TahunAjaran::where('nama', $row[4] ?? null)->first();
            $rombel = Rombel::where('nama', $row[6] ?? null)->first();
            $rayon = Rayon::where('nama', $row[7] ?? null)->first();

            if (empty($row[0]) || empty($row[1]) || ! $tahun || ! $rombel || ! $rayon) {
                continue;
            }

            $siswa = Siswa::updateOrCreate(
                ['nis' => $row[0]],
                [
                    'user_id' => User::where('email', $row[8] ?? null)->value('id'),
                    'nama' => $row[1],
                    'jenis_kelamin' => in_array($row[2] ?? null, ['L', 'P']) ? $row[2] : 'L',
                    'status' => in_array($row[3] ?? null, ['aktif', 'alumni']) ? $row[3] : 'aktif',
                ]
            );

            $siswa->akademik()
                ->where('tahun_ajaran_id', '!=', $tahun->id)
                ->where('status', 'aktif')
                ->update(['status' => 'naik']);

            $siswa->akademik()->updateOrCreate(
                ['tahun_ajaran_id' => $tahun->id],
                [
                    'tingkat' => $row[5] ?? 'X',
                    'rombel_id' => $rombel->id,
                    'rayon_id' => $rayon->id,
                    'status' => $siswa->status === 'aktif' ? 'aktif' : 'alumni',
                ]
            );
            $count++;
        }

        return $count;
    }

    private function komitmenInfak(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $siswa = Siswa::where('nis', $row[0] ?? null)->first();
            $tahun = TahunAjaran::where('nama', $row[1] ?? null)->first();

            if (! $siswa || ! $tahun || empty($row[2])) {
                continue;
            }

            $akademik = $siswa->akademik()
                ->where('tahun_ajaran_id', $tahun->id)
                ->first();

            if (! $akademik) {
                continue;
            }

            KomitmenInfak::updateOrCreate(
                ['siswa_akademik_id' => $akademik->id],
                [
                    'nominal_bulanan' => (int) $row[2],
                    'mulai_bulan' => $row[3] ?? null,
                ]
            );
            $count++;
        }

        return $count;
    }
}
