{{--
    Link Item Component

    Purpose:
    - Renders a full-width clickable link element.
    - Commonly used in dropdowns, menus, and navigation lists.

    Behavior:
    - Merges additional HTML attributes via $attributes (e.g., href, class, target).
    - Expands to full width for easy click/tap interaction.
    - Uses hover and focus states for clear interaction feedback.
--}}

<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-sm text-neutral-950 hover:bg-neutral-100 focus:outline-none focus:bg-neutral-100 transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</a>