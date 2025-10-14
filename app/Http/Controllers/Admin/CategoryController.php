<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = Category::withCount('complaints');

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('name')->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean'
        ]);

        // Set default values
        $validated['icon'] = $validated['icon'] ?? 'folder';
        $validated['color'] = $validated['color'] ?? '#6366f1';
        $validated['is_active'] = $validated['is_active'] ?? true;

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load(['complaints' => function($query) {
            $query->with('user')->orderBy('report_date', 'desc');
        }]);

        // Get statistics for this category
        $stats = [
            'total' => $category->complaints->count(),
            'pending' => $category->complaints->where('status', 'pending')->count(),
            'in_progress' => $category->complaints->where('status', 'in_progress')->count(),
            'resolved' => $category->complaints->where('status', 'resolved')->count(),
            'rejected' => $category->complaints->where('status', 'rejected')->count(),
        ];

        return view('admin.categories.show', compact('category', 'stats'));
    }

    /**
     * Show the form for editing the category
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'required|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean'
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.show', $category)
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has complaints
        $complaintCount = $category->complaints()->count();

        if ($complaintCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori tidak dapat dihapus karena masih memiliki {$complaintCount} keluhan.");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Kategori berhasil {$status}.");
    }

    /**
     * Get categories for API (used in dropdowns)
     */
    public function apiIndex()
    {
        $categories = Category::where('is_active', true)
            ->select('id', 'name', 'description', 'icon', 'color')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Bulk actions for categories
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id'
        ]);

        $categories = Category::whereIn('id', $validated['category_ids']);

        switch ($validated['action']) {
            case 'activate':
                $categories->update(['is_active' => true]);
                $message = 'Kategori yang dipilih berhasil diaktifkan.';
                break;

            case 'deactivate':
                $categories->update(['is_active' => false]);
                $message = 'Kategori yang dipilih berhasil dinonaktifkan.';
                break;

            case 'delete':
                // Check if any category has complaints
                $categoriesWithComplaints = Category::whereIn('id', $validated['category_ids'])
                    ->withCount('complaints')
                    ->having('complaints_count', '>', 0)
                    ->count();

                if ($categoriesWithComplaints > 0) {
                    return redirect()->back()
                        ->with('error', 'Beberapa kategori tidak dapat dihapus karena masih memiliki keluhan.');
                }

                $categories->delete();
                $message = 'Kategori yang dipilih berhasil dihapus.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export categories data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel'); // excel, pdf, csv

        $categories = Category::withCount('complaints')->get();

        // This will be implemented with export libraries
        return redirect()->back()
            ->with('info', "Fitur export {$format} akan segera tersedia.");
    }
}
