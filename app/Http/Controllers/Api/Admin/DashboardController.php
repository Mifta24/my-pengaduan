<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\User;
use App\Models\Announcement;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponse;
    /**
     * Get admin dashboard statistics
     */
    public function index()
    {
        try {
            // Current Month Stats
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $previousMonth = now()->subMonth()->month;
            $previousYear = now()->subMonth()->year;

            // Total Complaints (current vs previous month)
            $totalComplaints = Complaint::count();
            $complaintsThisMonth = Complaint::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();
            $complaintsPreviousMonth = Complaint::whereMonth('created_at', $previousMonth)
                ->whereYear('created_at', $previousYear)
                ->count();
            $complaintsPercentageChange = $this->calculatePercentageChange($complaintsThisMonth, $complaintsPreviousMonth);

            // Complaint by Status
            $pendingComplaints = Complaint::where('status', 'pending')->count();
            $inProgressComplaints = Complaint::where('status', 'in_progress')->count();
            $resolvedComplaints = Complaint::where('status', 'resolved')->count();
            $rejectedComplaints = Complaint::where('status', 'rejected')->count();

            // Total Users (current vs previous month)
            $totalUsers = User::role('user')->count();
            $usersThisMonth = User::role('user')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();
            $usersPreviousMonth = User::role('user')
                ->whereMonth('created_at', $previousMonth)
                ->whereYear('created_at', $previousYear)
                ->count();
            $usersPercentageChange = $this->calculatePercentageChange($usersThisMonth, $usersPreviousMonth);

            // Completion Rate (current vs previous month)
            $completionRateCurrent = $complaintsThisMonth > 0
                ? round(($resolvedComplaints / $totalComplaints) * 100, 2)
                : 0;

            $resolvedPreviousMonth = Complaint::where('status', 'resolved')
                ->whereMonth('updated_at', $previousMonth)
                ->whereYear('updated_at', $previousYear)
                ->count();
            $completionRatePrevious = $complaintsPreviousMonth > 0
                ? round(($resolvedPreviousMonth / $complaintsPreviousMonth) * 100, 2)
                : 0;
            $completionRateChange = $completionRatePrevious > 0
                ? round((($completionRateCurrent - $completionRatePrevious) / $completionRatePrevious) * 100, 2)
                : 0;

            // Average Response Time (in hours)
            $avgResponseTimeCurrent = $this->calculateAverageResponseTime($currentMonth, $currentYear);
            $avgResponseTimePrevious = $this->calculateAverageResponseTime($previousMonth, $previousYear);
            $avgResponseTimeChange = $avgResponseTimePrevious > 0
                ? round((($avgResponseTimeCurrent - $avgResponseTimePrevious) / $avgResponseTimePrevious) * 100, 2)
                : 0;

            // User Statistics (detailed)
            $verifiedUsers = User::role('user')->whereNotNull('email_verified_at')->count();
            $unverifiedUsers = User::role('user')->whereNull('email_verified_at')->count();

            // Category Statistics
            $totalCategories = Category::count();
            $activeCategories = Category::where('is_active', true)->count();

            // Announcement Statistics
            $totalAnnouncements = Announcement::count();
            $activeAnnouncements = Announcement::where('is_active', true)->count();
            $urgentAnnouncements = Announcement::where('priority', 'urgent')
                ->where('is_active', true)
                ->count();

            // Recent Activities
            $recentComplaints = Complaint::with(['user:id,name,email', 'category:id,name'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'status', 'user_id', 'category_id', 'created_at']);

            // Complaints by Status (for charts)
            $complaintsByStatus = [
                'pending' => $pendingComplaints,
                'in_progress' => $inProgressComplaints,
                'resolved' => $resolvedComplaints,
                'rejected' => $rejectedComplaints,
            ];

            // Complaints by Category (for charts)
            $complaintsByCategory = Category::withCount('complaints')
                ->orderBy('complaints_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'complaints_count']);

            // Monthly Complaints (last 6 months)
            $monthlyComplaints = Complaint::select(
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            $data = [
                // Main Statistics with Percentage Changes
                'total_complaints' => [
                    'count' => $totalComplaints,
                    'change' => $complaintsPercentageChange,
                    'trend' => $complaintsPercentageChange >= 0 ? 'increased' : 'decreased',
                ],
                'total_users' => [
                    'count' => $totalUsers,
                    'change' => $usersPercentageChange,
                    'trend' => $usersPercentageChange >= 0 ? 'increased' : 'decreased',
                ],
                'completion_rate' => [
                    'percentage' => $completionRateCurrent,
                    'change' => $completionRateChange,
                    'trend' => $completionRateChange >= 0 ? 'increased' : 'decreased',
                ],
                'average_response_time' => [
                    'hours' => $avgResponseTimeCurrent,
                    'change' => abs($avgResponseTimeChange),
                    'trend' => $avgResponseTimeChange <= 0 ? 'decreased' : 'increased',
                ],

                // Detailed Statistics
                'complaints' => [
                    'total' => $totalComplaints,
                    'pending' => $pendingComplaints,
                    'in_progress' => $inProgressComplaints,
                    'resolved' => $resolvedComplaints,
                    'rejected' => $rejectedComplaints,
                    'by_status' => $complaintsByStatus,
                    'by_category' => $complaintsByCategory,
                    'monthly' => $monthlyComplaints,
                ],
                'users' => [
                    'total' => $totalUsers,
                    'verified' => $verifiedUsers,
                    'unverified' => $unverifiedUsers,
                    'new_this_month' => $usersThisMonth,
                ],
                'categories' => [
                    'total' => $totalCategories,
                    'active' => $activeCategories,
                ],
                'announcements' => [
                    'total' => $totalAnnouncements,
                    'active' => $activeAnnouncements,
                    'urgent' => $urgentAnnouncements,
                ],
                'recent_complaints' => $recentComplaints,
            ];

            return $this->success($data, 'Dashboard statistics loaded successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to load dashboard statistics', $e);
        }
    }

    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Calculate average response time in hours for resolved complaints
     */
    private function calculateAverageResponseTime($month, $year)
    {
        $resolvedComplaints = Complaint::where('status', 'resolved')
            ->whereMonth('updated_at', $month)
            ->whereYear('updated_at', $year)
            ->get(['created_at', 'updated_at']);

        if ($resolvedComplaints->isEmpty()) {
            return 0;
        }

        $totalHours = 0;
        foreach ($resolvedComplaints as $complaint) {
            $hours = $complaint->created_at->diffInHours($complaint->updated_at);
            $totalHours += $hours;
        }

        return round($totalHours / $resolvedComplaints->count(), 1);
    }

    /**
     * Get quick stats for mobile dashboard
     */
    public function quickStats()
    {
        try {
            $stats = [
                'total_complaints' => Complaint::count(),
                'pending_complaints' => Complaint::where('status', 'pending')->count(),
                'total_users' => User::role('user')->count(),
                'resolved_today' => Complaint::where('status', 'resolved')
                    ->whereDate('updated_at', today())
                    ->count(),
                'new_complaints_today' => Complaint::whereDate('created_at', today())->count(),
            ];

            return $this->success($stats, 'Quick statistics loaded successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to load quick statistics', $e);
        }
    }
}
