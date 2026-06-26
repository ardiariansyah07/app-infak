<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\Siswa;
use App\Models\TagihanInfak;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $monthlyPayments = collect(range(5, 0))
            ->map(function (int $monthsAgo) {
                $month = Carbon::now()->subMonths($monthsAgo);

                return [
                    'label' => $month->translatedFormat('M'),
                    'total' => Pembayaran::where('status_verifikasi', 'valid')
                        ->whereYear('tanggal', $month->year)
                        ->whereMonth('tanggal', $month->month)
                        ->sum('nominal'),
                ];
            });

        $maxMonthlyPayment = max(1, (int) $monthlyPayments->max('total'));
        $tagihanStatus = TagihanInfak::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $totalTagihanRows = max(1, (int) $tagihanStatus->sum());

        return view('admin.dashboard', [
            'totalSiswa' => Siswa::count(),
            'totalRayon' => Rayon::count(),
            'totalTagihan' => TagihanInfak::sum('nominal'),
            'totalPembayaran' => Pembayaran::where('status_verifikasi', 'valid')->sum('nominal'),
            'pendingPembayaran' => Pembayaran::where('status_verifikasi', 'pending')->count(),
            'monthlyPayments' => $monthlyPayments,
            'maxMonthlyPayment' => $maxMonthlyPayment,
            'tagihanStatus' => $tagihanStatus,
            'totalTagihanRows' => $totalTagihanRows,
            'totalTunggakan' => max(0, TagihanInfak::sum('nominal') - Pembayaran::where('status_verifikasi', 'valid')->sum('nominal')),
        ]);
    }
}
