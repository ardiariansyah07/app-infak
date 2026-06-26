<?php

namespace App\Support;

use App\Models\TagihanInfak;

class InfakStatus
{
    public static function refreshTagihan(TagihanInfak $tagihan): void
    {
        $tagihan->loadMissing('alokasiPembayaran.pembayaran');

        $terbayar = $tagihan->alokasiPembayaran
            ->filter(fn ($alokasi) => $alokasi->pembayaran?->status_verifikasi === 'valid')
            ->sum('nominal');

        $status = match (true) {
            $terbayar <= 0 => 'belum',
            $terbayar < $tagihan->nominal => 'sebagian',
            default => 'lunas',
        };

        $tagihan->forceFill(['status' => $status])->save();
    }
}
