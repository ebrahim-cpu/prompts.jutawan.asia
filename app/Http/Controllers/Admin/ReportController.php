<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorLog;
use App\Models\UserAccessLog;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display interactive reports view with initial data.
     */
    public function index(Request $request)
    {
        $currentYear = (int) date('Y');
        $years = $this->getAvailableYears();

        $visitorYear = (int) $request->input('visitor_year', $currentYear);
        $visitorPeriod = $request->input('visitor_period', 'month'); // 'daily', 'weeks', 'month'

        $loginYear = (int) $request->input('login_year', $currentYear);
        $loginPeriod = $request->input('login_period', 'month');

        $promptYear = (int) $request->input('prompt_year', $currentYear);
        $promptPeriod = $request->input('prompt_period', 'month');

        $visitorData = $this->getReportData('visitors', $visitorYear, $visitorPeriod);
        $loginData   = $this->getReportData('logins', $loginYear, $loginPeriod);
        $promptData  = $this->getReportData('prompts', $promptYear, $promptPeriod);

        return view('admin.reports.index', compact(
            'years',
            'currentYear',
            'visitorYear',
            'visitorPeriod',
            'visitorData',
            'loginYear',
            'loginPeriod',
            'loginData',
            'promptYear',
            'promptPeriod',
            'promptData'
        ));
    }

    /**
     * Return JSON data for AJAX chart updates.
     */
    public function apiData(Request $request)
    {
        $type = $request->input('type', 'visitors'); // 'visitors', 'logins', 'prompts'
        $year = (int) $request->input('year', date('Y'));
        $period = strtolower($request->input('period', 'month')); // 'daily', 'weeks', 'month'

        $data = $this->getReportData($type, $year, $period);

        return response()->json([
            'success' => true,
            'type' => $type,
            'year' => $year,
            'period' => $period,
            'data' => $data
        ]);
    }

    /**
     * Export report data as Excel (CSV stream with UTF-8 BOM) or PDF print view.
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'visitors');
        $year = (int) $request->input('year', date('Y'));
        $period = strtolower($request->input('period', 'month'));
        $format = strtolower($request->input('format', 'excel')); // 'excel', 'pdf'

        $reportTitles = [
            'visitors' => 'Pelawat (Visitors)',
            'logins'   => 'Log Masuk Berjaya (Successfully Authenticated Logins)',
            'prompts'  => 'Prompt Baharu Ditambah (Prompts Added)'
        ];

        $reportTitle = $reportTitles[$type] ?? 'Laporan Interactive';
        $reportData = $this->getReportData($type, $year, $period);
        $filename = "laporan_{$type}_{$period}_{$year}_" . date('Ymd_His');

        if ($format === 'pdf') {
            return view('admin.reports.export_pdf', compact(
                'type',
                'year',
                'period',
                'reportTitle',
                'reportData',
                'filename'
            ));
        }

        // CSV / Excel Export
        return response()->stream(function () use ($reportTitle, $year, $period, $reportData) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility

            fputcsv($handle, ["LAPORAN INTERACTIVE - {$reportTitle}"]);
            fputcsv($handle, ["Tahun: {$year}", "Kekerapan: " . ucfirst($period)]);
            fputcsv($handle, ["Tarikh Dijana: " . date('Y-m-d H:i:s')]);
            fputcsv($handle, []); // Empty row

            fputcsv($handle, ['Nisbah / Tempoh', 'Jumlah Rekod', 'Peratusan (%)']);

            $total = $reportData['total'];
            foreach ($reportData['labels'] as $idx => $label) {
                $count = $reportData['values'][$idx] ?? 0;
                $pct = $total > 0 ? round(($count / $total) * 100, 2) : 0;
                fputcsv($handle, [$label, $count, $pct . '%']);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['JUMLAH KESELURUHAN', $total, '100%']);

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Fetch aggregated labels & values for a report type, year, and period.
     */
    private function getReportData(string $type, int $year, string $period): array
    {
        $labels = [];
        $values = [];

        if ($type === 'visitors') {
            $query = VisitorLog::whereYear('created_at', $year);
        } elseif ($type === 'logins') {
            $query = UserAccessLog::where('event_type', 'LOGIN')->whereYear('created_at', $year);
        } else { // 'prompts'
            $query = Prompt::whereYear('created_at', $year);
        }

        if ($period === 'daily') {
            // Group by date YYYY-MM-DD
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                $dateFormat = "strftime('%Y-%m-%d', created_at)";
            } else {
                $dateFormat = "DATE_FORMAT(created_at, '%Y-%m-%d')";
            }

            $rawResults = (clone $query)
                ->select(DB::raw("{$dateFormat} as date_label"), DB::raw('COUNT(*) as total'))
                ->groupBy('date_label')
                ->orderBy('date_label', 'asc')
                ->pluck('total', 'date_label')
                ->toArray();

            // If no results for the year, return empty or default days
            if (empty($rawResults)) {
                $labels = ['Tiada Rekod'];
                $values = [0];
            } else {
                foreach ($rawResults as $dateStr => $count) {
                    $labels[] = Carbon::parse($dateStr)->format('d M Y');
                    $values[] = (int) $count;
                }
            }
        } elseif ($period === 'weeks') {
            // Group by week (Week 1 to Week 52)
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                $weekExpr = "cast(strftime('%W', created_at) as integer) + 1";
            } else {
                $weekExpr = "WEEK(created_at, 1)";
            }

            $rawResults = (clone $query)
                ->select(DB::raw("{$weekExpr} as week_num"), DB::raw('COUNT(*) as total'))
                ->groupBy('week_num')
                ->orderBy('week_num', 'asc')
                ->pluck('total', 'week_num')
                ->toArray();

            for ($w = 1; $w <= 52; $w++) {
                $labels[] = "Minggu {$w}";
                $values[] = isset($rawResults[$w]) ? (int) $rawResults[$w] : 0;
            }
        } else {
            // Default: 'month' (Jan to Dec)
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                $monthExpr = "cast(strftime('%m', created_at) as integer)";
            } else {
                $monthExpr = "MONTH(created_at)";
            }

            $rawResults = (clone $query)
                ->select(DB::raw("{$monthExpr} as month_num"), DB::raw('COUNT(*) as total'))
                ->groupBy('month_num')
                ->orderBy('month_num', 'asc')
                ->pluck('total', 'month_num')
                ->toArray();

            $months = ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogo', 'Sep', 'Okt', 'Nov', 'Dis'];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $months[$m - 1];
                $values[] = isset($rawResults[$m]) ? (int) $rawResults[$m] : 0;
            }
        }

        $totalSum = array_sum($values);
        $maxValue = !empty($values) ? max($values) : 0;
        $maxIndex = array_search($maxValue, $values);
        $peakLabel = ($maxIndex !== false && isset($labels[$maxIndex]) && $maxValue > 0) ? $labels[$maxIndex] : '-';
        $avgValue = count($values) > 0 ? round($totalSum / count($values), 1) : 0;

        return [
            'labels'    => $labels,
            'values'    => $values,
            'total'     => $totalSum,
            'peakLabel' => $peakLabel,
            'peakValue' => $maxValue,
            'avgValue'  => $avgValue,
        ];
    }

    /**
     * Get list of available years for selector.
     */
    private function getAvailableYears(): array
    {
        $currentYear = (int) date('Y');
        
        $years = [$currentYear];
        
        try {
            $vMin = VisitorLog::min('created_at');
            $lMin = UserAccessLog::min('created_at');
            $pMin = Prompt::min('created_at');

            $dates = array_filter([$vMin, $lMin, $pMin]);
            if (!empty($dates)) {
                $minYear = (int) date('Y', strtotime(min($dates)));
                for ($y = $minYear; $y <= $currentYear; $y++) {
                    if (!in_array($y, $years)) {
                        $years[] = $y;
                    }
                }
            }
        } catch (\Throwable $e) {}

        sort($years);
        return array_reverse($years); // Latest year first
    }
}
