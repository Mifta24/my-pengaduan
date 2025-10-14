<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Complaint;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {

        // Get complaint statistics for public display
        $stats = [
            'total_complaints' => Complaint::count(),
            'resolved_complaints' => Complaint::where('status', 'resolved')->count(),
            'pending_complaints' => Complaint::where('status', 'pending')->count(),
            'total_users' => \App\Models\User::count(),
        ];

        // Get recent announcements for home page (3 latest)
        $announcements = Announcement::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('home', compact('stats', 'announcements'));
    }
}
