<?php

namespace App\Support;

use App\Models\Siswa;
use App\Models\TahunAjaran;

class AkademikStatus
{
    public static function syncAll(): void
    {
        $tahunAktifId = TahunAjaran::where('aktif', true)->value('id');

        Siswa::with('akademik.tahunAjaran')->chunk(100, function ($siswaList) use ($tahunAktifId) {
            foreach ($siswaList as $siswa) {
                self::syncSiswa($siswa, $tahunAktifId);
            }
        });
    }

    public static function syncSiswa(Siswa $siswa, ?int $tahunAktifId = null): void
    {
        $tahunAktifId ??= TahunAjaran::where('aktif', true)->value('id');

        if ($siswa->status === 'alumni') {
            $siswa->akademik()->update(['status' => 'alumni']);

            return;
        }

        $siswa->akademik()->update(['status' => 'naik']);

        if ($tahunAktifId) {
            $siswa->akademik()
                ->where('tahun_ajaran_id', $tahunAktifId)
                ->update(['status' => 'aktif']);
        }
    }
}
