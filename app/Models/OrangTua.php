<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tua';

    protected $fillable = [
        'user_id',
        'nama',
        'no_hp',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'siswa_orang_tua');
    }
}
