<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UpdateUserLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            Auth::user()->updateQuietly(['last_seen_at' => now()]);
        }
        $response = $next($request);

        try {
            if (DB::table('jobs')->exists()) {
                $lockKey = 'queue:auto_run:lock';
                if (Cache::add($lockKey, 1, now()->addSeconds(20))) {
                    $this->spawnQueueWorkOnce();
                }
            }
        } catch (\Throwable $e) {
        }

        return $response;
    }

    private function spawnQueueWorkOnce(): void
    {
        $php = PHP_BINARY;
        $artisan = base_path('artisan');

        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'start "" /B "' . $php . '" "' . $artisan . '" queue:work --stop-when-empty --sleep=2 --tries=3 --timeout=120 --max-jobs=200 --max-time=60';
            @pclose(@popen($cmd, 'r'));
            return;
        }

        $cmd = 'nohup "' . $php . '" "' . $artisan . '" queue:work --stop-when-empty --sleep=2 --tries=3 --timeout=120 --max-jobs=200 --max-time=60 > /dev/null 2>&1 &';
        @exec($cmd);
    }
}
