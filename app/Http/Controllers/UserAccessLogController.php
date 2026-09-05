<?php

namespace App\Http\Controllers;

use App\Models\UserAccessLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class UserAccessLogController
 *
 * Manages security audit trails of user authentication sessions.
 *
 * Scoping Rules:
 * - Regular users can view and clear only their own access logs.
 * - Administrators can inspect all historical user sessions across the entire system.
 *
 * @package App\Http\Controllers
 */
class UserAccessLogController extends Controller
{
    /**
     * Display a paginated audit log of login and logout events.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
    {
        $allowedPerPage = [50, 100, 200, 300];
        $perPage = (int) $request->input('per_page', 50);

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 50;
        }

        $user = auth()->user();
        $isAdmin = ($user->role === 'admin');

        $query = UserAccessLog::query()->latest();

        // Scope records: Regular user sees ONLY their own access logs; Admin sees ALL records
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        // Filter by Event Type (LOGIN / LOGOUT)
        if ($request->filled('event_type')) {
            $query->where('event_type', strtoupper($request->input('event_type')));
        }

        // Search by IP, user agent, or user identity
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search, $isAdmin) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%");
                
                if ($isAdmin) {
                    $q->orWhere('user_name', 'like', "%{$search}%")
                      ->orWhere('user_email', 'like', "%{$search}%");
                }
            });
        }

        $accessLogs = $query->paginate($perPage)->withQueryString();

        // Calculate aggregated session statistics
        $baseQuery = $isAdmin ? UserAccessLog::query() : UserAccessLog::where('user_id', $user->id);

        $totalLogins = (clone $baseQuery)->where('event_type', 'LOGIN')->count();
        $totalLogouts = (clone $baseQuery)->where('event_type', 'LOGOUT')->count();
        $todayAccesses = (clone $baseQuery)->whereDate('created_at', today())->count();
        $uniqueIPs = (clone $baseQuery)->distinct('ip_address')->count('ip_address');

        return view('user_access_logs.index', compact(
            'accessLogs',
            'perPage',
            'allowedPerPage',
            'totalLogins',
            'totalLogouts',
            'todayAccesses',
            'uniqueIPs',
            'isAdmin'
        ));
    }

    /**
     * Purge access logs according to caller's authorization level.
     *
     * Administrators truncate the entire table; regular users delete only their own rows.
     *
     * @return RedirectResponse
     */
    public function clear(): RedirectResponse
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            UserAccessLog::truncate();
            $msg = 'Semua log sesi log masuk/keluar pengguna telah berjaya dibersihkan!';
        } else {
            UserAccessLog::where('user_id', $user->id)->delete();
            $msg = 'Log sesi log masuk/keluar akaun anda telah berjaya dibersihkan!';
        }

        return redirect()->route('user_access_logs.index')->with('success', $msg);
    }
}
