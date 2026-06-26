<?php

namespace App\Http\Middleware;

use App\Support\MonthlyTagihanGenerator;
use App\Support\AkademikStatus;
use App\Support\TahunAjaranStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AutoGenerateMonthlyTagihan
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        $cacheKey = 'auto-generate-monthly-tagihan:'.now()->toDateString();

        if (! Cache::has($cacheKey)) {
            TahunAjaranStatus::syncToday();
            AkademikStatus::syncAll();
            MonthlyTagihanGenerator::generateCurrent();

            Cache::put($cacheKey, true, now()->endOfDay());
        }

        return $next($request);
    }
}
