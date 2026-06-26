<?php

namespace App\Support;

use App\Models\Siswa;
use App\Models\SiswaAkademik;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;

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

    public static function syncMany(array|Collection $siswaIds): void
    {
        $ids = collect($siswaIds)->filter()->unique()->values();

        if ($ids->isEmpty()) {
            return;
        }

        $tahunAktifId = TahunAjaran::where('aktif', true)->value('id');

        $alumniIds = Siswa::whereIn('id', $ids)
            ->where('status', 'alumni')
            ->pluck('id');

        if ($alumniIds->isNotEmpty()) {
            SiswaAkademik::whereIn('siswa_id', $alumniIds)->update(['status' => 'alumni']);
        }

        $aktifIds = Siswa::whereIn('id', $ids)
            ->where('status', 'aktif')
            ->pluck('id');

        if ($aktifIds->isEmpty()) {
            return;
        }

        SiswaAkademik::whereIn('siswa_id', $aktifIds)->update(['status' => 'naik']);

        if ($tahunAktifId) {
            SiswaAkademik::whereIn('siswa_id', $aktifIds)
                ->where('tahun_ajaran_id', $tahunAktifId)
                ->update(['status' => 'aktif']);
        }
    }
}
