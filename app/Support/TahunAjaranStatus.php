<?php

namespace App\Support;

use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class TahunAjaranStatus
{
    public static function syncToday(): void
    {
        $today = now()->toDateString();

        DB::transaction(function () use ($today) {
            TahunAjaran::query()->update(['aktif' => false]);

            $active = TahunAjaran::query()
                ->whereDate('tanggal_mulai', '<=', $today)
                ->whereDate('tanggal_selesai', '>=', $today)
                ->orderByDesc('tanggal_mulai')
                ->first();

            $active?->forceFill(['aktif' => true])->save();
        });
    }
}
