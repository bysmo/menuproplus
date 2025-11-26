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
        }

        $dateTimeFormat = $date->year === $now->year
            ? $date->translatedFormat('j F \\à H:i')       // ex: 22 août à 03:08
            : $date->translatedFormat('j F Y \\à H:i');    // ex: 22 août 2025 à 03:08

        $time = $date->translatedFormat('H:i');           // ex: 03:08
    }
@endphp

@if($date)
    @if(!$isToday)
        <span class="{{ $color }} text-xs">{{ $dateTimeFormat }}</span>
    @else
        <span class="{{ $color }} text-xs">{{ $time }}</span>
    @endif
    <p class="text-[11px] text-gray-400">{{ $date->diffForHumans(short: true) }}</p>
@else
    -
@endif
