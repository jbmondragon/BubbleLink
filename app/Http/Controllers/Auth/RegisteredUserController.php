<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function createCustomer(): View
    {
        return $this->renderRegisterView(
            heading: 'Customer registration',
            description: 'Create a customer account to place and track laundry orders.',
            formActionRoute: 'customer.register.store',
            loginRoute: 'customer.login',
            loginLabel: 'Already a customer?',
            alternateRegisterRoute: 'admin.register',
            alternateRegisterLabel: 'Need an admin account?'
        );
    }

    public function createAdmin(): View
    {
        return $this->renderRegisterView(
            heading: 'Admin registration',
            description: 'Create an admin account to start managing your laundry business.',
            formActionRoute: 'admin.register.store',
            loginRoute: 'admin.login',
            loginLabel: 'Already registered as admin?',
            alternateRegisterRoute: 'customer.register',
            alternateRegisterLabel: 'Need a customer account?'
        );
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($request->routeIs('admin.register.store')) {
            return redirect()
                ->route('admin.start')
                ->with('success', 'Admin account created. Set up your organization to start managing your business.');
        }

        return redirect(route('customer.shops.index', absolute: false));
    }

    private function renderRegisterView(
        string $heading,
        string $description,
        string $formActionRoute,
        string $loginRoute,
        string $loginLabel,
        string $alternateRegisterRoute,
        string $alternateRegisterLabel,
    ): View {
        return view('auth.register', [
            'heading' => $heading,
            'description' => $description,
            'formActionRoute' => $formActionRoute,
            'loginRoute' => $loginRoute,
            'loginLabel' => $loginLabel,
            'alternateRegisterRoute' => $alternateRegisterRoute,
            'alternateRegisterLabel' => $alternateRegisterLabel,
        ]);
    }
}
