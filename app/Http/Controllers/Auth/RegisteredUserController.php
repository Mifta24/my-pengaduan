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
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
            $upload = $this->uploadToCloudinary(
                $request->file('ktp_file'),
                'ktp/photos',
                1920,
                85
            );

            $ktpPath = $upload['url'];
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

        // Assign user role
        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('info', 'Akun Anda berhasil dibuat. Silakan menunggu verifikasi dari admin.');
    }
}
