<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Category;
use App\Models\Attachment;
use App\Events\ComplaintCreated;
use App\Services\VideoCompressionService;
use App\Traits\HandlesCloudinaryUpload;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ComplaintController extends Controller
{
    use HandlesCloudinaryUpload;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * User dashboard with complaint overview
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get user's complaint statistics
        $stats = [
            'total' => $user->complaints()->count(),
            'pending' => $user->complaints()->where('status', 'pending')->count(),
            'in_progress' => $user->complaints()->where('status', 'in_progress')->count(),
            'resolved' => $user->complaints()->where('status', 'resolved')->count(),
            'rejected' => $user->complaints()->where('status', 'rejected')->count(),
        ];

        // Get user's recent complaints
        $recentComplaints = $user->complaints()
            ->with('category')
            ->orderBy('report_date', 'desc')
            ->limit(5)
            ->get();

        // Get latest announcements (order by sticky, then priority, then published_at)
        $announcements = \App\Models\Announcement::where('is_active', true)
            ->where('published_at', '<=', now())
            ->orderBy('is_sticky', 'desc')
            ->orderByRaw("CASE
                WHEN priority = 'urgent' THEN 1
                WHEN priority = 'high' THEN 2
                WHEN priority = 'medium' THEN 3
                ELSE 4 END")
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('dashboard', compact('stats', 'recentComplaints', 'announcements'));
    }

    /**
     * Display a listing of user's complaints
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->complaints()->with('category');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $complaints = $query->orderBy('report_date', 'desc')->paginate(10);
        $categories = Category::where('is_active', true)->get();

        return view('complaints.index', compact('complaints', 'categories'));
    }

    /**
     * Show the form for creating a new complaint
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->distinct()
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'icon', 'color']);
        return view('complaints.create', compact('categories'));
    }

    /**
     * Store a newly created complaint
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'category_id' => 'required|exists:categories,id',
                'location' => 'nullable|string|max:255',
                'priority' => 'required|in:low,medium,high,urgent',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'videos' => 'nullable|array|max:3',
                'videos.*' => 'nullable|file|mimes:mp4,mov,webm,avi|max:102400',
            ], [
                'images.*.image' => 'File harus berupa gambar.',
                'images.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
                'images.*.max' => 'Ukuran gambar maksimal 10MB.',
                'videos.max' => 'Maksimal 3 video yang dapat diupload.',
                'videos.*.mimes' => 'Format video harus mp4, mov, webm, atau avi.',
                'videos.*.max' => 'Ukuran video maksimal 100MB.',
            ]);

            $complaint = Complaint::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'location' => $validated['location'] ?? null,
                'priority' => $validated['priority'],
                'status' => 'pending',
                'report_date' => now()
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('complaints', 'public');

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $image->getClientOriginalName(),
                        'file_path' => $imagePath,
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                        'attachment_type' => 'complaint',
                    ]);
                }
            }

            // Handle video uploads
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $this->storeVideoAttachment($video, $complaint);
                }
            }

            // Load relationships for notification
            $complaint->load(['category', 'attachments', 'user']);

            // Dispatch event to send notification to admins
            event(new ComplaintCreated($complaint));

            return redirect()->route('complaints.show', $complaint)
                ->with('success', 'Keluhan berhasil dikirim. Tim kami akan segera merespons.');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat menyimpan keluhan. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Display the specified complaint
     */
    public function show(Complaint $complaint)
    {
        // Check if user owns this complaint
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke keluhan ini.');
        }

        $complaint->load(['category', 'attachments']);

        return view('complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified complaint
     */
    public function edit(Complaint $complaint)
    {
        // Check if user owns this complaint
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke keluhan ini.');
        }

        // Check if complaint can be edited (only pending complaints)
        if ($complaint->status !== 'pending') {
            return redirect()->route('complaints.show', $complaint)
                ->with('error', 'Keluhan yang sudah diproses tidak dapat diubah.');
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('complaints.edit', compact('complaint', 'categories'));
    }

    /**
     * Update the specified complaint
     */
    public function update(Request $request, Complaint $complaint)
    {
        // Check if user owns this complaint
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke keluhan ini.');
        }

        // Check if complaint can be edited
        if ($complaint->status !== 'pending') {
            return redirect()->route('complaints.show', $complaint)
                ->with('error', 'Keluhan yang sudah diproses tidak dapat diubah.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'category_id' => 'required|exists:categories,id',
                'location' => 'nullable|string|max:255',
                'priority' => 'required|in:low,medium,high,urgent',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'videos' => 'nullable|array|max:3',
                'videos.*' => 'nullable|file|mimes:mp4,mov,webm,avi|max:102400',
                'deleted_images.*' => 'nullable|exists:attachments,id',
            ], [
                'images.*.image' => 'File harus berupa gambar.',
                'images.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
                'images.*.max' => 'Ukuran gambar maksimal 10MB.',
                'videos.max' => 'Maksimal 3 video yang dapat diupload.',
                'videos.*.mimes' => 'Format video harus mp4, mov, webm, atau avi.',
                'videos.*.max' => 'Ukuran video maksimal 100MB.',
            ]);

            $complaint->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'location' => $validated['location'] ?? null,
                'priority' => $validated['priority']
            ]);

            // Handle deleted images
            if ($request->has('deleted_images')) {
                foreach ($request->deleted_images as $attachmentId) {
                    $attachment = Attachment::where('id', $attachmentId)
                        ->where('attachable_id', $complaint->id)
                        ->where('attachable_type', Complaint::class)
                        ->first();

                    if ($attachment) {
                        // Delete file from storage
                        Storage::disk('public')->delete($attachment->file_path);
                        // Delete record from database
                        $attachment->delete();
                    }
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('complaints', 'public');

                    Attachment::create([
                        'attachable_type' => Complaint::class,
                        'attachable_id' => $complaint->id,
                        'file_name' => $image->getClientOriginalName(),
                        'file_path' => $imagePath,
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                        'attachment_type' => 'complaint',
                    ]);
                }
            }

            // Handle new video uploads
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $this->storeVideoAttachment($video, $complaint);
                }
            }

            return redirect()->route('complaints.show', $complaint)
                ->with('success', 'Keluhan berhasil diperbarui.');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat memperbarui keluhan.')
                ->withInput();
        }
    }

    /**
     * Track complaint status (public tracking page)
     */
    public function track(Complaint $complaint)
    {
        // Check if user owns this complaint
        if ($complaint->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke keluhan ini.');
        }

        $complaint->load(['category', 'attachments']);

        // Create timeline data
        $timeline = [
            [
                'title' => 'Keluhan Dikirim',
                'description' => 'Keluhan telah diterima oleh sistem',
                'date' => $complaint->report_date,
                'status' => 'completed',
                'icon' => 'send'
            ]
        ];

        if ($complaint->status === 'in_progress') {
            $timeline[] = [
                'title' => 'Sedang Diproses',
                'description' => 'Keluhan sedang ditindaklanjuti oleh tim terkait',
                'date' => $complaint->updated_at,
                'status' => 'current',
                'icon' => 'clock'
            ];
        }

        if ($complaint->status === 'resolved') {
            $timeline[] = [
                'title' => 'Sedang Diproses',
                'description' => 'Keluhan sedang ditindaklanjuti oleh tim terkait',
                'date' => $complaint->updated_at,
                'status' => 'completed',
                'icon' => 'clock'
            ];

            $timeline[] = [
                'title' => 'Keluhan Selesai',
                'description' => 'Keluhan telah diselesaikan',
                'date' => $complaint->updated_at,
                'status' => 'completed',
                'icon' => 'check'
            ];
        }

        if ($complaint->status === 'rejected') {
            $timeline[] = [
                'title' => 'Keluhan Ditolak',
                'description' => 'Keluhan tidak dapat diproses',
                'date' => $complaint->updated_at,
                'status' => 'rejected',
                'icon' => 'x'
            ];
        }

        return view('complaints.track', compact('complaint', 'timeline'));
    }

    /**
     * Store a video attachment.
     *
     * Cloudinary path  → uses PHP upload temp file directly (no local storage).
     * Local-only path  → saves to public disk, then compresses in place if needed.
     */
    private function storeVideoAttachment(UploadedFile $video, Complaint $complaint): void
    {
        $finalMime = $video->getMimeType();
        $finalName = $video->getClientOriginalName();
        $compression = new VideoCompressionService(40);

        // ── Cloudinary: work entirely in temp dir, never touch public storage ──
        if ($this->isCloudinaryEnabled()) {
            $srcPath           = $video->getRealPath();   // PHP upload temp file
            $compressedAbsPath = null;
            $uploadAbsPath     = $srcPath;

            if ($compression->needsCompression($srcPath)) {
                $tmpCompressed = sys_get_temp_dir() . '/' . uniqid('video_') . '.mp4';
                if ($compression->compress($srcPath, $tmpCompressed)) {
                    $compressedAbsPath = $tmpCompressed;
                    $uploadAbsPath     = $tmpCompressed;
                    $finalMime         = 'video/mp4';
                    $finalName         = pathinfo($finalName, PATHINFO_FILENAME) . '.mp4';
                }
            }

            $upload = $this->uploadVideoToCloudinary($uploadAbsPath, 'complaints/videos');

            // PHP cleans up $srcPath automatically; only our compressed temp needs manual removal
            if ($compressedAbsPath && file_exists($compressedAbsPath)) {
                @unlink($compressedAbsPath);
            }

            Attachment::create([
                'attachable_type' => Complaint::class,
                'attachable_id'   => $complaint->id,
                'file_name'       => $finalName,
                'file_path'       => $upload['url'],
                'file_size'       => $upload['size'],
                'mime_type'       => $finalMime,
                'attachment_type' => 'complaint',
            ]);
            return;
        }

        // ── Local storage fallback ────────────────────────────────────────────
        $storedRelPath = $video->store('complaints/videos', 'public');
        $storedAbsPath = Storage::disk('public')->path($storedRelPath);
        $finalRelPath  = $storedRelPath;

        if ($compression->needsCompression($storedAbsPath)) {
            $pathInfo      = pathinfo($storedRelPath);
            $mp4RelPath    = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.mp4';
            $mp4AbsPath    = Storage::disk('public')->path($mp4RelPath);
            $tmpCompressed = $storedAbsPath . '.compressed.mp4';

            if ($compression->compress($storedAbsPath, $tmpCompressed)) {
                if (file_exists($storedAbsPath)) @unlink($storedAbsPath);
                if (file_exists($mp4AbsPath))    @unlink($mp4AbsPath);
                rename($tmpCompressed, $mp4AbsPath);
                $finalRelPath = $mp4RelPath;
                $finalMime    = 'video/mp4';
                $finalName    = pathinfo($finalName, PATHINFO_FILENAME) . '.mp4';
            }
        }

        Attachment::create([
            'attachable_type' => Complaint::class,
            'attachable_id'   => $complaint->id,
            'file_name'       => $finalName,
            'file_path'       => $finalRelPath,
            'file_size'       => filesize(Storage::disk('public')->path($finalRelPath)),
            'mime_type'       => $finalMime,
            'attachment_type' => 'complaint',
        ]);
    }
}
