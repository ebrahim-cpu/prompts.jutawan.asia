<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitorLog;
use Illuminate\Support\Facades\Log;

/**
 * Class LogVisitor
 *
 * Lightweight, non-intrusive traffic analytics middleware.
 *
 * Deduplication Policy:
 * 1. Only records visits directed at the main homepage ('/' or route 'home').
 * 2. Skips all static media/asset requests (.css, .js, .png, etc.).
 * 3. Records at most ONE entry per unique IP address per calendar day.
 * 4. Fails silently in a try/catch block so logging exceptions never disrupt client responses.
 *
 * @package App\Http\Middleware
 */
class LogVisitor
{
    /**
     * Handle an incoming request and log daily unique visitor hits.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // 1. Strictly ONLY record visits to the main home page ('/' or named route 'home')
            $path = trim($request->path(), '/');
            if ($path !== '' && !$request->routeIs('home')) {
                return $response;
            }

            // Ignore static asset requests
            if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$/i', $path)) {
                return $response;
            }

            // 2. Only record UNIQUE IP address per day for the home page
            $ip = $request->ip() ?? '127.0.0.1';

            $alreadyLoggedToday = VisitorLog::where('ip_address', $ip)
                ->whereDate('created_at', today())
                ->exists();

            if (!$alreadyLoggedToday) {
                VisitorLog::create([
                    'ip_address' => $ip,
                    'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                    'url'        => substr($request->fullUrl(), 0, 500),
                    'method'     => $request->method(),
                    'referer'    => substr($request->header('referer') ?? '', 0, 500),
                ]);
            }
        } catch (\Throwable $e) {
            // Catch silently so middleware failure never breaks application requests
            Log::error('Visitor Log Middleware Error: ' . $e->getMessage());
        }

        return $response;
    }
}
