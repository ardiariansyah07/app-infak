<?php

namespace App\Support;

class Periode
{
    public static function label(?string $periode): string
    {
        if (! $periode || ! preg_match('/^\d{4}-\d{2}$/', $periode)) {
            return '-';
        }

        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        [$year, $month] = explode('-', $periode);

        return ($months[$month] ?? $month).' '.$year;
    }

    public static function labels($tagihan): string
    {
        return collect($tagihan)
            ->map(fn ($item) => self::label($item->periode ?? null))
            ->filter()
            ->join(', ');
    }
}
