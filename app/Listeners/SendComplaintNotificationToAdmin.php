<?php

namespace App\Listeners;

use App\Events\ComplaintCreated;
use App\Services\FirebaseService;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendComplaintNotificationToAdmin implements ShouldQueue
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
    public function handle(ComplaintCreated $event): void
    {
        $complaint = $event->complaint;

        // Get all admin users
        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            Log::warning('No admins found to send complaint notification');
            return;
        }

        // Prepare notification data
        $title = '🆕 Keluhan Baru #' . $complaint->id;
        $body = $complaint->user->name . ' membuat keluhan baru: ' . $complaint->title;
        $data = [
            'type' => 'complaint_created',
            'complaint_id' => (string) $complaint->id,
            'user_id' => (string) $complaint->user_id,
            'status' => $complaint->status,
            'click_action' => 'OPEN_COMPLAINT',
        ];

        // Send notification to each admin's devices
        foreach ($admins as $admin) {
            // Check if admin has notification enabled
            $settings = $admin->notificationSettings;
            if ($settings && !$settings->complaint_created) {
                continue; // Skip if admin disabled this notification
            }

            $tokens = $admin->getActiveDeviceTokens();

            if (empty($tokens)) {
                continue; // Skip if admin has no devices
            }

            // Send to all admin's devices
            $this->firebaseService->sendToMultipleDevices($tokens, $title, $body, $data);

            Log::info('Complaint notification sent to admin', [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'complaint_id' => $complaint->id,
                'device_count' => count($tokens),
            ]);
        }
    }
}
