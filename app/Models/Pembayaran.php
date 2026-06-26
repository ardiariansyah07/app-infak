<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'nominal',
        'bukti_transfer',
        'status_verifikasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function alokasiPembayaran()
    {
        return $this->hasMany(AlokasiPembayaran::class);
    }

    public function tagihanInfak()
    {
        return $this->belongsToMany(TagihanInfak::class, 'alokasi_pembayaran')
            ->withPivot('nominal')
            ->withTimestamps();
    }
}
