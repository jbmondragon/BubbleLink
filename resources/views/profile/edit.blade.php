<x-app-layout>
    <x-slot name="header">
        <!-- Header keeps the account area visually separate from shop-management screens. -->
        <div class="profile-page-header">
            <p class="profile-eyebrow">Account</p>
            <h2 class="profile-page-title">{{ __('Profile') }}</h2>
        </div>
    </x-slot>

    <div class="profile-page">
        <div class="profile-page-container">
            <!-- The profile page groups account info, password, and deletion into separate panels. -->
            <div class="profile-page-header">
                <p class="profile-eyebrow">Account Settings</p>
                <p class="profile-page-copy">Update your profile details, password, and account access from one place.</p>
            </div>

            <div class="profile-stack">
                <div class="profile-panel">
                    <div class="profile-panel-shell">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

                <div class="profile-panel">
                    <div class="profile-panel-shell">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

                <div class="profile-panel">
                    <div class="profile-panel-shell">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            </div>
        </div>
    </div>
</x-app-layout>
