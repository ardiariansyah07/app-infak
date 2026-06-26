<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlokasiPembayaran extends Model
{
    protected $table = 'alokasi_pembayaran';

    protected $fillable = [
        'pembayaran_id',
        'tagihan_infak_id',
        'nominal',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }

    public function tagihanInfak()
    {
        return $this->belongsTo(TagihanInfak::class);
    }
}
