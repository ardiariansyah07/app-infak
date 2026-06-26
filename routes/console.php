<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:version-bump {part=patch : major, minor, atau patch}', function () {
    $part = strtolower((string) $this->argument('part'));

    if (! in_array($part, ['major', 'minor', 'patch'], true)) {
        $this->error('Gunakan part: major, minor, atau patch.');

        return self::FAILURE;
    }

    $path = base_path('VERSION');
    $current = file_exists($path) ? trim((string) file_get_contents($path)) : '1.0.0';
    $segments = array_pad(array_map('intval', explode('.', $current)), 3, 0);

    if ($part === 'major') {
        $segments[0]++;
        $segments[1] = 0;
        $segments[2] = 0;
    }

    if ($part === 'minor') {
        $segments[1]++;
        $segments[2] = 0;
    }

    if ($part === 'patch') {
        $segments[2]++;
    }

    $next = implode('.', array_slice($segments, 0, 3));
    file_put_contents($path, $next.PHP_EOL);

    $this->info("Versi aplikasi naik dari v{$current} ke v{$next}.");

    return self::SUCCESS;
})->purpose('Menaikkan versi aplikasi sesuai jenis perubahan rilis');
