<x-guest-layout>

<!-- ******************************************************************************************************** -->
    {{-- Registration page for creating a user account and owner account --}}
<!-- ******************************************************************************************************** -->

    <div class="auth-intro">
        <p>BubbleLink Access</p>
        <h1>{{ $heading ?? 'Register' }}</h1>
        <p>{{ $description ?? 'Create your account.' }}</p>

        <div>
            <a href="{{ route($loginRoute ?? 'login') }}">Login</a> |
            <a href="{{ route($alternateRegisterRoute ?? 'admin.register') }}">Owner register</a>
        </div>
    </div>

    <form method="POST" action="{{ route($formActionRoute ?? 'register.store') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" :value="old('name')" required />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="contact_number" :value="__('Contact')" />
            <x-text-input id="contact_number" name="contact_number" :value="old('contact_number')" />
        </div>
<!-- ******************************************************************************************************** -->
        {{-- Additional information needed for owner account --}}
<!-- ******************************************************************************************************** -->

        @if ($showShopFields ?? false)
            <div>
                <h3>First Shop Details</h3>

                <x-input-label for="shop_name" :value="__('Shop Name')" />
                <x-text-input id="shop_name" name="shop_name" :value="old('shop_name')" required />

                <x-input-label for="shop_address" :value="__('Shop Address')" />
                <x-text-input id="shop_address" name="shop_address" :value="old('shop_address')" required />
            </div>
        @endif

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required />
        </div>

        <button type="submit">Register</button>
    </form>

</x-guest-layout>