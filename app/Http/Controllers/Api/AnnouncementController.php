<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Comment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group 📢 Announcements (Public)
 *
 * Endpoints untuk melihat pengumuman. Tidak memerlukan autentikasi.
 */
class AnnouncementController extends Controller
{
    use ApiResponse;

    /**
     * Get Active Announcements
     *
     * Mendapatkan daftar pengumuman yang aktif dan sudah dipublish.
     * Support pagination dan filtering.
     *
     * @unauthenticated
     *
     * @queryParam page integer Nomor halaman. Example: 1
     * @queryParam per_page integer Item per halaman. Example: 10
     * @queryParam priority string Filter by priority (low, medium, high, urgent). Example: high
     */
    public function index(Request $request)
    {
        try {
            $query = Announcement::where('is_active', true)
                ->where('published_at', '<=', now());

            // Filter by priority
            if ($request->filled('priority')) {
                $query->where('priority', $request->get('priority'));
            }

            // Search by title or content
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('content', 'like', '%' . $request->search . '%');
                });
            }

            $announcements = $query->orderBy('is_sticky', 'desc')
                ->orderByRaw("CASE
                    WHEN priority = 'urgent' THEN 1
                    WHEN priority = 'high' THEN 2
                    WHEN priority = 'medium' THEN 3
                    ELSE 4 END")
                ->orderBy('published_at', 'desc')
                ->paginate($request->get('per_page', 10));

            // Collect active filters
            $activeFilters = [];
            if ($request->has('priority')) {
                $activeFilters['priority'] = $request->priority;
            }
            if ($request->has('search')) {
                $activeFilters['search'] = $request->search;
            }
            if ($request->has('per_page')) {
                $activeFilters['per_page'] = (int) $request->per_page;
            }

            return $this->successWithPagination($announcements, 'Announcements list loaded successfully', $activeFilters);

        } catch (\Exception $e) {
            return $this->serverError('Failed to load announcements', $e);
        }
    }

    /**
     * Display the specified announcement
     */
    public function show(Announcement $announcement)
    {
        try {
            // Check if announcement is active and published
            if (!$announcement->is_active || $announcement->published_at > now()) {
                return $this->notFound('Announcement not found or not yet published');
            }

            $data = $announcement->only([
                'id', 'title', 'content', 'priority', 'is_sticky', 'published_at', 'created_at'
            ]);

            return $this->success($data, 'Announcement details loaded successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to load announcement details', $e);
        }
    }

    /**
     * Get latest urgent announcements
     */
    public function urgent()
    {
        try {
            $announcements = Announcement::where('is_active', true)
                ->where('priority', 'urgent')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit(5)
                ->select('id', 'title', 'content', 'published_at')
                ->get();

            return $this->success($announcements, 'Urgent announcements loaded successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to load urgent announcements', $e);
        }
    }

    /**
     * Get latest announcements (for homepage)
     */
    public function latest()
    {
        try {
            $announcements = Announcement::where('is_active', true)
                ->where('published_at', '<=', now())
                ->orderBy('is_sticky', 'desc')
                ->orderByRaw("CASE
                    WHEN priority = 'urgent' THEN 1
                    WHEN priority = 'high' THEN 2
                    WHEN priority = 'medium' THEN 3
                    ELSE 4 END")
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->select('id', 'title', 'content', 'priority', 'is_sticky', 'published_at')
                ->get();

            return $this->success($announcements, 'Latest announcements loaded successfully');

        } catch (\Exception $e) {
            return $this->serverError('Failed to load latest announcements', $e);
        }
    }

    /**
     * Store a comment for an announcement
     */
    public function storeComment(Request $request, $id)
    {
        try {
            // Find announcement
            $announcement = Announcement::findOrFail($id);

            // Check if announcement is active and published
            if (!$announcement->is_active || $announcement->published_at > now()) {
                return $this->notFound('Announcement not found or not yet published');
            }

            // Check if comments are allowed
            if (!$announcement->allow_comments) {
                return $this->unauthorized('Comments are not allowed for this announcement');
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|min:5|max:1000',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            // Create comment using polymorphic relationship
            $comment = $announcement->comments()->create([
                'user_id' => $request->user()->id,
                'content' => $request->content,
                'is_approved' => true, // Auto-approve for now
            ]);

            // Load user relationship for response
            $comment->load('user:id,name,email');

            return $this->created($comment, 'Comment added successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Announcement not found');
        } catch (\Exception $e) {
            return $this->serverError('Failed to add comment', $e);
        }
    }
}
