<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AppIdentityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function edit()
    {
        $settings = AppSetting::query()->pluck('value', 'key')->toArray();

        return view('admin.settings.identity', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'area_name' => 'required|string|max:255',
            'footer_text' => 'nullable|string|max:255',
            'contact_email' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            AppSetting::setValue($key, $value);
        }

        Cache::forget('app_identity_settings');

        return redirect()->route('admin.settings.identity.edit')
            ->with('success', 'Identitas web berhasil diperbarui.');
    }
}
