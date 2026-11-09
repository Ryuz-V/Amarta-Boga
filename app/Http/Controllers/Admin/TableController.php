<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $tables = Table::with(['activeOrder.items'])
            ->orderBy('location')
            ->orderBy('number')
            ->get();

        $locations = Table::select('location')->distinct()->pluck('location');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'tables' => $tables,
                'locations' => $locations
            ]);
        }

        return view('admin.tables.index', compact('tables', 'locations'));
    }

    public function store(Request $request)
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

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Meja {$table->name} berhasil ditambahkan!",
                'data' => $table,
            ], 201);
        }

        return redirect()->route('admin.tables.index')
            ->with('success', "Meja {$table->name} berhasil ditambahkan!");
    }

    public function update(Request $request, Table $table)
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

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data meja {$table->name} berhasil diperbarui!",
                'data' => $table,
            ]);
        }

        return redirect()->route('admin.tables.index')
            ->with('success', "Data meja {$table->name} berhasil diperbarui!");
    }

    public function destroy(Request $request, Table $table)
    {
        if ($table->activeOrder) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Meja {$table->name} sedang memiliki pesanan aktif dan tidak dapat dihapus."
                ], 422);
            }
            return back()->with('error', "Meja {$table->name} sedang memiliki pesanan aktif dan tidak dapat dihapus.");
        }

        $name = $table->name;
        $table->delete();

        ActivityLog::log(
            'DELETE_TABLE',
            "Administrator menghapus meja: {$name}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Meja {$name} berhasil dihapus."
            ]);
        }

        return redirect()->route('admin.tables.index')
            ->with('success', "Meja {$name} berhasil dihapus.");
    }
}
