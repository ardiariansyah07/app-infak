<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    protected $table = 'rombel';

    protected $fillable = [
        'nama',
        'tingkat',
    ];

    public function siswaAkademik()
    {
        return $this->hasMany(SiswaAkademik::class);
    }
}
