<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function createCustomer(): View
    {
        return $this->renderLoginView(
            heading: 'Customer login',
            description: 'Sign in to browse shops, place orders, and track your laundry.',
            formActionRoute: 'customer.login.store',
            alternateLoginRoute: 'admin.login',
            alternateLoginLabel: 'Shop Owner login',
            registerRoute: 'customer.register',
            registerLabel: 'Create customer account'
        );
    }

    public function createAdmin(): View
    {
        return $this->renderLoginView(
            heading: 'Shop Owner login',
            description: 'Sign in to manage your organization, services, staff, and orders.',
            formActionRoute: 'admin.login.store',
            alternateLoginRoute: 'customer.login',
            alternateLoginLabel: 'Customer login',
            registerRoute: 'admin.register',
            registerLabel: 'Create shop owner account'
        );
    }

    public function createPlatformAdmin(): View
    {
        return $this->renderLoginView(
            heading: 'Platform Admin login',
            description: 'Sign in to review and approve shop owner registration requests.',
            formActionRoute: 'platform-admin.login.store',
            alternateLoginRoute: 'admin.login',
            alternateLoginLabel: 'Shop Owner login',
            registerRoute: 'customer.login',
            registerLabel: 'Customer login'
        );
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->routeIs('platform-admin.login.store')) {
            if (! $request->user()->is_platform_admin) {
                return $this->rejectAuthenticatedLogin($request, 'Please check again your login credentials.');
            }

            return redirect()->intended(route('platform-admin.owner-registrations.index', absolute: false));
        }

        if ($request->routeIs('admin.login.store')) {
            if ($request->user()->is_platform_admin) {
                return $this->rejectAuthenticatedLogin($request, 'Please check again your login credentials.');
            }

            if ($request->user()->memberships()->exists()) {
                return redirect()->intended(route('dashboard', absolute: false));
            }

            if ($request->user()->isApprovedShopOwnerRegistration()) {
                return redirect()
                    ->route('admin.start')
                    ->with('success', 'Shop owner account approved. Create your organization to get started.');
            }

            if ($request->user()->isPendingShopOwnerApproval()) {
                return $this->rejectAuthenticatedLogin($request, 'Your shop owner registration is still pending approval.');
            }

            if ($request->user()->isRejectedShopOwnerRegistration()) {
                return $this->rejectAuthenticatedLogin($request, 'Your shop owner registration was rejected. Please contact the platform admin.');
            }

            return $this->rejectAuthenticatedLogin($request, 'Please check again your login credentials.');
        }

        if ($request->user()->is_platform_admin) {
            return $this->rejectAuthenticatedLogin($request, 'Please check again your login credentials.');
        }

        if ($request->user()->memberships()->exists() || $request->user()->owner_registration_status !== null) {
            return $this->rejectAuthenticatedLogin($request, 'Please check again your login credentials.');
        }

        $redirectRoute = $request->user()->memberships()->exists()
            ? route('dashboard', absolute: false)
            : route('customer.shops.index', absolute: false);

        return redirect()->intended($redirectRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function rejectAuthenticatedLogin(LoginRequest $request, string $message): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()
            ->withErrors(['email' => $message])
            ->onlyInput('email');
    }

    private function renderLoginView(
        string $heading,
        string $description,
        string $formActionRoute,
        string $alternateLoginRoute,
        string $alternateLoginLabel,
        string $registerRoute,
        string $registerLabel,
    ): View {
        return view('auth.login', [
            'heading' => $heading,
            'description' => $description,
            'formActionRoute' => $formActionRoute,
            'alternateLoginRoute' => $alternateLoginRoute,
            'alternateLoginLabel' => $alternateLoginLabel,
            'registerRoute' => $registerRoute,
            'registerLabel' => $registerLabel,
        ]);
    }
}
