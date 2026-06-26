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
            ->where('status', 'aktif')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nis', 'like', '%'.$search.'%')
                        ->orWhere('nama', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('nis')
            ->get();

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
