<x-guest-layout>
<!--**************************************************************************************************-->
    {{-- Password reset request page that sends a reset link to the user's email --}}
<!--**************************************************************************************************-->

    <div class="auth-note">
        {{ __('Forgot your password? No problem. Enter your email and we will send you a reset link.') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-actions">
            <x-primary-button>
                {{ __('Email Reset Link') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>