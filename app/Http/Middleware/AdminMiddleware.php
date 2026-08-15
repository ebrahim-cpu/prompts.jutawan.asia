<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
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
