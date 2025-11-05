<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Announcement;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ApiResponse;

    /**
     * Get overview statistics and trends
     */
    public function overview(Request $request)
    {
        try {
            // Get date range for current month
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // Basic statistics
            $stats = [
                'total_users' => User::count(),
                'total_complaints' => Complaint::count(),
                'total_categories' => Category::count(),
                'total_announcements' => Announcement::count(),
                'monthly_complaints' => Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'monthly_users' => User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            ];

            // Complaint status distribution
            $complaintsByStatus = Complaint::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            // Complaints by category
            $complaintsByCategory = Complaint::with('category:id,name')
                ->select('category_id', DB::raw('count(*) as total'))
                ->groupBy('category_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'category_id' => $item->category_id,
                        'category_name' => $item->category->name ?? 'No Category',
                        'total' => $item->total
                    ];
                });

            // Recent activity (last 30 days)
            $last30Days = Carbon::now()->subDays(30);
            $recentActivity = [
                'complaints' => Complaint::where('created_at', '>=', $last30Days)->count(),
                'users' => User::where('created_at', '>=', $last30Days)->count(),
            ];

            // Top categories by complaints
            $topCategories = Category::withCount('complaints')
                ->orderBy('complaints_count', 'desc')
                ->take(5)
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'icon' => $category->icon,
                        'color' => $category->color,
                        'complaints_count' => $category->complaints_count,
                    ];
                });

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

            // Resolution metrics
            $totalComplaints = $stats['total_complaints'];
            $resolvedComplaints = Complaint::where('status', 'resolved')->count();
            $resolutionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100, 1) : 0;

            // Average response time
            $avgResponseTime = $this->calculateAverageResponseTime();

            $data = [
                'statistics' => $stats,
                'complaints_by_status' => $complaintsByStatus,
                'complaints_by_category' => $complaintsByCategory,
                'recent_activity' => $recentActivity,
                'top_categories' => $topCategories,
                'user_trend' => $userTrend,
                'complaint_trend' => $complaintTrend,
                'resolution_rate' => $resolutionRate,
                'avg_response_time' => $avgResponseTime,
            ];

            return $this->success($data, 'Report overview loaded successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to load report overview', $e);
        }
    }

    /**
     * Get complaints report with filters
     */
    public function complaints(Request $request)
    {
        try {
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'status' => 'nullable|in:pending,in_progress,resolved,rejected',
                'category_id' => 'nullable|exists:categories,id',
                'priority' => 'nullable|in:low,medium,high',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
            $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));

            $query = Complaint::with(['user:id,name,email', 'category:id,name,icon,color'])
                ->whereBetween('created_at', [$dateFrom, $dateTo]);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
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
            $stats['avg_response_time'] = $this->calculateAverageResponseTime($statsQuery);

            // Calculate completion rate
            $completionRate = $stats['total'] > 0 ? round(($stats['resolved'] / $stats['total']) * 100, 1) : 0;
            $stats['completion_rate'] = $completionRate;

            $complaints = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            // Collect active filters
            $activeFilters = [];
            if ($request->has('date_from')) {
                $activeFilters['date_from'] = $dateFrom;
            }
            if ($request->has('date_to')) {
                $activeFilters['date_to'] = $dateTo;
            }
            if ($request->has('status')) {
                $activeFilters['status'] = $request->status;
            }
            if ($request->has('category_id')) {
                $activeFilters['category_id'] = (int) $request->category_id;
            }
            if ($request->has('priority')) {
                $activeFilters['priority'] = $request->priority;
            }
            if ($request->has('per_page')) {
                $activeFilters['per_page'] = (int) $request->per_page;
            }

            return $this->successWithPagination(
                $complaints,
                'Complaints report loaded successfully',
                $activeFilters,
                200,
                ['statistics' => $stats]
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->serverError('Failed to load complaints report', $e);
        }
    }

    /**
     * Get users report with filters
     */
    public function users(Request $request)
    {
        try {
            $validated = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'role' => 'nullable|in:admin,user',
                'is_active' => 'nullable|boolean',
                'search' => 'nullable|string|max:255',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
            $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));

            $query = User::with('roles:name')
                ->withCount(['complaints'])
                ->whereBetween('created_at', [$dateFrom, $dateTo]);

            // Apply filters
            if ($request->filled('role')) {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }

            if ($request->has('is_active') && $request->is_active !== null) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            // Calculate statistics before pagination
            $statsQuery = clone $query;
            $stats = [
                'total' => $statsQuery->count(),
                'active' => (clone $statsQuery)->where('is_active', true)->count(),
                'inactive' => (clone $statsQuery)->where('is_active', false)->count(),
                'this_month' => User::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
            ];

            $users = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            // Transform users data
            $users->getCollection()->transform(function ($user) {
                $userData = $user->toArray();
                // Replace roles array with single role string
                $userData['role'] = $user->roles->first()->name ?? 'user';
                unset($userData['roles']);
                return $userData;
            });

            // Collect active filters
            $activeFilters = [];
            if ($request->has('date_from')) {
                $activeFilters['date_from'] = $dateFrom;
            }
            if ($request->has('date_to')) {
                $activeFilters['date_to'] = $dateTo;
            }
            if ($request->has('role')) {
                $activeFilters['role'] = $request->role;
            }
            if ($request->has('is_active')) {
                $activeFilters['is_active'] = $request->boolean('is_active');
            }
            if ($request->has('search')) {
                $activeFilters['search'] = $request->search;
            }
            if ($request->has('per_page')) {
                $activeFilters['per_page'] = (int) $request->per_page;
            }

            return $this->successWithPagination(
                $users,
                'Users report loaded successfully',
                $activeFilters,
                200,
                ['statistics' => $stats]
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->serverError('Failed to load users report', $e);
        }
    }

    /**
     * Export report data
     */
    public function export(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:complaints,users,overview',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'status' => 'nullable|in:pending,in_progress,resolved,rejected',
                'category_id' => 'nullable|exists:categories,id',
                'role' => 'nullable|in:admin,user',
                'is_active' => 'nullable|boolean',
            ]);

            $type = $request->input('type');
            $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
            $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));

            switch ($type) {
                case 'complaints':
                    $data = $this->exportComplaintsData($request, $dateFrom, $dateTo);
                    break;
                case 'users':
                    $data = $this->exportUsersData($request, $dateFrom, $dateTo);
                    break;
                case 'overview':
                    $data = $this->exportOverviewData();
                    break;
                default:
                    return $this->error('Invalid report type');
            }

            return $this->success([
                'data' => $data,
                'exported_at' => now()->format('Y-m-d\TH:i:s.u\Z'),
                'filters' => $request->only(['date_from', 'date_to', 'status', 'category_id', 'role', 'is_active']),
            ], 'Report data exported successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->serverError('Failed to export report data', $e);
        }
    }

    /**
     * Export complaints data
     */
    private function exportComplaintsData(Request $request, $dateFrom, $dateTo)
    {
        $query = Complaint::with(['user:id,name,email', 'category:id,name'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'title' => $complaint->title,
                    'category' => $complaint->category->name ?? '',
                    'user' => $complaint->user->name,
                    'user_email' => $complaint->user->email,
                    'status' => $complaint->status,
                    'priority' => $complaint->priority,
                    'location' => $complaint->location,
                    'created_at' => $complaint->created_at->format('Y-m-d\TH:i:s.u\Z'),
                    'updated_at' => $complaint->updated_at->format('Y-m-d\TH:i:s.u\Z'),
                ];
            });
    }

    /**
     * Export users data
     */
    private function exportUsersData(Request $request, $dateFrom, $dateTo)
    {
        $query = User::with('roles:name')
            ->withCount(['complaints'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'role' => $user->roles->first()->name ?? 'user',
                    'is_active' => $user->is_active,
                    'email_verified' => $user->email_verified_at ? true : false,
                    'complaints_count' => $user->complaints_count,
                    'created_at' => $user->created_at->format('Y-m-d\TH:i:s.u\Z'),
                ];
            });
    }

    /**
     * Export overview data
     */
    private function exportOverviewData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            'statistics' => [
                'total_users' => User::count(),
                'total_complaints' => Complaint::count(),
                'total_categories' => Category::count(),
                'total_announcements' => Announcement::count(),
                'monthly_complaints' => Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'monthly_users' => User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            ],
            'complaints_by_status' => Complaint::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),
        ];
    }

    /**
     * Calculate average response time
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

            return round($avgTimeInHours ?? 0, 1) . ' hours';
        }

        return round($avgTimeInDays, 1) . ' days';
    }
}
