<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group 📱 Device Tokens (User)
 *
 * Endpoints untuk mengelola FCM device tokens untuk push notifications.
 */
class DeviceTokenController extends Controller
{
    use ApiResponse;

    /**
     * Register Device Token
     *
     * Register FCM device token untuk menerima push notifications.
     *
     * @authenticated
     *
     * @bodyParam device_token string required FCM Device Token. Example: dABC123...
     * @bodyParam device_type string Device type (android/ios). Example: android
     * @bodyParam device_name string Device name. Example: Samsung Galaxy S21
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_type' => 'required|in:android,ios',
            'device_model' => 'nullable|string',
            'os_version' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        $user = Auth::user();

        // device_token is globally unique (one FCM token belongs to whichever
        // user most recently registered it, e.g. a shared device or re-login
        // with a different account) - match on it alone, not user_id, or the
        // insert violates the unique constraint when the token already
        // belongs to a different user.
        $device = UserDevice::updateOrCreate(
            [
                'device_token' => $request->device_token,
            ],
            [
                'user_id' => $user->id,
                'device_type' => $request->device_type,
                'device_model' => $request->device_model,
                'os_version' => $request->os_version,
                'app_version' => $request->app_version,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return $this->created($device, 'Device token registered successfully');
    }

    /**
     * Get user devices
     */
    public function index()
    {
        $devices = Auth::user()->devices()
            ->orderBy('last_used_at', 'desc')
            ->get();

        return $this->success($devices, 'Devices list loaded successfully');
    }

    /**
     * Delete device token
     */
    public function destroy($id)
    {
        $device = UserDevice::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$device) {
            return $this->notFound('Device not found');
        }

        $device->delete();

        return $this->deleted('Device removed successfully');
    }
}

