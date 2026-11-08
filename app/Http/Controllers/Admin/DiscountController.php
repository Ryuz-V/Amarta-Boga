<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDiscountRequest;
use App\Models\ActivityLog;
use App\Models\Discount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function index(Request $request): View
    {
        $discounts = Discount::withCount('orders')
            ->latest()
            ->paginate(10);

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create(): View
    {
        return view('admin.discounts.create');
    }

    public function store(StoreDiscountRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $discount = Discount::create($validated);

        ActivityLog::log(
            'CREATE_DISCOUNT',
            "Administrator membuat promo/diskon baru: {$discount->name}"
        );

        return redirect()->route('admin.discounts.index')
            ->with('success', "Diskon {$discount->name} berhasil ditambahkan!");
    }

    public function edit(Discount $discount): View
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount): RedirectResponse
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

        return redirect()->route('admin.discounts.index')
            ->with('success', "Diskon {$discount->name} berhasil diperbarui!");
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        $name = $discount->name;
        $discount->delete();

        ActivityLog::log(
            'DELETE_DISCOUNT',
            "Administrator menghapus promo/diskon: {$name}"
        );

        return redirect()->route('admin.discounts.index')
            ->with('success', "Diskon {$name} berhasil dihapus.");
    }

    public function toggleStatus(Discount $discount): RedirectResponse
    {
        $discount->is_active = !$discount->is_active;
        $discount->save();

        $status = $discount->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Promo {$discount->name} berhasil {$status}.");
    }
}
