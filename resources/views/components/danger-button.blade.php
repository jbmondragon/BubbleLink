{{--
    Primary Button Component

    Purpose:
    - Renders a reusable primary action button (commonly used for form submission).

    Behavior:
    - Defaults to type="submit" for form usage.
    - Merges additional attributes via $attributes (e.g., class, id, disabled).
    - Allows full customization while preserving base styling.

--}}

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center px-4 py-2 bg-neutral-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-800 active:bg-black focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>