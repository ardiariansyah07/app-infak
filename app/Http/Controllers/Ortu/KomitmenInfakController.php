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

        $isSiswaLogin = Auth::user()->siswa !== null;

        return view('ortu.komitmen.index', compact('data', 'siswaAkademik', 'isSiswaLogin'));
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

        if (Auth::user()->siswa && KomitmenInfak::where('siswa_akademik_id', $akademik->id)->exists()) {
            return back()->with('error', 'Nominal infak hanya bisa diisi satu kali. Perubahan dapat dilakukan oleh admin atau petugas infak.');
        }

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
