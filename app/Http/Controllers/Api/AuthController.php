<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Authentication
 *
 * Endpoints untuk registrasi, login, dan logout user
 */
class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register User
     *
     * Mendaftarkan user baru ke sistem MyPengaduan.
     * Setelah registrasi berhasil, user akan mendapat token untuk autentikasi.
     *
     * @unauthenticated
     *
     * @bodyParam name string required Nama lengkap user. Example: John Doe
     * @bodyParam nik string required NIK 16 digit. Example: 3201234567890001
     * @bodyParam ktp_photo file required Foto KTP (JPG, JPEG, PNG, max 2MB).
     * @bodyParam email string required Email address yang valid. Example: john@example.com
     * @bodyParam rt string Rukun Tetangga. Example: 001
     * @bodyParam rw string Rukun Warga. Example: 001
     * @bodyParam phone string Nomor telepon user. Example: 081234567890
     * @bodyParam address string required Alamat lengkap user. Example: Jl. Mawar No. 10
     * @bodyParam password string required Password minimal 8 karakter. Example: password123
     * @bodyParam password_confirmation string required Konfirmasi password harus sama. Example: password123
     *
     * @response 201 {
     *   "status": true,
     *   "message": "User berhasil didaftarkan",
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "nik": "3201234567890001",
     *       "email": "john@example.com",
     *       "address": "Jl. Mawar No. 10",
     *       "rt": "001",
     *       "rw": "001",
     *       "phone": "081234567890",
     *       "ktp_photo": "ktp/abc123.jpg"
     *     },
     *     "token": "1|abc123def456..."
     *   }
     * }
     *
     * @response 422 {
     *   "status": false,
     *   "message": "Validation failed",
     *   "data": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'nik' => 'required|string|size:16|unique:users',
                'ktp_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'address' => 'required|string|max:500',
                'rt' => 'nullable|string|max:10',
                'rw' => 'nullable|string|max:10',
                'phone' => 'nullable|string|max:20'
            ]);

            // Handle KTP photo upload via Cloudinary
            $ktpPhotoPath = null;
            if ($request->hasFile('ktp_photo')) {
                $upload = app(\App\Traits\HandlesCloudinaryUpload::class)->uploadToCloudinary(
                    $request->file('ktp_photo'),
                    'ktp/photos',
                    1920,
                    85
                );

                $ktpPhotoPath = $upload['url'];
            }

            $user = User::create([
                'name' => $validated['name'],
                'nik' => $validated['nik'],
                'ktp_photo' => $ktpPhotoPath,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'address' => $validated['address'],
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]);

            // Assign user role
            $user->assignRole('user');

            // Create token
            $token = $user->createToken('api_token')->plainTextToken;

            // Refresh user to get roles
            $user->refresh();

            $data = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nik' => $user->nik,
                    'ktp_photo' => $user->ktp_photo,
                    'email' => $user->email,
                    'address' => $user->address,
                    'rt' => $user->rt,
                    'rw' => $user->rw,
                    'phone' => $user->phone,
                    'role' => $user->getRoleNames()->first(),
                    'email_verified_at' => $user->email_verified_at,
                    'is_verified' => (bool) $user->is_verified,
                    'created_at' => $user->created_at,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ];

            return $this->created($data, 'Registration successful');
        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->serverError('Registration failed', $e);
        }
    }

    /**
     * Login User
     *
     * Login dengan email dan password untuk mendapatkan authentication token.
     * Token ini digunakan untuk mengakses endpoint yang memerlukan autentikasi.
     *
     * @unauthenticated
     *
     * @bodyParam email string required Email user yang terdaftar. Example: john@example.com
     * @bodyParam password string required Password user. Example: password123
     *
     * @response 200 {
     *   "status": true,
     *   "message": "Login berhasil",
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "john@example.com",
     *       "address": "Jl. Mawar No. 10",
     *       "phone": "081234567890"
     *     },
     *     "token": "1|abc123def456..."
     *   }
     * }
     *
     * @response 401 {
     *   "status": false,
     *   "message": "Email atau password salah"
     * }
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if (!Auth::attempt($validated)) {
                return $this->unauthorized('Invalid email or password');
            }

            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            $data = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nik' => $user->nik,
                    'ktp_photo' => $user->ktp_photo ? asset('storage/' . $user->ktp_photo) : null,
                    'email' => $user->email,
                    'address' => $user->address,
                    'rt' => $user->rt,
                    'rw' => $user->rw,
                    'phone' => $user->phone,
                    'role' => $user->getRoleNames()->first(),
                    'email_verified_at' => $user->email_verified_at,
                    'is_verified' => (bool) $user->is_verified,
                    'created_at' => $user->created_at,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ];

            return $this->success($data, 'Login successful');
        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->serverError('Login failed', $e);
        }
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            $data = [
                'nik' => $user->nik,
                'ktp_photo' => $user->ktp_photo ? asset('storage/' . $user->ktp_photo) : null,
                'email' => $user->email,
                'address' => $user->address,
                'rt' => $user->rt,
                'rw' => $user->rw,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'address' => $user->address,
                'phone' => $user->phone,
                'role' => $user->getRoleNames()->first(),
                'email_verified_at' => $user->email_verified_at,
                'is_verified' => (bool) $user->is_verified,
                'created_at' => $user->created_at,
            ];

            return $this->success($data, 'Profile loaded successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to load profile', $e);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            $validated = $request->validate([
                'address' => 'nullable|string|max:255',
                'rt' => 'nullable|string|max:10',
                'rw' => 'nullable|string|max:10',
                'phone' => 'nullable|string|max:20'
            ]);

            $user->update($validated);

            $data = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nik' => $user->nik,
                    'ktp_photo' => $user->ktp_photo ? asset('storage/' . $user->ktp_photo) : null,
                    'email' => $user->email,
                    'address' => $user->address,
                    'rt' => $user->rt,
                    'rw' => $user->rw,
                    'phone' => $user->phone,
                ],
            ];
            $user->update($validated);

            $data = [
                'user' => $user->only(['id', 'name', 'email', 'address', 'phone'])
            ];

            return $this->success($data, 'Profile updated successfully');
        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->serverError('Failed to update profile', $e);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required',
                'password' => 'required|string|min:8|confirmed'
            ]);

            $user = $request->user();

            if (!Hash::check($validated['current_password'], $user->password)) {
                return $this->unauthorized('Current password is incorrect');
            }

            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            return $this->success(null, 'Password changed successfully');
        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->serverError('Failed to change password', $e);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->success(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->serverError('Logout failed', $e);
        }
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return $this->success(null, 'Logged out from all devices successfully');
        } catch (\Exception $e) {
            return $this->serverError('Logout failed', $e);
        }
    }
}
