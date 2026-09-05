<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class VisitorLogController
 *
 * Administrative controller for inspecting anonymous traffic metrics,
 * searching visitor IP logs, and purging historical analytics data.
 *
 * @package App\Http\Controllers\Admin
 */
class VisitorLogController extends Controller
{
    /**
     * Display a listing of visitor logs with pagination options (50, 100, 200, 300).
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

        $query = VisitorLog::query()->latest();

        // Optional Search by IP, URL, or User Agent
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%");
            });
        }

        $visitorLogs = $query->paginate($perPage)->withQueryString();

        // Compute traffic aggregations
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
     * Clear all visitor logs by truncating the table.
     *
     * @return RedirectResponse
     */
    public function clear(): RedirectResponse
    {
        VisitorLog::truncate();
        return redirect()->route('admin.visitors.index')->with('success', 'Semua log pelawat telah berjaya dibersihkan!');
    }
}
