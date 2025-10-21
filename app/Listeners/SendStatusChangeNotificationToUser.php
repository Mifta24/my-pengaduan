<?php

namespace App\Listeners;

use App\Events\ComplaintStatusChanged;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendStatusChangeNotificationToUser implements ShouldQueue
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
    public function handle(ComplaintStatusChanged $event): void
    {
        $complaint = $event->complaint;
        $user = $complaint->user;

        // Check if user has notification enabled
        $settings = $user->notificationSettings;
        if ($settings && !$settings->complaint_status_changed) {
            Log::info('User has disabled status change notifications', [
                'user_id' => $user->id,
            ]);
            return;
        }

        // Get user's active device tokens
        $tokens = $user->getActiveDeviceTokens();

        if (empty($tokens)) {
            Log::info('User has no active devices', [
                'user_id' => $user->id,
            ]);
            return;
        }

        // Prepare notification data
        $statusLabel = $this->getStatusLabel($event->newStatus);
        $icon = $this->getStatusIcon($event->newStatus);

        $title = $icon . ' Status Keluhan Diperbarui';
        $body = 'Keluhan #' . $complaint->id . ' - ' . $complaint->title . ' sekarang berstatus: ' . $statusLabel;
        $data = [
            'type' => 'complaint_status_changed',
            'complaint_id' => (string) $complaint->id,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'click_action' => 'OPEN_COMPLAINT',
        ];

        // Send notification
        $this->firebaseService->sendToMultipleDevices($tokens, $title, $body, $data);

        Log::info('Status change notification sent to user', [
            'user_id' => $user->id,
            'complaint_id' => $complaint->id,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'device_count' => count($tokens),
        ]);
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Menunggu',
            'process' => 'Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            default => ucfirst($status),
        };
    }

    /**
     * Get status icon
     */
    private function getStatusIcon(string $status): string
    {
        return match($status) {
            'pending' => '⏳',
            'process' => '🔄',
            'completed' => '✅',
            'rejected' => '❌',
            default => '📋',
        };
    }
}
