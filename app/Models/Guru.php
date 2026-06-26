<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';

    protected $fillable = [

        'nip',

        'user_id',

        'nama',

        'jenis_kelamin',

        'no_hp',

        'email',

        'alamat',

        'aktif',

    ];

    protected $casts = [

        'aktif' => 'boolean',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rayons()
    {
        return $this->hasMany(Rayon::class);
    }
}
