<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_PETUGAS = 'petugas_infak';

    public const ROLE_PEMBIMBING = 'pembimbing_rayon';

    public const ROLE_ORANG_TUA = 'orang_tua';

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (! $user->role) {
                $user->role = self::ROLE_ORANG_TUA;
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPetugas()
    {
        return $this->role === self::ROLE_PETUGAS;
    }

    public function isPembimbing()
    {
        return $this->role === self::ROLE_PEMBIMBING;
    }

    public function isOrtu()
    {
        return $this->role === self::ROLE_ORANG_TUA;
    }

    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    public function orangTua()
    {
        return $this->hasOne(OrangTua::class);
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }
}
