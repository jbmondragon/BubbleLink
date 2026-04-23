<x-guest-layout>
    <div class="mb-6 space-y-2 text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-600">BubbleLink Access</p>
        <h1 class="text-2xl font-semibold text-slate-900">{{ $heading ?? 'Customer registration' }}</h1>
        <p class="text-sm text-slate-600">{{ $description ?? 'Create your account to continue.' }}</p>
        <div class="flex items-center justify-center gap-3 text-sm">
            <a class="text-emerald-700 hover:text-emerald-900" href="{{ route($loginRoute ?? 'login') }}">{{ $loginLabel ?? 'Already registered?' }}</a>
            <span class="text-slate-300">|</span>
            <a class="text-emerald-700 hover:text-emerald-900" href="{{ route($alternateRegisterRoute ?? 'admin.register') }}">{{ $alternateRegisterLabel ?? 'Need an admin account?' }}</a>
        </div>
    </div>

    <form method="POST" action="{{ route($formActionRoute ?? 'register.store') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contact Number -->
        <div class="mt-4">
            <x-input-label for="contact_number" :value="__('Contact Number')" />
            <x-text-input id="contact_number" class="block mt-1 w-full" type="text" name="contact_number" :value="old('contact_number')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route($loginRoute ?? 'login') }}">
                {{ $loginLabel ?? __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
