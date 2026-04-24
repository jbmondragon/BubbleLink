<?php

use App\Http\Controllers\AdminOnboardingController;
use App\Http\Controllers\AdminSetupWizardController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerShopController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PlatformAdminOwnerApprovalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShopServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CustomerShopController::class, 'index'])->name('customer.shops.home');
Route::get('/shops', [CustomerShopController::class, 'index'])->name('customer.shops.index');
Route::get('/shops/{shop}/details', [CustomerShopController::class, 'show'])->name('customer.shops.show');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified', 'area:dashboard'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::middleware('area:platform-admin')->group(function () {
        Route::get('/platform-admin/owner-registrations', [PlatformAdminOwnerApprovalController::class, 'index'])->name('platform-admin.owner-registrations.index');
        Route::patch('/platform-admin/owner-registrations/{user}/approve', [PlatformAdminOwnerApprovalController::class, 'approve'])->name('platform-admin.owner-registrations.approve');
        Route::patch('/platform-admin/owner-registrations/{user}/reject', [PlatformAdminOwnerApprovalController::class, 'reject'])->name('platform-admin.owner-registrations.reject');
    });

    Route::middleware('area:organization-creator')->group(function () {
        Route::get('/admin/start', [AdminOnboardingController::class, 'show'])->name('admin.start');
        Route::get('/admin/setup', [AdminSetupWizardController::class, 'show'])->name('admin.setup');
        Route::post('/admin/setup/shop', [AdminSetupWizardController::class, 'storeShop'])->name('admin.setup.shop');
        Route::post('/admin/setup/service', [AdminSetupWizardController::class, 'storeService'])->name('admin.setup.service');
        Route::post('/admin/setup/member', [AdminSetupWizardController::class, 'storeMember'])->name('admin.setup.member');
        Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
        Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    });

    Route::middleware('area:customer')->group(function () {
        Route::get('/shops/{shop}/order', [CustomerOrderController::class, 'create'])->name('customer.orders.create');
        Route::post('/shops/{shop}/order', [CustomerOrderController::class, 'store'])->name('customer.orders.store');
        Route::get('/my-orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
        Route::get('/my-orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
        Route::patch('/my-orders/{order}/rating', [CustomerOrderController::class, 'rate'])->name('customer.orders.rate');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('area:business')->group(function () {
        Route::post('/organizations/switch', [OrganizationController::class, 'switch'])->name('organizations.switch');

        Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
        Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
        Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');
        Route::get('/shops/{shop}/edit', [ShopController::class, 'edit'])->name('shops.edit');
        Route::patch('/shops/{shop}', [ShopController::class, 'update'])->name('shops.update');
        Route::delete('/shops/{shop}', [ShopController::class, 'destroy'])->name('shops.destroy');

        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::patch('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

        Route::get('/memberships', [MembershipController::class, 'index'])->name('memberships.index');
        Route::post('/memberships', [MembershipController::class, 'store'])->name('memberships.store');
        Route::patch('/memberships/{membership}', [MembershipController::class, 'update'])->name('memberships.update');
        Route::delete('/memberships/{membership}', [MembershipController::class, 'destroy'])->name('memberships.destroy');

        Route::post('/shop-services', [ShopServiceController::class, 'store'])->name('shop-services.store');
        Route::delete('/shop-services/{shopService}', [ShopServiceController::class, 'destroy'])->name('shop-services.destroy');
    });
});

require __DIR__.'/auth.php';
