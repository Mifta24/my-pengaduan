<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\UserDevice;

class FirebaseService
{
    protected $messaging;
    protected $isConfigured = false;

    public function __construct()
    {
        try {
            // Check if using base64 encoded credentials (for Railway/Heroku deployment)
            if ($base64Creds = env('FIREBASE_CREDENTIALS_BASE64')) {
                Log::info('Using base64 encoded Firebase credentials');
                
                $credentials = base64_decode($base64Creds);
                if ($credentials === false) {
                    Log::error('Failed to decode base64 Firebase credentials');
                    $this->isConfigured = false;
                    return;
                }
                
                // Create temporary file for credentials
                $tempFile = tempnam(sys_get_temp_dir(), 'firebase_');
                file_put_contents($tempFile, $credentials);
                $credentialsPath = $tempFile;
                
            } else {
                // Use file-based credentials
                $credentialsConfig = config('firebase.projects.app.credentials') ?? env('FIREBASE_CREDENTIALS');

                if (!$credentialsConfig) {
                    Log::warning('Firebase credentials not configured in .env');
                    $this->isConfigured = false;
                    return;
                }

                // Build full path
                $credentialsPath = storage_path('app/' . $credentialsConfig);

                // Check if credentials file exists and is not a directory
                if (!file_exists($credentialsPath) || is_dir($credentialsPath)) {
                    Log::warning('Firebase credentials file not found or invalid', [
                        'path' => $credentialsPath,
                        'exists' => file_exists($credentialsPath),
                        'is_dir' => is_dir($credentialsPath),
                    ]);
                    $this->isConfigured = false;
                    return;
                }
            }

            $factory = (new Factory)
                ->withServiceAccount($credentialsPath);

            $this->messaging = $factory->createMessaging();
            $this->isConfigured = true;

            Log::info('Firebase initialized successfully', [
                'project_id' => config('firebase.projects.app.project_id') ?? env('FIREBASE_PROJECT_ID'),
                'method' => env('FIREBASE_CREDENTIALS_BASE64') ? 'base64' : 'file',
            ]);

        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            $this->isConfigured = false;
        }
    }

    /**
     * Check if Firebase is properly configured
     */
    public function isConfigured(): bool
    {
        return $this->isConfigured;
    }

    /**
     * Send notification to single device
     */
    public function sendToDevice(string $token, string $title, string $body, array $data = [])
    {
        if (!$this->isConfigured) {
            Log::warning('Firebase not configured, skipping notification', [
                'title' => $title,
            ]);
            return false;
        }

        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $result = $this->messaging->send($message);

            Log::info('FCM notification sent', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage(), [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
                'error' => $e->getMessage(),
            ]);

            // If token is invalid, mark device as inactive
            if (strpos($e->getMessage(), 'not-found') !== false ||
                strpos($e->getMessage(), 'invalid-registration-token') !== false) {
                $this->markTokenAsInactive($token);
            }

            return false;
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = [])
    {
        if (!$this->isConfigured) {
            Log::warning('Firebase not configured, skipping multicast notification');
            return false;
        }

        if (empty($tokens)) {
            return false;
        }

        try {
            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $result = $this->messaging->sendMulticast($message, $tokens);

            Log::info('FCM multicast sent', [
                'token_count' => count($tokens),
                'success_count' => $result->successes()->count(),
                'failure_count' => $result->failures()->count(),
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('FCM Multicast Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send to topic (broadcast to all subscribed users)
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        if (!$this->isConfigured) {
            Log::warning('Firebase not configured, skipping topic notification');
            return false;
        }

        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            return $this->messaging->send($message);
        } catch (\Exception $e) {
            Log::error('FCM Topic Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Subscribe devices to topic
     */
    public function subscribeToTopic(array $tokens, string $topic)
    {
        if (!$this->isConfigured) {
            Log::warning('Firebase not configured, skipping topic subscription');
            return false;
        }

        try {
            return $this->messaging->subscribeToTopic($topic, $tokens);
        } catch (\Exception $e) {
            Log::error('FCM Subscribe Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark device token as inactive
     */
    protected function markTokenAsInactive(string $token)
    {
        UserDevice::where('device_token', $token)
            ->update(['is_active' => false]);
    }
}
