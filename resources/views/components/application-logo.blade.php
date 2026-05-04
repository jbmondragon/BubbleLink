@props([])
{{--
    Application Logo Component

    Description:
    - Renders the main SVG logo for the application

    Features:
    - Accepts dynamic HTML attributes via $attributes (e.g., class, style)
    - Uses a tightened viewBox so the symbol fills the rendered space better
    - Colors inherit from the parent via currentColor

    Structure:
    - Soft bubbles: Decorative circles that hint at the BubbleLink name
    - Chain mark: Two linked rounded forms representing connection
    - Core bubble: Central bubble that anchors the icon at small sizes
--}}

<svg viewBox="48 56 220 212" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    {{-- Soft bubble field around the symbol. --}}
    <g fill="currentColor" opacity="0.16">
        <circle cx="82" cy="94" r="28" />
        <circle cx="230" cy="88" r="22" />
        <circle cx="258" cy="156" r="14" />
        <circle cx="92" cy="232" r="18" />
        <circle cx="226" cy="234" r="26" />
    </g>

    {{-- Linked loops form the core BubbleLink symbol. --}}
    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="20">
        <rect x="72" y="112" width="122" height="86" rx="43" transform="rotate(-35 133 155)" />
        <rect x="122" y="118" width="122" height="86" rx="43" transform="rotate(35 183 161)" />
    </g>

    {{-- Center bubbles keep the mark readable even at small icon sizes. --}}
    <g fill="currentColor">
        <circle cx="158" cy="158" r="24" />
        <circle cx="132" cy="136" r="11" opacity="0.88" />
        <circle cx="192" cy="182" r="13" opacity="0.82" />
    </g>
</svg>