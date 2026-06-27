<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomitmenInfak;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use App\Support\InfakStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KomitmenInfakController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString();

        $data = KomitmenInfak::with('siswaAkademik.siswa', 'siswaAkademik.rombel', 'siswaAkademik.rayon', 'siswaAkademik.tahunAjaran')
            ->join('siswa_akademik', 'siswa_akademik.id', '=', 'komitmen_infak.siswa_akademik_id')
            ->join('siswa', 'siswa.id', '=', 'siswa_akademik.siswa_id')
            ->join('rayon', 'rayon.id', '=', 'siswa_akademik.rayon_id')
            ->join('rombel', 'rombel.id', '=', 'siswa_akademik.rombel_id')
            ->join('tahun_ajaran', 'tahun_ajaran.id', '=', 'siswa_akademik.tahun_ajaran_id')
            ->select('komitmen_infak.*')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('siswa.nis', 'like', '%'.$search.'%')
                        ->orWhere('siswa.nama', 'like', '%'.$search.'%')
                        ->orWhere('rayon.nama', 'like', '%'.$search.'%')
                        ->orWhere('rombel.nama', 'like', '%'.$search.'%')
                        ->orWhere('tahun_ajaran.nama', 'like', '%'.$search.'%')
                        ->orWhere('komitmen_infak.nominal_bulanan', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('rayon.nama')
            ->orderBy('siswa.nis')
            ->paginate(100)
            ->withQueryString();

        return view('admin.komitmen.index', [
            'data' => $data,
            'prefix' => $this->prefix(),
        ]);
    }

    public function create()
    {
        return view('admin.komitmen.form', [
            'komitmen' => new KomitmenInfak,
            'siswaAkademik' => $this->siswaAkademikOptions(),
            'action' => route($this->prefix().'.komitmen-infak.store'),
            'method' => 'POST',
            'prefix' => $this->prefix(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_akademik_id' => ['required', 'exists:siswa_akademik,id', 'unique:komitmen_infak,siswa_akademik_id'],
            'nominal_bulanan' => ['required', 'integer', 'min:1000'],
            'mulai_bulan' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated) {
            $komitmen = KomitmenInfak::create($validated);
            $this->syncFutureUnpaidTagihan($komitmen);
        });

        return redirect()->route($this->prefix().'.komitmen-infak.index')->with('success', 'Komitmen infak berhasil disimpan');
    }

    public function edit(KomitmenInfak $komitmenInfak)
    {
        return view('admin.komitmen.form', [
            'komitmen' => $komitmenInfak,
            'siswaAkademik' => $this->siswaAkademikOptions(),
            'action' => route($this->prefix().'.komitmen-infak.update', $komitmenInfak),
            'method' => 'PUT',
            'prefix' => $this->prefix(),
        ]);
    }

    public function update(Request $request, KomitmenInfak $komitmenInfak)
    {
        $validated = $request->validate([
            'siswa_akademik_id' => ['required', 'exists:siswa_akademik,id', 'unique:komitmen_infak,siswa_akademik_id,'.$komitmenInfak->id],
            'nominal_bulanan' => ['required', 'integer', 'min:1000'],
            'mulai_bulan' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($komitmenInfak, $validated) {
            $komitmenInfak->update($validated);
            $this->syncFutureUnpaidTagihan($komitmenInfak);
        });

        return redirect()->route($this->prefix().'.komitmen-infak.index')->with('success', 'Komitmen infak berhasil diperbarui');
    }

    public function destroy(KomitmenInfak $komitmenInfak)
    {
        $komitmenInfak->delete();

        return redirect()->route($this->prefix().'.komitmen-infak.index')->with('success', 'Komitmen infak berhasil dihapus');
    }

    private function prefix(): string
    {
        return str(request()->route()?->getName() ?? 'admin.')->before('.')->toString();
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

    private function syncFutureUnpaidTagihan(KomitmenInfak $komitmen): void
    {
        $mulai = $komitmen->mulai_bulan?->format('Y-m') ?? now()->format('Y-m');

        TagihanInfak::where('siswa_akademik_id', $komitmen->siswa_akademik_id)
            ->where('periode', '>=', $mulai)
            ->whereDoesntHave('alokasiPembayaran.pembayaran', fn ($query) => $query->where('status_verifikasi', 'valid'))
            ->get()
            ->each(function (TagihanInfak $tagihan) use ($komitmen) {
                $tagihan->forceFill(['nominal' => $komitmen->nominal_bulanan])->save();
                InfakStatus::refreshTagihan($tagihan);
            });
    }
}
