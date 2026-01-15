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
use Intervention\Image\Laravel\Facades\Image;

/**
 * @group Complaints (User)
 *
 * Endpoints untuk user mengelola pengaduan mereka sendiri
 */
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

            $query = Complaint::with([
                'category:id,name,icon,color,description',
                'attachments',
                'user:id,name,email'
            ])
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
                    'user' => $complaint->user,
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
     * Create Complaint
     *
     * Membuat pengaduan baru. Mendukung upload foto dan attachment.
     * Foto akan otomatis dikompress untuk menghemat storage.
     *
     * @authenticated
     *
     * @bodyParam title string required Judul pengaduan (max: 255). Example: Lampu Jalan Rusak
     * @bodyParam description string required Deskripsi detail pengaduan. Example: Lampu jalan di depan rumah no. 15 sudah mati sejak 3 hari lalu
     * @bodyParam category_id integer required ID kategori pengaduan. Example: 1
     * @bodyParam location string required Lokasi kejadian (max: 255). Example: Jl. Mawar No. 15, RT 01/RW 01
     * @bodyParam priority string Priority level. Opsi: low, medium, high, urgent. Default: medium. Example: high
     * @bodyParam report_date date Tanggal kejadian (format: Y-m-d). Default: today. Example: 2025-01-09
     * @bodyParam photo file Foto pengaduan (jpeg,png,jpg,webp, max: 5MB).
     * @bodyParam attachments file[] Dokumen pendukung (pdf,doc,docx,xls,xlsx,jpeg,jpg,png,webp, max: 10MB per file).
     *
     * @response 201 {
     *   "status": true,
     *   "message": "Pengaduan berhasil dibuat",
     *   "data": {
     *     "complaint": {
     *       "id": 1,
     *       "title": "Lampu Jalan Rusak",
     *       "status": "pending",
     *       "priority": "high",
     *       "created_at": "2025-01-09T10:00:00.000000Z"
     *     }
     *   }
     * }
     *
     * @response 422 {
     *   "status": false,
     *   "message": "Validation failed",
     *   "data": {
     *     "title": ["The title field is required."]
     *   }
     * }
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'location' => 'required|string|max:255',
                'priority' => 'nullable|in:low,medium,high,urgent',
                'report_date' => 'nullable|date|before_or_equal:today',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpeg,jpg,png,webp|max:10240'
            ]);

            $complaint = Complaint::create([
                'user_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'location' => $validated['location'],
                'priority' => $validated['priority'] ?? 'medium',
                'status' => 'pending',
                'report_date' => isset($validated['report_date']) ? $validated['report_date'] : now()
            ]);

            // Handle photo upload with compression
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $fileName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $storagePath = storage_path('app/public/complaints/photos');

                // Create directory if not exists
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                $fullPath = $storagePath . '/' . $fileName;

                // Compress and save image
                $image = Image::read($photo->getRealPath());

                // Resize if larger than 1920px width, maintaining aspect ratio
                if ($image->width() > 1920) {
                    $image->scale(width: 1920);
                }

                // Save with 85% quality for JPEG/WebP
                $extension = strtolower($photo->getClientOriginalExtension());
                if (in_array($extension, ['jpg', 'jpeg'])) {
                    $image->toJpeg(quality: 85)->save($fullPath);
                } elseif ($extension === 'webp') {
                    $image->toWebp(quality: 85)->save($fullPath);
                } else {
                    $image->toPng()->save($fullPath);
                }

                $photoPath = 'complaints/photos/' . $fileName;
                $complaint->update(['photo' => $photoPath]);
            }

            // Handle attachments with validation and compression for images
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $mimeType = $file->getMimeType();
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $storagePath = storage_path('app/public/complaints/attachments');

                    // Create directory if not exists
                    if (!file_exists($storagePath)) {
                        mkdir($storagePath, 0755, true);
                    }

                    // Check if file is an image and compress it
                    if (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])) {
                        $image = Image::read($file->getRealPath());

                        // Resize if larger than 1920px width
                        if ($image->width() > 1920) {
                            $image->scale(width: 1920);
                        }

                        $fullPath = $storagePath . '/' . $fileName;
                        $extension = strtolower($file->getClientOriginalExtension());

                        if (in_array($extension, ['jpg', 'jpeg'])) {
                            $image->toJpeg(quality: 85)->save($fullPath);
                        } elseif ($extension === 'webp') {
                            $image->toWebp(quality: 85)->save($fullPath);
                        } else {
                            $image->toPng()->save($fullPath);
                        }

                        $fileSize = filesize($fullPath);
                    } else {
                        // Non-image files, store normally
                        $file->storeAs('complaints/attachments', $fileName, 'public');
                        $fileSize = $file->getSize();
                    }

                    $path = 'complaints/attachments/' . $fileName;

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $fileSize,
                        'mime_type' => $mimeType,
                    ]);
                }
            }

            $complaint->load(['category:id,name,icon,color,description', 'attachments']);

            // Dispatch event to send notification to admins
            $complaint->load(['category:id,name,icon,color,description', 'attachments', 'user:id,name,email']);

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
                'user' => $complaint->user,
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

            $complaint->load([
                'category:id,name,icon,color,description',
                'attachments',
                'user:id,name,email',
                'responses' => function($query) {
                    $query->with(['user:id,name,email', 'attachments'])->latest();
                }
            ]);

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
                'user' => $complaint->user,
                'category' => $complaint->category,
                'attachments' => $complaint->attachments,
                'responses' => $complaint->responses->map(function($response) {
                    return [
                        'id' => $response->id,
                        'content' => $response->content,
                        'photo' => $response->photo,
                        'photo_url' => $response->photo_url,
                        'created_at' => $response->created_at->format('Y-m-d\TH:i:s.u\Z'),
                        'user' => $response->user,
                        'attachments' => $response->attachments,
                    ];
                }),
                'responses_count' => $complaint->responses->count(),
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
                'priority' => 'nullable|in:low,medium,high,urgent',
                'report_date' => 'nullable|date|before_or_equal:today',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpeg,jpg,png,webp|max:10240'
            ]);

            $updateData = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'location' => $validated['location']
            ];

            // Add priority if provided
            if (isset($validated['priority'])) {
                $updateData['priority'] = $validated['priority'];
            }

            // Add report_date if provided
            if (isset($validated['report_date'])) {
                $updateData['report_date'] = $validated['report_date'];
            }

            $complaint->update($updateData);

            // Handle photo update with compression
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($complaint->photo) {
                    Storage::disk('public')->delete($complaint->photo);
                }

                $photo = $request->file('photo');
                $fileName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $storagePath = storage_path('app/public/complaints/photos');

                // Create directory if not exists
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                $fullPath = $storagePath . '/' . $fileName;

                // Compress and save image
                $image = Image::read($photo->getRealPath());

                // Resize if larger than 1920px width, maintaining aspect ratio
                if ($image->width() > 1920) {
                    $image->scale(width: 1920);
                }

                // Save with 85% quality for JPEG/WebP
                $extension = strtolower($photo->getClientOriginalExtension());
                if (in_array($extension, ['jpg', 'jpeg'])) {
                    $image->toJpeg(quality: 85)->save($fullPath);
                } elseif ($extension === 'webp') {
                    $image->toWebp(quality: 85)->save($fullPath);
                } else {
                    $image->toPng()->save($fullPath);
                }

                $photoPath = 'complaints/photos/' . $fileName;
                $complaint->update(['photo' => $photoPath]);
            }

            // Handle new attachments with compression for images
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $mimeType = $file->getMimeType();
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $storagePath = storage_path('app/public/complaints/attachments');

                    // Create directory if not exists
                    if (!file_exists($storagePath)) {
                        mkdir($storagePath, 0755, true);
                    }

                    // Check if file is an image and compress it
                    if (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])) {
                        $image = Image::read($file->getRealPath());

                        // Resize if larger than 1920px width
                        if ($image->width() > 1920) {
                            $image->scale(width: 1920);
                        }

                        $fullPath = $storagePath . '/' . $fileName;
                        $extension = strtolower($file->getClientOriginalExtension());

                        if (in_array($extension, ['jpg', 'jpeg'])) {
                            $image->toJpeg(quality: 85)->save($fullPath);
                        } elseif ($extension === 'webp') {
                            $image->toWebp(quality: 85)->save($fullPath);
                        } else {
                            $image->toPng()->save($fullPath);
                        }

                        $fileSize = filesize($fullPath);
                    } else {
                        // Non-image files, store normally
                        $file->storeAs('complaints/attachments', $fileName, 'public');
                        $fileSize = $file->getSize();
                    }

                    $path = 'complaints/attachments/' . $fileName;

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $fileSize,
                        'mime_type' => $mimeType,
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
