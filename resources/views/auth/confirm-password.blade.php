<x-guest-layout>
<!--**************************************************************************************************-->
    {{-- Password confirmation screen that asks the user to 
          re-enter their password before accessing a secure area --}}
<!--**************************************************************************************************-->
    
    <div class="auth-note">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
        @csrf


        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-actions">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>