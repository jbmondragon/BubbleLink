<?php

/**
 * Authentication Routes
 *
 * Defines all authentication-related endpoints for:
 * - Customers
 * - Shop Owners (Admins)
 * - Platform Admins
 *
 * Features:
 * - Reusable helper for GET/POST auth route pairs
 * - Role-based entry points (customer, admin, platform admin)
 * - Middleware separation (guest vs authenticated users)
 */

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| VII. AUTHENTICATION ENDPOINTS
|--------------------------------------------------------------------------
| GET  /register               -> RegisteredUserController@createCustomer
| POST /register               -> RegisteredUserController@store
| GET  /customer/register      -> RegisteredUserController@createCustomer
| POST /customer/register      -> RegisteredUserController@store
| GET  /shop-owner/register    -> RegisteredUserController@createAdmin
| POST /shop-owner/register    -> RegisteredUserController@store
| GET  /login                  -> AuthenticatedSessionController@createCustomer
| POST /login                  -> AuthenticatedSessionController@store
| GET  /customer/login         -> AuthenticatedSessionController@createCustomer
| POST /customer/login         -> AuthenticatedSessionController@store
| GET  /shop-owner/login       -> AuthenticatedSessionController@createAdmin
| POST /shop-owner/login       -> AuthenticatedSessionController@store
| GET  /platform-admin/login   -> AuthenticatedSessionController@createPlatformAdmin
| POST /platform-admin/login   -> AuthenticatedSessionController@store
*/

// Guest-only registration and login route pair helper.
$registerGuestAuthRoute = function (string $uri, array|string|callable|null $getAction, string $name): void {
    Route::get($uri, $getAction)->name($name);
    Route::post($uri, [str_contains($name, 'register') ? RegisteredUserController::class : AuthenticatedSessionController::class, 'store'])
        ->name("{$name}.store");
};

// Public authentication entry points.
Route::middleware('guest')->group(function () use ($registerGuestAuthRoute) {
    $registerGuestAuthRoute('register', [RegisteredUserController::class, 'createCustomer'], 'register');
    $registerGuestAuthRoute('customer/register', [RegisteredUserController::class, 'createCustomer'], 'customer.register');
    $registerGuestAuthRoute('shop-owner/register', [RegisteredUserController::class, 'createAdmin'], 'admin.register');

    $registerGuestAuthRoute('login', [AuthenticatedSessionController::class, 'createCustomer'], 'login');
    $registerGuestAuthRoute('customer/login', [AuthenticatedSessionController::class, 'createCustomer'], 'customer.login');
    $registerGuestAuthRoute('shop-owner/login', [AuthenticatedSessionController::class, 'createAdmin'], 'admin.login');
    $registerGuestAuthRoute('platform-admin/login', [AuthenticatedSessionController::class, 'createPlatformAdmin'], 'platform-admin.login');
});

/*
|--------------------------------------------------------------------------
| VIII. PASSWORD & SESSION ENDPOINTS
|--------------------------------------------------------------------------
| GET  /confirm-password -> ConfirmablePasswordController@show
| POST /confirm-password -> ConfirmablePasswordController@store
| PUT  /password         -> PasswordController@update
| POST /logout           -> AuthenticatedSessionController@destroy
*/
Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
