<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Rayon;
use App\Models\Siswa;
use App\Models\TagihanInfak;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalSiswa' => Siswa::count(),
            'totalRayon' => Rayon::count(),
            'totalTagihan' => TagihanInfak::sum('nominal'),
            'totalPembayaran' => Pembayaran::where('status_verifikasi', 'valid')->sum('nominal'),
            'pendingPembayaran' => Pembayaran::where('status_verifikasi', 'pending')->count(),
        ]);
    }
}
