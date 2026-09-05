<?php

namespace App\Http\Controllers;

use App\Models\UserAccessLog;
use Illuminate\Http\Request;

class UserAccessLogController extends Controller
{
    /**
     * Display a listing of user login/logout access logs.
     */
    public function index(Request $request)
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

        // Optional Search
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

        // Calculate Stats
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
     * Clear user access logs (Admin only clears all; User clears their own).
     */
    public function clear()
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
