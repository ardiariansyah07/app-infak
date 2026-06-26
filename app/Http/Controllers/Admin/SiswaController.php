<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrangTua;
use App\Models\Rayon;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    public function index()
    {
        $data = Siswa::with('akademikAktif.rombel', 'akademikAktif.rayon', 'orangTua')
            ->latest()
            ->get();

        return view('admin.siswa.index', compact('data'));
    }

    public function create()
    {
        return $this->form(new Siswa, route('admin.siswa.store'), 'POST');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        DB::transaction(function () use ($validated) {
            $siswa = Siswa::create($validated['siswa']);
            $siswa->akademik()->create($validated['akademik']);
            $siswa->orangTua()->sync($validated['orang_tua_ids'] ?? []);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil disimpan');
    }

    public function edit(Siswa $siswa)
    {
        $siswa->load('akademikAktif', 'orangTua');

        return $this->form($siswa, route('admin.siswa.update', $siswa), 'PUT');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $this->validateData($request, $siswa);

        DB::transaction(function () use ($validated, $siswa) {
            $siswa->update($validated['siswa']);
            $siswa->akademik()
                ->where('tahun_ajaran_id', '!=', $validated['akademik']['tahun_ajaran_id'])
                ->where('status', 'aktif')
                ->update(['status' => 'naik']);

            $siswa->akademik()->updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $validated['akademik']['tahun_ajaran_id'],
                ],
                $validated['akademik']
            );
            $siswa->orangTua()->sync($validated['orang_tua_ids'] ?? []);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil diperbarui');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus');
    }

    private function form(Siswa $siswa, string $action, string $method)
    {
        return view('admin.siswa.form', [
            'siswa' => $siswa,
            'akademik' => $siswa->akademikAktif,
            'riwayatAkademik' => $siswa->akademik()->with('tahunAjaran', 'rombel', 'rayon')->latest()->get(),
            'users' => User::where('role', User::ROLE_ORANG_TUA)->orderBy('name')->get(),
            'rombel' => Rombel::orderBy('nama')->get(),
            'rayon' => Rayon::orderBy('nama')->get(),
            'tahunAjaran' => TahunAjaran::orderByDesc('aktif')->orderBy('nama')->get(),
            'orangTua' => OrangTua::orderBy('nama')->get(),
            'action' => $action,
            'method' => $method,
        ]);
    }

    private function validateData(Request $request, ?Siswa $siswa = null): array
    {
        $siswaId = $siswa?->id ?? 'NULL';

        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:50', 'unique:siswa,nis,'.$siswaId],
            'user_id' => ['nullable', 'exists:users,id'],
            'nama' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'status' => ['required', 'in:aktif,alumni'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'tingkat' => ['required', 'in:X,XI,XII'],
            'rombel_id' => ['required', 'exists:rombel,id'],
            'rayon_id' => ['required', 'exists:rayon,id'],
            'orang_tua_ids' => ['array'],
            'orang_tua_ids.*' => ['exists:orang_tua,id'],
        ]);

        return [
            'siswa' => [
                'user_id' => $validated['user_id'] ?? null,
                'nis' => $validated['nis'],
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'status' => $validated['status'],
            ],
            'akademik' => [
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                'tingkat' => $validated['tingkat'],
                'rombel_id' => $validated['rombel_id'],
                'rayon_id' => $validated['rayon_id'],
                'status' => $validated['status'] === 'aktif' ? 'aktif' : 'alumni',
            ],
            'orang_tua_ids' => $validated['orang_tua_ids'] ?? [],
        ];
    }
}
