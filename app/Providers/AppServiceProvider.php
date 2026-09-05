<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\UserAccessLog;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Rebind public path for cPanel shared hosting
        $cpanelPublic = base_path('../public_html/prompts.jutawan.asia');
        if (is_dir($cpanelPublic)) {
            $this->app->bind('path.public', function () use ($cpanelPublic) {
                return $cpanelPublic;
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Listen to Successful Login Events
        Event::listen(Login::class, function (Login $event) {
            try {
                $user = $event->user;
                $request = request();
                if ($user && $request) {
                    UserAccessLog::create([
                        'user_id'    => $user->id,
                        'user_name'  => $user->name,
                        'user_email' => $user->email,
                        'event_type' => 'LOGIN',
                        'ip_address' => $request->ip() ?? '127.0.0.1',
                        'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                        'url'        => substr($request->fullUrl(), 0, 500),
                        'method'     => $request->method(),
                        'referer'    => substr($request->header('referer') ?? '', 0, 500),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Login Event Log Error: ' . $e->getMessage());
            }
        });

        // Listen to Logout Events
        Event::listen(Logout::class, function (Logout $event) {
            try {
                $user = $event->user;
                $request = request();
                if ($user && $request) {
                    UserAccessLog::create([
                        'user_id'    => $user->id,
                        'user_name'  => $user->name,
                        'user_email' => $user->email,
                        'event_type' => 'LOGOUT',
                        'ip_address' => $request->ip() ?? '127.0.0.1',
                        'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                        'url'        => substr($request->fullUrl(), 0, 500),
                        'method'     => $request->method(),
                        'referer'    => substr($request->header('referer') ?? '', 0, 500),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Logout Event Log Error: ' . $e->getMessage());
            }
        });
    }
}
