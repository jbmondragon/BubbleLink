@props(['status'])

{{--
    Auth Session Status Component

    Purpose:
    - Displays a session-based status message (e.g., success, info, or feedback).

    Usage:
    - Typically used for messages like:
        * "Password reset link sent"
        * "Login successful"
        * "Account created"
--}}

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-neutral-700']) }}>
        {{ $status }}
    </div>
@endif