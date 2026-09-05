<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorLog;
use Illuminate\Http\Request;

class VisitorLogController extends Controller
{
    /**
     * Display a listing of visitor logs with pagination options (50, 100, 200, 300).
     */
    public function index(Request $request)
    {
        $allowedPerPage = [50, 100, 200, 300];
        $perPage = (int) $request->input('per_page', 50);

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 50;
        }

        $query = VisitorLog::query()->latest();

        // Optional Search by IP or URL
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%");
            });
        }

        $visitorLogs = $query->paginate($perPage)->withQueryString();

        // Calculate Stats
        $totalVisits = VisitorLog::count();
        $uniqueVisitors = VisitorLog::distinct('ip_address')->count('ip_address');
        $todayVisits = VisitorLog::whereDate('created_at', today())->count();
        $thisMonthVisits = VisitorLog::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return view('admin.visitors.index', compact(
            'visitorLogs',
            'perPage',
            'allowedPerPage',
            'totalVisits',
            'uniqueVisitors',
            'todayVisits',
            'thisMonthVisits'
        ));
    }

    /**
     * Clear all visitor logs.
     */
    public function clear()
    {
        VisitorLog::truncate();
        return redirect()->route('admin.visitors.index')->with('success', 'Semua log pelawat telah berjaya dibersihkan!');
    }
}
