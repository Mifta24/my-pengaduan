<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HandlesCloudinaryUpload;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    use HandlesCloudinaryUpload;

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'nik' => ['required', 'string', 'size:16', 'unique:'.User::class],
            'ktp_file' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'address' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'rt_number' => ['nullable', 'string', 'max:3'],
            'rw_number' => ['nullable', 'string', 'max:3'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);

        // Handle KTP upload via Cloudinary
        $ktpPath = null;
        if ($request->hasFile('ktp_file')) {
            if ($this->isCloudinaryEnabled()) {
                $upload = $this->uploadToCloudinary(
                    $request->file('ktp_file'),
                    'ktp/photos',
                    1920,
                    85
                );

                $ktpPath = $upload['url'];
            } else {
                $ktpPath = $request->file('ktp_file')->store('ktp/photos', 'public');
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'ktp_path' => $ktpPath,
            'address' => $request->address,
            'phone' => $request->phone,
            'rt_number' => $request->rt_number,
            'rw_number' => $request->rw_number,
            'password' => Hash::make($request->password),
            'is_verified' => false, // Admin needs to verify
        ]);

        // Assign role when role data exists; keep registration flow resilient.
        try {
            if (Role::query()->where('name', 'user')->exists()) {
                $user->assignRole('user');
            }
        } catch (\Throwable $e) {
            Log::warning('Skipping role assignment during registration', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('info', 'Akun Anda berhasil dibuat. Silakan menunggu verifikasi dari admin.');
    }
}
