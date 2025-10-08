<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
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
            'is_urgent' => 'boolean',
            'published_at' => 'nullable|date'
        ]);

        // Set default values
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['is_urgent'] = $validated['is_urgent'] ?? false;
        $validated['published_at'] = $validated['published_at'] ?? now();

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
            'content' => 'required|string',
            'is_active' => 'boolean',
            'is_urgent' => 'boolean',
            'published_at' => 'nullable|date'
        ]);

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
        $announcement->update(['is_active' => !$announcement->is_active]);

        $status = $announcement->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Pengumuman berhasil {$status}.");
    }

    /**
     * Toggle urgent status
     */
    public function toggleUrgent(Announcement $announcement)
    {
        $announcement->update(['is_urgent' => !$announcement->is_urgent]);

        $status = $announcement->is_urgent ? 'ditandai sebagai urgent' : 'urgent dihapus';

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
        $newAnnouncement->is_active = false;
        $newAnnouncement->published_at = null;
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
            'action' => 'required|in:activate,deactivate,delete,urgent,not_urgent',
            'announcement_ids' => 'required|array',
            'announcement_ids.*' => 'exists:announcements,id'
        ]);

        $announcements = Announcement::whereIn('id', $validated['announcement_ids']);

        switch ($validated['action']) {
            case 'activate':
                $announcements->update(['is_active' => true]);
                $message = 'Pengumuman yang dipilih berhasil diaktifkan.';
                break;

            case 'deactivate':
                $announcements->update(['is_active' => false]);
                $message = 'Pengumuman yang dipilih berhasil dinonaktifkan.';
                break;

            case 'urgent':
                $announcements->update(['is_urgent' => true]);
                $message = 'Pengumuman yang dipilih berhasil ditandai sebagai urgent.';
                break;

            case 'not_urgent':
                $announcements->update(['is_urgent' => false]);
                $message = 'Pengumuman yang dipilih berhasil dihapus status urgentnya.';
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
            ->orderBy('is_urgent', 'desc')
            ->orderBy('published_at', 'desc')
            ->select('id', 'title', 'content', 'is_urgent', 'published_at')
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
            'data' => $announcement->only(['id', 'title', 'content', 'is_urgent', 'published_at'])
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
