<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Category;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        // Get basic statistics
        $totalComplaints = Complaint::count();
        $totalUsers = User::role('user')->count();
        $totalCategories = Category::where('is_active', true)->count();
        $totalAnnouncements = Announcement::count();

        // Get complaint statistics by status
        $complaintsByStatus = Complaint::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Get complaints by category with category names
        $complaintsByCategory = Complaint::with('category')
            ->select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->name,
                    'total' => $item->total,
                    'color' => $item->category->color
                ];
            });

        // Get monthly complaint trends (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Complaint::whereYear('report_date', $month->year)
                ->whereMonth('report_date', $month->month)
                ->count();

            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        // Get recent complaints (last 5)
        $recentComplaints = Complaint::with(['user', 'category'])
            ->orderBy('report_date', 'desc')
            ->limit(5)
            ->get();

        // Get status completion rate
        $resolvedCount = Complaint::where('status', 'resolved')->count();
        $completionRate = $totalComplaints > 0 ? round(($resolvedCount / $totalComplaints) * 100, 1) : 0;

        // Get response time statistics (SQLite compatible)
        $avgResponseTime = $this->calculateAverageResponseTime();

        return view('admin.dashboard', compact(
            'totalComplaints',
            'totalUsers',
            'totalCategories',
            'totalAnnouncements',
            'complaintsByStatus',
            'complaintsByCategory',
            'monthlyData',
            'recentComplaints',
            'completionRate',
            'avgResponseTime'
        ));
    }

    public function complaints()
    {
        $complaints = Complaint::with(['user', 'category'])
            ->orderBy('report_date', 'desc')
            ->paginate(15);

        $categories = Category::where('is_active', true)->get();

        return view('admin.complaints.index', compact('complaints', 'categories'));
    }

    public function users()
    {
        $users = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function categories()
    {
        $categories = Category::orderBy('name')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function announcements()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Generate reports in various formats
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, quarter, year
        $format = $request->get('format', 'view'); // view, pdf, excel

        $startDate = match($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth()
        };

        $endDate = Carbon::now();

        // Get complaints data for the period
        $complaints = Complaint::with(['user', 'category'])
            ->whereBetween('report_date', [$startDate, $endDate])
            ->orderBy('report_date', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $complaints->count(),
            'resolved' => $complaints->where('status', 'resolved')->count(),
            'in_progress' => $complaints->where('status', 'in_progress')->count(),
            'pending' => $complaints->where('status', 'pending')->count(),
            'by_category' => $complaints->groupBy('category.name')->map->count(),
        ];

        $reportData = compact('complaints', 'stats', 'period', 'startDate', 'endDate');

        if ($format === 'pdf') {
            return $this->generatePDFReport($reportData);
        } elseif ($format === 'excel') {
            return $this->generateExcelReport($reportData);
        }

        return view('admin.reports.index', $reportData);
    }

    private function generatePDFReport($data)
    {
        // This will be implemented when we create the PDF functionality
        // For now, return to view
        return view('admin.reports.pdf', $data);
    }

    private function generateExcelReport($data)
    {
        // This will be implemented when we create the Excel functionality
        // For now, return to view
        return view('admin.reports.excel', $data);
    }

    /**
     * Calculate average response time with consistent method
     */
    private function calculateAverageResponseTime($query = null)
    {
        if ($query === null) {
            $query = Complaint::query();
        }

        $avgTimeInDays = $query->whereNotNull('updated_at')
            ->where('status', '!=', 'pending')
            ->get()
            ->avg(function ($complaint) {
                return $complaint->created_at->diffInDays($complaint->updated_at);
            });

        $avgTimeInDays = $avgTimeInDays ?? 0;

        // Convert to hours if less than 1 day
        if ($avgTimeInDays < 1 && $avgTimeInDays > 0) {
            $avgTimeInHours = $query->whereNotNull('updated_at')
                ->where('status', '!=', 'pending')
                ->get()
                ->avg(function ($complaint) {
                    return $complaint->created_at->diffInHours($complaint->updated_at);
                });

            return round($avgTimeInHours ?? 0, 1) . ' jam';
        }

        return round($avgTimeInDays, 1) . ' hari';
    }
}
