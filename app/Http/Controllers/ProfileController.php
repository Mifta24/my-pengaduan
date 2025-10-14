<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
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
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
        $userData = $user->load(['complaints.category', 'complaints.responses', 'complaints.attachments']);

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
                            'message' => $response->message,
                            'created_at' => $response->created_at,
                            'user' => $response->user->name ?? 'N/A',
                        ];
                    }),
                    'attachments' => $complaint->attachments->map(function($attachment) {
                        return [
                            'filename' => $attachment->filename,
                            'file_type' => $attachment->file_type,
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
