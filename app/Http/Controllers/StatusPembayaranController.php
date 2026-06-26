<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TagihanInfak;
use Illuminate\Http\Request;

class StatusPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->toString();

        $data = Siswa::with('akademikAktif.rombel', 'akademikAktif.rayon')
            ->with(['akademik.tagihanInfak' => fn ($query) => $query->orderBy('periode')])
            ->leftJoin('siswa_akademik as akademik_aktif', function ($join) {
                $join->on('akademik_aktif.siswa_id', '=', 'siswa.id')
                    ->where('akademik_aktif.status', 'aktif');
            })
            ->leftJoin('rayon', 'rayon.id', '=', 'akademik_aktif.rayon_id')
            ->select('siswa.*')
            ->where('siswa.status', 'aktif')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('siswa.nis', 'like', '%'.$search.'%')
                        ->orWhere('siswa.nama', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('rayon.nama')
            ->orderBy('siswa.nis')
            ->paginate(100)
            ->withQueryString();

        return view('pembayaran.status.index', [
            'data' => $data,
            'prefix' => $this->prefix($request),
            'search' => $search,
        ]);
    }

    public function show(Request $request, Siswa $siswa)
    {
        $siswa->load(
            'akademik.tahunAjaran',
            'akademik.rombel',
            'akademik.rayon',
            'akademik.tagihanInfak.alokasiPembayaran.pembayaran',
        );

        $tagihan = TagihanInfak::with('siswaAkademik.tahunAjaran', 'siswaAkademik.rombel', 'siswaAkademik.rayon')
            ->whereHas('siswaAkademik', fn ($query) => $query->where('siswa_id', $siswa->id))
            ->orderBy('periode')
            ->get();

        $riwayat = Pembayaran::with([
            'alokasiPembayaran.tagihanInfak.siswaAkademik.tahunAjaran',
            'tagihanInfak',
        ])
            ->where('siswa_id', $siswa->id)
            ->latest('tanggal')
            ->get();

        return view('pembayaran.status.show', [
            'siswa' => $siswa,
            'tagihan' => $tagihan,
            'riwayat' => $riwayat,
            'prefix' => $this->prefix($request),
        ]);
    }

    private function prefix(Request $request): string
    {
        return str($request->route()?->getName() ?? 'admin.')->before('.')->toString();
    }
}
