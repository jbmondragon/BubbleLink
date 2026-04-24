<x-guest-layout>
    <div class="mb-6 space-y-2 text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-600">BubbleLink Access</p>
        <h1 class="text-2xl font-semibold text-slate-900">{{ $heading ?? 'Customer login' }}</h1>
        <p class="text-sm text-slate-600">{{ $description ?? 'Sign in to continue.' }}</p>
        <div class="flex items-center justify-center gap-3 text-sm">
            <a class="text-emerald-700 hover:text-emerald-900" href="{{ route($alternateLoginRoute ?? 'admin.login') }}">{{ $alternateLoginLabel ?? 'Shop Owner login' }}</a>
            <span class="text-slate-300">|</span>
            <a class="text-emerald-700 hover:text-emerald-900" href="{{ route($registerRoute ?? 'customer.register') }}">{{ $registerLabel ?? 'Create account' }}</a>
        </div>

        @if (request()->filled('demo_email'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                Demo credentials loaded for <span class="font-semibold">{{ request('demo_email') }}</span>.
            </div>
        @endif
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route($formActionRoute ?? 'login.store') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', request('demo_email'))" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            :value="request('demo_password')"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
