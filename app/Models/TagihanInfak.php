<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanInfak extends Model
{
    protected $table = 'tagihan_infak';

    protected $fillable = [
        'siswa_akademik_id',
        'periode',
        'nominal',
        'status',
    ];

    public function siswaAkademik()
    {
        return $this->belongsTo(SiswaAkademik::class);
    }

    public function alokasiPembayaran()
    {
        return $this->hasMany(AlokasiPembayaran::class);
    }

    public function getTerbayarAttribute(): int
    {
        return (int) $this->alokasiPembayaran()
            ->whereHas('pembayaran', fn ($query) => $query->where('status_verifikasi', 'valid'))
            ->sum('nominal');
    }

    public function getSisaAttribute(): int
    {
        return max(0, (int) $this->nominal - $this->terbayar);
    }
}
