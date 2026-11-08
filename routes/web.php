<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\KdsController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

// Root entry point (auto redirect based on role or to login)
Route::get('/', DashboardRedirectController::class)->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

// Authenticated Routes
Route::middleware(['auth', 'active.user'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Default dashboard redirect helper
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');

    // STAFF / KASIR MODULE
    Route::middleware('role:staff|admin')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::get('/pos/receipt/{order}/pdf', [PosController::class, 'receiptPdf'])->name('pos.receipt.pdf');
        Route::get('/pos/orders', [PosController::class, 'orders'])->name('pos.orders');
        Route::post('/pos/hold', [PosController::class, 'holdOrder'])->name('pos.hold');
        Route::get('/pos/held-orders', [PosController::class, 'heldOrders'])->name('pos.held-orders');
        Route::post('/pos/orders/{order}/resume', [PosController::class, 'resumeOrder'])->name('pos.orders.resume');
        Route::post('/pos/orders/{order}/void', [PosController::class, 'voidOrder'])->name('pos.orders.void');

        // Shift Kasir
        Route::get('/pos/shift/current', [ShiftController::class, 'current'])->name('pos.shift.current');
        Route::post('/pos/shift/open', [ShiftController::class, 'open'])->name('pos.shift.open');
        Route::post('/pos/shift/close', [ShiftController::class, 'close'])->name('pos.shift.close');
    });

    // KOKI / KITCHEN DISPLAY SYSTEM (KDS) MODULE
    Route::middleware('role:koki|admin')->group(function () {
        Route::get('/kds', [KdsController::class, 'index'])->name('kds.index');
        Route::patch('/kds/orders/{order}/status', [KdsController::class, 'updateStatus'])->name('kds.orders.status');
        Route::patch('/kds/items/{item}/status', [KdsController::class, 'updateItemStatus'])->name('kds.items.status');
        Route::post('/kds/items/{item}/cancel', [KdsController::class, 'cancelItem'])->name('kds.items.cancel');
        Route::post('/kds/menus/{menu}/sold-out', [KdsController::class, 'toggleSoldOut'])->name('kds.menus.sold-out');
    });

    // ADMIN MODULE (Strictly Admin only)
    Route::prefix('admin')->as('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::resource('users', UserController::class);

        // Categories
        Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);

        // Menus
        Route::patch('/menus/{menu}/toggle', [MenuController::class, 'toggleAvailability'])->name('menus.toggle');
        Route::get('/menus/{menu}/recipes', [MenuController::class, 'recipes'])->name('menus.recipes');
        Route::post('/menus/{menu}/recipes', [MenuController::class, 'updateRecipes'])->name('menus.recipes.update');
        Route::resource('menus', MenuController::class);

        // Inventory & Stocks
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/ingredients', [InventoryController::class, 'storeIngredient'])->name('inventory.ingredients.store');
        Route::put('/inventory/ingredients/{ingredient}', [InventoryController::class, 'updateIngredient'])->name('inventory.ingredients.update');
        Route::delete('/inventory/ingredients/{ingredient}', [InventoryController::class, 'destroyIngredient'])->name('inventory.ingredients.destroy');
        Route::post('/inventory/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.adjust');
        Route::get('/inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');

        // Tables
        Route::resource('tables', TableController::class)->except(['create', 'edit', 'show']);

        // Discounts & Promos
        Route::patch('/discounts/{discount}/toggle', [DiscountController::class, 'toggleStatus'])->name('discounts.toggle');
        Route::resource('discounts', DiscountController::class);

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Reports & Analytics
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/sales/excel', [ReportController::class, 'exportExcel'])->name('reports.sales.excel');
        Route::get('/reports/sales/pdf', [ReportController::class, 'exportPdf'])->name('reports.sales.pdf');
        Route::get('/reports/shifts', [ReportController::class, 'shifts'])->name('reports.shifts');
    });
});
