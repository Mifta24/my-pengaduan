<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of public announcements
     */
    public function index(Request $request)
    {
        $query = Announcement::with(['author'])
            ->where('is_active', true)
            ->where('published_at', '<=', now());

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by title, content, or summary
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%')
                  ->orWhere('summary', 'like', '%' . $search . '%');
            });
        }

        $announcements = $query->orderBy('is_sticky', 'desc')
            ->orderByRaw("CASE
                WHEN priority = 'urgent' THEN 1
                WHEN priority = 'high' THEN 2
                WHEN priority = 'medium' THEN 3
                ELSE 4 END")
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Display the specified announcement
     */
    public function show(Announcement $announcement)
    {
        // Check if announcement is active and published
        if (!$announcement->is_active || $announcement->published_at > now()) {
            abort(404, 'Pengumuman tidak ditemukan atau belum dipublikasikan.');
        }

        // Load relationships
        $announcement->load(['author', 'comments.user']);

        // Increment view count if not already counted in this session
        $viewKey = 'announcement_' . $announcement->id . '_viewed';
        if (!session()->has($viewKey)) {
            $announcement->increment('views_count');
            session()->put($viewKey, true);
        }

        // Get related announcements (same priority level, different announcement)
        $relatedAnnouncements = Announcement::with(['author'])
            ->where('is_active', true)
            ->where('published_at', '<=', now())
            ->where('priority', $announcement->priority)
            ->where('id', '!=', $announcement->id)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        // If no related announcements with same priority, get latest ones
        if ($relatedAnnouncements->count() < 2) {
            $relatedAnnouncements = Announcement::with(['author'])
                ->where('is_active', true)
                ->where('published_at', '<=', now())
                ->where('id', '!=', $announcement->id)
                ->orderBy('published_at', 'desc')
                ->limit(4)
                ->get();
        }

        return view('announcements.show', compact('announcement', 'relatedAnnouncements'));
    }

    /**
     * Store a comment for the announcement
     */
    public function storeComment(Request $request, Announcement $announcement)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // Check if comments are allowed
        if (!$announcement->allow_comments) {
            abort(403, 'Komentar tidak diizinkan untuk pengumuman ini.');
        }

        $announcement->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
