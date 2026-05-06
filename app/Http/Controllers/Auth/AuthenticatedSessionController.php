<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Implements the login portion of SID1 and the access-gating flow for SID6.
 *
 * Responsibilities:
 * - SID1: Authenticate customers and shop owners into the correct area
 * - SID6: Block unapproved owner access until platform admin review is complete
 * - Route platform admins to registration approval management
 */
class AuthenticatedSessionController extends Controller
{
    // ===== UNIFIED LOGIN VIEW =====

    public function createUnified(): View
    {
        return view('auth.unified_login');
    }

    // ===== LOGIN VIEWS =====

    public function createCustomer(): View
    {
        return $this->loginView(
            'Customer login',
            'Sign in to browse shops, place orders, and track your laundry.',
            'customer.login.store',
            'admin.login',
            'Shop Owner login',
            'customer.register',
            'Create customer account'
        );
    }

    public function createAdmin(): View
    {
        return $this->loginView(
            'Shop Owner login',
            'Sign in to manage your shops, services, and orders.',
            'admin.login.store',
            'customer.login',
            'Customer login',
            'admin.register',
            'Create shop owner account'
        );
    }

    public function createPlatformAdmin(): View
    {
        return $this->loginView(
            'Platform Admin login',
            'Sign in to review and approve shop owner registration requests.',
            'platform-admin.login.store',
            'admin.login',
            'Shop Owner login',
            'customer.login',
            'Customer login'
        );
    }

    // ===== LOGIN LOGIC =====

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // Unified Login Logic - automatically detect user role
        if ($request->routeIs('unified.login.store')) {
            // Platform Admin Login
            if ($user->is_platform_admin) {
                return redirect()->intended(route('platform-admin.owner-registrations.index', absolute: false));
            }

            // Shop Owner Login
            if ($user->shops()->exists()) {
                return redirect()->intended(route('dashboard', absolute: false));
            }

            if ($user->isApprovedShopOwnerRegistration()) {
                return redirect()
                    ->route('dashboard')
                    ->with('success', 'Shop owner account approved. Finish your first shop profile to get started.');
            }

            if ($user->isPendingShopOwnerApproval()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Your shop owner registration is still pending approval.'])->onlyInput('email');
            }

            if ($user->isRejectedShopOwnerRegistration()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Your shop owner registration was rejected. Please contact the platform admin.'])->onlyInput('email');
            }

            if ($user->owner_registration_status) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Please check again your login credentials.'])->onlyInput('email');
            }

            // Customer Login (default)
        if ($user->is_platform_admin || $user->shops()->exists() || $user->owner_registration_status) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors(['email' => 'Please check again your login credentials.'])->onlyInput('email');
        }

        return redirect()->intended(route('customer.shops.index', absolute: false));
        }

        // Platform Admin Login
        if ($request->routeIs('platform-admin.login.store')) {
            return $user->is_platform_admin
                ? redirect()->intended(route('platform-admin.owner-registrations.index', absolute: false))
                : $this->reject($request, 'Please check again your login credentials.');
        }

        // Shop Owner Login
        if ($request->routeIs('admin.login.store')) {
            if ($user->is_platform_admin) {
                return $this->reject($request, 'Please check again your login credentials.');
            }

            if ($user->shops()->exists()) {
                return redirect()->intended(route('dashboard', absolute: false));
            }

            if ($user->isApprovedShopOwnerRegistration()) {
                return redirect()
                    ->route('dashboard')
                    ->with('success', 'Shop owner account approved. Finish your first shop profile to get started.');
            }

            if ($user->isPendingShopOwnerApproval()) {
                return $this->reject($request, 'Your shop owner registration is still pending approval.');
            }

            if ($user->isRejectedShopOwnerRegistration()) {
                return $this->reject($request, 'Your shop owner registration was rejected. Please contact the platform admin.');
            }

            return $this->reject($request, 'Please check again your login credentials.');
        }

        // Customer Login
        if ($user->is_platform_admin || $user->shops()->exists() || $user->owner_registration_status) {
            return $this->reject($request, 'Please check again your login credentials.');
        }

        return redirect()->intended(route('customer.shops.index', absolute: false));
    }

    // ===== LOGOUT =====

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ===== HELPERS =====

    private function reject(LoginRequest $request, string $message): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors(['email' => $message])->onlyInput('email');
    }

    private function loginView(
        string $heading,
        string $description,
        string $formActionRoute,
        string $alternateLoginRoute,
        string $alternateLoginLabel,
        string $registerRoute,
        string $registerLabel
    ): View {
        return view('auth.login', compact(
            'heading',
            'description',
            'formActionRoute',
            'alternateLoginRoute',
            'alternateLoginLabel',
            'registerRoute',
            'registerLabel'
        ));
    }
}
