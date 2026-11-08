<?php

namespace App\Http\Controllers\Admin;

use App\Events\MenuAvailabilityChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Http\Requests\Admin\UpdateMenuRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\MenuAddon;
use App\Models\MenuRecipe;
use App\Models\MenuVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $query = Menu::with(['category', 'variants', 'addons']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'sold_out') {
                $query->where('is_available', false);
            } elseif ($request->input('status') === 'available') {
                $query->where('is_available', true);
            }
        }

        $menus = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.menus.create', compact('categories'));
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Menu::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $imagePath = $validated['image_url'] ?? null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $imagePath = Storage::url($path);
        }

        DB::transaction(function () use ($validated, $slug, $imagePath, $request, &$menu) {
            $menu = Menu::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'image' => $imagePath,
                'price' => $validated['price'],
                'cost_price' => $validated['cost_price'] ?? 0,
                'is_active' => $request->boolean('is_active', true),
                'is_available' => $request->boolean('is_available', true),
                'is_favorite' => $request->boolean('is_favorite', false),
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            // Save Variants
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $v) {
                    if (!empty($v['name'])) {
                        MenuVariant::create([
                            'menu_id' => $menu->id,
                            'group_name' => $v['group'] ?? 'Pilihan',
                            'name' => $v['name'],
                            'price_adjustment' => $v['price'] ?? 0,
                            'is_available' => true,
                        ]);
                    }
                }
            }

            // Save Addons
            if (!empty($validated['addons'])) {
                foreach ($validated['addons'] as $a) {
                    if (!empty($a['name'])) {
                        MenuAddon::create([
                            'menu_id' => $menu->id,
                            'name' => $a['name'],
                            'price' => $a['price'] ?? 0,
                            'is_available' => true,
                        ]);
                    }
                }
            }
        });

        ActivityLog::log(
            'CREATE_MENU',
            "Administrator membuat menu baru: {$menu->name} (Rp " . number_format($menu->price, 0) . ")"
        );

        return redirect()->route('admin.menus.index')
            ->with('success', "Menu {$menu->name} berhasil ditambahkan!");
    }

    public function edit(Menu $menu): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $menu->load(['variants', 'addons', 'recipes.ingredient']);

        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = $menu->image;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $imagePath = Storage::url($path);
        } elseif (!empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        DB::transaction(function () use ($menu, $validated, $imagePath, $request) {
            $menu->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'image' => $imagePath,
                'price' => $validated['price'],
                'cost_price' => $validated['cost_price'] ?? 0,
                'is_active' => $request->boolean('is_active', true),
                'is_available' => $request->boolean('is_available', true),
                'is_favorite' => $request->boolean('is_favorite', false),
                'sort_order' => $validated['sort_order'] ?? $menu->sort_order,
            ]);

            // Sync Variants: Delete and re-create for simplicity
            $menu->variants()->delete();
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $v) {
                    if (!empty($v['name'])) {
                        MenuVariant::create([
                            'menu_id' => $menu->id,
                            'group_name' => $v['group'] ?? 'Pilihan',
                            'name' => $v['name'],
                            'price_adjustment' => $v['price'] ?? 0,
                            'is_available' => true,
                        ]);
                    }
                }
            }

            // Sync Addons
            $menu->addons()->delete();
            if (!empty($validated['addons'])) {
                foreach ($validated['addons'] as $a) {
                    if (!empty($a['name'])) {
                        MenuAddon::create([
                            'menu_id' => $menu->id,
                            'name' => $a['name'],
                            'price' => $a['price'] ?? 0,
                            'is_available' => true,
                        ]);
                    }
                }
            }
        });

        ActivityLog::log(
            'UPDATE_MENU',
            "Administrator memperbarui menu: {$menu->name}"
        );

        return redirect()->route('admin.menus.index')
            ->with('success', "Menu {$menu->name} berhasil diperbarui!");
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $name = $menu->name;
        $menu->delete();

        ActivityLog::log(
            'DELETE_MENU',
            "Administrator menghapus menu: {$name}"
        );

        return redirect()->route('admin.menus.index')
            ->with('success', "Menu {$name} berhasil dihapus.");
    }

    public function toggleAvailability(Menu $menu): RedirectResponse
    {
        $menu->is_available = !$menu->is_available;
        $menu->save();

        $statusText = $menu->is_available ? 'Tersedia' : 'Habis (Sold Out)';

        // Real-time broadcast to Cashier & Kitchen!
        try {
            broadcast(new MenuAvailabilityChanged($menu, $menu->is_available));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Reverb broadcast failed: {$e->getMessage()}");
        }

        ActivityLog::log(
            'TOGGLE_MENU_AVAILABILITY',
            "Ketersediaan menu {$menu->name} diubah menjadi {$statusText}",
            ['menu_id' => $menu->id, 'is_available' => $menu->is_available]
        );

        return back()->with('success', "Status menu {$menu->name} diubah menjadi {$statusText}.");
    }

    public function recipes(Menu $menu): View
    {
        $menu->load('recipes.ingredient');
        $ingredients = Ingredient::orderBy('name')->get();

        return view('admin.menus.recipes', compact('menu', 'ingredients'));
    }

    public function updateRecipes(Request $request, Menu $menu): RedirectResponse
    {
        $request->validate([
            'recipes' => ['nullable', 'array'],
            'recipes.*.ingredient_id' => ['required_with:recipes', 'exists:ingredients,id'],
            'recipes.*.quantity' => ['required_with:recipes', 'numeric', 'min:0.01'],
        ]);

        DB::transaction(function () use ($request, $menu) {
            $menu->recipes()->delete();

            if ($request->has('recipes')) {
                foreach ($request->input('recipes') as $r) {
                    if (!empty($r['ingredient_id']) && !empty($r['quantity'])) {
                        MenuRecipe::create([
                            'menu_id' => $menu->id,
                            'ingredient_id' => $r['ingredient_id'],
                            'quantity_required' => $r['quantity'],
                        ]);
                    }
                }
            }
        });

        ActivityLog::log(
            'UPDATE_RECIPES',
            "Administrator memperbarui komposisi resep bahan baku untuk: {$menu->name}"
        );

        return redirect()->route('admin.menus.index')
            ->with('success', "Resep bahan baku untuk {$menu->name} berhasil disimpan!");
    }
}
