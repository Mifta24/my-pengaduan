<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|superadmin']);
    }

    public function dashboard()
    {
        $totalComplaints = Complaint::count();
        $totalUsers = User::count();

        // Calculate completion rate
        $completedComplaints = Complaint::where('status', 'resolved')->count();
        $completionRate = $totalComplaints > 0 ? round(($completedComplaints / $totalComplaints) * 100, 1) : 0;

        // Calculate average response time (in days) from first response per complaint.
        $firstResponseRows = DB::table('complaints')
            ->joinSub(
                DB::table('responses')
                    ->select('complaint_id', DB::raw('MIN(created_at) as first_response_at'))
                    ->groupBy('complaint_id'),
                'first_responses',
                function ($join) {
                    $join->on('complaints.id', '=', 'first_responses.complaint_id');
                }
            )
            ->select('complaints.created_at', 'first_responses.first_response_at')
            ->get();

        $avgResponseTime = $firstResponseRows->isNotEmpty()
            ? round($firstResponseRows->avg(function ($row) {
                return Carbon::parse($row->created_at)
                    ->diffInSeconds(Carbon::parse($row->first_response_at)) / 86400;
            }), 1)
            : 0;

        // Get complaints by status
        $complaintsByStatus = [
            'pending' => Complaint::where('status', 'pending')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'rejected' => Complaint::where('status', 'rejected')->count(),
        ];

        // Get recent complaints
        $recentComplaints = Complaint::with('user', 'category')
            ->latest()
            ->limit(5)
            ->get();

        // Get complaints by category
        $complaintsByCategory = Category::withCount('complaints')
            ->having('complaints_count', '>', 0)
            ->get()
            ->map(function ($category) {
                return [
                    'category' => $category->name,
                    'total' => $category->complaints_count,
                    'color' => $this->getCategoryColor($category->name)
                ];
            });

        return view('admin.dashboard', compact(
            'totalComplaints',
            'totalUsers',
            'completionRate',
            'avgResponseTime',
            'complaintsByStatus',
            'recentComplaints',
            'complaintsByCategory'
        ));
    }

    private function getCategoryColor($categoryName)
    {
        $colors = [
            '#3B82F6', // blue
            '#EF4444', // red
            '#10B981', // green
            '#F59E0B', // yellow
            '#8B5CF6', // purple
            '#EC4899', // pink
            '#06B6D4', // cyan
            '#84CC16', // lime
        ];

        $index = abs(crc32($categoryName)) % count($colors);
        return $colors[$index];
    }
}
