@props(['align' => 'right'])

@php
$alignmentClasses = match ($align) {
    'left' => 'left-0',
    'right' => 'right-0',
    default => 'right-0',
};
@endphp

<div class="relative" x-data="{ open: false }">
    {{-- Trigger --}}
    <div @click="open = !open" @click.away="open = false">
        {{ $trigger }}
    </div>

    {{-- Content --}}
    <div x-cloak
         x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $alignmentClasses }} w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
        {{ $content }}
    </div>
</div>
