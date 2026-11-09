<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        $preset = $request->get('preset', '7days');
        $today = Carbon::today();

        switch ($preset) {
            case 'today':
                $startDate = $today->copy()->startOfDay();
                $endDate = $today->copy()->endOfDay();
                break;
            case 'yesterday':
                $startDate = $today->copy()->subDay()->startOfDay();
                $endDate = $today->copy()->subDay()->endOfDay();
                break;
            case '30days':
                $startDate = $today->copy()->subDays(29)->startOfDay();
                $endDate = $today->copy()->endOfDay();
                break;
            case 'this_month':
                $startDate = $today->copy()->startOfMonth()->startOfDay();
                $endDate = $today->copy()->endOfMonth()->endOfDay();
                break;
            case 'custom':
                $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : $today->copy()->subDays(6)->startOfDay();
                $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : $today->copy()->endOfDay();
                break;
            case '7days':
            default:
                $startDate = $today->copy()->subDays(6)->startOfDay();
                $endDate = $today->copy()->endOfDay();
                $preset = '7days';
                break;
        }

        // 1. Query Dasar Pesanan
        $ordersQuery = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled');

        $totalOrders = (clone $ordersQuery)->count();
        $grossSales = (float) (clone $ordersQuery)->sum('subtotal');
        $totalDiscount = (float) (clone $ordersQuery)->sum('discount_amount');
        $totalTax = (float) (clone $ordersQuery)->sum('tax_amount');
        $totalService = (float) (clone $ordersQuery)->sum('service_charge');
        $netSales = (float) (clone $ordersQuery)->sum('total_amount');
        $aov = $totalOrders > 0 ? ($netSales / $totalOrders) : 0;

        // 2. Kalkulasi HPP / COGS (Cost of Goods Sold) & Laba Kotor
        $orderIds = (clone $ordersQuery)->pluck('id');
        $totalCogs = (float) OrderItem::whereIn('order_id', $orderIds)
            ->selectRaw('SUM(cost_price * quantity) as cogs')
            ->value('cogs') ?? 0;

        $grossProfit = max(0, $netSales - $totalCogs);
        $profitMargin = $netSales > 0 ? (($grossProfit / $netSales) * 100) : 0;

        // 3. Data Grafik Tren Penjualan Harian (Chart.js Line)
        $salesTrendData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $trendLabels = [];
        $trendValues = [];
        foreach ($salesTrendData as $row) {
            $trendLabels[] = Carbon::parse($row->date)->format('d M');
            $trendValues[] = (float) $row->total;
        }

        // 4. Data Metode Pembayaran (Chart.js Doughnut)
        $paymentBreakdown = Payment::whereIn('order_id', $orderIds)
            ->where('status', 'success')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        $paymentLabels = [];
        $paymentValues = [];
        foreach ($paymentBreakdown as $pb) {
            $paymentLabels[] = strtoupper($pb->payment_method);
            $paymentValues[] = (float) $pb->total;
        }

        // 5. Top 10 Menu Terlaris (Chart.js Bar)
        $topMenus = OrderItem::whereIn('order_id', $orderIds)
            ->select('menu_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('menu_name')
            ->orderByDesc('total_qty')
            ->take(8)
            ->get();

        $topMenuLabels = $topMenus->pluck('menu_name')->toArray();
        $topMenuValues = $topMenus->pluck('total_qty')->toArray();

        // 6. Breakdown Penjualan per Kategori Menu
        $categoryBreakdown = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('categories', 'menus.category_id', '=', 'categories.id')
            ->whereIn('order_items.order_id', $orderIds)
            ->select('categories.name as category_name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // 7. Pesanan Terbaru
        $recentOrders = (clone $ordersQuery)->with(['user', 'table', 'payments'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'period' => [
                    'preset' => $preset,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'kpis' => [
                    'total_orders' => $totalOrders,
                    'gross_sales' => $grossSales,
                    'total_discount' => $totalDiscount,
                    'total_tax' => $totalTax,
                    'total_service' => $totalService,
                    'net_sales' => $netSales,
                    'aov' => $aov,
                    'total_cogs' => $totalCogs,
                    'gross_profit' => $grossProfit,
                    'profit_margin' => $profitMargin,
                ],
                'charts' => [
                    'daily_trends' => array_combine($trendLabels, $trendValues),
                    'payment_methods' => array_combine($paymentLabels, $paymentValues),
                    'top_menus' => array_combine($topMenuLabels, $topMenuValues),
                ],
                'category_breakdown' => $categoryBreakdown,
                'recent_orders' => $recentOrders,
            ]);
        }

        return view('admin.reports.sales', compact(
            'preset',
            'startDate',
            'endDate',
            'totalOrders',
            'grossSales',
            'totalDiscount',
            'totalTax',
            'totalService',
            'netSales',
            'aov',
            'totalCogs',
            'grossProfit',
            'profitMargin',
            'trendLabels',
            'trendValues',
            'paymentLabels',
            'paymentValues',
            'topMenus',
            'topMenuLabels',
            'topMenuValues',
            'categoryBreakdown',
            'recentOrders'
        ));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::today()->subDays(6)->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        $fileName = 'laporan-penjualan-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.xlsx';

        return Excel::download(new SalesReportExport($startDate, $endDate), $fileName);
    }

    public function exportPdf(Request $request): Response
    {
        $startDate = $request->get('start_date', Carbon::today()->subDays(6)->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        $orders = Order::with(['table', 'user', 'payments', 'items'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $totalTax = $orders->sum('tax_amount');
        $totalDiscount = $orders->sum('discount_amount');

        $settings = Setting::pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('admin.reports.sales-pdf', compact(
            'orders',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrders',
            'totalTax',
            'totalDiscount',
            'settings'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("laporan-penjualan-{$startDate}-{$endDate}.pdf");
    }

    public function shifts(Request $request): View
    {
        $shifts = Shift::with(['user'])
            ->when($request->user_id, function ($query, $uid) {
                $query->where('user_id', $uid);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('start_time')
            ->paginate(15)
            ->withQueryString();

        $cashiers = User::role('staff')->get();

        $totalStartingCash = Shift::sum('starting_cash');
        $totalActualCash = Shift::where('status', 'closed')->sum('actual_cash');
        $totalVariance = Shift::where('status', 'closed')->sum('difference');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'total_starting_cash' => $totalStartingCash,
                'total_actual_cash' => $totalActualCash,
                'total_variance' => $totalVariance,
                'shifts' => $shifts,
            ]);
        }

        return view('admin.reports.shifts', compact(
            'shifts',
            'cashiers',
            'totalStartingCash',
            'totalActualCash',
            'totalVariance'
        ));
    }
}
