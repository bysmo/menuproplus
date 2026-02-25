<div class="grid lg:grid-cols-3">
    <!-- Sidebar -->
    <div class="bg-gray-100 lg:h-screen p-4 sm:flex items-center dark:bg-gray-800 dark:border-gray-700">
        <section class="py-8 md:py-16 px-6">
            <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
                <div class="mx-auto max-w-3xl space-y-6 sm:space-y-8">
                    <div class="mb-4">
                        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('app.hello') {{ user()->name }}!</h1>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Configuration Rapide</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Sélectionnez les éléments à générer pour lancer votre activité en quelques secondes.</p>

                    <ol class="relative border-s border-gray-200 dark:border-gray-700 space-y-8">
                        @foreach([
                            1 => ['title' => 'Zones et Tables', 'desc' => 'Créez vos espaces de consommation'],
                            2 => ['title' => 'Menus', 'desc' => 'Sélectionnez vos cartes principales'],
                            3 => ['title' => 'Catégories', 'desc' => 'Structurez votre offre'],
                            4 => ['title' => 'Articles de Menu', 'desc' => 'Ajoutez vos plats et boissons'],
                            5 => ['title' => 'Moyens de Paiement', 'desc' => 'Configurez vos encaissements']
                        ] as $stepNb => $stepInfo)
                        <li class="ms-6">
                            <span class="absolute -start-2.5 flex h-5 w-5 items-center justify-center rounded-full ring-8 ring-white dark:ring-gray-900 {{ $currentStep >= $stepNb ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-200 dark:bg-gray-700' }}">
                                <span class="text-xs font-bold {{ $currentStep >= $stepNb ? 'text-white' : 'text-gray-500' }}">{{ $stepNb }}</span>
                            </span>
                            <h3 class="mb-1.5 text-base font-semibold leading-none {{ $currentStep == $stepNb ? 'text-blue-600 dark:text-blue-500' : 'text-gray-900 dark:text-white' }}">{{ $stepInfo['title'] }}</h3>
                            <p class="text-sm font-normal text-gray-500 dark:text-gray-400 tracking-wide">{{ $stepInfo['desc'] }}</p>
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>
    </div>

    <!-- Content Area -->
    <div class="col-span-2 flex flex-col w-full onboarding-steps bg-white dark:bg-gray-900 h-screen">
        <form wire:submit.prevent="generateSelectedData" class="flex flex-col h-full overflow-hidden">
            <!-- Scrollable content -->
            <div class="flex-1 overflow-y-auto px-8 lg:px-16 py-10 space-y-6">
                @if($currentStep === 1)
                    <h2 class="text-2xl font-bold dark:text-white mb-2">Quelles zones souhaitez-vous générer ?</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Chaque zone sélectionnée sera créée avec un ensemble de tables prêtes à l'emploi.</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($catalogZones as $zone)
                        <label class="flex p-4 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($zone['id'], $selectedZones) ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                            <input type="checkbox" wire:model.live="selectedZones" value="{{ $zone['id'] }}" class="hidden">
                            <div class="ml-2 flex-grow">
                                <span class="block font-semibold text-gray-900 dark:text-white">{{ $zone['name'] }}</span>
                                <span class="block text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $zone['desc'] }}</span>
                                @if(in_array($zone['id'], $selectedZones))
                                <div class="mt-3 flex items-center" onclick="event.stopPropagation()">
                                    <label class="text-xs text-gray-600 dark:text-gray-400 mr-2">Nombre de tables :</label>
                                    <input type="number" wire:model="zoneTablesCount.{{ $zone['id'] }}" class="w-20 px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white" min="1" max="50">
                                </div>
                                @endif
                            </div>
                            <div class="ml-4 h-6 w-6 rounded-full border-2 flex items-center justify-center {{ in_array($zone['id'], $selectedZones) ? 'border-blue-500 bg-blue-500' : 'border-gray-300' }}">
                                @if(in_array($zone['id'], $selectedZones))
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                @elseif($currentStep === 2)
                    <h2 class="text-2xl font-bold dark:text-white mb-1">Quels menus allez-vous proposer ?</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-5">Choisissez votre profil — les menus seront pré-sélectionnés automatiquement.</p>

                    {{-- TYPE SELECTOR --}}
                    <div class="mb-7">
                        <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">Type d'établissement</span>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($restaurantTypes as $typeKey => $typeInfo)
                            <label class="group relative flex flex-col items-center justify-center gap-1 p-4 border-2 rounded-2xl cursor-pointer text-center transition-all duration-200 hover:scale-[1.02] hover:shadow-md
                                {{ $restaurantType === $typeKey
                                    ? 'border-blue-500 bg-gradient-to-b from-blue-50 to-white dark:from-blue-900/40 dark:to-gray-900 shadow-md shadow-blue-100'
                                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-300' }}">
                                <input type="radio" wire:model.live="restaurantType" value="{{ $typeKey }}" class="hidden">
                                <span class="text-3xl leading-none">{{ $typeInfo['icon'] }}</span>
                                <span class="text-[11px] font-semibold text-gray-800 dark:text-white leading-tight mt-1">{{ $typeInfo['label'] }}</span>
                                @if($restaurantType === $typeKey)
                                <span class="absolute top-2 right-2 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                        @if($restaurantType)
                        <div class="mt-3 flex items-center gap-2 text-xs font-medium text-green-600 dark:text-green-400">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-green-100 dark:bg-green-900/40">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Profil <strong class="ml-1">{{ $restaurantTypes[$restaurantType]['label'] }}</strong> — vous pouvez encore ajuster ci-dessous.
                        </div>
                        @endif
                    </div>

                    {{-- MENUS LIST --}}
                    <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">Cartes de menu à générer</span>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($catalogMenus as $menu)
                        @php $isChecked = in_array($menu['id'], $selectedMenus); @endphp
                        <label class="relative flex items-center gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-150 hover:shadow-sm
                            {{ $isChecked ? 'border-blue-400 bg-blue-50/60 dark:bg-blue-900/20' : 'border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:border-gray-300' }}">
                            <input type="checkbox" wire:model.live="selectedMenus" value="{{ $menu['id'] }}" class="hidden">
                            {{-- Icon bubble --}}
                            <span class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-2xl
                                {{ $isChecked ? 'bg-blue-100 dark:bg-blue-800/50' : 'bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600' }}">
                                {{ $menu['icon'] ?? '🍽️' }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <span class="block text-sm font-bold text-gray-900 dark:text-white truncate">{{ $menu['name'] }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $menu['desc'] }}</span>
                            </div>
                            {{-- Checkmark --}}
                            <span class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors
                                {{ $isChecked ? 'bg-blue-500 border-blue-500' : 'border-gray-300 dark:border-gray-600' }}">
                                @if($isChecked)
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </span>
                        </label>
                        @endforeach
                    </div>
                @elseif($currentStep === 3)
                    <h2 class="text-2xl font-bold dark:text-white mb-1">Sélectionnez vos catégories</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-5">Organisez votre offre en activant les catégories correspondantes.</p>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($catalogCategories as $cat)
                        @php $isCatChecked = in_array($cat['id'], $selectedCategories); @endphp
                        <label class="relative flex flex-col items-start gap-2 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-150 hover:shadow-sm
                            {{ $isCatChecked ? 'border-blue-400 bg-blue-50/60 dark:bg-blue-900/20' : 'border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:border-gray-300' }}">
                            <input type="checkbox" wire:model.live="selectedCategories" value="{{ $cat['id'] }}" class="hidden">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-2xl leading-none">{{ $cat['icon'] ?? '🗂️' }}</span>
                                <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                    {{ $isCatChecked ? 'bg-blue-500 border-blue-500' : 'border-gray-300 dark:border-gray-600' }}">
                                    @if($isCatChecked)
                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ $cat['name'] }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $cat['desc'] }}</span>
                            </div>
                            {{-- Parent menu badge --}}
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-gray-200/80 dark:bg-gray-700 text-gray-600 dark:text-gray-300 truncate max-w-full">
                                {{ $cat['menu_ref'] ?? '' }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                @elseif($currentStep === 4)
                    <h2 class="text-2xl font-bold dark:text-white mb-1">Articles populaires à générer</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-5">Ces plats seront pré-créés dans vos catégories. Décochez ceux que vous ne souhaitez pas.</p>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($catalogItems as $item)
                        @if(in_array($item['cat_id'], $selectedCategories))
                        @php
                            $isItemChecked = in_array($item['id'], $selectedItems);
                            $itemImage = $itemImages[$item['id']] ?? $categoryImages[$item['cat_id']] ?? null;
                        @endphp
                        <label class="group relative flex flex-col rounded-2xl border-2 cursor-pointer overflow-hidden transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5
                            {{ $isItemChecked ? 'border-blue-400' : 'border-gray-200 dark:border-gray-700' }}">
                            <input type="checkbox" wire:model.live="selectedItems" value="{{ $item['id'] }}" class="hidden">

                            {{-- Photo hero --}}
                            <div class="relative h-32 overflow-hidden bg-gray-100 dark:bg-gray-800">
                                @if($itemImage)
                                <img src="{{ $itemImage }}"
                                     alt="{{ $item['name'] }}"
                                     class="w-full h-full object-cover transition-all duration-300 {{ $isItemChecked ? 'opacity-100 scale-100' : 'opacity-75 scale-105 group-hover:opacity-90 group-hover:scale-100' }}"
                                     loading="lazy">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-3xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700">
                                    {{ $catalogCategories[$item['cat_id']]['icon'] ?? '🍽️' }}
                                </div>
                                @endif

                                {{-- Category badge top-left --}}
                                <span class="absolute top-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-full
                                    bg-black/50 text-white backdrop-blur-sm truncate max-w-[70%]">
                                    {{ $item['cat_id'] }}
                                </span>

                                {{-- Checkmark badge top-right --}}
                                <span class="absolute top-2 right-2 w-7 h-7 rounded-full border-2 flex items-center justify-center transition-all duration-150 shadow-sm
                                    {{ $isItemChecked
                                        ? 'bg-blue-500 border-blue-500 shadow-blue-300/50'
                                        : 'bg-white/80 border-white dark:bg-gray-900/70 dark:border-gray-700' }}">
                                    @if($isItemChecked)
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    @endif
                                </span>

                                {{-- Bottom gradient overlay --}}
                                <div class="absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-black/60 to-transparent"></div>
                            </div>

                            {{-- Info block --}}
                            <div class="p-3 bg-white dark:bg-gray-900 flex items-center justify-between gap-2">
                                <span class="text-sm font-bold text-gray-900 dark:text-white leading-snug line-clamp-2 flex-1">
                                    {{ $item['name'] }}
                                </span>
                                <span class="flex-shrink-0 text-xs font-extrabold px-2 py-1 rounded-lg
                                    {{ $isItemChecked ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-blue-600 dark:text-blue-400' }}">
                                    {{ $item['price'] > 0 ? currency_format($item['price'], restaurant()->currency_id) : 'Offert' }}
                                </span>
                            </div>
                        </label>
                        @endif
                        @endforeach
                    </div>
                @elseif($currentStep === 5)
                    <h2 class="text-2xl font-bold dark:text-white mb-1">Moyens de paiement</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-5">Comment vos clients vont-ils régler leurs commandes ?</p>
                    <div class="grid sm:grid-cols-3 gap-4">
                        @foreach($catalogPayments as $payment)
                        @php $isPayChecked = in_array($payment['id'], $selectedPayments); @endphp
                        <label class="relative flex flex-col items-center gap-3 p-6 rounded-2xl border-2 cursor-pointer text-center transition-all duration-150 hover:scale-[1.02] hover:shadow-md
                            {{ $isPayChecked
                                ? 'border-green-500 bg-gradient-to-b from-green-50 to-white dark:from-green-900/30 dark:to-gray-900 shadow-md shadow-green-100'
                                : 'border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:border-gray-300' }}">
                            <input type="checkbox" wire:model.live="selectedPayments" value="{{ $payment['id'] }}" class="hidden">
                            <span class="text-4xl leading-none">{{ $payment['icon'] ?? '💳' }}</span>
                            <div>
                                <span class="block text-base font-bold text-gray-900 dark:text-white">{{ $payment['name'] }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $payment['desc'] }}</span>
                            </div>
                            {{-- Checkmark badge --}}
                            <span class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 flex items-center justify-center
                                {{ $isPayChecked ? 'bg-green-500 border-green-500' : 'border-gray-300 dark:border-gray-600' }}">
                                @if($isPayChecked)
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </span>
                        </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Footer Navigation — toujours visible, jamais scrollé -->
            <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center bg-white dark:bg-gray-900 px-8 lg:px-16 py-4 shadow-[0_-6px_12px_-3px_rgba(0,0,0,0.06)] dark:shadow-[0_-6px_12px_-3px_rgba(0,0,0,0.4)]">
                @if($currentStep > 1)
                    <button type="button" wire:click="previousStep" class="px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Précédent
                    </button>
                @else
                    <div></div>
                @endif

                @if($currentStep < 5)
                    <button type="button" wire:click="nextStep" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                        Étape suivante
                    </button>
                @else
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 flex items-center" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="generateSelectedData">
                            Générer et Terminer
                        </span>
                        <span wire:loading wire:target="generateSelectedData" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Génération...
                        </span>
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
