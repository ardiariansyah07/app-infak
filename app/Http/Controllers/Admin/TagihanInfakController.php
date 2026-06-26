<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomitmenInfak;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use Illuminate\Http\Request;

class TagihanInfakController extends Controller
{
    public function index()
    {
        $data = TagihanInfak::with('siswaAkademik.siswa', 'siswaAkademik.rombel', 'siswaAkademik.rayon')
            ->latest()
            ->get();

        return view('admin.tagihan.index', compact('data'));
    }

    public function create()
    {
        return view('admin.tagihan.form', [
            'tagihan' => new TagihanInfak,
            'siswaAkademik' => $this->siswaAkademikOptions(),
            'action' => route('admin.tagihan.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_akademik_id' => ['required', 'exists:siswa_akademik,id'],
            'periode' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'nominal' => ['required', 'integer', 'min:1000'],
        ]);

        TagihanInfak::updateOrCreate(
            [
                'siswa_akademik_id' => $validated['siswa_akademik_id'],
                'periode' => $validated['periode'],
            ],
            [
                'nominal' => $validated['nominal'],
                'status' => 'belum',
            ]
        );

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil disimpan');
    }

    public function edit(TagihanInfak $tagihan)
    {
        return view('admin.tagihan.form', [
            'tagihan' => $tagihan,
            'siswaAkademik' => $this->siswaAkademikOptions(),
            'action' => route('admin.tagihan.update', $tagihan),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, TagihanInfak $tagihan)
    {
        $validated = $request->validate([
            'siswa_akademik_id' => ['required', 'exists:siswa_akademik,id'],
            'periode' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'nominal' => ['required', 'integer', 'min:1000'],
        ]);

        $tagihan->update($validated);

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil diperbarui');
    }

    public function destroy(TagihanInfak $tagihan)
    {
        $tagihan->delete();

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dihapus');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'periode' => ['required', 'regex:/^\d{4}-\d{2}$/'],
        ]);

        $created = 0;

        KomitmenInfak::with('siswaAkademik')
            ->whereHas('siswaAkademik', fn ($query) => $query->where('status', 'aktif'))
            ->each(function (KomitmenInfak $komitmen) use ($validated, &$created) {
                $tagihan = TagihanInfak::firstOrCreate(
                    [
                        'siswa_akademik_id' => $komitmen->siswa_akademik_id,
                        'periode' => $validated['periode'],
                    ],
                    [
                        'nominal' => $komitmen->nominal_bulanan,
                        'status' => 'belum',
                    ]
                );

                if ($tagihan->wasRecentlyCreated) {
                    $created++;
                }
            });

        return redirect()->route('admin.tagihan.index')->with('success', $created.' tagihan berhasil dibuat');
    }

    private function siswaAkademikOptions()
    {
        return SiswaAkademik::with('siswa', 'rombel', 'rayon')
            ->where('status', 'aktif')
            ->get()
            ->sortBy('siswa.nama');
    }
}
