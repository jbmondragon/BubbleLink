<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CustomerShopController,
    CustomerOrderController,
    DashboardController,
    OrderController,
    PlatformAdminOwnerApprovalController,
    ProfileController,
    ServiceController,
    ShopController,
    ShopServiceController
};

// ===== PUBLIC ROUTES =====
Route::get('/', [CustomerShopController::class, 'index'])->name('customer.shops.home');

Route::prefix('shops')->name('customer.shops.')->group(function () {
    Route::get('/', [CustomerShopController::class, 'index'])->name('index');
    Route::get('{shop}/details', [CustomerShopController::class, 'show'])->name('show');
});

// ===== DASHBOARD =====
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'area:dashboard'])
    ->name('dashboard');

// ===== AUTHENTICATED ROUTES =====
Route::middleware('auth')->group(function () {

    // ---- PLATFORM ADMIN ----
    Route::prefix('platform-admin')->middleware('area:platform-admin')->group(function () {
        Route::get('owner-registrations', [PlatformAdminOwnerApprovalController::class, 'index'])
            ->name('platform-admin.owner-registrations.index');

        Route::patch('owner-registrations/{user}/approve', [PlatformAdminOwnerApprovalController::class, 'approve'])
            ->name('platform-admin.owner-registrations.approve');

        Route::patch('owner-registrations/{user}/reject', [PlatformAdminOwnerApprovalController::class, 'reject'])
            ->name('platform-admin.owner-registrations.reject');
    });

    // ---- CUSTOMER ----
    Route::middleware('area:customer')->group(function () {
        Route::prefix('shops/{shop}')->group(function () {
            Route::get('order', [CustomerOrderController::class, 'create'])->name('customer.orders.create');
            Route::post('order', [CustomerOrderController::class, 'store'])->name('customer.orders.store');
        });

        Route::prefix('my-orders')->name('customer.orders.')->group(function () {
            Route::get('/', [CustomerOrderController::class, 'index'])->name('index');
            Route::get('{order}', [CustomerOrderController::class, 'show'])->name('show');
            Route::patch('{order}/rating', [CustomerOrderController::class, 'rate'])->name('rate');
        });
    });

    // ---- PROFILE (SHARED) ----
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ---- SHOP OWNER / BUSINESS ----
    Route::middleware('area:business')->group(function () {

        // Shops
        Route::resource('shops', ShopController::class)->only(['create', 'store', 'show']);

        // Services
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::post('/', [OrderController::class, 'store'])->name('store');
            Route::patch('{order}', [OrderController::class, 'update'])->name('update');
        });

        // Shop Services
        Route::prefix('shop-services')->name('shop-services.')->group(function () {
            Route::post('/', [ShopServiceController::class, 'store'])->name('store');
            Route::delete('{shopService}', [ShopServiceController::class, 'destroy'])->name('destroy');
        });
    });
});

require __DIR__ . '/auth.php';