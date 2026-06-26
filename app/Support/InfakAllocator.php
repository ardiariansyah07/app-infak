<?php

namespace App\Support;

use App\Models\Pembayaran;
use App\Models\TagihanInfak;

class InfakAllocator
{
    public static function allocateOldest(Pembayaran $pembayaran): void
    {
        $remaining = (int) $pembayaran->nominal;

        TagihanInfak::whereHas('siswaAkademik', fn ($query) => $query->where('siswa_id', $pembayaran->siswa_id))
            ->whereIn('status', ['belum', 'sebagian'])
            ->orderBy('periode')
            ->get()
            ->each(function (TagihanInfak $tagihan) use ($pembayaran, &$remaining) {
                if ($remaining <= 0) {
                    return;
                }

                $amount = min($remaining, $tagihan->sisa);

                if ($amount <= 0) {
                    return;
                }

                $pembayaran->alokasiPembayaran()->create([
                    'tagihan_infak_id' => $tagihan->id,
                    'nominal' => $amount,
                ]);

                $remaining -= $amount;

                if ($pembayaran->status_verifikasi === 'valid') {
                    InfakStatus::refreshTagihan($tagihan);
                }
            });
    }
}
