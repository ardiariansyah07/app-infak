<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    public const SUMBER_ADMIN = 'admin';

    public const SUMBER_PETUGAS = 'petugas_infak';

    public const SUMBER_ORANG_TUA = 'orang_tua';

    public const SUMBER_PEMBIMBING = 'pembimbing_rayon';

    public const SUMBER_IMPORT_SALDO_AWAL = 'import_saldo_awal';

    public const SUMBER_IMPORT_TAGIHAN_AWAL = 'import_tagihan_awal';

    public const METODE_CASH = 'cash';

    public const METODE_TRANSFER = 'transfer';

    public const METODE_SALDO_AWAL = 'saldo_awal';

    protected $table = 'pembayaran';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'nominal',
        'bukti_transfer',
        'sumber',
        'metode_pembayaran',
        'status_verifikasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if ($search === '') {
            return $query;
        }

        $needle = strtolower($search);
        $matchingSources = collect([
            self::SUMBER_ADMIN => 'Input admin',
            self::SUMBER_PETUGAS => 'Input petugas infak',
            self::SUMBER_ORANG_TUA => 'Unggahan orang tua',
            self::SUMBER_PEMBIMBING => 'Unggahan pembimbing rayon',
            self::SUMBER_IMPORT_SALDO_AWAL => 'Import saldo awal',
            self::SUMBER_IMPORT_TAGIHAN_AWAL => 'Import tagihan awal',
            'unggahan_lama' => 'Unggahan bukti',
        ])->filter(fn ($label) => str_contains(strtolower($label), $needle))->keys();
        $matchingMethods = collect([
            self::METODE_CASH => 'Cash',
            self::METODE_TRANSFER => 'Transfer',
            self::METODE_SALDO_AWAL => 'Saldo awal',
        ])->filter(fn ($label) => str_contains(strtolower($label), $needle))->keys();

        return $query->where(function (Builder $query) use ($search, $matchingSources, $matchingMethods) {
            $query->whereHas('siswa', function (Builder $query) use ($search) {
                $query->where('nis', 'like', '%'.$search.'%')
                    ->orWhere('nama', 'like', '%'.$search.'%');
            })
                ->orWhere('tanggal', 'like', '%'.$search.'%')
                ->orWhere('nominal', 'like', '%'.$search.'%')
                ->orWhere('status_verifikasi', 'like', '%'.$search.'%')
                ->when($matchingSources->isNotEmpty(), fn (Builder $query) => $query->orWhereIn('sumber', $matchingSources))
                ->when($matchingMethods->isNotEmpty(), fn (Builder $query) => $query->orWhereIn('metode_pembayaran', $matchingMethods));
        });
    }

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

    public function punyaBuktiUnggahan(): bool
    {
        return filled($this->bukti_transfer)
            && in_array($this->sumber, [self::SUMBER_ORANG_TUA, self::SUMBER_PEMBIMBING, 'unggahan_lama'], true);
    }

    public function labelSumber(): string
    {
        return match ($this->sumber) {
            self::SUMBER_ADMIN => 'Input admin',
            self::SUMBER_PETUGAS => 'Input petugas infak',
            self::SUMBER_ORANG_TUA => 'Unggahan orang tua',
            self::SUMBER_PEMBIMBING => 'Unggahan pembimbing rayon',
            self::SUMBER_IMPORT_SALDO_AWAL => 'Import saldo awal',
            self::SUMBER_IMPORT_TAGIHAN_AWAL => 'Import tagihan awal',
            'unggahan_lama' => 'Unggahan bukti',
            default => 'Input manual',
        };
    }

    public function labelMetodePembayaran(): string
    {
        return match ($this->metode_pembayaran) {
            self::METODE_CASH => 'Cash',
            self::METODE_TRANSFER => 'Transfer',
            self::METODE_SALDO_AWAL => 'Saldo awal',
            default => '-',
        };
    }
}
