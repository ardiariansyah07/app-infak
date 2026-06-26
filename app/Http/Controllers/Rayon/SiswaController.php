<?php

namespace App\Http\Controllers\Rayon;

use App\Http\Controllers\Controller;
use App\Models\SiswaAkademik;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->guru;

        $data = SiswaAkademik::with('siswa', 'rombel', 'rayon', 'komitmenInfak', 'tagihanInfak')
            ->when($guru, fn ($query) => $query->whereHas('rayon', fn ($rayon) => $rayon->where('guru_id', $guru->id)))
            ->when(! $guru, fn ($query) => $query->whereRaw('1 = 0'))
            ->where('status', 'aktif')
            ->get();

        return view('rayon.siswa.index', compact('data'));
    }
}
