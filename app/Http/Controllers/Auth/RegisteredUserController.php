<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    // Show customer registration form
    public function createCustomer(): View
    {
        return $this->registerView(
            'Customer registration',
            'Create an account to place and track orders.',
            'customer.register.store',
            'customer.login',
            'Already have an account?',
            false
        );
    }

    // Show shop owner registration form
    public function createAdmin(): View
    {
        return $this->registerView(
            'Shop Owner registration',
            'Register your shop and wait for admin approval.',
            'admin.register.store',
            'admin.login',
            'Already registered?',
            true
        );
    }

    // Handle registration
    public function store(Request $request): RedirectResponse
    {
        $isOwner = $request->routeIs('admin.register.store');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'contact_number' => 'nullable|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($isOwner) {
            $rules += [
                'shop_name' => 'required|string|max:255',
                'shop_address' => 'required|string|max:255',
                'shop_contact_number' => 'nullable|string|max:255',
                'shop_description' => 'nullable|string|max:255',
            ];
        }

        $data = $request->validate($rules);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'contact_number' => $data['contact_number'] ?? null,
            'password' => Hash::make($data['password']),

            // Only for shop owners
            'owner_registration_status' => $isOwner ? 'pending' : null,
            'pending_shop_name' => $isOwner ? $data['shop_name'] ?? null : null,
            'pending_shop_address' => $isOwner ? $data['shop_address'] ?? null : null,
            'pending_shop_contact_number' => $isOwner ? $data['shop_contact_number'] ?? null : null,
            'pending_shop_description' => $isOwner ? $data['shop_description'] ?? null : null,
        ]);

        event(new Registered($user));

        return redirect()
            ->route($isOwner ? 'admin.login' : 'customer.login')
            ->with($isOwner ? 'success' : 'status', $isOwner
                ? 'Shop owner registration submitted. Wait for platform admin approval before logging in.'
                : 'Registration success, log-in using your log-in credentials.');
    }

    // Reusable register view
    private function registerView(
        string $heading,
        string $description,
        string $formActionRoute,
        string $loginRoute,
        string $loginLabel,
        bool $showShopFields
    ): View {
        return view('auth.register', compact(
            'heading',
            'description',
            'formActionRoute',
            'loginRoute',
            'loginLabel',
            'showShopFields'
        ));
    }
}
