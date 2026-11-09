<?php

namespace App\Http\Controllers\Admin;

use App\Events\LowStockAlert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockAdjustmentRequest;
use App\Models\ActivityLog;
use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Ingredient::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'low_stock') {
                $query->whereColumn('current_stock', '<=', 'minimum_stock');
            }
        }

        $ingredients = $query->orderBy('name')->paginate(12)->withQueryString();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $recentMovements = StockMovement::with(['ingredient', 'supplier', 'user'])->latest()->take(8)->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'ingredients' => $ingredients,
                'suppliers' => $suppliers,
                'recent_movements' => $recentMovements
            ]);
        }

        return view('admin.inventory.index', compact('ingredients', 'suppliers', 'recentMovements'));
    }

    public function storeIngredient(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:ingredients,sku'],
            'unit' => ['required', 'string', 'max:20'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'cost_per_unit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $ingredient = Ingredient::create($validated);

        // Record initial stock movement if > 0
        if ($ingredient->current_stock > 0) {
            StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity' => $ingredient->current_stock,
                'before_stock' => 0,
                'after_stock' => $ingredient->current_stock,
                'cost_per_unit' => $ingredient->cost_per_unit,
                'notes' => 'Stok awal bahan baku baru',
            ]);
        }

        ActivityLog::log(
            'CREATE_INGREDIENT',
            "Administrator menambahkan bahan baku baru: {$ingredient->name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Bahan baku {$ingredient->name} berhasil ditambahkan!",
                'data' => $ingredient,
            ], 201);
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', "Bahan baku {$ingredient->name} berhasil ditambahkan!");
    }

    public function updateIngredient(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'cost_per_unit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $ingredient->update($validated);

        ActivityLog::log(
            'UPDATE_INGREDIENT',
            "Administrator memperbarui data bahan baku: {$ingredient->name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Bahan baku {$ingredient->name} berhasil diperbarui!",
                'data' => $ingredient,
            ]);
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', "Bahan baku {$ingredient->name} berhasil diperbarui!");
    }

    public function destroyIngredient(Request $request, Ingredient $ingredient)
    {
        if ($ingredient->recipes()->count() > 0) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Bahan baku {$ingredient->name} tidak dapat dihapus karena terhubung dalam resep menu."
                ], 422);
            }
            return back()->with('error', "Bahan baku {$ingredient->name} tidak dapat dihapus karena terhubung dalam resep menu.");
        }

        $name = $ingredient->name;
        $ingredient->delete();

        ActivityLog::log(
            'DELETE_INGREDIENT',
            "Administrator menghapus bahan baku: {$name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Bahan baku {$name} berhasil dihapus."
            ]);
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', "Bahan baku {$name} berhasil dihapus.");
    }

    public function adjustStock(StockAdjustmentRequest $request)
    {
        $validated = $request->validated();
        $ingredient = Ingredient::findOrFail($validated['ingredient_id']);

        DB::transaction(function () use ($validated, $ingredient) {
            $before = (float) $ingredient->current_stock;
            $qty = (float) $validated['quantity'];
            $type = $validated['type'];

            if ($type === 'in') {
                $after = $before + $qty;
            } elseif ($type === 'out' || $type === 'waste') {
                $after = max(0, $before - $qty);
            } else { // adjustment
                $after = $qty; // Qty becomes the actual physical count
            }

            $ingredient->current_stock = $after;
            if (!empty($validated['cost_per_unit'])) {
                $ingredient->cost_per_unit = $validated['cost_per_unit'];
            }
            $ingredient->save();

            StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'user_id' => Auth::id(),
                'type' => $type,
                'quantity' => $qty,
                'before_stock' => $before,
                'after_stock' => $after,
                'cost_per_unit' => $validated['cost_per_unit'] ?? $ingredient->cost_per_unit,
                'notes' => $validated['notes'] ?? 'Penyesuaian stok oleh Administrator',
            ]);

            // If low stock, broadcast event!
            if ($ingredient->isLowStock()) {
                try {
                    broadcast(new LowStockAlert($ingredient));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Reverb broadcast failed: {$e->getMessage()}");
                }
            }
        });

        ActivityLog::log(
            'STOCK_MOVEMENT',
            "Pergerakan stok {$validated['type']} sebesar {$validated['quantity']} {$ingredient->unit} pada {$ingredient->name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Mutasi stok {$ingredient->name} berhasil dicatat!",
                'data' => $ingredient->fresh()
            ]);
        }

        return redirect()->route('admin.inventory.index')
            ->with('success', "Mutasi stok {$ingredient->name} berhasil dicatat!");
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['ingredient', 'supplier', 'user']);

        if ($request->filled('ingredient_id')) {
            $query->where('ingredient_id', $request->input('ingredient_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $movements = $query->latest()->paginate(20)->withQueryString();
        $ingredients = Ingredient::orderBy('name')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'movements' => $movements,
                'ingredients' => $ingredients
            ]);
        }

        return view('admin.inventory.movements', compact('movements', 'ingredients'));
    }
}
