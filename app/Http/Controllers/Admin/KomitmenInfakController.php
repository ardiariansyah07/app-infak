<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomitmenInfak;
use App\Models\SiswaAkademik;
use Illuminate\Http\Request;

class KomitmenInfakController extends Controller
{
    public function index()
    {
        $data = KomitmenInfak::with('siswaAkademik.siswa', 'siswaAkademik.rombel', 'siswaAkademik.rayon')
            ->latest()
            ->get();

        return view('admin.komitmen.index', compact('data'));
    }

    public function create()
    {
        return view('admin.komitmen.form', [
            'komitmen' => new KomitmenInfak,
            'siswaAkademik' => $this->siswaAkademikOptions(),
            'action' => route('admin.komitmen-infak.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_akademik_id' => ['required', 'exists:siswa_akademik,id', 'unique:komitmen_infak,siswa_akademik_id'],
            'nominal_bulanan' => ['required', 'integer', 'min:1000'],
            'mulai_bulan' => ['nullable', 'date'],
        ]);

        KomitmenInfak::create($validated);

        return redirect()->route('admin.komitmen-infak.index')->with('success', 'Komitmen infak berhasil disimpan');
    }

    public function edit(KomitmenInfak $komitmenInfak)
    {
        return view('admin.komitmen.form', [
            'komitmen' => $komitmenInfak,
            'siswaAkademik' => $this->siswaAkademikOptions(),
            'action' => route('admin.komitmen-infak.update', $komitmenInfak),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, KomitmenInfak $komitmenInfak)
    {
        $validated = $request->validate([
            'siswa_akademik_id' => ['required', 'exists:siswa_akademik,id', 'unique:komitmen_infak,siswa_akademik_id,'.$komitmenInfak->id],
            'nominal_bulanan' => ['required', 'integer', 'min:1000'],
            'mulai_bulan' => ['nullable', 'date'],
        ]);

        $komitmenInfak->update($validated);

        return redirect()->route('admin.komitmen-infak.index')->with('success', 'Komitmen infak berhasil diperbarui');
    }

    public function destroy(KomitmenInfak $komitmenInfak)
    {
        $komitmenInfak->delete();

        return redirect()->route('admin.komitmen-infak.index')->with('success', 'Komitmen infak berhasil dihapus');
    }

    private function siswaAkademikOptions()
    {
        return SiswaAkademik::with('siswa', 'rombel', 'rayon')
            ->where('status', 'aktif')
            ->get()
            ->sortBy('siswa.nama');
    }
}
