<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\KomitmenInfak;
use App\Models\SiswaAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomitmenInfakController extends Controller
{
    public function index()
    {
        $siswaIds = $this->siswaIds();

        $data = KomitmenInfak::with('siswaAkademik.siswa')
            ->whereHas('siswaAkademik', fn ($query) => $query->whereIn('siswa_id', $siswaIds))
            ->get();

        $siswaAkademik = SiswaAkademik::with('siswa')
            ->whereIn('siswa_id', $siswaIds)
            ->where('status', 'aktif')
            ->get();

        return view('ortu.komitmen.index', compact('data', 'siswaAkademik'));
    }

    public function update(Request $request)
    {
        $siswaIds = $this->siswaIds();

        $validated = $request->validate([
            'siswa_akademik_id' => ['required', 'exists:siswa_akademik,id'],
            'nominal_bulanan' => ['required', 'integer', 'min:1000'],
        ]);

        $akademik = SiswaAkademik::whereIn('siswa_id', $siswaIds)
            ->where('id', $validated['siswa_akademik_id'])
            ->firstOrFail();

        KomitmenInfak::updateOrCreate(
            ['siswa_akademik_id' => $akademik->id],
            ['nominal_bulanan' => $validated['nominal_bulanan']]
        );

        return back()->with('success', 'Nominal infak berhasil diperbarui');
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
