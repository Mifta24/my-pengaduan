<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Complaint;
use App\Models\Attachment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Events\ComplaintStatusChanged;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Response as ComplaintResponse;

class ComplaintController extends Controller
{
    use ApiResponse;
    /**
     * Get all complaints (admin view)
     */
    public function index(Request $request)
    {
        try {
            $query = Complaint::with(['user:id,name,email', 'category:id,name,icon,color']);

            // Filter by status
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $complaints = $query->paginate($perPage);

            // Collect active filters
            $activeFilters = [];
            if ($request->has('status') && $request->status !== 'all') {
                $activeFilters['status'] = $request->status;
            }
            if ($request->has('category_id')) {
                $activeFilters['category_id'] = (int) $request->category_id;
            }
            if ($request->has('search')) {
                $activeFilters['search'] = $request->search;
            }
            if ($request->has('sort_by')) {
                $activeFilters['sort_by'] = $request->sort_by;
            }
            if ($request->has('sort_order')) {
                $activeFilters['sort_order'] = $request->sort_order;
            }

            return $this->successWithPagination($complaints, 'Complaints list loaded successfully', $activeFilters);
        } catch (\Exception $e) {
            return $this->serverError('Failed to load complaints list', $e);
        }
    }

    /**
     * Get specific complaint details
     */
    public function show($id)
    {
        try {
            $complaint = Complaint::with([
                'user:id,name,email,phone,address',
                'category:id,name,icon,color,description',
                'attachments',
                'responses.user:id,name,email'
            ])->findOrFail($id);

            return $this->success($complaint, 'Complaint details loaded successfully');
        } catch (\Exception $e) {
            return $this->notFound('Complaint not found');
        }
    }

