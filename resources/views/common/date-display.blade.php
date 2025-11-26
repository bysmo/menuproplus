@php
    use Carbon\Carbon;

    Carbon::setLocale('fr');

    $now = Carbon::now(timezone());
    $color = 'text-gray-500';
    $isToday = false;

    $date = isset($date) && $date ? Carbon::parse($date)->locale('fr')->setTimezone(timezone()) : null;

    if ($date) {
        if ($date->isToday()) {
            $color = 'text-green-600';
            $isToday = true;
        } elseif ($date->isYesterday()) {
            $color = 'text-blue-800';
        } elseif ($date->between($now->copy()->subDays(7), $now)) {
            $color = 'text-yellow-600';
        } elseif ($date->between($now->copy()->subDays(30), $now)) {
            $color = 'text-orange-500';
        }

        $dateFormat = $date->year === $now->year
            ? $date->translatedFormat('j F')        // ex: 22 août
            : $date->translatedFormat('j F Y');     // ex: 22 août 2025

        $time = $date->translatedFormat('H:i');     // ex: 03:08
    }
@endphp

@if($date)
    @if(!$isToday)
        <span class="{{ $color }} text-xs">{{ $dateFormat }}</span>
    @else
        <span class="{{ $color }} text-xs">{{ $time }}</span>
    @endif
    <p class="text-[11px] text-gray-400">{{ $date->diffForHumans(short: true) }}</p>
@else
    -
@endif
