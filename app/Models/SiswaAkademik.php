<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaAkademik extends Model
{
    protected $table = 'siswa_akademik';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'tingkat',
        'rombel_id',
        'rayon_id',
        'status',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    public function rayon()
    {
        return $this->belongsTo(Rayon::class);
    }

    public function komitmenInfak()
    {
        return $this->hasOne(KomitmenInfak::class);
    }

    public function tagihanInfak()
    {
        return $this->hasMany(TagihanInfak::class);
    }
}