    /**
     * Create new complaint (by admin on behalf of user)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'location' => 'required|string|max:255',
                'priority' => 'nullable|in:low,medium,high,urgent',
                'status' => 'nullable|in:pending,in_progress,resolved,rejected',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'attachments.*' => 'nullable|file|max:10240',
                'report_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            // Create complaint
            $complaint = Complaint::create([
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'location' => $request->location,
                'priority' => $request->get('priority', 'medium'),
                'status' => $request->get('status', 'pending'),
                'report_date' => $request->get('report_date', now()),
            ]);

            // Handle main photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('complaints/photos', 'public');
                $complaint->update(['photo' => $photoPath]);
            }

            // Handle additional attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('complaints', 'public');

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'attachment_type' => 'complaint',
                    ]);
                }
            }

            return $this->created(
                $complaint->load(['user:id,name,email', 'category:id,name,icon,color', 'attachments']),
                'Complaint created successfully'
            );
        } catch (\Exception $e) {
            return $this->serverError('Failed to create complaint', $e);
        }
    }

    /**
     * Update complaint data
     */
    public function update(Request $request, $id)
    {
        try {
            $complaint = Complaint::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'category_id' => 'sometimes|required|exists:categories,id',
                'location' => 'sometimes|required|string|max:255',
                'priority' => 'sometimes|required|in:low,medium,high,urgent',
                'status' => 'sometimes|required|in:pending,in_progress,resolved,rejected',
                'admin_response' => 'nullable|string',
                'estimated_resolution' => 'nullable|date',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'attachments.*' => 'nullable|file|max:10240',
                'delete_attachments' => 'nullable|array',
                'delete_attachments.*' => 'exists:attachments,id',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            // Update basic info
            $complaint->update($request->only([
                'title',
                'description',
                'category_id',
                'location',
                'priority',
                'status',
                'admin_response',
                'estimated_resolution',
            ]));

            // Handle photo update
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($complaint->photo) {
                    Storage::disk('public')->delete($complaint->photo);
                }
                $photoPath = $request->file('photo')->store('complaints/photos', 'public');
                $complaint->update(['photo' => $photoPath]);
            }

            // Handle attachment deletion
            if ($request->has('delete_attachments')) {
                foreach ($request->delete_attachments as $attachmentId) {
                    $attachment = Attachment::find($attachmentId);
                    if ($attachment && $attachment->attachable_id == $complaint->id) {
                        Storage::disk('public')->delete($attachment->file_path);
                        $attachment->delete();
                    }
                }
            }

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('complaints', 'public');

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'attachment_type' => 'complaint',
                    ]);
                }
            }

            return $this->success(
                $complaint->fresh(['user:id,name,email', 'category:id,name,icon,color', 'attachments']),
                'Complaint updated successfully'
            );
        } catch (\Exception $e) {
            return $this->serverError('Failed to update complaint', $e);
        }
    }

    /**
     * Delete complaint
     */
    public function destroy($id)
    {
        try {
            $complaint = Complaint::findOrFail($id);

            // Delete associated files
            if ($complaint->photo) {
                Storage::disk('public')->delete($complaint->photo);
            }

            // Delete attachments
            foreach ($complaint->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

            $complaint->delete();

            return $this->deleted('Complaint deleted successfully');
        } catch (\Exception $e) {
            return $this->notFound('Complaint not found');
        }
    }

    /**
     * Update complaint status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,resolved,rejected',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $complaint = Complaint::findOrFail($id);
            $oldStatus = $complaint->status;

            // Update status
            $complaint->status = $request->status;
            $complaint->save();

            // Add response if notes provided
            if ($request->filled('notes')) {
                ComplaintResponse::create([
                    'complaint_id' => $complaint->id,
                    'user_id' => $request->user()->id,
                    'content' => $request->notes,
                ]);
            }

            // Dispatch event for notification (only if status changed)
            if ($oldStatus !== $request->status) {
                event(new ComplaintStatusChanged($complaint, $oldStatus, $request->status));
            }

            return $this->success($complaint->fresh(['user', 'category']), 'Complaint status updated successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to update complaint status', $e);
        }
    }

    /**
     * Add response/comment to complaint
     */
    public function addResponse(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $complaint = Complaint::findOrFail($id);

            $response = ComplaintResponse::create([
                'complaint_id' => $complaint->id,
                'user_id' => $request->user()->id,
                'content' => $request->message,
            ]);

            return $this->created($response->load('user:id,name,email'), 'Response added successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to add response', $e);
        }
    }

    /**
     * Delete attachment
     */
    public function deleteAttachment($id)
    {
        try {
            $attachment = Attachment::findOrFail($id);

            // Delete file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $attachment->delete();

            return $this->deleted('Attachment deleted successfully');
        } catch (\Exception $e) {
            return $this->notFound('Attachment not found');
        }
    }

    /**
     * Get complaints statistics for admin
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => Complaint::count(),
                'by_status' => [
                    'pending' => Complaint::where('status', 'pending')->count(),
                    'in_progress' => Complaint::where('status', 'in_progress')->count(),
                    'resolved' => Complaint::where('status', 'resolved')->count(),
                    'rejected' => Complaint::where('status', 'rejected')->count(),
                ],
                'today' => Complaint::whereDate('created_at', today())->count(),
                'this_week' => Complaint::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => Complaint::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'by_category' => Complaint::select('category_id', DB::raw('count(*) as total'))
                    ->with('category:id,name')
                    ->groupBy('category_id')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'category' => $item->category->name ?? 'Unknown',
                            'total' => $item->total,
                        ];
                    }),
            ];

            return $this->success($stats, 'Statistics loaded successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to load statistics', $e);
        }
    }

    /**
     * Bulk update complaints
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'complaint_ids' => 'required|array',
                'complaint_ids.*' => 'exists:complaints,id',
                'action' => 'required|in:update_status,delete',
                'status' => 'required_if:action,update_status|in:pending,in_progress,resolved,rejected',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $complaintIds = $request->complaint_ids;
            $action = $request->action;

            if ($action === 'update_status') {
                Complaint::whereIn('id', $complaintIds)->update(['status' => $request->status]);
                $message = 'Complaints status updated successfully';
            } elseif ($action === 'delete') {
                Complaint::whereIn('id', $complaintIds)->delete();
                $message = 'Complaints deleted successfully';
            }


            return $this->success(null, $message);
        } catch (\Exception $e) {
            return $this->serverError('Failed to perform bulk action', $e);
        }
    }

    /**
     * Mark complaint as resolved with resolution response and photos
     */
    public function markAsResolved(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'resolution_response' => 'required|string',
                'resolution_photos' => 'nullable|array|max:3',
                'resolution_photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $complaint = Complaint::findOrFail($id);
            $oldStatus = $complaint->status;

            // Update complaint status to resolved with admin response
            $complaint->update([
                'status' => 'resolved',
                'admin_response' => $request->resolution_response,
            ]);

            // Add resolution response as a complaint response
            ComplaintResponse::create([
                'complaint_id' => $complaint->id,
                'user_id' => $request->user()->id,
                'content' => $request->resolution_response,
            ]);

            // Handle resolution photos
            $uploadedPhotos = [];
            if ($request->hasFile('resolution_photos')) {
                foreach ($request->file('resolution_photos') as $file) {
                    $path = $file->store('complaints/resolutions', 'public');

                    $attachment = Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'attachment_type' => 'resolution',
                    ]);

                    $uploadedPhotos[] = [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'file_url' => $attachment->file_url,
                        'file_size_human' => $attachment->file_size_human,
                    ];
                }
            }

            // Dispatch event for notification (only if status changed)
            if ($oldStatus !== 'resolved') {
                event(new ComplaintStatusChanged($complaint, $oldStatus, 'resolved'));
            }

            $meta = [
                'previous_status' => $oldStatus,
                'current_status' => 'resolved',
                'resolution_photos_count' => count($uploadedPhotos),
            ];

            $data = [
                'complaint' => $complaint->fresh(['user:id,name,email', 'category:id,name,icon,color']),
                'resolution_photos' => $uploadedPhotos,
            ];

            return $this->success($data, 'Complaint marked as resolved successfully', $meta);
        } catch (\Exception $e) {
            return $this->serverError('Failed to mark complaint as resolved', $e);
        }
    }
}
