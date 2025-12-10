@props(['variant' => 'dots'])

<div {{ $attributes->merge(['class' => 'absolute inset-0 -z-10']) }}>
    @if($variant === 'dots')
        <div class="aladin-pattern-dots absolute inset-0"></div>
    @elseif($variant === 'gradient')
        <div class="aladin-pattern-bg absolute inset-0"></div>
    @else
        <!-- Geometric patterns -->
        <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="aladin-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                    <circle cx="20" cy="20" r="2" fill="#1E40AF" opacity="0.1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#aladin-pattern)"/>
        </svg>
    @endif
</div>
