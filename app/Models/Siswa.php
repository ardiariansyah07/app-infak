<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'jenis_kelamin',
        'status',
    ];

    public function akademik()
    {
        return $this->hasMany(SiswaAkademik::class);
    }

    public function akademikAktif()
    {
        return $this->hasOne(SiswaAkademik::class)->where('status', 'aktif');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orangTua()
    {
        return $this->belongsToMany(OrangTua::class, 'siswa_orang_tua');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
