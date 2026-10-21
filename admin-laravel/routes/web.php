<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Cimohot (Unified: Customer + Admin)
|--------------------------------------------------------------------------
*/

// =========================================================
// CUSTOMER ROUTES (public-facing)
// =========================================================

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth (Customer)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post');

// API (AJAX endpoints)
Route::any('/api', [HomeController::class, 'api'])->name('api');

// Legacy Compatibility Redirects (.php to clean routes)
Route::redirect('/login/login.php', '/login');
Route::redirect('/login/register.php', '/register');
Route::redirect('/login.php', '/login');
Route::redirect('/register.php', '/register');
Route::redirect('/logout.php', '/logout');
Route::redirect('/akun.php', '/akun');
Route::redirect('/profile.php', '/akun');
Route::redirect('/profil.php', '/akun');
Route::redirect('/cart/cart.php', '/cart');
Route::redirect('/cart.php', '/cart');
Route::redirect('/checkout.php', '/cart');
Route::redirect('/checkout', '/cart');
Route::redirect('/order_confirmation.php', '/cart/confirmation');
Route::redirect('/order_confirmation', '/cart/confirmation');
Route::redirect('/cart/order_confirmation.php', '/cart/confirmation');
Route::redirect('/cart/order_confirmation', '/cart/confirmation');
Route::redirect('/cart/confirmation.php', '/cart/confirmation');
Route::redirect('/index.php', '/');
Route::redirect('/admin/admin-dashboard.php', '/admin/dashboard');
Route::redirect('/admin/admin-login.php', '/admin/login');
Route::redirect('/admin/logout.php', '/admin/logout');

// Customer Protected Routes
Route::middleware(['auth.customer'])->group(function () {
    Route::get('/akun', [ProfileController::class, 'index'])->name('akun');
    Route::post('/akun/update', [ProfileController::class, 'update'])->name('akun.update');
    Route::post('/akun/password', [ProfileController::class, 'changePassword'])->name('akun.password');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/cart/confirmation', [CartController::class, 'confirmation'])->name('cart.confirmation');
});

// =========================================================
// ADMIN ROUTES
// =========================================================

Route::prefix('admin')->group(function () {
    // Admin Auth
    Route::get('/login', [AdminLoginController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
    Route::get('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout.get');
    Route::get('/sso-login', [AdminLoginController::class, 'ssoLogin'])->name('admin.sso');

    // Admin Protected
    Route::middleware(['admin'])->group(function () {
        Route::get('/', function () { return redirect()->route('admin.dashboard'); });
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders');
        Route::get('/menu', [MenuController::class, 'index'])->name('admin.menu');
        Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers');
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
        Route::get('/promotions', [PromotionController::class, 'index'])->name('admin.promotions');

        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('admin.reports.export.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('admin.reports.export.excel');

        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');
            Route::get('/sales-chart', [DashboardController::class, 'getSalesChart'])->name('sales-chart');
            Route::get('/notifications', [DashboardController::class, 'getNotifications'])->name('notifications');
            Route::get('/orders', [OrderController::class, 'getAll'])->name('orders.all');
            Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
            Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
            Route::get('/menu', [MenuController::class, 'getAll'])->name('menu.all');
            Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
            Route::post('/menu/{id}', [MenuController::class, 'update'])->name('menu.update');
            Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');
            Route::get('/customers', [CustomerController::class, 'getAll'])->name('customers.all');
            Route::get('/customers/{id}/history', [CustomerController::class, 'getHistory'])->name('customers.history');
            Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
            Route::get('/promotions', [PromotionController::class, 'getAll'])->name('promotions.all');
            Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
            Route::put('/promotions/{id}', [PromotionController::class, 'update'])->name('promotions.update');
            Route::patch('/promotions/{id}/toggle', [PromotionController::class, 'toggleStatus'])->name('promotions.toggle');
            Route::delete('/promotions/{id}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
            Route::patch('/menu/{id}/stock', [DashboardController::class, 'quickRestock'])->name('menu.quick-stock');
        });
    });
});

// Alias for admin AJAX endpoints when called via /api/* directly
Route::middleware(['admin'])->prefix('api')->group(function () {
    Route::get('/stats', [DashboardController::class, 'getStats']);
    Route::get('/sales-chart', [DashboardController::class, 'getSalesChart']);
    Route::get('/notifications', [DashboardController::class, 'getNotifications']);
    Route::get('/orders', [OrderController::class, 'getAll']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::get('/menu', [MenuController::class, 'getAll']);
    Route::post('/menu', [MenuController::class, 'store']);
    Route::post('/menu/{id}', [MenuController::class, 'update']);
    Route::patch('/menu/{id}/stock', [DashboardController::class, 'quickRestock']);
    Route::delete('/menu/{id}', [MenuController::class, 'destroy']);
    Route::get('/customers', [CustomerController::class, 'getAll']);
    Route::get('/customers/{id}/history', [CustomerController::class, 'getHistory']);
    Route::get('/reports/generate', [ReportController::class, 'generate']);
    Route::get('/promotions', [PromotionController::class, 'getAll']);
    Route::post('/promotions', [PromotionController::class, 'store']);
    Route::put('/promotions/{id}', [PromotionController::class, 'update']);
    Route::patch('/promotions/{id}/toggle', [PromotionController::class, 'toggleStatus']);
    Route::delete('/promotions/{id}', [PromotionController::class, 'destroy']);
});

// Legacy api.php compatibility bridge (for JS that still calls /api.php)
Route::any('/api.php', [HomeController::class, 'api'])->name('api.legacy');


