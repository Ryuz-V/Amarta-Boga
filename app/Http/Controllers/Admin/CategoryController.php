<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('menus')
            ->orderBy('sort_order')
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $slug = Str::slug($validated['name']);

        // Check unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'icon' => $validated['icon'] ?? 'utensils',
            'sort_order' => $validated['sort_order'] ?? (Category::max('sort_order') + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log(
            'CREATE_CATEGORY',
            "Administrator membuat kategori baru: {$category->name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kategori {$category->name} berhasil ditambahkan!",
                'data' => $category,
            ], 201);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori {$category->name} berhasil ditambahkan!");
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $request->input('name'),
            'icon' => $request->input('icon', $category->icon),
            'sort_order' => $request->input('sort_order', $category->sort_order),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log(
            'UPDATE_CATEGORY',
            "Administrator memperbarui kategori: {$category->name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kategori {$category->name} berhasil diperbarui!",
                'data' => $category,
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori {$category->name} berhasil diperbarui!");
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->menus()->count() > 0) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Kategori {$category->name} tidak dapat dihapus karena masih memiliki {$category->menus()->count()} menu."
                ], 422);
            }
            return back()->with('error', "Kategori {$category->name} tidak dapat dihapus karena masih memiliki {$category->menus()->count()} menu.");
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::log(
            'DELETE_CATEGORY',
            "Administrator menghapus kategori: {$name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kategori {$name} berhasil dihapus."
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori {$name} berhasil dihapus.");
    }
}
