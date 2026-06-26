<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanInfak;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $siswaIds = $this->siswaIds();

        return view('ortu.dashboard', [
            'jumlahAnak' => $siswaIds->count(),
            'tagihanAktif' => TagihanInfak::whereHas(
                'siswaAkademik',
                fn ($query) => $query->whereIn('siswa_id', $siswaIds)
            )->whereIn('status', ['belum', 'sebagian'])->count(),
            'pembayaranPending' => Pembayaran::whereIn('siswa_id', $siswaIds)
                ->where('status_verifikasi', 'pending')
                ->count(),
        ]);
    }

    private function siswaIds()
    {
        $user = Auth::user();

        if ($user->siswa) {
            return collect([$user->siswa->id]);
        }

        return $user->orangTua?->siswa()->pluck('siswa.id') ?? collect();
    }
}
