<x-guest-layout>
    <!-- Explains that the account exists but email verification must finish before protected routes unlock. -->
    <div class="auth-note">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <!-- Confirms that another verification email has been requested successfully. -->
        <div class="auth-note auth-note--success">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <!-- Lets the user resend the verification link or abandon the session. -->
    <div class="auth-actions auth-actions--spread">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="auth-link auth-link--underline">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
