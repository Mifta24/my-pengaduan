<?php

namespace App\Listeners;

use App\Events\AnnouncementCreated;
use App\Services\FirebaseService;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendAnnouncementNotificationToAll implements ShouldQueue
{
    use InteractsWithQueue;

    protected $firebaseService;

    /**
     * Create the event listener.
     */
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the event.
     */
    public function handle(AnnouncementCreated $event): void
    {
        $announcement = $event->announcement;

        // Get all users (both admin and regular users)
        $users = User::where('is_active', true)->get();

        if ($users->isEmpty()) {
            Log::warning('No active users found to send announcement notification');
            return;
        }

        // Prepare notification data
        $icon = $announcement->priority === 'urgent' ? '🚨' : '📢';
        $title = $icon . ' Pengumuman Baru';
        $body = $announcement->title;
        $data = [
            'type' => 'announcement_created',
            'announcement_id' => (string) $announcement->id,
            'priority' => $announcement->priority,
            'click_action' => 'OPEN_ANNOUNCEMENT',
        ];

        // Collect all active device tokens
        $allTokens = [];
        $notifiedUserCount = 0;

        foreach ($users as $user) {
            // Check if user has notification enabled
            $settings = $user->notificationSettings;
            if ($settings && !$settings->announcement_created) {
                continue; // Skip if user disabled announcement notifications
            }

            $tokens = $user->getActiveDeviceTokens();

            if (empty($tokens)) {
                continue; // Skip if user has no devices
            }

            $allTokens = array_merge($allTokens, $tokens);
            $notifiedUserCount++;
        }

        if (empty($allTokens)) {
            Log::info('No devices found to send announcement notification');
            return;
        }

        // Send notification to all devices at once (more efficient)
        $this->firebaseService->sendToMultipleDevices($allTokens, $title, $body, $data);

        Log::info('Announcement notification sent to all users', [
            'announcement_id' => $announcement->id,
            'total_users' => $notifiedUserCount,
            'total_devices' => count($allTokens),
            'priority' => $announcement->priority,
        ]);
    }
}
