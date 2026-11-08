<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function current(): JsonResponse
    {
        $user = Auth::user();

        $activeShift = Shift::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('start_time')
            ->first();

        if (!$activeShift) {
            return response()->json([
                'has_active_shift' => false,
                'shift' => null,
            ]);
        }

        // Hitung total penjualan tunai selama shift berlangsung
        $cashSales = (float) Payment::where('user_id', $user->id)
            ->where('payment_method', 'cash')
            ->where('status', 'success')
            ->where('created_at', '>=', $activeShift->start_time)
            ->sum('amount');

        $nonCashSales = (float) Payment::where('user_id', $user->id)
            ->where('payment_method', '!=', 'cash')
            ->where('status', 'success')
            ->where('created_at', '>=', $activeShift->start_time)
            ->sum('amount');

        $totalOrdersCount = Order::where('user_id', $user->id)
            ->where('created_at', '>=', $activeShift->start_time)
            ->count();

        $expectedCash = (float) $activeShift->starting_cash + $cashSales;

        return response()->json([
            'has_active_shift' => true,
            'shift' => [
                'id' => $activeShift->id,
                'start_time' => $activeShift->start_time->format('d/m/Y H:i'),
                'starting_cash' => (float) $activeShift->starting_cash,
                'cash_sales' => $cashSales,
                'non_cash_sales' => $nonCashSales,
                'expected_cash' => $expectedCash,
                'orders_count' => $totalOrdersCount,
            ]
        ]);
    }

    public function open(Request $request): JsonResponse
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        // Cek jika kasir sudah punya shift terbuka
        $existing = Shift::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih memiliki shift aktif yang belum ditutup.',
            ], 422);
        }

        $shift = Shift::create([
            'user_id' => $user->id,
            'start_time' => now(),
            'starting_cash' => (float) $request->starting_cash,
            'status' => 'open',
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'OPEN_SHIFT',
            'description' => "Kasir {$user->name} membuka shift baru dengan modal awal Rp " . number_format($shift->starting_cash, 0, ',', '.'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift kasir berhasil dibuka!',
            'shift' => $shift,
        ]);
    }

    public function close(Request $request): JsonResponse
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        $shift = Shift::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('start_time')
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift aktif yang perlu ditutup.',
            ], 422);
        }

        $cashSales = (float) Payment::where('user_id', $user->id)
            ->where('payment_method', 'cash')
            ->where('status', 'success')
            ->where('created_at', '>=', $shift->start_time)
            ->sum('amount');

        $expectedCash = (float) $shift->starting_cash + $cashSales;
        $actualCash = (float) $request->actual_cash;
        $difference = $actualCash - $expectedCash;

        $shift->end_time = now();
        $shift->expected_cash = $expectedCash;
        $shift->actual_cash = $actualCash;
        $shift->difference = $difference;
        $shift->status = 'closed';
        $shift->notes = $request->notes;
        $shift->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'CLOSE_SHIFT',
            'description' => "Kasir {$user->name} menutup shift. Diharapkan: Rp " . number_format($expectedCash, 0, ',', '.') . ", Aktual: Rp " . number_format($actualCash, 0, ',', '.') . ", Selisih: Rp " . number_format($difference, 0, ',', '.'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift kasir berhasil ditutup dan direkonsiliasi!',
            'summary' => [
                'starting_cash' => (float) $shift->starting_cash,
                'cash_sales' => $cashSales,
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'difference' => $difference,
                'notes' => $shift->notes,
                'start_time' => $shift->start_time->format('d/m/Y H:i'),
                'end_time' => $shift->end_time->format('d/m/Y H:i'),
            ]
        ]);
    }
}
