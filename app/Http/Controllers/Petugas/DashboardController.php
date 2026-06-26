<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanInfak;

class DashboardController extends Controller
{
    public function index()
    {
        return view('petugas.dashboard', [
            'pendingPembayaran' => Pembayaran::where('status_verifikasi', 'pending')->count(),
            'validPembayaran' => Pembayaran::where('status_verifikasi', 'valid')->count(),
            'tagihanBelum' => TagihanInfak::whereIn('status', ['belum', 'sebagian'])->count(),
        ]);
    }
}
