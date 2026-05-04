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

    <!-- Secondary form exists only to trigger a fresh verification email when needed. -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

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

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="profile-status-copy">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="profile-link-button">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="profile-status-copy profile-status-copy--success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
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
