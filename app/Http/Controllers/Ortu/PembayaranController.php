<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TagihanInfak;
use App\Support\InfakAllocator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PembayaranController extends Controller
{
    public function index()
    {
        $data = Pembayaran::with('siswa', 'tagihanInfak')
            ->whereIn('siswa_id', $this->siswaIds())
            ->latest()
            ->get();

        $tagihanBulanan = TagihanInfak::whereHas(
            'siswaAkademik',
            fn ($query) => $query->whereIn('siswa_id', $this->siswaIds())
        )->orderBy('periode')->get();

        return view('ortu.pembayaran.index', compact('data', 'tagihanBulanan'));
    }

    public function create()
    {
        $siswaIds = $this->siswaIds();

        return view('ortu.pembayaran.form', [
            'siswa' => Siswa::whereIn('id', $siswaIds)->orderBy('nama')->get(),
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
            'tagihan_infak_ids' => ['array'],
            'tagihan_infak_ids.*' => ['exists:tagihan_infak,id'],
        ]);

        $this->ensureRequestedTagihanBelongToSiswa($validated['tagihan_infak_ids'] ?? [], (int) $validated['siswa_id']);

        $path = $request->file('bukti_transfer')->store('bukti-pembayaran', 'public');

        DB::transaction(function () use ($validated, $path) {
            $pembayaran = Pembayaran::create([
                'siswa_id' => $validated['siswa_id'],
                'tanggal' => $validated['tanggal'],
                'nominal' => $validated['nominal'],
                'bukti_transfer' => $path,
                'status_verifikasi' => 'pending',
            ]);

            InfakAllocator::allocateOldest($pembayaran);
        });

        return redirect()->route('ortu.pembayaran.index')->with('success', 'Laporan pembayaran dikirim dan menunggu validasi');
    }

    private function siswaIds()
    {
        $user = Auth::user();

        if ($user->siswa) {
            return collect([$user->siswa->id]);
        }

        return $user->orangTua?->siswa()->pluck('siswa.id') ?? collect();
    }

    private function ensureRequestedTagihanBelongToSiswa(array $tagihanIds, int $siswaId): void
    {
        if ($tagihanIds === []) {
            return;
        }

        $allowedTagihanCount = TagihanInfak::whereIn('id', $tagihanIds)
            ->whereHas('siswaAkademik', fn ($query) => $query->where('siswa_id', $siswaId))
            ->count();

        abort_if($allowedTagihanCount !== count($tagihanIds), 403);
    }
}
