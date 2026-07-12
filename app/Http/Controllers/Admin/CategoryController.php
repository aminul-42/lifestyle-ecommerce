<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * List all categories (top-level + nested children eager loaded).
     */
    public function index()
    {
        $categories = Category::with('parent')
            ->withCount('products')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Flat list of all categories, used to populate the "Parent Category" <select>
        $allCategories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.categories.index', compact('categories', 'allCategories'));
    }

    /**
     * Return a single category as JSON (used by the Edit modal to populate the form).
     */
    public function edit(Category $category)
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'parent_id' => $category->parent_id,
            'is_active' => (bool) $category->is_active,
        ]);
    }

    /**
     * Store a new category (AJAX).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'parent_id' => $validated['parent_id'] ?: null,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => $category,
        ]);
    }

    /**
     * Update an existing category (AJAX).
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                // A category can't be its own parent
                Rule::notIn([$category->id]),
            ],
        ]);

        // Prevent setting parent to one of its own descendants (would break the tree)
        if (!empty($validated['parent_id']) && $this->isDescendant($category, (int) $validated['parent_id'])) {
            return response()->json([
                'message' => 'Cannot set a category as a child of its own descendant.',
                'errors' => ['parent_id' => ['Invalid parent selection.']],
            ], 422);
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $category->name === $validated['name']
                ? $category->slug
                : $this->uniqueSlug($validated['name'], $category->id),
            'parent_id' => $validated['parent_id'] ?: null,
        ]);

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => $category,
        ]);
    }

    /**
     * Toggle is_active status (used by the .ajax-toggle checkbox in the master layout).
     */
    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'message' => $category->is_active ? 'Category activated.' : 'Category deactivated.',
            'is_active' => $category->is_active,
        ]);
    }

    /**
     * Delete a category. Blocked if it has children or products attached.
     */
    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return response()->json([
                'message' => 'Cannot delete a category that has subcategories. Delete or reassign them first.',
            ], 422);
        }

        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Cannot delete a category that has products assigned to it.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }

    /**
     * Generate a unique slug, ignoring the given category id on update.
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Category::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Walk up the tree from $potentialParentId to see if $category appears as an ancestor,
     * which would mean assigning it as parent creates a cycle.
     */
    private function isDescendant(Category $category, int $potentialParentId): bool
    {
        $current = Category::find($potentialParentId);

        while ($current) {
            if ($current->id === $category->id) {
                return true;
            }
            $current = $current->parent_id ? Category::find($current->parent_id) : null;
        }

        return false;
    }
}