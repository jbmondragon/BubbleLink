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

/*
|--------------------------------------------------------------------------
| I. PUBLIC ENDPOINTS
|--------------------------------------------------------------------------
| GET /                  -> CustomerShopController@index
| GET /shops             -> CustomerShopController@index
| GET /shops/{shop}/details -> CustomerShopController@show
*/
Route::get('/', [CustomerShopController::class, 'index'])->name('customer.shops.home');

Route::prefix('shops')->name('customer.shops.')->group(function () {
    Route::get('/', [CustomerShopController::class, 'index'])->name('index');
    Route::get('search', [CustomerShopController::class, 'search'])->name('search');
    Route::get('{shop}/details', [CustomerShopController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| II.DASHBOARD ENDPOINT
|--------------------------------------------------------------------------
| GET /dashboard -> DashboardController
| Middleware: auth, area:dashboard
*/
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'area:dashboard'])
    ->name('dashboard');

/*
* Grouped by business area so each section matches the API design document.
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | III. PLATFORM ADMIN ENDPOINTS
    |--------------------------------------------------------------------------
    | GET   /platform-admin/owner-registrations
    | PATCH /platform-admin/owner-registrations/{user}/approve
    | PATCH /platform-admin/owner-registrations/{user}/reject
    */
    Route::prefix('platform-admin')->middleware('area:platform-admin')->group(function () {
        Route::get('owner-registrations', [PlatformAdminOwnerApprovalController::class, 'index'])
            ->name('platform-admin.owner-registrations.index');

        Route::patch('owner-registrations/{user}/approve', [PlatformAdminOwnerApprovalController::class, 'approve'])
            ->name('platform-admin.owner-registrations.approve');

        Route::patch('owner-registrations/{user}/reject', [PlatformAdminOwnerApprovalController::class, 'reject'])
            ->name('platform-admin.owner-registrations.reject');

        Route::patch('owner-registrations/{user}/revoke', [PlatformAdminOwnerApprovalController::class, 'revoke'])
            ->name('platform-admin.owner-registrations.revoke');
    });

    /*
    |--------------------------------------------------------------------------
    | IV. CUSTOMER ORDER ENDPOINTS
    |--------------------------------------------------------------------------
    | GET   /shops/{shop}/order
    | POST  /shops/{shop}/order
    | GET   /my-orders
    | GET   /my-orders/{order}
    | PATCH /my-orders/{order}/rating
    */
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

    /*
    |--------------------------------------------------------------------------
    | V. PROFILE ENDPOINTS
    |--------------------------------------------------------------------------
    | GET    /profile
    | PATCH  /profile
    | DELETE /profile
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | VI. SHOP OWNER ENDPOINTS
    |--------------------------------------------------------------------------
    | GET    /shops/create
    | POST   /shops
    | GET    /shops/{shop}
    | GET    /services
    | GET    /orders
    | POST   /orders
    | PATCH  /orders/{order}
    | POST   /shop-services
    | DELETE /shop-services/{shopService}
    */
    Route::middleware('area:business')->group(function () {

        // Shop profile workspace
        Route::resource('shops', ShopController::class)->only(['create', 'store', 'show']);

        // Service management
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');

        // Owner order management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::post('/', [OrderController::class, 'store'])->name('store');
            Route::patch('{order}', [OrderController::class, 'update'])->name('update');
        });

        // Shop-service assignments and pricing
        Route::prefix('shop-services')->name('shop-services.')->group(function () {
            Route::post('/', [ShopServiceController::class, 'store'])->name('store');
            Route::delete('{shopService}', [ShopServiceController::class, 'destroy'])->name('destroy');
        });
    });
});

require __DIR__ . '/auth.php';