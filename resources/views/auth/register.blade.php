<x-guest-layout>

<!-- ******************************************************************************************************** -->
    {{-- Registration page for creating a user account and owner account --}}
<!-- ******************************************************************************************************** -->

    <div class="auth-intro">
        <p class="auth-eyebrow">BubbleLink Access</p>
        <h1 class="auth-title">{{ $heading ?? 'Register' }}</h1>
        <p class="auth-copy">
            {{ ($showShopFields ?? false) ? 'After registration, wait for the approval of the admin.' : ($description ?? 'Create your account.') }}
        </p>

        <div class="auth-link-row">
            <a class="auth-link" href="{{ route($loginRoute ?? 'login') }}">Login</a>
        </div>

        @if ($showShopFields ?? false)
            <div class="flex justify-center pt-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center px-4 py-2 rounded-full bg-neutral-950 text-white text-xs font-semibold uppercase tracking-[0.2em] hover:bg-neutral-800 focus:ring-2 focus:ring-neutral-400 transition"
                >
                    Dashboard
                </a>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route($formActionRoute ?? 'register.store') }}" class="auth-form">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" name="name" :value="old('name')" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="contact_number" :value="__('Contact')" />
            <x-text-input id="contact_number" class="block mt-1 w-full" name="contact_number" :value="old('contact_number')" />
        </div>
<!-- ******************************************************************************************************** -->
        {{-- Additional information needed for owner account --}}
<!-- ******************************************************************************************************** -->

        @if ($showShopFields ?? false)
            <div class="auth-panel">
                <h3 class="auth-panel-title">First Shop Details</h3>

                <x-input-label for="shop_name" :value="__('Shop Name')" />
                <x-text-input id="shop_name" class="block mt-1 w-full" name="shop_name" :value="old('shop_name')" required />
                <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />

                <x-input-label for="shop_address" :value="__('Shop Address')" />
                <x-text-input id="shop_address" class="block mt-1 w-full" name="shop_address" :value="old('shop_address')" required />
                <x-input-error :messages="$errors->get('shop_address')" class="mt-2" />
            </div>
        @endif

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
        </div>

        <div class="auth-actions">
            <x-primary-button>
                Register
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>