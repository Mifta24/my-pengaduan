<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Comment;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Get date range for current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Basic statistics
        $totalUsers = User::count();
        $totalComplaints = Complaint::count();
        $totalCategories = Category::count();
        $totalAnnouncements = Announcement::count();

        // Monthly statistics
        $monthlyComplaints = Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $monthlyUsers = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // Complaint status distribution
        $complaintsByStatus = Complaint::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Complaints by category
        $complaintsByCategory = Complaint::with('category')
            ->select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->name ?? 'Tidak ada kategori',
                    'total' => $item->total
                ];
            });

        // Recent activity (last 30 days)
        $last30Days = Carbon::now()->subDays(30);
        $recentComplaints = Complaint::where('created_at', '>=', $last30Days)->count();
        $recentComments = Comment::where('created_at', '>=', $last30Days)->count();

        // Top categories by complaints
        $topCategories = Category::withCount('complaints')
            ->orderBy('complaints_count', 'desc')
            ->take(5)
            ->get();

        // User registration trend (last 6 months)
        $userTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $userTrend[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        // Complaint trend (last 6 months)
        $complaintTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Complaint::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $complaintTrend[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        // Resolution rate
        $resolvedComplaints = Complaint::where('status', 'resolved')->count();
        $resolutionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100, 1) : 0;

        // Average response time (in days)
        $avgResponseTime = $this->calculateAverageResponseTime();

        return view('admin.reports.index', compact(
            'totalUsers',
            'totalComplaints',
            'totalCategories',
            'totalAnnouncements',
            'monthlyComplaints',
            'monthlyUsers',
            'complaintsByStatus',
            'complaintsByCategory',
            'recentComplaints',
            'recentComments',
            'topCategories',
            'userTrend',
            'complaintTrend',
            'resolutionRate',
            'avgResponseTime'
        ));
    }

    public function complaints(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        $status = $request->input('status');
        $categoryId = $request->input('category_id');

        $query = Complaint::with(['user', 'category'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Calculate statistics before pagination
        $statsQuery = clone $query;
        $stats = [
            'total' => $statsQuery->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'in_progress' => (clone $statsQuery)->where('status', 'in_progress')->count(),
            'resolved' => (clone $statsQuery)->where('status', 'resolved')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
        ];

        // Calculate average response time for filtered data
        $responseTimeQuery = clone $statsQuery;
        $stats['avg_response_time'] = $this->calculateAverageResponseTime($responseTimeQuery);

        // Calculate completion rate for filtered data
        $completionRate = $stats['total'] > 0 ? round(($stats['resolved'] / $stats['total']) * 100, 1) : 0;
        $stats['completion_rate'] = $completionRate;

        $complaints = $query->orderBy('created_at', 'desc')->paginate(20);

        $categories = Category::orderBy('name')->get();

        return view('admin.reports.complaints', compact(
            'complaints',
            'stats',
            'categories',
            'dateFrom',
            'dateTo',
            'status',
            'categoryId'
        ));
    }

    public function users(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        $role = $request->input('role');
        $search = $request->input('search');
        $isActive = $request->input('is_active');

        $query = User::with('roles')
            ->withCount(['complaints', 'comments'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($role) {
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Calculate statistics before pagination
        $statsQuery = clone $query;
        $stats = [
            'total' => $statsQuery->count(),
            'active' => (clone $statsQuery)->where('is_active', true)->count(),
            'inactive' => (clone $statsQuery)->where('is_active', false)->count(),
            'this_month' => (clone $statsQuery)->whereMonth('created_at', Carbon::now()->month)
                                             ->whereYear('created_at', Carbon::now()->year)
                                             ->count(),
        ];

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.users', compact(
            'users',
            'stats',
            'dateFrom',
            'dateTo',
            'role',
            'search',
            'isActive'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'complaints');
        $format = $request->input('format', 'csv');

        switch ($type) {
            case 'complaints':
                return $this->exportComplaints($request, $format);
            case 'users':
                return $this->exportUsers($request, $format);
            default:
                return redirect()->back()->with('error', 'Tipe laporan tidak valid.');
        }
    }

    private function exportComplaints(Request $request, string $format)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        $status = $request->input('status');
        $categoryId = $request->input('category_id');

        $query = Complaint::with(['user', 'category'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $complaints = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'csv') {
            $filename = 'laporan_keluhan_' . $dateFrom . '_to_' . $dateTo . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($complaints) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fwrite($file, "\xEF\xBB\xBF");

                // CSV Headers
                fputcsv($file, [
                    'ID',
                    'Judul',
                    'Kategori',
                    'Pengguna',
                    'Status',
                    'Prioritas',
                    'Lokasi',
                    'Tanggal Dibuat',
                    'Tanggal Update'
                ]);

                // CSV Data
                foreach ($complaints as $complaint) {
                    fputcsv($file, [
                        $complaint->id,
                        $complaint->title,
                        $complaint->category->name ?? '',
                        $complaint->user->name,
                        $this->getStatusLabel($complaint->status),
                        $this->getPriorityLabel($complaint->priority),
                        $complaint->location,
                        $complaint->created_at->format('d/m/Y H:i'),
                        $complaint->updated_at->format('d/m/Y H:i')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // JSON format
        return response()->json([
            'data' => $complaints,
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $status,
                'category_id' => $categoryId
            ]
        ]);
    }

    private function exportUsers(Request $request, string $format)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        $role = $request->input('role');
        $isActive = $request->input('is_active');

        $query = User::with('roles')
            ->withCount(['complaints', 'comments'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($role) {
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'csv') {
            $filename = 'laporan_pengguna_' . $dateFrom . '_to_' . $dateTo . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fwrite($file, "\xEF\xBB\xBF");

                // CSV Headers
                fputcsv($file, [
                    'ID',
                    'Nama',
                    'Email',
                    'Telepon',
                    'Role',
                    'Status',
                    'Email Verified',
                    'Total Keluhan',
                    'Total Komentar',
                    'Tanggal Daftar'
                ]);

                // CSV Data
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->phone ?? '',
                        $user->roles->first()->name ?? 'user',
                        $user->is_active ? 'Aktif' : 'Tidak Aktif',
                        $user->email_verified_at ? 'Ya' : 'Tidak',
                        $user->complaints_count,
                        $user->comments_count,
                        $user->created_at->format('d/m/Y H:i')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // JSON format
        return response()->json([
            'data' => $users,
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'role' => $role,
                'is_active' => $isActive
            ]
        ]);
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Pending',
            'in_progress' => 'Dalam Progress',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak'
        ];

        return $labels[$status] ?? $status;
    }

    private function getPriorityLabel($priority)
    {
        $labels = [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi'
        ];

        return $labels[$priority] ?? $priority;
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
