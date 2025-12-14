<li>
    <a href="{{ $link }}" wire:navigate
        @class(['flex items-center p-2 text-base text-white/80 rounded-xl hover:bg-white/10 hover:text-white group', 'text-[#FBBF24] font-bold bg-white/10' => $active])
        style="color: {{ $active ? '#FBBF24' : 'rgba(255,255,255,0.8)' }}; {{ $active ? 'background-color: rgba(255,255,255,0.1);' : '' }}">
        {!! $customIcon ?? $icon !!}
        <span class="ltr:ml-3 rtl:mr-3" sidebar-toggle-item>{{ $name }}</span>
    </a>
</li>
