<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'complaints']);

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by account status
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'],
            'phone' => $validated['phone'] ?? null,
            'email_verified_at' => now(), // Auto-verify admin created accounts
        ]);

        // Assign role
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['roles', 'complaints.category']);

        // Get user statistics
        $stats = [
            'total_complaints' => $user->complaints->count(),
            'pending_complaints' => $user->complaints->where('status', 'pending')->count(),
            'in_progress_complaints' => $user->complaints->where('status', 'in_progress')->count(),
            'resolved_complaints' => $user->complaints->where('status', 'resolved')->count(),
            'rejected_complaints' => $user->complaints->where('status', 'rejected')->count(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load(['roles', 'complaints', 'comments']);
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name'
        ]);

        // Update user data
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
        ];

        // Update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Update role
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Check if user has complaints
        $complaintCount = $user->complaints()->count();

        if ($complaintCount > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', "Pengguna tidak dapat dihapus karena masih memiliki {$complaintCount} keluhan.");
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Verify user email
     */
    public function verifyEmail(User $user)
    {
        $user->update(['email_verified_at' => now()]);

        return redirect()->back()
            ->with('success', 'Email pengguna berhasil diverifikasi.');
    }

    /**
     * Unverify user email
     */
    public function unverifyEmail(User $user)
    {
        $user->update(['email_verified_at' => null]);

        return redirect()->back()
            ->with('success', 'Verifikasi email pengguna berhasil dibatalkan.');
    }

    /**
     * Change user role
     */
    public function changeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        // Prevent changing own role
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->back()
            ->with('success', 'Role pengguna berhasil diubah.');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->back()
            ->with('success', 'Password pengguna berhasil direset.');
    }

    /**
     * Bulk actions for users
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:verify,unverify,delete,change_role',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required_if:action,change_role|exists:roles,name'
        ]);

        $users = User::whereIn('id', $validated['user_ids']);

        // Prevent actions on current user
        if (in_array(Auth::id(), $validated['user_ids'])) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat melakukan aksi pada akun Anda sendiri.');
        }

        switch ($validated['action']) {
            case 'verify':
                $users->update(['email_verified_at' => now()]);
                $message = 'Email pengguna yang dipilih berhasil diverifikasi.';
                break;

            case 'unverify':
                $users->update(['email_verified_at' => null]);
                $message = 'Verifikasi email pengguna yang dipilih berhasil dibatalkan.';
                break;

            case 'change_role':
                foreach ($users->get() as $user) {
                    $user->syncRoles([$validated['role']]);
                }
                $message = 'Role pengguna yang dipilih berhasil diubah.';
                break;

            case 'delete':
                // Check if any user has complaints
                $usersWithComplaints = User::whereIn('id', $validated['user_ids'])
                    ->withCount('complaints')
                    ->having('complaints_count', '>', 0)
                    ->count();

                if ($usersWithComplaints > 0) {
                    return redirect()->back()
                        ->with('error', 'Beberapa pengguna tidak dapat dihapus karena masih memiliki keluhan.');
                }

                $users->delete();
                $message = 'Pengguna yang dipilih berhasil dihapus.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        // Prevent admin from disabling their own account
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Pengguna {$user->name} berhasil {$status}.");
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel'); // excel, pdf, csv

        $users = User::with('roles')->get();

        // This will be implemented with export libraries
        return redirect()->back()
            ->with('info', "Fitur export {$format} akan segera tersedia.");
    }
}
