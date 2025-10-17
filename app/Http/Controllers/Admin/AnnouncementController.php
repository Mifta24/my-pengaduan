<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of announcements
     */
    public function index(Request $request)
    {
        $query = Announcement::query();

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Search by title or content
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate stats
        $stats = [
            'total' => Announcement::count(),
            'published' => Announcement::where('is_active', true)->count(),
            'draft' => Announcement::where('is_active', false)->where('published_at', null)->count(),
            'unpublished' => Announcement::where('is_active', false)->count(),
        ];

        return view('admin.announcements.index', compact('announcements', 'stats'));
    }

    /**
     * Show the form for creating a new announcement
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created announcement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'is_sticky' => 'boolean',
            'priority' => 'nullable|in:urgent,high,medium,low',
            'published_at' => 'nullable|date'
        ]);

        // Set default values
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['is_sticky'] = $validated['is_sticky'] ?? false;
        $validated['priority'] = $validated['priority'] ?? 'medium';
        $validated['published_at'] = $validated['published_at'] ?? now();
        $validated['author_id'] = auth()->id(); // Add author_id

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Display the specified announcement
     */
    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the announcement
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:announcements,slug,' . $announcement->id,
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'is_active' => 'nullable|boolean',
            'is_sticky' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'priority' => 'nullable|in:urgent,high,medium,low',
            'target_audience' => 'nullable|array',
            'published_at' => 'nullable|date'
        ]);

        // Handle checkboxes
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;
        $validated['is_sticky'] = $request->has('is_sticky') ? true : false;
        $validated['allow_comments'] = $request->has('allow_comments') ? true : false;

        // Handle attachments removal
        if ($request->has('remove_attachments')) {
            $attachments = $announcement->attachments ?? [];
            foreach ($request->remove_attachments as $index) {
                if (isset($attachments[$index])) {
                    // Delete file from storage if exists
                    if (is_array($attachments[$index]) && isset($attachments[$index]['path'])) {
                        Storage::disk('public')->delete($attachments[$index]['path']);
                    }
                    unset($attachments[$index]);
                }
            }
            $validated['attachments'] = array_values($attachments); // Re-index array
        }

        $announcement->update($validated);

        return redirect()->route('admin.announcements.show', $announcement)
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified announcement
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Toggle announcement status
     */
    public function toggleStatus(Announcement $announcement)
    {
        $newStatus = !$announcement->is_active;

        // If activating and published_at is null, set it to now
        $updateData = ['is_active' => $newStatus];
        if ($newStatus && is_null($announcement->published_at)) {
            $updateData['published_at'] = now();
        }

        $announcement->update($updateData);

        $status = $announcement->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Pengumuman berhasil {$status}.");
    }

    /**
     * Toggle urgent status
     */
    public function toggleUrgent(Announcement $announcement)
    {
        // Backward-compatible: toggle between urgent and medium priority
        $newPriority = $announcement->priority === 'urgent' ? 'medium' : 'urgent';
        $announcement->update(['priority' => $newPriority]);

        $status = $newPriority === 'urgent' ? 'ditandai sebagai urgent' : 'status urgent dihapus';

        return redirect()->back()
            ->with('success', "Pengumuman berhasil {$status}.");
    }

    /**
     * Publish announcement
     */
    public function publish(Announcement $announcement)
    {
        $announcement->update([
            'is_active' => true,
            'published_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    /**
     * Unpublish announcement
     */
    public function unpublish(Announcement $announcement)
    {
        $announcement->update(['is_active' => false]);

        return redirect()->back()
            ->with('success', 'Pengumuman berhasil dibatalkan publikasinya.');
    }

    /**
     * Duplicate announcement
     */
    public function duplicate(Announcement $announcement)
    {
        $newAnnouncement = $announcement->replicate();
        $newAnnouncement->title = $announcement->title . ' (Copy)';

        // Generate unique slug
        $baseSlug = $announcement->slug . '-copy';
        $slug = $baseSlug;
        $counter = 1;

        while (Announcement::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $newAnnouncement->slug = $slug;
        $newAnnouncement->is_active = false;
        $newAnnouncement->published_at = null;
        $newAnnouncement->views_count = 0;
        $newAnnouncement->save();

        return redirect()->route('admin.announcements.edit', $newAnnouncement)
            ->with('success', 'Pengumuman berhasil diduplikasi.');
    }

    /**
     * Bulk actions for announcements
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete,urgent,not_urgent,sticky,not_sticky',
            'announcement_ids' => 'required|array',
            'announcement_ids.*' => 'exists:announcements,id'
        ]);

        $announcements = Announcement::whereIn('id', $validated['announcement_ids']);

        switch ($validated['action']) {
            case 'activate':
                // Set published_at if null when activating
                $announcementIds = $validated['announcement_ids'];
                Announcement::whereIn('id', $announcementIds)
                    ->whereNull('published_at')
                    ->update(['published_at' => now()]);

                $announcements->update(['is_active' => true]);
                $message = 'Pengumuman yang dipilih berhasil diaktifkan.';
                break;

            case 'deactivate':
                $announcements->update(['is_active' => false]);
                $message = 'Pengumuman yang dipilih berhasil dinonaktifkan.';
                break;

            case 'urgent':
                $announcements->update(['priority' => 'urgent']);
                $message = 'Pengumuman yang dipilih berhasil ditandai sebagai urgent.';
                break;

            case 'not_urgent':
                $announcements->update(['priority' => 'medium']);
                $message = 'Pengumuman yang dipilih berhasil dihapus status urgentnya.';
                break;

            case 'sticky':
                $announcements->update(['is_sticky' => true]);
                $message = 'Pengumuman yang dipilih berhasil dipin (sticky).';
                break;

            case 'not_sticky':
                $announcements->update(['is_sticky' => false]);
                $message = 'Pin (sticky) pada pengumuman yang dipilih dihapus.';
                break;

            case 'delete':
                $announcements->delete();
                $message = 'Pengumuman yang dipilih berhasil dihapus.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get announcements for API (public announcements)
     */
    public function apiIndex()
    {
        $announcements = Announcement::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderBy('is_sticky', 'desc')
            ->orderByRaw("CASE
                WHEN priority = 'urgent' THEN 1
                WHEN priority = 'high' THEN 2
                WHEN priority = 'medium' THEN 3
                ELSE 4 END")
            ->orderBy('published_at', 'desc')
            ->select('id', 'title', 'content', 'priority', 'is_sticky', 'published_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Get specific announcement for API
     */
    public function apiShow(Announcement $announcement)
    {
        if (!$announcement->is_active || $announcement->published_at > now()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan atau tidak aktif.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $announcement->only(['id', 'title', 'content', 'priority', 'is_sticky', 'published_at'])
        ]);
    }

    /**
     * Export announcements data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel'); // excel, pdf, csv

        $announcements = Announcement::orderBy('created_at', 'desc')->get();

        // This will be implemented with export libraries
        return redirect()->back()
            ->with('info', "Fitur export {$format} akan segera tersedia.");
    }
}
