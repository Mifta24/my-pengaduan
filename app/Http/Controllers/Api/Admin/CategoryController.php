<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @group 👨‍💼 Admin - Categories
 *
 * Endpoints untuk admin mengelola kategori pengaduan.
 */
class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Get All Categories (Admin)
     *
     * Mendapatkan semua kategori dengan statistik complaint.
     *
     * @authenticated
     *
     * @queryParam search string Search category name. Example: infrastruktur
     * @queryParam is_active boolean Filter by status. Example: 1
     */
    public function index(Request $request)
    {
        try {
            $query = Category::withCount('complaints');

            // Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination or all
            if ($request->boolean('all')) {
                $categories = $query->get();
                return $this->success($categories, 'List Category loaded successfully');
            } else {
                $perPage = $request->get('per_page', 15);
                $categories = $query->paginate($perPage);

                // Collect active filters
                $activeFilters = [];
                if ($request->has('is_active')) {
                    $activeFilters['is_active'] = $request->boolean('is_active');
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

                return $this->successWithPagination($categories, 'List Category loaded successfully', $activeFilters);
            }
        } catch (\Exception $e) {
            return $this->serverError('Failed to load categories', $e);
        }
    }

    /**
     * Get active categories only (for dropdowns)
     */
    public function active()
    {
        try {
            $categories = Category::where('is_active', true)
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'icon', 'color', 'description']);

            return $this->success($categories, 'List Category loaded successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to load active categories', $e);
        }
    }

    /**
     * Get specific category
     */
    public function show($id)
    {
        try {
            $category = Category::withCount('complaints')->findOrFail($id);
            return $this->success($category, 'Detail Category loaded successfully');
        } catch (\Exception $e) {
            return $this->notFound('Category not found');
        }
    }

    /**
     * Create new category
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:categories,name',
                'slug' => 'nullable|string|max:150', // slug not stored in DB, just validated
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            // Only include fields that exist in database
            $data = $request->only(['name', 'description', 'icon', 'color', 'is_active']);

            $category = Category::create($data);

            return $this->created($category, 'Category created successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to create category', $e);
        }
    }

    /**
     * Update category
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:categories,name,' . $id,
                'slug' => 'nullable|string|max:150', // slug not stored in DB, just validated
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:20',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            // Only include fields that exist in database
            $data = $request->only(['name', 'description', 'icon', 'color', 'is_active']);

            $category->update($data);

            return $this->success($category->fresh(), 'Category updated successfully');
        } catch (\Exception $e) {
            return $this->notFound('Category not found');
        }
    }

    /**
     * Delete category
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            // Check if category has complaints
            if ($category->complaints()->count() > 0) {
                return $this->error('Cannot delete category with complaints');
            }

            $category->delete();

            return $this->deleted('Category deleted successfully');
        } catch (\Exception $e) {
            return $this->notFound('Category not found');
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->is_active = !$category->is_active;
            $category->save();

            $status = $category->is_active ? 'activated' : 'deactivated';
            return $this->success($category, "Category successfully {$status}");
        } catch (\Exception $e) {
            return $this->notFound('Category not found');
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_ids' => 'required|array',
                'category_ids.*' => 'exists:categories,id',
                'action' => 'required|in:activate,deactivate,delete',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $categoryIds = $request->category_ids;
            $action = $request->action;

            if ($action === 'activate') {
                Category::whereIn('id', $categoryIds)->update(['is_active' => true]);
                $message = 'Category successfully activated';
            } elseif ($action === 'deactivate') {
                Category::whereIn('id', $categoryIds)->update(['is_active' => false]);
                $message = 'Category successfully deactivated';
            } elseif ($action === 'delete') {
                // Check if any category has complaints
                $categoriesWithComplaints = Category::whereIn('id', $categoryIds)
                    ->has('complaints')
                    ->count();

                if ($categoriesWithComplaints > 0) {
                    return $this->error('Cannot delete category with complaints');
                }

                Category::whereIn('id', $categoryIds)->delete();
                $message = 'Category successfully deleted';
            }

            return $this->success(null, $message);
        } catch (\Exception $e) {
            return $this->serverError('Failed to perform bulk actionP', $e);
        }
    }
}
