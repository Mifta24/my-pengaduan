<?php

namespace App\Http\Controllers\Api\Admin;


use Illuminate\Support\Str;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Events\AnnouncementCreated;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Traits\HandlesCloudinaryUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @group 👨‍💼 Admin - Announcements
 *
 * Endpoints untuk admin mengelola pengumuman.
 */
class AnnouncementController extends Controller
{
    use ApiResponse;
    use HandlesCloudinaryUpload;

    /**
     * Get All Announcements (Admin)
     *
     * Mendapatkan semua pengumuman termasuk draft dan unpublished.
     *
     * @authenticated
     *
     * @queryParam search string Search in title/content. Example: gotong
     * @queryParam priority string Filter by priority. Example: urgent
     * @queryParam is_active boolean Filter active status. Example: 1
     * @queryParam page integer Page number. Example: 1
     */
    public function index(Request $request)
    {
        try {
            $query = Announcement::withCount('comments');

            // Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Filter by priority
            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            // Filter by sticky
            if ($request->has('is_sticky')) {
                $query->where('is_sticky', $request->boolean('is_sticky'));
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'published_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $announcements = $query->paginate($perPage);

            // Collect active filters
            $activeFilters = [];
            if ($request->has('is_active')) {
                $activeFilters['is_active'] = $request->boolean('is_active');
            }
            if ($request->has('priority')) {
                $activeFilters['priority'] = $request->priority;
            }
            if ($request->has('is_sticky')) {
                $activeFilters['is_sticky'] = $request->boolean('is_sticky');
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

            return $this->successWithPagination($announcements, 'Announcements list loaded successfully', $activeFilters);
        } catch (\Exception $e) {
            return $this->serverError('Failed to load announcements list', $e);
        }
    }

    /**
     * Get specific announcement
     */
    public function show($id)
    {
        try {
            $announcement = Announcement::withCount('comments')
                ->with(['comments' => function ($query) {
                    $query->latest()->limit(10);
                }])
                ->findOrFail($id);

            return $this->success($announcement, 'Announcement details loaded successfully');
        } catch (\Exception $e) {
            return $this->notFound('Announcement not found');
        }
    }

    /**
     * Create new announcement
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:announcements,slug',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'is_sticky' => 'boolean',
                'is_active' => 'boolean',
                'published_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $data = $request->all();
            $data['slug'] = $request->slug ?? Str::slug($request->title);
            $data['published_at'] = $request->published_at ?? now();
            $data['author_id'] = auth()->id();

            // Note: API admin announcements currently do not manage attachments or images directly.

            $announcement = Announcement::create($data);

            // Dispatch event for notification
            event(new AnnouncementCreated($announcement));

            return $this->created($announcement, 'Announcement created successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to create announcement', $e);
        }
    }

    /**
     * Update announcement
     */
    public function update(Request $request, $id)
    {
        try {
            $announcement = Announcement::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:announcements,slug,' . $id,
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'is_sticky' => 'boolean',
                'is_active' => 'boolean',
                'published_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $data = $request->all();
            if ($request->has('title') && !$request->has('slug')) {
                $data['slug'] = Str::slug($request->title);
            }

            // Note: API admin announcements currently do not manage attachments or images directly.

            $announcement->update($data);

            return $this->success($announcement->fresh(), 'Announcement updated successfully');
        } catch (\Exception $e) {
            return $this->notFound('Announcement not found');
        }
    }

    /**
     * Delete announcement
     */
    public function destroy($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);

            // Note: Attachments cleanup (including Cloudinary) is handled via the web admin controller logic.

            $announcement->delete();

            return $this->deleted('Announcement deleted successfully');
        } catch (\Exception $e) {
            return $this->notFound('Announcement not found');
        }
    }

    /**
     * Toggle announcement status
     */
    public function toggleStatus($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->is_active = !$announcement->is_active;
            $announcement->save();

            $status = $announcement->is_active ? 'activated' : 'deactivated';
            return $this->success($announcement, "Announcement {$status} successfully");
        } catch (\Exception $e) {
            return $this->notFound('Announcement not found');
        }
    }

    /**
     * Toggle announcement sticky status
     */
    public function toggleSticky($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->is_sticky = !$announcement->is_sticky;
            $announcement->save();

            $status = $announcement->is_sticky ? 'pinned' : 'unpinned';
            return $this->success($announcement, "Announcement {$status} successfully");
        } catch (\Exception $e) {
            return $this->notFound('Announcement not found');
        }
    }

    /**
     * Publish announcement
     */
    public function publish($id)
    {
        try {
            $announcement = Announcement::with('author:id,name')->findOrFail($id);

            // Store previous status
            $previousStatus = $announcement->status;

            $announcement->is_active = true;
            $announcement->published_at = now();
            $announcement->save();

            // Refresh to get updated status
            $announcement->refresh();
            $currentStatus = $announcement->status;

            // Dispatch event for notification
            event(new AnnouncementCreated($announcement));

            // Format response data
            $data = [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'summary' => $announcement->summary,
                'content' => $announcement->content,
                'priority' => $announcement->priority,
                'is_active' => $announcement->is_active,
                'status' => $announcement->status,
                'is_sticky' => $announcement->is_sticky,
                'allow_comments' => $announcement->allow_comments,
                'published_at' => $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i:s.u\Z') : null,
                'updated_at' => $announcement->updated_at ? $announcement->updated_at->format('Y-m-d\TH:i:s\Z') : null,
                'views_count' => $announcement->views_count,
                'author' => $announcement->author ? [
                    'id' => $announcement->author->id,
                    'name' => $announcement->author->name,
                ] : null,
            ];

            $meta = [
                'previous_status' => $previousStatus,
                'current_status' => $currentStatus,
            ];

            return $this->success($data, 'Announcement published successfully', $meta);
        } catch (\Exception $e) {
            return $this->notFound('Announcement not found');
        }
    }

    /**
     * Unpublish announcement
     */
    public function unpublish($id)
    {
        try {
            $announcement = Announcement::with('author:id,name')->findOrFail($id);

            // Store previous status
            $previousStatus = $announcement->status;

            $announcement->is_active = false;
            $announcement->save();

            // Refresh to get updated status
            $announcement->refresh();
            $currentStatus = $announcement->status;

            // Format response data
            $data = [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'summary' => $announcement->summary,
                'content' => $announcement->content,
                'priority' => $announcement->priority,
                'is_active' => $announcement->is_active,
                'status' => $announcement->status,
                'is_sticky' => $announcement->is_sticky,
                'allow_comments' => $announcement->allow_comments,
                'published_at' => $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i:s.u\Z') : null,
                'updated_at' => $announcement->updated_at ? $announcement->updated_at->format('Y-m-d\TH:i:s\Z') : null,
                'views_count' => $announcement->views_count,
                'author' => $announcement->author ? [
                    'id' => $announcement->author->id,
                    'name' => $announcement->author->name,
                ] : null,
            ];

            $meta = [
                'previous_status' => $previousStatus,
                'current_status' => $currentStatus,
            ];

            return $this->success($data, 'Announcement unpublished successfully', $meta);
        } catch (\Exception $e) {
            return $this->notFound('Announcement not found');
        }
    }
}
