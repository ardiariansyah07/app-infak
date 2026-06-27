<?php

namespace App\Http\Controllers\Rayon;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Support\InfakAllocator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PembayaranController extends Controller
{
    public function index()
    {
        $siswaIds = $this->siswaIds();

        $data = Pembayaran::with('siswa.akademikAktif.rombel', 'siswa.akademikAktif.rayon', 'tagihanInfak')
            ->whereIn('siswa_id', $siswaIds)
            ->latest()
            ->paginate(100);

        return view('rayon.pembayaran.index', compact('data'));
    }

    public function create()
    {
        $siswaIds = $this->siswaIds();

        if (! Auth::user()->guru) {
            return redirect()->route('rayon.pembayaran.index')
                ->with('error', 'Akun pembimbing belum terhubung ke data guru. Hubungi admin untuk menghubungkan akun dengan guru pembimbing rayon.');
        }

        return view('rayon.pembayaran.form', [
            'siswa' => Siswa::with('akademikAktif.rombel', 'akademikAktif.rayon')
                ->whereIn('siswa.id', $siswaIds)
                ->leftJoin('siswa_akademik as akademik_aktif', function ($join) {
                    $join->on('akademik_aktif.siswa_id', '=', 'siswa.id')
                        ->where('akademik_aktif.status', 'aktif');
                })
                ->leftJoin('rayon', 'rayon.id', '=', 'akademik_aktif.rayon_id')
                ->select('siswa.*')
                ->orderBy('rayon.nama')
                ->orderBy('siswa.nis')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $siswaIds = $this->siswaIds()->all();

        $validated = $request->validate([
            'siswa_id' => ['required', Rule::in($siswaIds)],
            'tanggal' => ['required', 'date'],
            'nominal' => ['required', 'integer', 'min:1000'],
            'bukti_transfer' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $path = $request->file('bukti_transfer')->store('bukti-pembayaran', 'public');

        DB::transaction(function () use ($validated, $path) {
            $pembayaran = Pembayaran::create([
                'siswa_id' => $validated['siswa_id'],
                'tanggal' => $validated['tanggal'],
                'nominal' => $validated['nominal'],
                'bukti_transfer' => $path,
                'sumber' => Pembayaran::SUMBER_PEMBIMBING,
                'metode_pembayaran' => Pembayaran::METODE_TRANSFER,
                'status_verifikasi' => 'pending',
            ]);

            InfakAllocator::allocateOldest($pembayaran);
        });

        return redirect()->route('rayon.pembayaran.index')
            ->with('success', 'Laporan pembayaran dikirim dan menunggu validasi');
    }

    private function siswaIds()
    {
        $guru = Auth::user()->guru;

        if (! $guru) {
            return collect();
        }

        return Siswa::whereHas('akademikAktif.rayon', fn ($query) => $query->where('guru_id', $guru->id))
            ->pluck('id');
    }
}
