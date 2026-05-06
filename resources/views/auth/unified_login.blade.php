<x-guest-layout>

    {{-- Unified login page that supports all user types with register buttons --}}

    <div class="auth-intro">
        <p class="auth-eyebrow">BubbleLink Access</p>
        <h1 class="auth-title">Login to BubbleLink</h1>
        <p class="auth-copy">Sign in to access your account as a customer, shop owner, or platform admin.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (request()->filled('demo_email'))
        <div class="auth-banner mb-4">
            Demo credentials loaded for {{ request('demo_email') }}.
        </div>
    @endif

    <form method="POST" action="{{ route('unified.login.store') }}" class="auth-form">
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

        <!-- Register Buttons -->
        <div class="auth-register-links mt-6">
            <p class="auth-register-label text-center text-sm text-neutral-600 mb-3">Don't have an account?</p>
            <div class="space-y-2">
                <a href="{{ route('customer.register') }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition">
                    Create customer account
                </a>
                <a href="{{ route('admin.register') }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100 transition">
                    Create shop owner account
                </a>
            </div>
        </div>
    </form>

</x-guest-layout>
