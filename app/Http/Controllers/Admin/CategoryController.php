<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::withCount('menus')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
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

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori {$category->name} berhasil ditambahkan!");
    }

    public function update(Request $request, Category $category): RedirectResponse
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

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori {$category->name} berhasil diperbarui!");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->menus()->count() > 0) {
            return back()->with('error', "Kategori {$category->name} tidak dapat dihapus karena masih memiliki {$category->menus()->count()} menu.");
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::log(
            'DELETE_CATEGORY',
            "Administrator menghapus kategori: {$name}"
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori {$name} berhasil dihapus.");
    }
}
