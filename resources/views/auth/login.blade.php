<x-guest-layout>

    {{-- Login page that supports customer/owner access, shows links to alternate auth routes, and handles sign-in --}}

    <div class="auth-intro">
        <p class="auth-eyebrow">BubbleLink Access</p>
        <h1 class="auth-title">{{ $heading ?? 'Customer login' }}</h1>
        <p class="auth-copy">{{ $description ?? 'Sign in to continue.' }}</p>

        <div class="auth-link-row">
            <a class="auth-link" href="{{ route($alternateLoginRoute ?? 'admin.login') }}">
                {{ $alternateLoginLabel ?? 'Shop Owner login' }}
            </a>
            <span>|</span>
            <a class="auth-link" href="{{ route($registerRoute ?? 'customer.register') }}">
                {{ $registerLabel ?? 'Create account' }}
            </a>
        </div>

    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (request()->filled('demo_email'))
        <div class="auth-banner mb-4">
            Demo credentials loaded for {{ request('demo_email') }}.
        </div>
    @endif

    <form method="POST" action="{{ route($formActionRoute ?? 'login.store') }}" class="auth-form">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email', request('demo_email'))"
                required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                :value="old('password', request('demo_password'))"
                required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label>
                <input type="checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="auth-actions">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>