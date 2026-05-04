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

/**
 * Handles customer and shop-owner registration, including the owner approval
 * intake fields captured during sign-up.
 */
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function createCustomer(): View
    {

        /**
         * Build the shared registration view payload for the customer and shop-owner
         * registration screens.
         */
        return $this->renderRegisterView(
            heading: 'Customer registration',
            description: 'Create a customer account to place and track laundry orders.',
            formActionRoute: 'customer.register.store',
            loginRoute: 'customer.login',
            loginLabel: 'Already a customer?',
            alternateRegisterRoute: 'admin.register',
            alternateRegisterLabel: 'Need a shop owner account?',
            showShopFields: false,
        );
    }

    public function createAdmin(): View
    {
        return $this->renderRegisterView(
            heading: 'Shop Owner registration',
            description: 'Create a shop owner account, submit your first shop details, and wait for platform admin approval before managing your laundry business.',
            formActionRoute: 'admin.register.store',
            loginRoute: 'admin.login',
            loginLabel: 'Already registered as a shop owner?',
            alternateRegisterRoute: 'customer.register',
            alternateRegisterLabel: 'Need a customer account?',
            showShopFields: true,
        );
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $isShopOwnerRegistration = $request->routeIs('admin.register.store');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($isShopOwnerRegistration) {
            $rules['shop_name'] = ['required', 'string', 'max:255'];
            $rules['shop_address'] = ['required', 'string', 'max:255'];
            $rules['shop_contact_number'] = ['nullable', 'string', 'max:255'];
            $rules['shop_description'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'owner_registration_status' => $isShopOwnerRegistration ? 'pending' : null,
            'pending_shop_name' => $isShopOwnerRegistration ? $validated['shop_name'] : null,
            'pending_shop_address' => $isShopOwnerRegistration ? $validated['shop_address'] : null,
            'pending_shop_contact_number' => $isShopOwnerRegistration ? ($validated['shop_contact_number'] ?? null) : null,
            'pending_shop_description' => $isShopOwnerRegistration ? ($validated['shop_description'] ?? null) : null,
        ]);

        event(new Registered($user));

        if ($isShopOwnerRegistration) {
            return redirect()
                ->route('admin.login')
                ->with('success', 'Shop owner registration submitted. Wait for platform admin approval before logging in.');
        }

        Auth::login($user);

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
        bool $showShopFields,
    ): View {
        return view('auth.register', [
            'heading' => $heading,
            'description' => $description,
            'formActionRoute' => $formActionRoute,
            'loginRoute' => $loginRoute,
            'loginLabel' => $loginLabel,
            'alternateRegisterRoute' => $alternateRegisterRoute,
            'alternateRegisterLabel' => $alternateRegisterLabel,
            'showShopFields' => $showShopFields,
        ]);
    }
}
