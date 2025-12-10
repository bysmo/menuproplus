@props(['disabled' => false, 'error' => false])

<input 
    {{ $disabled ? 'disabled' : '' }} 
    {!! $attributes->merge(['class' => 'aladin-input' . ($error ? ' error' : '')]) !!}
>
