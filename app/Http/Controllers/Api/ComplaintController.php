<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ComplaintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of user's complaints
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $query = Complaint::with(['category', 'attachments'])
                ->where('user_id', $user->id);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Search
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            $complaints = $query->orderBy('report_date', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => [
                    'complaints' => $complaints->items(),
                    'pagination' => [
                        'current_page' => $complaints->currentPage(),
                        'last_page' => $complaints->lastPage(),
                        'per_page' => $complaints->perPage(),
                        'total' => $complaints->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data keluhan'
            ], 500);
        }
    }

    /**
     * Store a newly created complaint
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'location' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'attachments.*' => 'nullable|file|max:10240'
            ]);

            $complaint = Complaint::create([
                'user_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'location' => $validated['location'],
                'status' => 'pending',
                'report_date' => now()
            ]);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('complaints/photos', 'public');
                $complaint->update(['photo' => $photoPath]);
            }

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('complaints/attachments', 'public');

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            $complaint->load(['category', 'attachments']);

            return response()->json([
                'success' => true,
                'message' => 'Keluhan berhasil dikirim',
                'data' => $complaint
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan keluhan'
            ], 500);
        }
    }

    /**
     * Display the specified complaint
     */
    public function show(Request $request, Complaint $complaint)
    {
        try {
            // Check if user owns this complaint
            if ($complaint->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke keluhan ini'
                ], 403);
            }

            $complaint->load(['category', 'attachments']);

            return response()->json([
                'success' => true,
                'data' => $complaint
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail keluhan'
            ], 500);
        }
    }

    /**
     * Update the specified complaint (only if still pending)
     */
    public function update(Request $request, Complaint $complaint)
    {
        try {
            // Check if user owns this complaint
            if ($complaint->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke keluhan ini'
                ], 403);
            }

            // Check if complaint can be updated
            if ($complaint->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Keluhan yang sudah diproses tidak dapat diubah'
                ], 422);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'location' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'attachments.*' => 'nullable|file|max:10240'
            ]);

            $complaint->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'location' => $validated['location']
            ]);

            // Handle photo update
            if ($request->hasFile('photo')) {
                if ($complaint->photo) {
                    Storage::disk('public')->delete($complaint->photo);
                }
                $photoPath = $request->file('photo')->store('complaints/photos', 'public');
                $complaint->update(['photo' => $photoPath]);
            }

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('complaints/attachments', 'public');

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            $complaint->load(['category', 'attachments']);

            return response()->json([
                'success' => true,
                'message' => 'Keluhan berhasil diperbarui',
                'data' => $complaint
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui keluhan'
            ], 500);
        }
    }

    /**
     * Remove the specified complaint (only if pending)
     */
    public function destroy(Request $request, Complaint $complaint)
    {
        try {
            // Check if user owns this complaint
            if ($complaint->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke keluhan ini'
                ], 403);
            }

            // Check if complaint can be deleted
            if ($complaint->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Keluhan yang sudah diproses tidak dapat dihapus'
                ], 422);
            }

            // Delete files
            if ($complaint->photo) {
                Storage::disk('public')->delete($complaint->photo);
            }

            foreach ($complaint->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

            $complaint->delete();

            return response()->json([
                'success' => true,
                'message' => 'Keluhan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus keluhan'
            ], 500);
        }
    }

    /**
     * Get categories for dropdown
     */
    public function categories()
    {
        try {
            $categories = Category::where('is_active', true)
                ->select('id', 'name', 'description', 'icon', 'color')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil kategori'
            ], 500);
        }
    }

    /**
     * Get complaint statistics for user
     */
    public function statistics(Request $request)
    {
        try {
            $user = $request->user();

            $stats = [
                'total' => $user->complaints()->count(),
                'pending' => $user->complaints()->where('status', 'pending')->count(),
                'in_progress' => $user->complaints()->where('status', 'in_progress')->count(),
                'completed' => $user->complaints()->where('status', 'completed')->count(),
                'rejected' => $user->complaints()->where('status', 'rejected')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik'
            ], 500);
        }
    }
}
