<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TagihanInfak;
use App\Support\InfakAllocator;
use App\Support\InfakStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $data = Pembayaran::with('siswa.akademikAktif.rombel', 'siswa.akademikAktif.rayon', 'tagihanInfak')
            ->latest()
            ->get();

        return view('pembayaran.index', [
            'data' => $data,
            'prefix' => $this->prefix($request),
        ]);
    }

    public function create(Request $request)
    {
        return view('pembayaran.form', [
            'siswa' => Siswa::with('akademikAktif')->where('status', 'aktif')->orderBy('nis')->get(),
            'tagihan' => TagihanInfak::with('siswaAkademik.siswa')
                ->whereIn('status', ['belum', 'sebagian'])
                ->orderBy('periode')
                ->get(),
            'prefix' => $this->prefix($request),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'tanggal' => ['required', 'date'],
            'nominal' => ['required', 'integer', 'min:1000'],
            'tagihan_infak_ids' => ['array'],
            'tagihan_infak_ids.*' => ['exists:tagihan_infak,id'],
        ]);

        $this->ensureRequestedTagihanBelongToSiswa($validated['tagihan_infak_ids'] ?? [], (int) $validated['siswa_id']);

        DB::transaction(function () use ($validated) {
            $pembayaran = Pembayaran::create([
                'siswa_id' => $validated['siswa_id'],
                'tanggal' => $validated['tanggal'],
                'nominal' => $validated['nominal'],
                'status_verifikasi' => 'valid',
            ]);

            InfakAllocator::allocateOldest($pembayaran);
        });

        return redirect()->route($this->routeName($request, 'pembayaran.index'))
            ->with('success', 'Pembayaran berhasil diinput');
    }

    public function verify(Request $request, Pembayaran $pembayaran)
    {
        $validated = $request->validate([
            'status_verifikasi' => ['required', 'in:valid,ditolak'],
        ]);

        $pembayaran->update($validated);

        $pembayaran->tagihanInfak->each(fn (TagihanInfak $tagihan) => InfakStatus::refreshTagihan($tagihan));

        return back()->with('success', 'Status pembayaran berhasil diperbarui');
    }

    private function prefix(Request $request): string
    {
        return str($request->route()?->getName() ?? 'admin.')->before('.')->toString();
    }

    private function routeName(Request $request, string $suffix): string
    {
        return $this->prefix($request).'.'.$suffix;
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
