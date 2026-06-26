<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomitmenInfak extends Model
{
    protected $table = 'komitmen_infak';

    protected $fillable = [
        'siswa_akademik_id',
        'nominal_bulanan',
        'mulai_bulan',
    ];

    protected $casts = [
        'mulai_bulan' => 'date',
    ];

    public function siswaAkademik()
    {
        return $this->belongsTo(SiswaAkademik::class);
    }
}
