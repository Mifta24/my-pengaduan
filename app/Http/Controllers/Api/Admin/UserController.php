<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use ApiResponse;
    /**
     * Get all users
     */
    public function index(Request $request)
    {
        try {
            $query = User::withCount('complaints');

            // Filter by role
            if ($request->has('role')) {
                $query->role($request->role);
            }

            // Filter by verification status
            if ($request->has('verified')) {
                if ($request->boolean('verified')) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            }

            // Filter by user verification
            if ($request->has('is_verified')) {
                $query->where('is_verified', $request->boolean('is_verified'));
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            // Collect active filters
            $activeFilters = [];
            if ($request->has('role')) {
                $activeFilters['role'] = $request->role;
            }
            if ($request->has('verified')) {
                $activeFilters['verified'] = $request->boolean('verified');
            }
            if ($request->has('is_verified')) {
                $activeFilters['is_verified'] = $request->boolean('is_verified');
            }
            if ($request->has('search')) {
                $activeFilters['search'] = $request->search;
            }
            if ($request->has('sort_by')) {
                $activeFilters['sort_by'] = $request->sort_by;
            }
            if ($request->has('sort_order')) {
                $activeFilters['sort_order'] = $request->sort_order;
            }

            return $this->successWithPagination($users, 'Users list loaded successfully', $activeFilters);
        } catch (\Exception $e) {
            return $this->serverError('Failed to load users list', $e);
        }
    }

    /**
     * Get specific user
     */
    public function show($id)
    {
        try {
            $user = User::withCount('complaints')
                ->with(['complaints' => function ($query) {
                    $query->latest()->limit(5);
                }])
                ->findOrFail($id);

            return $this->success($user, 'User details loaded successfully');
        } catch (\Exception $e) {
            return $this->notFound('User not found');
        }
    }

    /**
     * Create new user
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => ['required', 'confirmed', Password::defaults()],
                'phone' => 'nullable|string|max:20',
                'nik' => 'nullable|string|max:20|unique:users,nik',
                'address' => 'nullable|string',
                'rt' => 'nullable|string|max:10',
                'rw' => 'nullable|string|max:10',
                'role' => 'required|in:admin,user',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $userData = $request->except(['password', 'password_confirmation', 'role']);
            $userData['password'] = Hash::make($request->password);
            $userData['email_verified_at'] = now(); // Auto-verify admin-created users

            $user = User::create($userData);
            $user->assignRole($request->role);

            return $this->created($user, 'User created successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to create user', $e);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'nik' => 'nullable|string|max:20|unique:users,nik,' . $id,
                'address' => 'nullable|string',
                'rt' => 'nullable|string|max:10',
                'rw' => 'nullable|string|max:10',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $user->update($request->only([
                'name', 'email', 'phone', 'nik', 'address', 'rt', 'rw'
            ]));

            return $this->success($user->fresh(), 'User updated successfully');
        } catch (\Exception $e) {
            return $this->notFound('User not found');
        }
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting own account
            if ($user->id === auth()->id()) {
                return $this->error('Cannot delete your own account');
            }

            $user->delete();

            return $this->deleted('User deleted successfully');
        } catch (\Exception $e) {
            return $this->notFound('User not found');
        }
    }

    /**
     * Verify user email
     */
    public function verifyEmail($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->email_verified_at = now();
            $user->save();

            return $this->success($user, 'User email verified successfully');
        } catch (\Exception $e) {
            return $this->notFound('User not found');
        }
    }

    /**
     * Verify user identity
     */
    public function verifyUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_verified = true;
            $user->verified_at = now();
            $user->save();

            return $this->success($user, 'User verified successfully');
        } catch (\Exception $e) {
            return $this->notFound('User not found');
        }
    }

    /**
     * Change user role
     */
    public function changeRole(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role' => 'required|in:admin,user',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $user = User::findOrFail($id);

            // Prevent changing own role
            if ($user->id === auth()->id()) {
                return $this->error('Cannot change your own role');
            }

            $user->syncRoles([$request->role]);

            return $this->success($user->fresh(), 'User role updated successfully');
        } catch (\Exception $e) {
            return $this->notFound('User not found');
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
            $user->save();

            return $this->success(null, 'Password reset successfully');
        } catch (\Exception $e) {
            return $this->notFound('User not found');
        }
    }
}
