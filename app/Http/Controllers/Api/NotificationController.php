<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get user notifications (from database)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->when($request->unread, function ($query) {
                $query->whereNull('read_at');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Get notification settings
     */
    public function getSettings()
    {
        $settings = Auth::user()->notificationSettings;

        if (!$settings) {
            $settings = NotificationSetting::create([
                'user_id' => Auth::id(),
                'complaint_created' => true,
                'complaint_status_changed' => true,
                'announcement_created' => true,
                'admin_response' => true,
                'comment_added' => true,
                'push_enabled' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'complaint_created' => 'boolean',
            'complaint_status_changed' => 'boolean',
            'announcement_created' => 'boolean',
            'admin_response' => 'boolean',
            'comment_added' => 'boolean',
            'push_enabled' => 'boolean',
        ]);

        $settings = Auth::user()->notificationSettings;

        if (!$settings) {
            $settings = new NotificationSetting(['user_id' => Auth::id()]);
        }

        $settings->fill($request->all());
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated',
            'data' => $settings,
        ]);
    }
}

