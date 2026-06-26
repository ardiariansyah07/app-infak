<?php

namespace App\Support;

class AppVersion
{
    public static function current(): string
    {
        $path = base_path('VERSION');

        if (! file_exists($path)) {
            return 'v1.0.0';
        }

        $version = trim((string) file_get_contents($path));

        return 'v'.($version !== '' ? $version : '1.0.0');
    }
}
