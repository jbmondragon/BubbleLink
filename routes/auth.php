<?php

/**
 * Authentication & Authorization Routes
 *
 * This file defines all authentication-related routes for the application,
 * including registration, login, email verification, and logout.
 *
 * The routes are grouped by middleware:
 *
 * - "guest" middleware:
 *   Handles unauthenticated user actions such as:
 *   - Customer/Admin/Platform Admin registration
 *   - Login for different user roles
 * - "auth" middleware:
 *   Handles authenticated user actions such as:
 *   - Email verification flow
 *   - Password confirmation and update
 *   - Logout functionality
 *
 * Multi-role support is implemented (Customer, Shop Owner/Admin, Platform Admin),
 * each with dedicated login and registration endpoints to support role-based access control.
 *
 * Note:
 * - Ensure route names remain consistent when used in frontend or API integrations.
 * - Throttle middleware is applied to sensitive endpoints such as verification for security.
 * - Signed routes are used for email verification integrity.
 *
 * This structure is designed for scalability and separation of concerns in production environments.
 */

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Registration and login routes for guests (unauthenticated users)
$registerGuestAuthRoute = function (string $uri, array|string|callable|null $getAction, string $name): void {
    Route::get($uri, $getAction)->name($name);
    Route::post($uri, [str_contains($name, 'register') ? RegisteredUserController::class : AuthenticatedSessionController::class, 'store'])
        ->name("{$name}.store");
};

Route::middleware('guest')->group(function () use ($registerGuestAuthRoute) {
    $registerGuestAuthRoute('register', [RegisteredUserController::class, 'createCustomer'], 'register');
    $registerGuestAuthRoute('customer/register', [RegisteredUserController::class, 'createCustomer'], 'customer.register');
    $registerGuestAuthRoute('shop-owner/register', [RegisteredUserController::class, 'createAdmin'], 'admin.register');

    $registerGuestAuthRoute('login', [AuthenticatedSessionController::class, 'createCustomer'], 'login');
    $registerGuestAuthRoute('customer/login', [AuthenticatedSessionController::class, 'createCustomer'], 'customer.login');
    $registerGuestAuthRoute('shop-owner/login', [AuthenticatedSessionController::class, 'createAdmin'], 'admin.login');
    $registerGuestAuthRoute('platform-admin/login', [AuthenticatedSessionController::class, 'createPlatformAdmin'], 'platform-admin.login');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
