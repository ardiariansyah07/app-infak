<?php

namespace App\Http\Controllers\Rayon;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        $search = $request->string('q')->toString();

        $data = SiswaAkademik::with('siswa', 'rombel', 'rayon', 'komitmenInfak')
            ->leftJoin('siswa', 'siswa.id', '=', 'siswa_akademik.siswa_id')
            ->select('siswa_akademik.*')
            ->selectSub(function ($query) {
                $query->from('tagihan_infak')
                    ->join('siswa_akademik as semua_akademik', 'semua_akademik.id', '=', 'tagihan_infak.siswa_akademik_id')
                    ->whereColumn('semua_akademik.siswa_id', 'siswa_akademik.siswa_id')
                    ->where('tagihan_infak.status', 'lunas')
                    ->selectRaw('COUNT(*)');
            }, 'bulan_lunas_count')
            ->selectSub(function ($query) {
                $query->from('tagihan_infak')
                    ->join('siswa_akademik as semua_akademik', 'semua_akademik.id', '=', 'tagihan_infak.siswa_akademik_id')
                    ->whereColumn('semua_akademik.siswa_id', 'siswa_akademik.siswa_id')
                    ->whereIn('tagihan_infak.status', ['belum', 'sebagian'])
                    ->selectRaw('COUNT(*)');
            }, 'bulan_belum_count')
            ->selectSub(function ($query) {
                $query->from('tagihan_infak')
                    ->join('siswa_akademik as semua_akademik', 'semua_akademik.id', '=', 'tagihan_infak.siswa_akademik_id')
                    ->whereColumn('semua_akademik.siswa_id', 'siswa_akademik.siswa_id')
                    ->whereIn('tagihan_infak.status', ['belum', 'sebagian'])
                    ->selectRaw('COALESCE(SUM(tagihan_infak.nominal), 0)');
            }, 'nominal_tunggakan')
            ->when($guru, fn ($query) => $query->whereHas('rayon', fn ($rayon) => $rayon->where('guru_id', $guru->id)))
            ->when(! $guru, fn ($query) => $query->whereRaw('1 = 0'))
            ->where('siswa_akademik.status', 'aktif')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('siswa.nis', 'like', '%'.$search.'%')
                        ->orWhere('siswa.nama', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('siswa.nis')
            ->paginate(100)
            ->withQueryString();

        $totalTunggakan = DB::table('siswa_akademik as aktif')
            ->join('siswa', 'siswa.id', '=', 'aktif.siswa_id')
            ->leftJoin('siswa_akademik as semua_akademik', 'semua_akademik.siswa_id', '=', 'siswa.id')
            ->leftJoin('tagihan_infak', 'tagihan_infak.siswa_akademik_id', '=', 'semua_akademik.id')
            ->when($guru, fn ($query) => $query->whereExists(function ($exists) use ($guru) {
                $exists->selectRaw('1')
                    ->from('rayon')
                    ->whereColumn('rayon.id', 'aktif.rayon_id')
                    ->where('rayon.guru_id', $guru->id);
            }))
            ->when(! $guru, fn ($query) => $query->whereRaw('1 = 0'))
            ->where('aktif.status', 'aktif')
            ->whereIn('tagihan_infak.status', ['belum', 'sebagian'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('siswa.nis', 'like', '%'.$search.'%')
                        ->orWhere('siswa.nama', 'like', '%'.$search.'%');
                });
            })
            ->sum('tagihan_infak.nominal');

        return view('rayon.siswa.index', compact('data', 'search', 'totalTunggakan'));
    }

    public function show(Siswa $siswa)
    {
        $guru = Auth::user()->guru;

        abort_unless(
            $guru && $siswa->akademik()
                ->where('status', 'aktif')
                ->whereHas('rayon', fn ($query) => $query->where('guru_id', $guru->id))
                ->exists(),
            403
        );

        $siswa->load(
            'akademik.tahunAjaran',
            'akademik.rombel',
            'akademik.rayon',
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

        return view('rayon.siswa.show', compact('siswa', 'tagihan', 'riwayat'));
    }
}
