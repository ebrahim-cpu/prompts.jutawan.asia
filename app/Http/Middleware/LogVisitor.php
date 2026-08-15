<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitorLog;
use Illuminate\Support\Facades\Log;

class LogVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // 1. Only record visitors who are NOT logged in (Guests only)
            if (\Illuminate\Support\Facades\Auth::check()) {
                return $response;
            }

            // 2. Ignore static asset requests
            $path = $request->path();
            if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$/i', $path)) {
                return $response;
            }

            // 3. Only record visits on the front page (muka hadapan: '/')
            if (trim($path, '/') !== '' && !$request->routeIs('home')) {
                return $response;
            }

            // Log guest visitor on front page
            VisitorLog::create([
                'ip_address' => $request->ip() ?? '127.0.0.1',
                'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                'url'        => substr($request->fullUrl(), 0, 500),
                'method'     => $request->method(),
                'referer'    => substr($request->header('referer') ?? '', 0, 500),
            ]);
        } catch (\Throwable $e) {
            // Catch silently so middleware failure never breaks application requests
            Log::error('Visitor Log Middleware Error: ' . $e->getMessage());
        }

        return $response;
    }
}
