<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(Request $request): View
    {
        $tables = Table::with(['activeOrder.items'])
            ->orderBy('location')
            ->orderBy('number')
            ->get();

        $locations = Table::select('location')->distinct()->pluck('location');

        return view('admin.tables.index', compact('tables', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'string', 'max:20', 'unique:tables,number'],
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
            'location' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:available,occupied,reserved,cleaning'],
        ]);

        $table = Table::create($validated);

        ActivityLog::log(
            'CREATE_TABLE',
            "Administrator menambahkan meja baru: {$table->name} ({$table->number})"
        );

        return redirect()->route('admin.tables.index')
            ->with('success', "Meja {$table->name} berhasil ditambahkan!");
    }

    public function update(Request $request, Table $table): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'string', 'max:20', 'unique:tables,number,' . $table->id],
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
            'location' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:available,occupied,reserved,cleaning'],
        ]);

        $table->update($validated);

        ActivityLog::log(
            'UPDATE_TABLE',
            "Administrator memperbarui data meja: {$table->name}"
        );

        return redirect()->route('admin.tables.index')
            ->with('success', "Data meja {$table->name} berhasil diperbarui!");
    }

    public function destroy(Table $table): RedirectResponse
    {
        if ($table->activeOrder) {
            return back()->with('error', "Meja {$table->name} sedang memiliki pesanan aktif dan tidak dapat dihapus.");
        }

        $name = $table->name;
        $table->delete();

        ActivityLog::log(
            'DELETE_TABLE',
            "Administrator menghapus meja: {$name}"
        );

        return redirect()->route('admin.tables.index')
            ->with('success', "Meja {$name} berhasil dihapus.");
    }
}
