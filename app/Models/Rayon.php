<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rayon extends Model
{
    protected $table = 'rayon';

    protected $fillable = [
        'nama',
        'guru_id',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function siswaAkademik()
    {
        return $this->hasMany(SiswaAkademik::class);
    }
}
