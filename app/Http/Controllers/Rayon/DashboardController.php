<?php

namespace App\Http\Controllers\Rayon;

use App\Http\Controllers\Controller;
use App\Models\SiswaAkademik;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->guru;

        $siswaRayon = SiswaAkademik::when(
            $guru,
            fn ($query) => $query->whereHas('rayon', fn ($rayon) => $rayon->where('guru_id', $guru->id))
        )
            ->when(! $guru, fn ($query) => $query->whereRaw('1 = 0'))
            ->where('status', 'aktif')
            ->count();

        return view('rayon.dashboard', compact('siswaRayon'));
    }
}
