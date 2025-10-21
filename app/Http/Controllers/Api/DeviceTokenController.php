<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    /**
     * Register device token (dipanggil dari mobile app)
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

        // Update or create device
        $device = UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_token' => $request->device_token,
            ],
            [
                'device_type' => $request->device_type,
                'device_model' => $request->device_model,
                'os_version' => $request->os_version,
                'app_version' => $request->app_version,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token registered successfully',
            'data' => $device,
        ]);
    }

    /**
     * Get user devices
     */
    public function index()
    {
        $devices = Auth::user()->devices()
            ->orderBy('last_used_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
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
            return response()->json([
                'success' => false,
                'message' => 'Device not found',
            ], 404);
        }

        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device removed successfully',
        ]);
    }
}

