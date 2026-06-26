<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TagihanInfak;
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

        return view('ortu.pembayaran.index', compact('data'));
    }

    public function create()
    {
        $siswaIds = $this->siswaIds();

        return view('ortu.pembayaran.form', [
            'siswa' => Siswa::whereIn('id', $siswaIds)->orderBy('nama')->get(),
            'tagihan' => TagihanInfak::with('siswaAkademik.siswa')
                ->whereHas('siswaAkademik', fn ($query) => $query->whereIn('siswa_id', $siswaIds))
                ->whereIn('status', ['belum', 'sebagian'])
                ->orderBy('periode')
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
            'tagihan_infak_ids' => ['required', 'array', 'min:1'],
            'tagihan_infak_ids.*' => ['exists:tagihan_infak,id'],
        ]);

        $allowedTagihanCount = TagihanInfak::whereIn('id', $validated['tagihan_infak_ids'])
            ->whereHas('siswaAkademik', fn ($query) => $query->whereIn('siswa_id', $siswaIds))
            ->count();

        abort_if($allowedTagihanCount !== count($validated['tagihan_infak_ids']), 403);

        $path = $request->file('bukti_transfer')->store('bukti-pembayaran', 'public');

        DB::transaction(function () use ($validated, $path) {
            $pembayaran = Pembayaran::create([
                'siswa_id' => $validated['siswa_id'],
                'tanggal' => $validated['tanggal'],
                'nominal' => $validated['nominal'],
                'bukti_transfer' => $path,
                'status_verifikasi' => 'pending',
            ]);

            $remaining = $pembayaran->nominal;

            TagihanInfak::whereIn('id', $validated['tagihan_infak_ids'])
                ->orderBy('periode')
                ->get()
                ->each(function (TagihanInfak $tagihan) use ($pembayaran, &$remaining) {
                    if ($remaining <= 0) {
                        return;
                    }

                    $amount = min($remaining, $tagihan->sisa);

                    if ($amount > 0) {
                        $pembayaran->alokasiPembayaran()->create([
                            'tagihan_infak_id' => $tagihan->id,
                            'nominal' => $amount,
                        ]);

                        $remaining -= $amount;
                    }
                });
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
}
