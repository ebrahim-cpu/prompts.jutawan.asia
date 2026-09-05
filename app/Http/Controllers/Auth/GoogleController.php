<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect user to Google OAuth page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl('https://prompts.jutawan.asia/auth/google/callback')
            ->redirect();
    }

    /**
     * Handle callback from Google OAuth.
     */
    public function handleGoogleCallback(Request $request)
    {
        // Fix for cPanel/LiteSpeed dropping QUERY_STRING from GET requests
        if (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '?')) {
            $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            if ($queryString) {
                parse_str($queryString, $parsedGet);
                foreach ($parsedGet as $key => $val) {
                    $request->query->set($key, $val);
                    $request->request->set($key, $val);
                    $_GET[$key] = $val;
                    $_REQUEST[$key] = $val;
                }
            }
        }

        // Detailed Server & Request Logging for Debugging
        Log::info('Google Callback Incoming Details', [
            'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
            'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? 'N/A',
            'GET' => $_GET ?? [],
            'request_query' => $request->query(),
            'full_url' => $request->fullUrl()
        ]);

        try {
            $code = $request->query('code') ?? $_GET['code'] ?? null;

            if (!$code) {
                $rawUri = $_SERVER['REQUEST_URI'] ?? $request->fullUrl();
                $rawQuery = $_SERVER['QUERY_STRING'] ?? 'None';
                throw new Exception("Parameter code tidak dijumpai dalam URL callback. (REQUEST_URI: {$rawUri} | QUERY_STRING: {$rawQuery})");
            }

            // Explicitly specify redirectUrl to match Google OAuth credentials
            $googleUser = Socialite::driver('google')
                ->redirectUrl('https://prompts.jutawan.asia/auth/google/callback')
                ->stateless()
                ->user();

            if (!$googleUser || !$googleUser->getEmail()) {
                throw new Exception("Maklumat e-mel tidak ditemui daripada akaun Google.");
            }

            // Find user by google_id or email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Update existing user with google_id & avatar (do NOT overwrite uploaded avatar)
                $user->google_id = $googleUser->getId();
                if (empty($user->avatar)) {
                    $user->avatar = $googleUser->getAvatar();
                }
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }
                $user->save();
            } else {
                // Register new user via Google
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'user',
                    'tier' => 'free',
                    'email_verified_at' => now(),
                ]);
            }

            $request->session()->regenerate();
            Auth::login($user, true);

            // Redirect to temporary troubleshoot success page
            return redirect()->route('auth.google.debug_success');
        } catch (Exception $e) {
            Log::error('Google OAuth Callback Error: ' . $e->getMessage(), [
                'full_url' => $request->fullUrl(),
                'query' => $request->getQueryString(),
                'trace' => $e->getTraceAsString()
            ]);

            $debugInfo = "URL Callback dipanggil: " . $request->fullUrl() . " | Error: " . $e->getMessage();

            return redirect()->route('auth.google.debug_fail')->with('error_message', $debugInfo);
        }
    }

    /**
     * Temporary troubleshoot success page.
     */
    public function debugSuccess()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi tidak ditemui. Sila log masuk semula.']);
        }
        return view('auth.google_success');
    }

    /**
     * Temporary troubleshoot fail page.
     */
    public function debugFail()
    {
        return view('auth.google_fail');
    }
}
