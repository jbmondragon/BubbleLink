<section class="profile-stack">
    <!-- Account deletion stays behind a confirmation modal because the action is permanent. -->
    <header>
        <h2 class="profile-section-title">
            {{ __('Delete Account') }}
        </h2>

        <p class="profile-section-copy">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data="modalTrigger(@js('confirm-user-deletion'))"
        x-on:click.prevent="open()"
    >{{ __('Delete Account') }}</x-danger-button>

    <!-- Modal collects the current password before the destructive delete request is submitted. -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="profile-modal-body">
            @csrf
            @method('delete')

            <h2 class="profile-modal-title">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="profile-modal-copy">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="profile-modal-field">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="profile-modal-input"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="profile-modal-actions">
                <x-secondary-button x-data="modalTrigger()" x-on:click="close()">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="profile-modal-danger">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
