<?php

namespace App\Http\Middleware;

use App\Support\MonthlyTagihanGenerator;
use App\Support\AkademikStatus;
use App\Support\TahunAjaranStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoGenerateMonthlyTagihan
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            TahunAjaranStatus::syncToday();
            AkademikStatus::syncAll();
            MonthlyTagihanGenerator::generateCurrent();
        }

        return $next($request);
    }
}
