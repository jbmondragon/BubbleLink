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
            alternateLoginLabel: 'Admin login',
            registerRoute: 'customer.register',
            registerLabel: 'Create customer account'
        );
    }

    public function createAdmin(): View
    {
        return $this->renderLoginView(
            heading: 'Admin login',
            description: 'Sign in to manage your organization, services, staff, and orders.',
            formActionRoute: 'admin.login.store',
            alternateLoginRoute: 'customer.login',
            alternateLoginLabel: 'Customer login',
            registerRoute: 'admin.register',
            registerLabel: 'Create admin account'
        );
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->routeIs('admin.login.store') && ! $request->user()->memberships()->exists()) {
            return redirect()
                ->route('admin.start')
                ->with('warning', 'No organization is linked to this admin account yet. Start your admin setup to continue.');
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
