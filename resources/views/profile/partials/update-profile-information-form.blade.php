<section>
    <!-- Profile information form updates the user's basic identity and email address. -->
    <header>
        <h2 class="profile-section-title">
            {{ __('Profile Information') }}
        </h2>

        <p class="profile-section-copy">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <!-- Primary account form persists name and email changes for the signed-in user. -->
    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="profile-form-actions">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="flashMessage()"
                    x-show="show"
                    x-transition
                    class="profile-status-copy"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
