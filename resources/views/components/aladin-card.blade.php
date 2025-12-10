@props(['glass' => true])

<div {{ $attributes->merge(['class' => $glass ? 'aladin-card' : 'aladin-card-dark']) }}>
    {{ $slot }}
</div>
