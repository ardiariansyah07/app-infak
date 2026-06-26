<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\SiswaAkademik;
use App\Models\TagihanInfak;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->string('periode')->toString();

        $tagihanQuery = TagihanInfak::query();
        $pembayaranQuery = Pembayaran::query();

        if ($periode !== '') {
            $tagihanQuery->where('periode', $periode);
            $pembayaranQuery->whereYear('tanggal', substr($periode, 0, 4))
                ->whereMonth('tanggal', substr($periode, 5, 2));
        }

        $totalTagihan = (clone $tagihanQuery)->sum('nominal');
        $pembayaranValid = (clone $pembayaranQuery)->where('status_verifikasi', 'valid')->sum('nominal');
        $pembayaranPending = (clone $pembayaranQuery)->where('status_verifikasi', 'pending')->count();
        $tunggakan = max(0, $totalTagihan - $pembayaranValid);

        $statusTagihan = (clone $tagihanQuery)
            ->selectRaw('status, COUNT(*) as total, SUM(nominal) as nominal')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $rekapRayon = Rayon::with('guru')
            ->withCount(['siswaAkademik as siswa_aktif_count' => fn ($query) => $query->where('status', 'aktif')])
            ->get()
            ->map(function (Rayon $rayon) use ($periode) {
                $akademikIds = SiswaAkademik::where('rayon_id', $rayon->id)->pluck('id');
                $siswaIds = SiswaAkademik::where('rayon_id', $rayon->id)->pluck('siswa_id')->unique();

                $tagihan = TagihanInfak::whereIn('siswa_akademik_id', $akademikIds)
                    ->when($periode !== '', fn ($query) => $query->where('periode', $periode))
                    ->sum('nominal');

                $pembayaran = Pembayaran::whereIn('siswa_id', $siswaIds)
                    ->where('status_verifikasi', 'valid')
                    ->when($periode !== '', function ($query) use ($periode) {
                        $query->whereYear('tanggal', substr($periode, 0, 4))
                            ->whereMonth('tanggal', substr($periode, 5, 2));
                    })
                    ->sum('nominal');

                return [
                    'nama' => $rayon->nama,
                    'pembimbing' => $rayon->guru?->nama ?? '-',
                    'siswa' => $rayon->siswa_aktif_count,
                    'tagihan' => $tagihan,
                    'pembayaran' => $pembayaran,
                    'tunggakan' => max(0, $tagihan - $pembayaran),
                ];
            })
            ->sortByDesc('tunggakan')
            ->values();

        $siswaMenunggak = TagihanInfak::with('siswaAkademik.siswa', 'siswaAkademik.rombel', 'siswaAkademik.rayon')
            ->when($periode !== '', fn ($query) => $query->where('periode', $periode))
            ->whereIn('status', ['belum', 'sebagian'])
            ->latest()
            ->limit(25)
            ->get();

        return view('admin.laporan.index', compact(
            'periode',
            'totalTagihan',
            'pembayaranValid',
            'pembayaranPending',
            'tunggakan',
            'statusTagihan',
            'rekapRayon',
            'siswaMenunggak',
        ));
    }
}
