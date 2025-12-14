<li x-data="{ active: @entangle('active') }" x-init="if (active) { setTimeout(() => { $el.scrollIntoView({ behavior: 'smooth' }); }, 400); }">
    <a href="{{ $link }}" wire:navigate
        @class(['flex items-center p-2 text-base text-white/70 transition duration-75 rounded-lg pl-11 group hover:bg-white/10 hover:text-white', 'text-[#FBBF24] font-bold bg-white/5' => $active])
        style="color: {{ $active ? '#FBBF24' : 'rgba(255,255,255,0.7)' }}; {{ $active ? 'background-color: rgba(255,255,255,0.05);' : '' }}">{{ $name }}</a>
</li>
