<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of active announcements
     */
    public function index(Request $request)
    {
        try {
            $query = Announcement::where('is_active', true)
                ->where('published_at', '<=', now());

            // Filter by urgent
            if ($request->filled('urgent')) {
                $isUrgent = $request->boolean('urgent');
                $query->where('is_urgent', $isUrgent);
            }

            // Search by title or content
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('content', 'like', '%' . $request->search . '%');
                });
            }

            $announcements = $query->orderBy('is_urgent', 'desc')
                ->orderBy('published_at', 'desc')
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => [
                    'announcements' => $announcements->items(),
                    'pagination' => [
                        'current_page' => $announcements->currentPage(),
                        'last_page' => $announcements->lastPage(),
                        'per_page' => $announcements->perPage(),
                        'total' => $announcements->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil pengumuman'
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Pengumuman tidak ditemukan atau belum dipublikasikan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $announcement->only([
                    'id', 'title', 'content', 'is_urgent', 'published_at', 'created_at'
                ])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail pengumuman'
            ], 500);
        }
    }

    /**
     * Get latest urgent announcements
     */
    public function urgent()
    {
        try {
            $announcements = Announcement::where('is_active', true)
                ->where('is_urgent', true)
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->limit(5)
                ->select('id', 'title', 'content', 'published_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $announcements
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil pengumuman urgent'
            ], 500);
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
                ->orderBy('is_urgent', 'desc')
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->select('id', 'title', 'content', 'is_urgent', 'published_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $announcements
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil pengumuman terbaru'
            ], 500);
        }
    }
}
