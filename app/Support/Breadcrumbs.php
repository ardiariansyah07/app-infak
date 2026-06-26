<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Breadcrumbs
{
    public static function items(): array
    {
        $routeName = Route::currentRouteName();

        if (! $routeName) {
            return [];
        }

        if (str_starts_with($routeName, 'profile.')) {
            return [
                ['title' => 'Dashboard', 'url' => route('dashboard')],
                ['title' => 'Profil'],
            ];
        }

        $parts = explode('.', $routeName);
        $area = $parts[0] ?? null;
        $resource = $parts[1] ?? null;
        $action = $parts[2] ?? null;
        $dashboardRoute = self::dashboardRoute($area);

        if (! $dashboardRoute) {
            return [];
        }

        if ($routeName === $dashboardRoute) {
            return [
                ['title' => 'Dashboard'],
            ];
        }

        $items = [
            ['title' => 'Dashboard', 'url' => route($dashboardRoute)],
        ];

        if (! $resource) {
            return $items;
        }

        $resourceTitle = self::resourceTitle($area, $resource);
        $indexRoute = $area.'.'.$resource.'.index';

        if ($action && $action !== 'index' && Route::has($indexRoute)) {
            $items[] = [
                'title' => $resourceTitle,
                'url' => route($indexRoute),
            ];
        } else {
            $items[] = [
                'title' => $resourceTitle,
            ];
        }

        if ($action && $action !== 'index') {
            $items[] = [
                'title' => self::actionTitle($action),
            ];
        }

        return $items;
    }

    private static function dashboardRoute(?string $area): ?string
    {
        return match ($area) {
            'admin' => 'admin.dashboard',
            'petugas' => 'petugas.dashboard',
            'rayon' => 'rayon.dashboard',
            'ortu' => 'ortu.dashboard',
            default => null,
        };
    }

    private static function resourceTitle(string $area, string $resource): string
    {
        $labels = [
            'admin' => [
                'tahun-ajaran' => 'Tahun Ajaran',
                'guru' => 'Guru',
                'rayon' => 'Rayon',
                'rombel' => 'Rombel',
                'siswa' => 'Siswa',
                'orang-tua' => 'Orang Tua',
                'user' => 'User & Role',
                'komitmen-infak' => 'Komitmen Infak',
                'tagihan' => 'Tagihan Infak',
                'pembayaran' => 'Pembayaran',
                'status-pembayaran' => 'Status Pembayaran',
                'laporan' => 'Laporan',
            ],
            'petugas' => [
                'pembayaran' => 'Pembayaran',
                'status-pembayaran' => 'Status Pembayaran',
                'komitmen-infak' => 'Komitmen Infak',
            ],
            'rayon' => [
                'siswa' => 'Siswa Rayon',
            ],
            'ortu' => [
                'komitmen' => 'Nominal Infak',
                'pembayaran' => 'Pembayaran',
            ],
        ];

        return $labels[$area][$resource]
            ?? Str::headline(str_replace('-', ' ', $resource));
    }

    private static function actionTitle(string $action): string
    {
        return match ($action) {
            'create' => 'Tambah',
            'edit' => 'Edit',
            'show' => 'Detail',
            'pdf' => 'PDF',
            default => Str::headline(str_replace('-', ' ', $action)),
        };
    }
}
