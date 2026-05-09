<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Traits\HandlesCloudinaryUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use HandlesCloudinaryUpload;

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->except(['avatar']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle avatar removal
        if ($request->boolean('remove_avatar')) {
            if ($user->avatar) {
                $this->deleteAvatarFromCloudinary($user->avatar);
            }
            $user->avatar = null;
        } elseif ($request->hasFile('avatar')) {
            // Delete old avatar from Cloudinary before uploading new one
            if ($user->avatar) {
                $this->deleteAvatarFromCloudinary($user->avatar);
            }
            $upload = $this->uploadToCloudinary(
                $request->file('avatar'),
                'avatars',
                800,
                85
            );
            $user->avatar = $upload['url'];
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Extract public_id from Cloudinary URL and delete the asset.
     */
    private function deleteAvatarFromCloudinary(string $avatarUrl): void
    {
        // Cloudinary URL format: .../image/upload/v{version}/{public_id}.{ext}
        // or .../image/upload/{public_id}.{ext}
        if (filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
            if (preg_match('/\/upload\/(?:v\d+\/)?(.+?)(?:\.\w+)?$/', $avatarUrl, $matches)) {
                $this->deleteFromCloudinary($matches[1]);
            }
        }
    }

    /**
     * Update the user's notification preferences.
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'complaint_updates' => 'boolean',
            'system_announcements' => 'boolean',
            'marketing_emails' => 'boolean',
        ]);

        $user = $request->user();

        // Update notification preferences
        // You can store these in a JSON column or separate notification_preferences table
        $preferences = [
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
            'complaint_updates' => $request->boolean('complaint_updates'),
            'system_announcements' => $request->boolean('system_announcements'),
            'marketing_emails' => $request->boolean('marketing_emails'),
        ];

        // For now, we'll assume you have a notification_preferences JSON column in users table
        // If not, you can create a migration to add it
        $user->update(['notification_preferences' => $preferences]);

        return Redirect::route('profile.edit')->with('status', 'notifications-updated');
    }

    /**
     * Export user's personal data and complaints.
     */
    public function export(Request $request)
    {
        $user = $request->user();

        // Load user with related data
        $userData = $user->load(['complaints.category', 'complaints.responses.user', 'complaints.attachments']);

        // Prepare export data
        $exportData = [
            'user_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'address' => $user->address,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'email_verified_at' => $user->email_verified_at,
                'notification_preferences' => $user->notification_preferences,
            ],
            'complaints' => $user->complaints->map(function($complaint) {
                return [
                    'id' => $complaint->id,
                    'title' => $complaint->title,
                    'description' => $complaint->description,
                    'category' => $complaint->category->name ?? 'N/A',
                    'status' => $complaint->status,
                    'priority' => $complaint->priority,
                    'location' => $complaint->location,
                    'created_at' => $complaint->created_at,
                    'updated_at' => $complaint->updated_at,
                    'responses' => $complaint->responses->map(function($response) {
                        return [
                            'message' => $response->content,
                            'created_at' => $response->created_at,
                            'user' => $response->user->name ?? 'N/A',
                        ];
                    }),
                    'attachments' => $complaint->attachments->map(function($attachment) {
                        return [
                            'filename' => $attachment->file_name,
                            'file_type' => $attachment->mime_type,
                            'file_size' => $attachment->file_size,
                            'created_at' => $attachment->created_at,
                        ];
                    }),
                ];
            }),
            'export_info' => [
                'exported_at' => now(),
                'export_type' => 'complete_user_data',
                'total_complaints' => $user->complaints->count(),
            ]
        ];

        // Generate filename
        $filename = 'user_data_export_' . $user->id . '_' . now()->format('Y_m_d_H_i_s') . '.json';

        // Return as download
        return response()->json($exportData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
