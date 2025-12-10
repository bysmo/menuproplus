@props(['size' => 'md'])

@php
    $sizes = [
        'sm' => 'text-lg',
        'md' => 'text-2xl',
        'lg' => 'text-4xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'aladin-logo ' . $sizeClass]) }}>
    <div class="aladin-logo-icon">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.86-.96-7-5.29-7-10V8.3l7-3.11 7 3.11V10c0 4.71-3.14 9.04-7 10z"/>
            <path d="M12 6L7 8.5v5.5c0 2.89 2 5.62 5 6.5 3-.88 5-3.61 5-6.5V8.5L12 6zm0 11c-2.21-.66-4-2.93-4-5.5V10l4-1.78L16 10v1.5c0 2.57-1.79 4.84-4 5.5z"/>
        </svg>
    </div>
    <span class="aladin-logo-text font-bold">
        Aladin Technologies
    </span>
</div>
