<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use App\Support\MonthlyTagihanGenerator;
use Illuminate\Http\Request;

class TagihanInfakController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->toString();
        $status = $request->string('status')->toString();

        $data = TagihanInfak::with('siswaAkademik.siswa', 'siswaAkademik.rombel', 'siswaAkademik.rayon', 'siswaAkademik.tahunAjaran')
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('siswaAkademik.siswa', function ($query) use ($search) {
                    $query->where('nis', 'like', '%'.$search.'%')
                        ->orWhere('nama', 'like', '%'.$search.'%');
                });
            })
            ->when(in_array($status, ['belum', 'sebagian', 'lunas'], true), fn ($query) => $query->where('status', $status))
            ->orderByDesc('periode')
            ->orderByDesc('id')
            ->paginate(100)
            ->withQueryString();

        return view('admin.tagihan.index', compact('data', 'search', 'status'));
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

        $created = MonthlyTagihanGenerator::generate($validated['periode']);

        return redirect()->route('admin.tagihan.index')->with('success', $created.' tagihan berhasil dibuat');
    }

    private function siswaAkademikOptions()
    {
        return SiswaAkademik::with('siswa', 'rombel', 'rayon', 'tahunAjaran')
            ->where('siswa_akademik.status', 'aktif')
            ->join('siswa', 'siswa.id', '=', 'siswa_akademik.siswa_id')
            ->join('rayon', 'rayon.id', '=', 'siswa_akademik.rayon_id')
            ->select('siswa_akademik.*')
            ->orderBy('rayon.nama')
            ->orderBy('siswa.nis')
            ->get()
            ->values();
    }
}
