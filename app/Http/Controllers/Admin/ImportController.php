<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\KomitmenInfak;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TagihanInfak;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Support\InfakStatus;
use App\Support\AkademikStatus;
use App\Support\SimpleXlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
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
        'tagihan-awal' => ['nis', 'tahun_ajaran', 'periode', 'nominal_tagihan', 'nominal_terbayar', 'tanggal_bayar'],
    ];

    private array $examples = [
        'tahun-ajaran' => [
            ['nama' => '2024/2025', 'tanggal_mulai' => '2024-07-15', 'tanggal_selesai' => '2025-06-30', 'aktif' => ''],
            ['nama' => '2025/2026', 'tanggal_mulai' => '2025-07-14', 'tanggal_selesai' => '2026-06-30', 'aktif' => ''],
            ['nama' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'aktif' => ''],
        ],
        'guru' => [
            ['nip' => 'G001', 'nama' => 'Budi Santoso', 'jenis_kelamin' => 'L', 'no_hp' => '081234567890', 'email' => 'budi@sekolah.sch.id', 'alamat' => 'Bogor', 'aktif' => 'aktif'],
        ],
        'rayon' => [
            ['nama' => 'Cicurug 1', 'nip_guru' => 'G001'],
        ],
        'rombel' => [
            ['nama' => 'X PPLG 1', 'tingkat' => 'X'],
            ['nama' => 'XI PPLG 1', 'tingkat' => 'XI'],
            ['nama' => 'XII PPLG 1', 'tingkat' => 'XII'],
        ],
        'siswa' => [
            ['nis' => '24250001', 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L', 'status' => 'aktif', 'tahun_ajaran' => '2024/2025', 'tingkat' => 'X', 'rombel' => 'X PPLG 1', 'rayon' => 'Cicurug 1', 'email_login' => '24250001@app-infak.id'],
            ['nis' => '24250001', 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L', 'status' => 'aktif', 'tahun_ajaran' => '2025/2026', 'tingkat' => 'XI', 'rombel' => 'XI PPLG 1', 'rayon' => 'Cicurug 1', 'email_login' => '24250001@app-infak.id'],
            ['nis' => '24250001', 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L', 'status' => 'aktif', 'tahun_ajaran' => '2026/2027', 'tingkat' => 'XII', 'rombel' => 'XII PPLG 1', 'rayon' => 'Cicurug 1', 'email_login' => '24250001@app-infak.id'],
        ],
        'komitmen-infak' => [
            ['nis' => '24250001', 'tahun_ajaran' => '2024/2025', 'nominal_bulanan' => '50000', 'mulai_bulan' => '2024-07-01'],
            ['nis' => '24250001', 'tahun_ajaran' => '2025/2026', 'nominal_bulanan' => '50000', 'mulai_bulan' => '2025-07-01'],
        ],
        'tagihan-awal' => [
            ['nis' => '24250001', 'tahun_ajaran' => '2024/2025', 'periode' => '2024-07', 'nominal_tagihan' => '50000', 'nominal_terbayar' => '50000', 'tanggal_bayar' => '2024-07-10'],
            ['nis' => '24250001', 'tahun_ajaran' => '2025/2026', 'periode' => '2025-07', 'nominal_tagihan' => '50000', 'nominal_terbayar' => '20000', 'tanggal_bayar' => '2025-07-10'],
            ['nis' => '24250001', 'tahun_ajaran' => '2025/2026', 'periode' => '2025-08', 'nominal_tagihan' => '50000', 'nominal_terbayar' => '0', 'tanggal_bayar' => ''],
        ],
    ];

    public function template(string $master): BinaryFileResponse
    {
        abort_unless(isset($this->templates[$master]), 404);

        $path = SimpleXlsx::template($this->templates[$master], $master, $this->examples[$master] ?? []);

        return response()->download($path, 'template-'.$master.'.xlsx')->deleteFileAfterSend();
    }

    public function import(Request $request, string $master)
    {
        abort_unless(isset($this->templates[$master]), 404);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:4096'],
        ]);

        try {
            $rows = SimpleXlsx::rows($request->file('file')->getRealPath());
            array_shift($rows);

            $count = match ($master) {
                'tahun-ajaran' => $this->tahunAjaran($rows),
                'guru' => $this->guru($rows),
                'rayon' => $this->rayon($rows),
                'rombel' => $this->rombel($rows),
                'siswa' => $this->siswa($rows),
                'komitmen-infak' => $this->komitmenInfak($rows),
                'tagihan-awal' => $this->tagihanAwal($rows),
            };
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Import gagal: '.$exception->getMessage());
        }

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
                    'tanggal_mulai' => $this->dateValue($row[1] ?? null),
                    'tanggal_selesai' => $this->dateValue($row[2] ?? null),
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
        $validRows = [];

        foreach ($rows as $row) {
            $tahun = TahunAjaran::where('nama', $row[4] ?? null)->first();
            $rombel = Rombel::where('nama', $row[6] ?? null)->first();
            $rayon = Rayon::where('nama', $row[7] ?? null)->first();

            if (empty($row[0]) || empty($row[1]) || ! $tahun || ! $rombel || ! $rayon) {
                continue;
            }

            $validRows[] = compact('row', 'tahun', 'rombel', 'rayon');
        }

        usort($validRows, function ($left, $right) {
            return strcmp(
                (string) $left['tahun']->tanggal_mulai?->format('Y-m-d'),
                (string) $right['tahun']->tanggal_mulai?->format('Y-m-d')
            );
        });

        foreach ($validRows as $data) {
            DB::transaction(function () use ($data) {
                ['row' => $row, 'tahun' => $tahun, 'rombel' => $rombel, 'rayon' => $rayon] = $data;
                $userId = User::where('email', $row[8] ?? null)->value('id');

                $siswa = Siswa::updateOrCreate(
                    ['nis' => $row[0]],
                    [
                        'user_id' => $userId,
                        'nama' => $row[1],
                        'jenis_kelamin' => in_array($row[2] ?? null, ['L', 'P']) ? $row[2] : 'L',
                        'status' => in_array($row[3] ?? null, ['aktif', 'alumni']) ? $row[3] : 'aktif',
                    ]
                );

                $siswa->akademik()->updateOrCreate(
                    ['tahun_ajaran_id' => $tahun->id],
                    [
                        'tingkat' => in_array($row[5] ?? null, ['X', 'XI', 'XII']) ? $row[5] : 'X',
                        'rombel_id' => $rombel->id,
                        'rayon_id' => $rayon->id,
                        'status' => 'naik',
                    ]
                );

                AkademikStatus::syncSiswa($siswa);
            });
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
                    'nominal_bulanan' => $this->moneyValue($row[2]),
                    'mulai_bulan' => $this->dateValue($row[3] ?? null),
                ]
            );
            $count++;
        }

        return $count;
    }

    private function tagihanAwal(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $siswa = Siswa::where('nis', $row[0] ?? null)->first();
            $tahun = TahunAjaran::where('nama', $row[1] ?? null)->first();
            $periode = $this->monthValue($row[2] ?? null);
            $nominalTagihan = $this->moneyValue($row[3] ?? 0);
            $nominalTerbayar = $this->moneyValue($row[4] ?? 0);

            if (! $siswa || ! $tahun || ! preg_match('/^\d{4}-\d{2}$/', (string) $periode) || $nominalTagihan <= 0) {
                continue;
            }

            $akademik = $siswa->akademik()
                ->where('tahun_ajaran_id', $tahun->id)
                ->first();

            if (! $akademik) {
                continue;
            }

            DB::transaction(function () use ($row, $siswa, $akademik, $periode, $nominalTagihan, $nominalTerbayar) {
                $tagihan = TagihanInfak::updateOrCreate(
                    [
                        'siswa_akademik_id' => $akademik->id,
                        'periode' => $periode,
                    ],
                    [
                        'nominal' => $nominalTagihan,
                        'status' => 'belum',
                    ]
                );

                $existingImportPaymentIds = $tagihan->alokasiPembayaran()
                    ->whereHas('pembayaran', fn ($query) => $query->where('bukti_transfer', 'import-tagihan-awal'))
                    ->pluck('pembayaran_id');

                if ($existingImportPaymentIds->isNotEmpty()) {
                    $tagihan->alokasiPembayaran()
                        ->whereIn('pembayaran_id', $existingImportPaymentIds)
                        ->delete();

                    Pembayaran::whereIn('id', $existingImportPaymentIds)->delete();
                }

                if ($nominalTerbayar > 0) {
                    $pembayaran = Pembayaran::create([
                        'siswa_id' => $siswa->id,
                        'tanggal' => $this->dateValue($row[5] ?? null) ?: $periode.'-01',
                        'nominal' => min($nominalTerbayar, $nominalTagihan),
                        'bukti_transfer' => 'import-tagihan-awal',
                        'status_verifikasi' => 'valid',
                    ]);

                    $pembayaran->alokasiPembayaran()->create([
                        'tagihan_infak_id' => $tagihan->id,
                        'nominal' => min($nominalTerbayar, $nominalTagihan),
                    ]);
                }

                InfakStatus::refreshTagihan($tagihan);
            });

            $count++;
        }

        return $count;
    }

    private function moneyValue(mixed $value): int
    {
        return (int) preg_replace('/[^0-9]/', '', (string) $value);
    }

    private function dateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && (int) $value > 20000) {
            return gmdate('Y-m-d', ((int) $value - 25569) * 86400);
        }

        $timestamp = strtotime((string) $value);

        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function monthValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}$/', (string) $value)) {
            return (string) $value;
        }

        $date = $this->dateValue($value);

        return $date ? substr($date, 0, 7) : null;
    }
}
