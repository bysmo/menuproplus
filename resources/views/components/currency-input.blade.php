@props([
    'placeholder' => '0.00',
])

@php
    $symbol = restaurant()->currency->currency_symbol;
    $position = restaurant()->currency->currency_position ?? 'left';
    
    $isLeft = in_array($position, ['left', 'left_with_space']);
    $hasSpace = in_array($position, ['left_with_space', 'right_with_space']);
    
    // Calculate padding based on symbol length to prevent overlap
    $symbolLength = mb_strlen($symbol);
    if ($isLeft) {
        // Much more padding for longer symbols like "F CFA"
        if ($symbolLength > 4) {
            $paddingClass = 'pl-24'; // For "F CFA" and similar long symbols (96px)
        } elseif ($symbolLength > 2) {
            $paddingClass = 'pl-16'; // For "USD", "EUR" (64px)
        } else {
            $paddingClass = 'pl-12'; // For "$", "€" (48px)
        }
    } else {
        if ($symbolLength > 4) {
            $paddingClass = 'pr-24';
        } elseif ($symbolLength > 2) {
            $paddingClass = 'pr-16';
        } else {
            $paddingClass = 'pr-12';
        }
    }
    
    // Extract wrapper classes from attributes
    $wrapperClass = $attributes->get('class', '');
    $inputAttributes = $attributes->except('class');
@endphp

<div class="relative {{ $wrapperClass }}">
    @if($isLeft)
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <span class="text-gray-500 text-xs font-medium whitespace-nowrap">{{ $symbol }}</span>
        </div>
    @endif
    
    <input {{ $inputAttributes->merge(['class' => "block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white {$paddingClass} text-gray-900 placeholder:text-gray-400 focus:ring-skin-base focus:border-skin-base"]) }} 
           type="number" 
           step="0.01" 
           min="0"
           placeholder="{{ $placeholder }}">
    
    @if(!$isLeft)
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <span class="text-gray-500 text-xs font-medium whitespace-nowrap">{{ $symbol }}</span>
        </div>
    @endif
</div>
