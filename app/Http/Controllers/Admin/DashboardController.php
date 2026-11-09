<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = now()->today();

        $todaySales = Order::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $todayOrdersCount = Order::whereDate('created_at', $today)->count();

        $activeOrdersCount = Order::whereNotIn('status', ['completed', 'cancelled'])->count();

        $lowStockIngredients = Ingredient::whereColumn('current_stock', '<=', 'minimum_stock')
            ->take(5)
            ->get();

        $latestOrders = Order::with(['user', 'table', 'payments'])
            ->latest()
            ->take(6)
            ->get();

        $totalUsers = User::count();
        $totalMenus = Menu::count();

        return view('admin_dashboard.index', compact(
            'todaySales',
            'todayOrdersCount',
            'activeOrdersCount',
            'lowStockIngredients',
            'latestOrders',
            'totalUsers',
            'totalMenus'
        ));
    }
}
