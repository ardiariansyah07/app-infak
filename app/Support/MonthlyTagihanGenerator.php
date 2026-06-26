<?php

namespace App\Support;

use App\Models\KomitmenInfak;
use App\Models\TagihanInfak;

class MonthlyTagihanGenerator
{
    public static function generateCurrent(): int
    {
        return self::generate(now()->format('Y-m'));
    }

    public static function generate(string $periode): int
    {
        $created = 0;

        KomitmenInfak::with('siswaAkademik')
            ->whereHas('siswaAkademik', fn ($query) => $query->where('status', 'aktif'))
            ->each(function (KomitmenInfak $komitmen) use ($periode, &$created) {
                $tagihan = TagihanInfak::firstOrCreate(
                    [
                        'siswa_akademik_id' => $komitmen->siswa_akademik_id,
                        'periode' => $periode,
                    ],
                    [
                        'nominal' => $komitmen->nominal_bulanan,
                        'status' => 'belum',
                    ]
                );

                if ($tagihan->wasRecentlyCreated) {
                    $created++;
                }
            });

        return $created;
    }
}
