<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckSubscriptionExpiry
 *
 * Automatically inspects the authenticated user's premium expiration date on every web request.
 *
 * If a user's subscription timestamp has passed, this middleware invokes
 * $user->autoExpireSubscription(), reverting their membership tier back to 'free'
 * and revoking access to locked premium prompt content without requiring scheduled background cron tasks.
 *
 * @package App\Http\Middleware
 */
class CheckSubscriptionExpiry
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
        if (auth()->check()) {
            auth()->user()->autoExpireSubscription();
        }

        return $next($request);
    }
}
