<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmNotification;
use App\Models\NotificationSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group 🔔 Notifications (User)
 *
 * Endpoints untuk mengelola notifikasi user.
 */
class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Get User Notifications
     *
     * Mendapatkan daftar notifikasi user dengan pagination.
     *
     * @authenticated
     *
     * @queryParam page integer Nomor halaman. Example: 1
     * @queryParam per_page integer Item per halaman (default: 20). Example: 20
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            $query = FcmNotification::where('user_id', $user->id);

            // Filter by read status
            if ($request->has('status')) {
                if ($request->status === 'unread') {
                    $query->unread();
                } elseif ($request->status === 'read') {
                    $query->read();
                }
            }

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            $notifications = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            // Transform notifications for Android
            $notifications->getCollection()->transform(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'data' => $notification->data,
                    'is_read' => $notification->is_read,
                    'read_at' => $notification->read_at ? $notification->read_at->format('Y-m-d\TH:i:s.u\Z') : null,
                    'created_at' => $notification->created_at->format('Y-m-d\TH:i:s.u\Z'),
                    'updated_at' => $notification->updated_at->format('Y-m-d\TH:i:s.u\Z'),
                ];
            });

            // Collect active filters
            $activeFilters = [];
            if ($request->has('status')) {
                $activeFilters['status'] = $request->status;
            }
            if ($request->has('type')) {
                $activeFilters['type'] = $request->type;
            }
            if ($request->has('per_page')) {
                $activeFilters['per_page'] = (int) $request->per_page;
            }

            // Get unread count
            $unreadCount = FcmNotification::where('user_id', $user->id)
                ->unread()
                ->count();

            return $this->successWithPagination(
                $notifications,
                'Notifications loaded successfully',
                $activeFilters,
                200,
                ['unread_count' => $unreadCount]
            );

        } catch (\Exception $e) {
            return $this->serverError('Failed to load notifications', $e);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = FcmNotification::where('user_id', Auth::id())
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return $this->notFound('Notification not found');
            }

            $notification->markAsRead();

            // Return updated notification data
            $data = [
                'id' => $notification->id,
                'is_read' => $notification->is_read,
                'read_at' => $notification->read_at->format('Y-m-d\TH:i:s.u\Z'),
            ];

            return $this->success($data, 'Notification marked as read');

        } catch (\Exception $e) {
            return $this->serverError('Failed to mark notification as read', $e);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            FcmNotification::where('user_id', Auth::id())
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return $this->success(null, 'All notifications marked as read');

        } catch (\Exception $e) {
            return $this->serverError('Failed to mark all notifications as read', $e);
        }
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

        return $this->success($settings, 'Notification settings loaded successfully');
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

        return $this->success($settings, 'Notification settings updated successfully');
    }
}

