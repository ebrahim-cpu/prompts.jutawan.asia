<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminMiddleware
 *
 * Restricts route access strictly to authenticated users with the 'admin' role.
 *
 * Unauthenticated visitors are redirected to login with an informational notice.
 * Non-admin authenticated users are redirected to their dashboard with an access denied alert.
 *
 * @package App\Http\Middleware
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): (Response)  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sila log masuk sebagai Pentadbir (Admin) untuk mengakses halaman ini.'
            ]);
        }

        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard')->with(
                'error',
                'Akses Ditolak: Akaun anda tidak mempunyai kebenaran Pentadbir (Admin).'
            );
        }

        return $next($request);
    }
}
