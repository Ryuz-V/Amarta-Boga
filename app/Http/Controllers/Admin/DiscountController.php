<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDiscountRequest;
use App\Models\ActivityLog;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $discounts = Discount::withCount('orders')
            ->latest()
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'discounts' => $discounts
            ]);
        }

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create(): View
    {
        return view('admin.discounts.create');
    }

    public function store(StoreDiscountRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $discount = Discount::create($validated);

        ActivityLog::log(
            'CREATE_DISCOUNT',
            "Administrator membuat promo/diskon baru: {$discount->name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Diskon {$discount->name} berhasil ditambahkan!",
                'data' => $discount
            ], 201);
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', "Diskon {$discount->name} berhasil ditambahkan!");
    }

    public function show(Request $request, Discount $discount)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $discount->loadCount('orders')
            ]);
        }

        return redirect()->route('admin.discounts.edit', $discount);
    }

    public function edit(Discount $discount): View
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'uppercase', 'unique:discounts,code,' . $discount->id],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_spend' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $discount->update($validated);

        ActivityLog::log(
            'UPDATE_DISCOUNT',
            "Administrator memperbarui promo/diskon: {$discount->name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Diskon {$discount->name} berhasil diperbarui!",
                'data' => $discount
            ]);
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', "Diskon {$discount->name} berhasil diperbarui!");
    }

    public function destroy(Request $request, Discount $discount)
    {
        $name = $discount->name;
        $discount->delete();

        ActivityLog::log(
            'DELETE_DISCOUNT',
            "Administrator menghapus promo/diskon: {$name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Diskon {$name} berhasil dihapus."
            ]);
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', "Diskon {$name} berhasil dihapus.");
    }

    public function toggleStatus(Request $request, Discount $discount)
    {
        $discount->is_active = !$discount->is_active;
        $discount->save();

        $status = $discount->is_active ? 'diaktifkan' : 'dinonaktifkan';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $discount->is_active,
                'message' => "Promo {$discount->name} berhasil {$status}."
            ]);
        }

        return back()->with('success', "Promo {$discount->name} berhasil {$status}.");
    }
}
