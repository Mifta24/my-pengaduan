<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\User;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of complaints
     */
    public function index(Request $request)
    {
        $query = Complaint::with(['user', 'category', 'attachments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search by title or description
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('report_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('report_date', '<=', $request->end_date);
        }

        $complaints = $query->orderBy('report_date', 'desc')->paginate(15);
        $categories = Category::where('is_active', true)->get();

        return view('admin.complaints.index', compact('complaints', 'categories'));
    }

    /**
     * Show the form for creating a new complaint
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $users = User::role('user')->get();

        return view('admin.complaints.create', compact('categories', 'users'));
    }

    /**
     * Store a newly created complaint
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'rejected'])],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
            'report_date' => 'required|date'
        ]);

        $complaint = Complaint::create($validated);

        // Handle main photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('complaints/photos', 'public');
            $complaint->update(['photo' => $photoPath]);
        }

        // Handle additional attachments
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

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Keluhan berhasil ditambahkan.');
    }

    /**
     * Display the specified complaint
     */
    public function show(Complaint $complaint)
    {
        $complaint->load([
            'user',
            'category',
            'attachments',
            'complaintAttachments',
            'resolutionAttachments',
            'responses.user'
        ]);

        return view('admin.complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the complaint
     */
    public function edit(Complaint $complaint)
    {
        $categories = Category::where('is_active', true)->get();
        $users = User::role('user')->get();

        return view('admin.complaints.edit', compact('complaint', 'categories', 'users'));
    }

    /**
     * Update the specified complaint
     */
    public function update(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => ['required', Rule::in(['pending', 'in_progress', 'resolved', 'rejected'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'admin_response' => 'nullable|string',
            'estimated_resolution' => 'nullable|date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deleted_images.*' => 'nullable|exists:attachments,id',
            'keep_images.*' => 'nullable|exists:attachments,id'
        ]);

        // Update basic complaint info
        $complaint->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'admin_response' => $validated['admin_response'],
            'estimated_resolution' => $validated['estimated_resolution'] ? \Carbon\Carbon::parse($validated['estimated_resolution']) : null,
        ]);

        // Handle deleted images
        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $attachmentId) {
                $attachment = Attachment::find($attachmentId);
                if ($attachment && $attachment->attachable_id == $complaint->id) {
                    Storage::disk('public')->delete($attachment->file_path);
                    $attachment->delete();
                }
            }
        }

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('complaints', 'public');

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

        return redirect()->route('admin.complaints.show', $complaint)
            ->with('success', 'Keluhan berhasil diperbarui.');
    }

    /**
     * Remove the specified complaint
     */
    public function destroy(Complaint $complaint)
    {
        // Delete associated files
        if ($complaint->photo) {
            Storage::disk('public')->delete($complaint->photo);
        }

        // Delete attachments
        foreach ($complaint->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $complaint->delete();

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Keluhan berhasil dihapus.');
    }

    /**
     * Update complaint status
     */
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'in_progress', 'resolved', 'rejected'])],
            'admin_response' => 'nullable|string',
            'resolution_photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Update complaint status and response
        $complaint->update([
            'status' => $validated['status'],
            'admin_response' => $validated['admin_response'] ?? $complaint->admin_response,
        ]);

        // Handle resolution photos if status is resolved
        if ($validated['status'] === 'resolved' && $request->hasFile('resolution_photos')) {
            foreach ($request->file('resolution_photos') as $file) {
                $path = $file->store('complaints/resolutions', 'public');

                Attachment::create([
                    'attachable_type' => Complaint::class,
                    'attachable_id' => $complaint->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'attachment_type' => 'resolution', // New field to distinguish resolution photos
                ]);
            }
        }

        $statusText = match($validated['status']) {
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
        };

        return redirect()->back()
            ->with('success', "Status keluhan berhasil diubah menjadi: {$statusText}.");
    }

    /**
     * Add response to complaint
     */
    public function addResponse(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'response' => 'required|string',
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'rejected'])]
        ]);

        // Update complaint response
        $complaint->update([
            'response' => $validated['response'],
            'status' => $validated['status'] ?? $complaint->status
        ]);

        return redirect()->back()
            ->with('success', 'Tanggapan berhasil ditambahkan.');
    }

    /**
     * Delete attachment
     */
    public function deleteAttachment(Attachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return redirect()->back()
            ->with('success', 'Lampiran berhasil dihapus.');
    }

    /**
     * Export complaints to PDF
     */
    public function exportPdf(Request $request)
    {
        // This will be implemented with DomPDF
        return redirect()->back()
            ->with('info', 'Fitur export PDF akan segera tersedia.');
    }

    /**
     * Export complaints to Excel
     */
    public function exportExcel(Request $request)
    {
        // This will be implemented with Laravel Excel
        return redirect()->back()
            ->with('info', 'Fitur export Excel akan segera tersedia.');
    }
}
