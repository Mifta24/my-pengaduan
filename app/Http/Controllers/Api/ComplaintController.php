<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\Attachment;
use App\Models\Announcement;
use App\Events\ComplaintCreated;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ComplaintController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of user's complaints
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $query = Complaint::with(['category:id,name,icon,color,description', 'attachments'])
                ->where('user_id', $user->id);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Search
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            $complaints = $query->orderBy('report_date', 'desc')
                ->paginate($request->get('per_page', 15));

            // Collect active filters
            $activeFilters = [];
            if ($request->has('status')) {
                $activeFilters['status'] = $request->status;
            }
            if ($request->has('category_id')) {
                $activeFilters['category_id'] = (int) $request->category_id;
            }
            if ($request->has('search')) {
                $activeFilters['search'] = $request->search;
            }
            if ($request->has('per_page')) {
                $activeFilters['per_page'] = (int) $request->per_page;
            }

            // Transform data to hide unnecessary fields
            $complaints->getCollection()->transform(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'title' => $complaint->title,
                    'description' => $complaint->description,
                    'location' => $complaint->location,
                    'priority' => $complaint->priority,
                    'status' => $complaint->status,
                    'photo' => $complaint->photo,
                    'photo_url' => $complaint->photo_url,
                    'admin_response' => $complaint->admin_response,
                    'estimated_resolution' => $complaint->estimated_resolution ? $complaint->estimated_resolution->format('Y-m-d\TH:i:s.u\Z') : null,
                    'report_date' => $complaint->report_date ? $complaint->report_date->format('Y-m-d\TH:i:s.u\Z') : null,
                    'created_at' => $complaint->created_at->format('Y-m-d\TH:i:s.u\Z'),
                    'updated_at' => $complaint->updated_at->format('Y-m-d\TH:i:s.u\Z'),
                    'category' => $complaint->category,
                    'attachments' => $complaint->attachments,
                ];
            });

            return $this->successWithPagination($complaints, 'Complaints list loaded successfully', $activeFilters);

        } catch (\Exception $e) {
            return $this->serverError('Failed to load complaints list', $e);
        }
    }

    /**
     * Store a newly created complaint
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'location' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'attachments.*' => 'nullable|file|max:10240'
            ]);

            $complaint = Complaint::create([
                'user_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'location' => $validated['location'],
                'status' => 'pending',
                'report_date' => now()
            ]);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('complaints/photos', 'public');
                $complaint->update(['photo' => $photoPath]);
            }

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('complaints/attachments', 'public');

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            $complaint->load(['category:id,name,icon,color,description', 'attachments']);

            // Dispatch event to send notification to admins
            event(new ComplaintCreated($complaint));

            // Transform data to hide unnecessary fields
            $data = [
                'id' => $complaint->id,
                'title' => $complaint->title,
                'description' => $complaint->description,
                'location' => $complaint->location,
                'priority' => $complaint->priority,
                'status' => $complaint->status,
                'photo' => $complaint->photo,
                'photo_url' => $complaint->photo_url,
                'report_date' => $complaint->report_date ? $complaint->report_date->format('Y-m-d\TH:i:s.u\Z') : null,
                'created_at' => $complaint->created_at->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $complaint->updated_at->format('Y-m-d\TH:i:s.u\Z'),
                'category' => $complaint->category,
                'attachments' => $complaint->attachments,
            ];

            return $this->created($data, 'Complaint submitted successfully');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->serverError('Failed to submit complaint', $e);
        }
    }

    /**
     * Display the specified complaint
     */
    public function show(Request $request, Complaint $complaint)
    {
        try {
            // Check if user owns this complaint
            if ($complaint->user_id !== $request->user()->id) {
                return $this->unauthorized('You do not have access to this complaint');
            }

            $complaint->load(['category:id,name,icon,color,description', 'attachments']);

            // Transform data to hide unnecessary fields
            $data = [
                'id' => $complaint->id,
                'title' => $complaint->title,
                'description' => $complaint->description,
                'location' => $complaint->location,
                'priority' => $complaint->priority,
                'status' => $complaint->status,
                'photo' => $complaint->photo,
                'photo_url' => $complaint->photo_url,
                'admin_response' => $complaint->admin_response,
                'estimated_resolution' => $complaint->estimated_resolution ? $complaint->estimated_resolution->format('Y-m-d\TH:i:s.u\Z') : null,
                'report_date' => $complaint->report_date ? $complaint->report_date->format('Y-m-d\TH:i:s.u\Z') : null,
                'created_at' => $complaint->created_at->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $complaint->updated_at->format('Y-m-d\TH:i:s.u\Z'),
                'category' => $complaint->category,
                'attachments' => $complaint->attachments,
            ];

            return $this->success($data, 'Complaint details loaded successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to load complaint details', $e);
        }
    }

    /**
     * Update the specified complaint (only if still pending)
     */
    public function update(Request $request, Complaint $complaint)
    {
        try {
            // Check if user owns this complaint
            if ($complaint->user_id !== $request->user()->id) {
                return $this->unauthorized('You do not have access to this complaint');
            }

            // Check if complaint can be updated
            if ($complaint->status !== 'pending') {
                return $this->error('Complaints that have been processed cannot be modified');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'location' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'attachments.*' => 'nullable|file|max:10240'
            ]);

            $complaint->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'location' => $validated['location']
            ]);

            // Handle photo update
            if ($request->hasFile('photo')) {
                if ($complaint->photo) {
                    Storage::disk('public')->delete($complaint->photo);
                }
                $photoPath = $request->file('photo')->store('complaints/photos', 'public');
                $complaint->update(['photo' => $photoPath]);
            }

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('complaints/attachments', 'public');

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            $complaint->load(['category:id,name,icon,color,description', 'attachments']);

            // Transform data to hide unnecessary fields
            $data = [
                'id' => $complaint->id,
                'title' => $complaint->title,
                'description' => $complaint->description,
                'location' => $complaint->location,
                'priority' => $complaint->priority,
                'status' => $complaint->status,
                'photo' => $complaint->photo,
                'photo_url' => $complaint->photo_url,
                'admin_response' => $complaint->admin_response,
                'estimated_resolution' => $complaint->estimated_resolution ? $complaint->estimated_resolution->format('Y-m-d\TH:i:s.u\Z') : null,
                'report_date' => $complaint->report_date ? $complaint->report_date->format('Y-m-d\TH:i:s.u\Z') : null,
                'created_at' => $complaint->created_at->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $complaint->updated_at->format('Y-m-d\TH:i:s.u\Z'),
                'category' => $complaint->category,
                'attachments' => $complaint->attachments,
            ];

            return $this->success($data, 'Complaint updated successfully');

        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->serverError('Failed to update complaint', $e);
        }
    }

    /**
     * Remove the specified complaint (only if pending)
     */
    public function destroy(Request $request, Complaint $complaint)
    {
        try {
            // Check if user owns this complaint
            if ($complaint->user_id !== $request->user()->id) {
                return $this->unauthorized('You do not have access to this complaint');
            }

            // Check if complaint can be deleted
            if ($complaint->status !== 'pending') {
                return $this->error('Complaints that have been processed cannot be deleted');
            }

            // Delete files
            if ($complaint->photo) {
                Storage::disk('public')->delete($complaint->photo);
            }

            foreach ($complaint->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

            $complaint->delete();

            return $this->deleted('Complaint deleted successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to delete complaint', $e);
        }
    }

    /**
     * Get categories for dropdown
     */
    public function categories()
    {
        try {
            $categories = Category::where('is_active', true)
                ->select('id', 'name', 'description', 'icon', 'color')
                ->orderBy('name')
                ->get();

            return $this->success($categories, 'Categories list loaded successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to load categories', $e);
        }
    }

    /**
     * Get complaint statistics for user
     */
    public function statistics(Request $request)
    {
        try {
            $user = $request->user();

            $stats = [
                'total' => $user->complaints()->count(),
                'pending' => $user->complaints()->where('status', 'pending')->count(),
                'in_progress' => $user->complaints()->where('status', 'in_progress')->count(),
                'resolved' => $user->complaints()->where('status', 'resolved')->count(),
                'rejected' => $user->complaints()->where('status', 'rejected')->count(),
            ];

            return $this->success($stats, 'Statistics loaded successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to load statistics', $e);
        }
    }

    /**
     * User dashboard with stats and recent activities
     */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();

            // Get user's complaint statistics
            $stats = [
                'total' => Complaint::where('user_id', $user->id)->count(),
                'pending' => Complaint::where('user_id', $user->id)->where('status', 'pending')->count(),
                'in_progress' => Complaint::where('user_id', $user->id)->where('status', 'in_progress')->count(),
                'resolved' => Complaint::where('user_id', $user->id)->where('status', 'resolved')->count(),
                'rejected' => Complaint::where('user_id', $user->id)->where('status', 'rejected')->count(),
            ];

            // Get user's recent complaints (latest 5)
            $recentComplaints = Complaint::where('user_id', $user->id)
                ->with(['category'])
                ->latest()
                ->take(5)
                ->get();

            // Get latest announcements (active, urgent, or sticky)
            $announcements = Announcement::where('is_active', true)
                ->where(function($query) {
                    $query->where('priority', 'urgent')
                          ->orWhere('is_sticky', true);
                })
                ->latest('published_at')
                ->take(3)
                ->get();

            $data = [
                'stats' => $stats,
                'recent_complaints' => $recentComplaints,
                'announcements' => $announcements,
            ];

            return $this->success($data, 'Dashboard data loaded successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to load dashboard data', $e);
        }
    }

    /**
     * Track complaint with full timeline
     */
    public function track($id, Request $request)
    {
        try {
            $user = $request->user();

            // Get complaint with relationships
            $complaint = Complaint::with([
                'category',
                'attachments',
                'responses' => function($query) {
                    $query->latest();
                }
            ])->findOrFail($id);

            // Check if user owns this complaint
            if ($complaint->user_id !== $user->id) {
                return $this->unauthorized('You do not have access to this complaint');
            }

            // Build timeline from status changes and responses
            $timeline = [];

            // Add created event
            $timeline[] = [
                'type' => 'created',
                'status' => 'pending',
                'title' => 'Complaint Created',
                'description' => 'Your complaint has been successfully created',
                'created_at' => $complaint->created_at->format('Y-m-d\TH:i:s.u\Z'),
            ];

            // Add status change events (if status_history column exists)
            // Otherwise, just show current status
            if ($complaint->status !== 'pending') {
                $statusLabels = [
                    'in_progress' => 'In Progress',
                    'resolved' => 'Resolved',
                    'rejected' => 'Rejected',
                ];

                $timeline[] = [
                    'type' => 'status_change',
                    'status' => $complaint->status,
                    'title' => $statusLabels[$complaint->status] ?? 'Status Changed',
                    'description' => 'Complaint status has been updated',
                    'created_at' => $complaint->updated_at->format('Y-m-d\TH:i:s.u\Z'),
                ];
            }

            // Add response events
            foreach ($complaint->responses as $response) {
                $timeline[] = [
                    'type' => 'response',
                    'status' => $complaint->status,
                    'title' => 'Response from Admin',
                    'description' => $response->content,
                    'photo' => $response->photo,
                    'created_at' => $response->created_at->format('Y-m-d\TH:i:s.u\Z'),
                ];
            }

            // Sort timeline by date (newest first)
            usort($timeline, function($a, $b) {
                return strcmp($b['created_at'], $a['created_at']);
            });

            $data = [
                'complaint' => $complaint,
                'timeline' => $timeline,
            ];

            return $this->success($data, 'Complaint tracking loaded successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Complaint not found');
        } catch (\Exception $e) {
            return $this->serverError('Failed to track complaint', $e);
        }
    }
}
