@props(['align' => 'right', 'width' => 'w-48', 'contentClasses' => 'py-1 bg-white'])

{{-- Dropdown menu with toggle, positioning, and basic styling --}}
<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div x-show="open"
        x-transition
        class="absolute mt-2 z-50 {{ $width }} {{ $align === 'left' ? 'start-0' : 'end-0' }} rounded-md shadow-lg"
        style="display: none;"
        @click="open = false">
        
        <div class="rounded-md ring-1 ring-black/5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>