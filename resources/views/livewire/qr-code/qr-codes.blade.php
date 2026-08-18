<div>
    {{-- ============================================================ --}}
    {{--  PAGE HEADER                                                  --}}
    {{-- ============================================================ --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    @lang('menu.qrCodes')
                </h1>

                {{-- 🎨 Bouton Personnaliser les QR Codes --}}
                <button type="button" wire:click="openCustomizer"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg shadow-md transition duration-150 cursor-pointer focus:outline-none"
                    style="background-color: #4f46e5; color: #ffffff;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" />
                    </svg>
                    <span>Personnaliser les QR Codes</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Flash message --}}
    @if (session()->has('success'))
        <div class="mx-4 my-2 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 flex items-center gap-2 border border-green-200 dark:border-green-800">
            <svg class="h-5 w-5 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{--  MODALE DE PERSONNALISATION ENCASTRÉE & SCROLLABLE            --}}
    {{-- ============================================================ --}}
    @if ($showCustomizer)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5"
             style="background-color: rgba(15, 23, 42, 0.75); backdrop-filter: blur(6px);">
            
            {{-- Boîte principale modale avec hauteur contrainte et encadrée --}}
            <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-5xl flex flex-col border border-gray-200 dark:border-gray-700 overflow-hidden"
                 style="height: 86vh; max-height: 800px;">

                {{-- 1. EN-TÊTE FIXE EN HAUT --}}
                <div class="flex items-center justify-between px-6 py-3.5 flex-shrink-0 border-b border-indigo-700"
                     style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-white/20 text-white shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white tracking-wide">Personnaliser les QR Codes</h2>
                            <p class="text-indigo-100 text-xs">Concevez le design appliqué à tous les QR codes de votre restaurant</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeCustomizer"
                        class="p-1.5 rounded-lg text-white/80 hover:text-white bg-white/10 hover:bg-white/20 transition cursor-pointer"
                        title="Fermer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- 2. ZONE CENTRALE (Gauche scrollable, Droite fixe) --}}
                <div class="flex-1 flex flex-col lg:flex-row overflow-hidden min-h-0">

                    {{-- ── COLONNE GAUCHE (Formulaire de réglages SCROLLABLE) ── --}}
                    <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-5" style="scrollbar-width: thin;">

                        {{-- ─── 1. SECTION: COULEURS DU QR ─── --}}
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 inline-block"></span>
                                Couleurs du QR Code
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Couleur QR (Body) --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Couleur QR (Corps)
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" wire:model.live="foreground_color"
                                            class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5 shadow-sm">
                                        <input type="text" wire:model.live="foreground_color"
                                            maxlength="7"
                                            class="flex-1 text-xs border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white font-mono uppercase"
                                            placeholder="#000000">
                                    </div>
                                </div>
                                {{-- Arrière-plan --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Arrière-plan
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" wire:model.live="background_color"
                                            class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5 shadow-sm">
                                        <input type="text" wire:model.live="background_color"
                                            maxlength="7"
                                            class="flex-1 text-xs border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white font-mono uppercase"
                                            placeholder="#FFFFFF">
                                    </div>
                                </div>
                            </div>

                            {{-- Palettes prédéfinies --}}
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium">Palettes prédéfinies</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach([
                                        ['fg' => '#000000', 'bg' => '#FFFFFF', 'name' => 'Classique'],
                                        ['fg' => '#1E3A8A', 'bg' => '#FFFFFF', 'name' => 'Bleu Nuit'],
                                        ['fg' => '#4F46E5', 'bg' => '#FFFFFF', 'name' => 'Indigo'],
                                        ['fg' => '#7C3AED', 'bg' => '#FFFFFF', 'name' => 'Violet'],
                                        ['fg' => '#059669', 'bg' => '#ECFDF5', 'name' => 'Émeraude'],
                                        ['fg' => '#DC2626', 'bg' => '#FFF1F2', 'name' => 'Rubis'],
                                        ['fg' => '#D97706', 'bg' => '#FFFBEB', 'name' => 'Ambre'],
                                        ['fg' => '#0F172A', 'bg' => '#F8FAFC', 'name' => 'Sombre'],
                                    ] as $preset)
                                        <button type="button"
                                            wire:click="setColorPreset('{{ $preset['fg'] }}', '{{ $preset['bg'] }}')"
                                            class="group flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-indigo-400 transition cursor-pointer shadow-xs">
                                            <span class="w-3.5 h-3.5 rounded-full border border-gray-300 shadow-inner"
                                                style="background: linear-gradient(135deg, {{ $preset['bg'] }} 50%, {{ $preset['fg'] }} 50%)"></span>
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $preset['name'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- ─── 2. SECTION: COINS & YEUX (FINDER PATTERNS) ─── --}}
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-violet-600 inline-block"></span>
                                Coins & Yeux (Finder Patterns)
                            </h3>

                            {{-- Forme des coins --}}
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Forme des coins
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                    @foreach([
                                        ['key' => 'square', 'label' => 'Carré classique'],
                                        ['key' => 'rounded', 'label' => 'Arrondi moderne'],
                                        ['key' => 'circle', 'label' => 'Cercle parfait'],
                                        ['key' => 'leaf', 'label' => 'Goutte / Feuille'],
                                    ] as $shape)
                                        <button type="button"
                                            wire:click="setEyeShape('{{ $shape['key'] }}')"
                                            class="flex flex-col items-center justify-center p-2.5 rounded-xl border-2 transition cursor-pointer text-center
                                                   {{ $eye_shape === $shape['key'] ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-bold shadow-xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-300' }}">
                                            
                                            <div class="w-8 h-8 mb-1 flex items-center justify-center">
                                                @if($shape['key'] === 'square')
                                                    <div class="w-6 h-6 border-2 border-current flex items-center justify-center">
                                                        <div class="w-2 h-2 bg-current"></div>
                                                    </div>
                                                @elseif($shape['key'] === 'rounded')
                                                    <div class="w-6 h-6 border-2 border-current rounded-md flex items-center justify-center">
                                                        <div class="w-2 h-2 bg-current rounded-xs"></div>
                                                    </div>
                                                @elseif($shape['key'] === 'circle')
                                                    <div class="w-6 h-6 border-2 border-current rounded-full flex items-center justify-center">
                                                        <div class="w-2 h-2 bg-current rounded-full"></div>
                                                    </div>
                                                @elseif($shape['key'] === 'leaf')
                                                    <div class="w-6 h-6 border-2 border-current rounded-tl-xl rounded-br-xl flex items-center justify-center">
                                                        <div class="w-2 h-2 bg-current rounded-tl-sm rounded-br-sm"></div>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="text-xs">{{ $shape['label'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Couleur des coins --}}
                            <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                        Couleur des coins
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-gray-600 dark:text-gray-400 font-medium">
                                        <input type="checkbox" wire:model.live="sync_eye_color"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span>Même couleur que le corps du QR</span>
                                    </label>
                                </div>

                                @if (!$sync_eye_color)
                                    <div class="flex items-center gap-2 mt-2 animate-fade-in">
                                        <input type="color" wire:model.live="eye_color"
                                            class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5 shadow-sm">
                                        <input type="text" wire:model.live="eye_color"
                                            maxlength="7"
                                            class="flex-1 text-xs border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white font-mono uppercase"
                                            placeholder="#000000">
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ─── 3. SECTION: LOGO AU CENTRE ─── --}}
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 inline-block"></span>
                                Logo au centre
                            </h3>

                            {{-- Switch toggle --}}
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mb-3 shadow-xs">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                        Intégrer le logo au centre
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Intègre votre logo au centre des QR codes avec correction d'erreur haute
                                    </p>
                                </div>
                                <button type="button" wire:click="toggleShowLogo"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 cursor-pointer focus:outline-none"
                                    style="background-color: {{ $show_logo ? '#4f46e5' : '#cbd5e1' }};">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 {{ $show_logo ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </div>

                            @if ($show_logo)
                                <div class="space-y-3.5 pt-1 animate-fade-in">
                                    {{-- État du logo actif --}}
                                    @if ($custom_logo)
                                        <div class="flex items-center justify-between p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-200 dark:border-indigo-700">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $custom_logo->temporaryUrl() }}" alt="Custom Logo" class="w-10 h-10 object-contain rounded-lg bg-white p-1 shadow border">
                                                <div>
                                                    <p class="text-xs font-bold text-indigo-900 dark:text-indigo-300">Nouveau logo sélectionné</p>
                                                    <p class="text-[11px] text-indigo-700 dark:text-indigo-400">{{ $custom_logo->getClientOriginalName() }}</p>
                                                </div>
                                            </div>
                                            <button type="button" wire:click="removeCustomLogo"
                                                class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 bg-red-50 hover:bg-red-100 rounded-lg transition cursor-pointer">
                                                Supprimer
                                            </button>
                                        </div>
                                    @elseif ($existing_custom_logo)
                                        <div class="flex items-center justify-between p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-200 dark:border-indigo-700">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ asset_url_local_s3('logo/' . $existing_custom_logo) }}" alt="Custom Logo" class="w-10 h-10 object-contain rounded-lg bg-white p-1 shadow border">
                                                <div>
                                                    <p class="text-xs font-bold text-indigo-900 dark:text-indigo-300">Logo personnalisé actif</p>
                                                    <p class="text-[11px] text-indigo-700 dark:text-indigo-400">Appliqué sur tous les QR codes</p>
                                                </div>
                                            </div>
                                            <button type="button" wire:click="removeCustomLogo"
                                                class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 bg-red-50 hover:bg-red-100 rounded-lg transition cursor-pointer">
                                                Supprimer
                                            </button>
                                        </div>
                                    @elseif (restaurant()?->logo)
                                        <div class="flex items-center gap-3 p-3 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                                            <img src="{{ restaurant()->logoUrl }}" alt="Logo" class="w-10 h-10 object-contain rounded-lg bg-white p-1 shadow border">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Logo du restaurant</p>
                                                <p class="text-[11px] text-gray-500">Logo actuel de votre établissement</p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Téléversement de logo --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Téléverser un logo personnalisé
                                        </label>
                                        <input type="file" wire:model="custom_logo" accept="image/png,image/jpeg,image/webp"
                                            class="block w-full text-xs text-gray-500 dark:text-gray-400
                                                   file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0
                                                   file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                                   hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 cursor-pointer">
                                    </div>

                                    {{-- Curseur de taille du logo --}}
                                    <div>
                                        <div class="flex items-center justify-between text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            <span>Taille du logo</span>
                                            <span class="font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/40 px-2 py-0.5 rounded">{{ $logo_size }}%</span>
                                        </div>
                                        <input type="range" wire:model.live="logo_size"
                                            min="5" max="30" step="1"
                                            class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                        <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                                            <span>5% (Discret)</span>
                                            <span>20% (Recommandé)</span>
                                            <span>30% (Grand)</span>
                                        </div>
                                    </div>

                                    {{-- Curseur d'espace blanc / padding autour du logo --}}
                                    <div>
                                        <div class="flex items-center justify-between text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            <span>Espace blanc autour du logo (Padding)</span>
                                            <span class="font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/40 px-2 py-0.5 rounded">{{ $logo_padding }}px</span>
                                        </div>
                                        <input type="range" wire:model.live="logo_padding"
                                            min="0" max="20" step="1"
                                            class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                        <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                                            <span>0px (Sans marge)</span>
                                            <span>6px (Idéal)</span>
                                            <span>20px (Large)</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- ─── 4. SECTION: TEXTE SOUS LE QR ─── --}}
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                                Texte sous le QR Code
                            </h3>

                            <div class="space-y-3.5">
                                {{-- Saisie du texte --}}
                                <div>
                                    <div class="flex justify-between text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        <span>Texte affiché</span>
                                        <span class="text-gray-400">{{ strlen($label_text) }}/100</span>
                                    </div>
                                    <input type="text" wire:model.live="label_text"
                                        maxlength="100"
                                        placeholder="Ex : Scannez pour commander"
                                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                </div>

                                {{-- Suggestions rapides --}}
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">Suggestions de texte</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach([
                                            'Scannez pour commander',
                                            'Consultez notre menu',
                                            'Bienvenue à votre table',
                                            'Scanner pour voir la carte',
                                        ] as $preset)
                                            <button type="button"
                                                wire:click="setLabelPreset('{{ $preset }}')"
                                                class="px-2.5 py-1 text-xs bg-white hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 rounded-full border border-gray-200 hover:border-indigo-300 transition cursor-pointer shadow-xs dark:bg-gray-800 dark:text-gray-300">
                                                {{ $preset }}
                                            </button>
                                        @endforeach
                                        <button type="button"
                                            wire:click="clearLabelText"
                                            class="px-2.5 py-1 text-xs bg-red-50 hover:bg-red-100 text-red-600 rounded-full border border-red-200 transition cursor-pointer">
                                            Aucun texte
                                        </button>
                                    </div>
                                </div>

                                @if ($label_text)
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-gray-200 dark:border-gray-700 animate-fade-in">
                                        {{-- Couleur du texte --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Couleur du texte
                                            </label>
                                            <div class="flex items-center gap-2">
                                                <input type="color" wire:model.live="label_color"
                                                    class="w-9 h-9 rounded-lg border border-gray-300 cursor-pointer p-0.5 shadow-sm">
                                                <input type="text" wire:model.live="label_color"
                                                    maxlength="7"
                                                    class="flex-1 text-xs border border-gray-300 rounded-lg px-2 py-1.5 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white font-mono uppercase"
                                                    placeholder="#000000">
                                            </div>
                                        </div>

                                        {{-- Police de caractères --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Police
                                            </label>
                                            <select wire:model.live="label_font"
                                                class="w-full text-xs border border-gray-300 rounded-lg px-2.5 py-2 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                                <option value="noto_sans">Noto Sans</option>
                                                <option value="open_sans">Open Sans</option>
                                            </select>
                                        </div>

                                        {{-- Taille de police --}}
                                        <div>
                                            <div class="flex items-center justify-between text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                <span>Taille de police</span>
                                                <span class="font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/40 px-1.5 py-0.5 rounded">{{ $label_size }}px</span>
                                            </div>
                                            <input type="range" wire:model.live="label_size"
                                                min="10" max="36" step="1"
                                                class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ─── 5. SECTION: PIED DE PAGE & SIGNATURE ALTES (BRANDING) ─── --}}
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 inline-block"></span>
                                    Signature & Pied de Page (ALTES)
                                </h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.live="show_branding" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-2 text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ $show_branding ? 'Activé' : 'Désactivé' }}
                                    </span>
                                </label>
                            </div>

                            @if ($show_branding)
                                <div class="space-y-3 pt-2 border-t border-gray-200 dark:border-gray-700 animate-fade-in">
                                    <div class="p-3 bg-blue-50/60 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800 flex items-center gap-3">
                                        <img src="{{ asset('img/altes-logo.png') }}" alt="ALTES Logo" class="w-8 h-8 object-contain rounded-md bg-white p-0.5 shadow-xs border border-blue-200">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-blue-900 dark:text-blue-200">Pied de page de marque officiel</p>
                                            <p class="text-[11px] text-blue-700 dark:text-blue-300 truncate">Intègre la signature ALTES, le logo et le lien web au bas du QR code</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        {{-- Texte de marque --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Texte de signature
                                            </label>
                                            <input type="text" wire:model.live="branding_text"
                                                maxlength="120"
                                                class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
                                                placeholder="Menupro+, designed by ALTES">
                                        </div>

                                        {{-- Lien Web --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Site Web Menupro+
                                            </label>
                                            <input type="text" wire:model.live="branding_website"
                                                maxlength="150"
                                                class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
                                                placeholder="https://menuproplus.aladints.com/">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>{{-- /left controls --}}

                    {{-- ── COLONNE DROITE: APERÇU FIXE & EN DIRECT ── --}}
                    <div class="lg:w-80 flex-shrink-0 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 flex flex-col items-center justify-between p-5 gap-3">
                        <div class="w-full flex flex-col items-center gap-3">
                            <h3 class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Aperçu en direct
                            </h3>

                            {{-- Conteneur QR avec dimensions fixes --}}
                            <div class="relative bg-white dark:bg-gray-900 p-3 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 flex items-center justify-center w-full min-h-[220px]">
                                <div wire:loading wire:target="refreshPreview,foreground_color,background_color,eye_color,eye_shape,show_logo,logo_size,custom_logo,label_text,label_color,label_size,label_font,removeCustomLogo,setEyeShape,setColorPreset,setLabelPreset,clearLabelText,toggleShowLogo,toggleSyncEyeColor,show_branding,branding_text,branding_website,toggleShowBranding"
                                    class="absolute inset-0 flex flex-col items-center justify-center bg-white/85 dark:bg-gray-900/85 rounded-2xl z-20 backdrop-blur-xs">
                                    <svg class="animate-spin h-7 w-7 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span class="text-xs text-indigo-600 font-semibold mt-1.5">Génération de l'aperçu...</span>
                                </div>

                                @if ($previewData)
                                    <img src="{{ $previewData }}" alt="Aperçu QR Code"
                                        class="w-52 h-auto rounded-xl object-contain max-h-[260px]">
                                @else
                                    <div class="w-52 h-52 flex flex-col items-center justify-center text-gray-400 gap-2">
                                        <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                                        </svg>
                                        <span class="text-xs text-center font-medium">Génération de l'aperçu...</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Résumé des réglages appliqués --}}
                            <div class="w-full bg-white dark:bg-gray-900 rounded-xl p-3 border border-gray-200 dark:border-gray-700 text-xs space-y-1.5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Couleur QR</span>
                                    <div class="flex items-center gap-1.5 font-mono font-semibold">
                                        <span class="w-3 h-3 rounded-full border" style="background-color: {{ $foreground_color }}"></span>
                                        <span>{{ $foreground_color }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Forme des coins</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                                        @if($eye_shape === 'square') Carré classique
                                        @elseif($eye_shape === 'rounded') Arrondi moderne
                                        @elseif($eye_shape === 'circle') Cercle parfait
                                        @elseif($eye_shape === 'leaf') Goutte / Feuille
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Logo central</span>
                                    <span class="font-semibold {{ $show_logo ? 'text-emerald-600' : 'text-gray-400' }}">
                                        {{ $show_logo ? 'Oui (' . $logo_size . '%)' : 'Non' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Signature ALTES</span>
                                    <span class="font-semibold {{ $show_branding ? 'text-blue-600' : 'text-gray-400' }}">
                                        {{ $show_branding ? 'Activée' : 'Désactivée' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Texte table</span>
                                    <span class="font-semibold truncate max-w-[110px] text-right {{ $label_text ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400' }}">
                                        {{ $label_text ?: '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Bouton actualiser --}}
                        <button type="button" wire:click="refreshPreview"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center gap-1.5 cursor-pointer py-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Actualiser l'aperçu
                        </button>
                    </div>{{-- /colonne droite --}}

                </div>{{-- /zone centrale --}}

                {{-- 3. PIED DE PAGE FIXE EN BAS --}}
                <div class="flex items-center justify-between px-6 py-3.5 flex-shrink-0 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <button type="button" wire:click="resetCustomization"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Réinitialiser
                    </button>

                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="closeCustomizer"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer">
                            Annuler
                        </button>

                        {{-- Bouton d'enregistrement bien visible et contrasté --}}
                        <button type="button" wire:click="saveCustomization"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold text-white rounded-lg shadow-md transition cursor-pointer focus:outline-none"
                            style="background-color: #4f46e5; color: #ffffff;">
                            <span wire:loading.remove wire:target="saveCustomization">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            <span wire:loading wire:target="saveCustomization">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </span>
                            <span>Enregistrer & Régénérer tous les QR Codes</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{--  MAIN CONTENT                                                 --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col my-4 px-4">
        <div class="mb-6 lg:flex lg:justify-between">
            <ul class="inline-flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400 mb-4">
                <li class="me-2" wire:key='area-fltr-{{ microtime() }}'>
                    <a href="javascript:;" wire:click="$set('areaID', null)"
                        @class([
                            'inline-block px-4 py-3 rounded-lg cursor-pointer',
                            'text-skin-base dark:bg-skin-base/[.1] bg-skin-base/[.2]' => is_null($areaID),
                            'hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white' => !is_null($areaID),
                        ])>@lang('modules.table.allAreas')</a>
                </li>

                @foreach ($areas as $item)
                    <li class="me-2" wire:key='area-fltr-{{ $item->id . microtime() }}'>
                        <a href="javascript:;" wire:click="$set('areaID', '{{ $item->id }}')"
                            @class([
                                'inline-block px-4 py-3 rounded-lg cursor-pointer',
                                'text-skin-base dark:bg-skin-base/[.1] bg-skin-base/[.2]' => $areaID == $item->id,
                                'hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white' => $areaID != $item->id,
                            ])>
                            {{ $item->area_name }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="inline-flex items-center gap-3 lg:fixed bottom-10 right-5 lg:bg-white lg:px-3 lg:py-2 lg:shadow-md lg:rounded-md z-10">
                <div class="inline-flex items-center text-sm text-gray-600 gap-1 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-circle-fill text-green-500" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="8" />
                    </svg>
                    @lang('modules.table.available')
                </div>
                <div class="inline-flex items-center text-sm text-gray-600 gap-1 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-circle-fill text-blue-500" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="8" />
                    </svg>
                    @lang('modules.table.running')
                </div>
                <div class="inline-flex items-center text-sm text-gray-600 gap-1 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-circle-fill text-red-500" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="8" />
                    </svg>
                    @lang('modules.table.reserved')
                </div>
            </div>
        </div>

        {{-- ── Card Section ── --}}
        <div class="space-y-8">
            @if (is_null($areaID) && branch()->qRCodeUrl)
                <div class="flex flex-col gap-3 sm:gap-4 space-y-1" wire:key='area-mainqr-{{ microtime() }}'>
                    <div class="grid sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                        <div class='group flex flex-col gap-3 border shadow-sm rounded-xl hover:shadow-md transition bg-white dark:bg-gray-800 dark:border-gray-700 p-4'
                            href="javascript:;">
                            <div class="w-full flex justify-center">
                                <img src="{{ branch()->qRCodeUrl }}" alt="QR Code"
                                    class="rounded-lg max-w-full" style="max-height:220px">
                            </div>
                            <div class="flex items-center gap-3 justify-center w-full mt-2">
                                <x-secondary-button wire:click="downloadBranchQrCode" class="text-xs cursor-pointer"
                                    data-tooltip-target="download-tooltip-toggle" type="button"
                                    data-tooltip-placement="top">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </x-secondary-button>
                                <div id="download-tooltip-toggle" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                    @lang('app.download')
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                                <x-secondary-link target="_blank" :href="route('table_order', [restaurant()->id]) .
                                    '?branch=' .branch()->unique_hash .'&hash='. restaurant()->hash .'&from_qr=1'" class="text-xs"
                                    data-tooltip-target="visit-tooltip-toggle" type="button"
                                    data-tooltip-placement="top">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </x-secondary-link>
                                <div id="visit-tooltip-toggle" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                    @lang('app.visitLink')
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>

                                <x-secondary-button wire:click="generateQrCode" class="text-xs cursor-pointer" type="button"
                                    data-tooltip-target="generate-qr-code-tooltip-toggle" data-tooltip-placement="top">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </x-secondary-button>

                                <div id="generate-qr-code-tooltip-toggle" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                    @lang('modules.table.generateQrCode')
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @foreach ($tables as $area)
                <div class="flex flex-col gap-3 sm:gap-4 space-y-1" wire:key='area-{{ $area->id . microtime() }}'>
                    <h3 class="f-15 font-semibold inline-flex gap-2 items-center text-gray-800 dark:text-neutral-200">
                        {{ $area->area_name }}
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">
                            {{ $area->tables->count() }} @lang('modules.table.table')
                        </span>
                    </h3>

                    <div class="grid sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                        @foreach ($area->tables as $item)
                            <div @class([
                                'group flex flex-col gap-3 border shadow-sm rounded-xl hover:shadow-md transition p-3.5',
                                'bg-red-50 border-red-200 dark:bg-gray-800' => $item->status == 'inactive',
                                'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700'  => $item->status == 'active',
                            ]) wire:key='table-{{ $item->id . microtime() }}'
                                href="javascript:;">

                                <div class="flex items-center gap-4 justify-between w-full">
                                    <div @class([
                                        'px-3 py-1.5 rounded-lg tracking-wide font-bold text-sm',
                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' => $item->available_status == 'available',
                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'     => $item->available_status == 'reserved',
                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'   => $item->available_status == 'running',
                                    ])>
                                        <h3 wire:loading.class.delay='opacity-50'>
                                            {{ $item->table_code }}
                                        </h3>
                                    </div>
                                    <div class="space-y-1 text-right">
                                        <p @class(['text-xs font-medium dark:text-neutral-300 text-gray-500'])>
                                            {{ $item->seating_capacity }} @lang('modules.table.seats')
                                        </p>

                                        @if ($item->available_status == 'reserved')
                                            <div class="px-1.5 py-0.5 border border-red-500 text-[10px] text-red-600 rounded font-semibold inline-block">
                                                @lang('modules.table.reserved')
                                            </div>
                                        @endif

                                        @if ($item->status == 'inactive')
                                            <div class="inline-flex text-xs gap-1 text-red-600 font-semibold">
                                                @lang('app.inactive')
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="w-full flex justify-center py-2">
                                    <img src="{{ $item->qRCodeUrl }}" alt="" class="rounded-lg max-w-full" style="max-height:180px">
                                </div>

                                <div class="flex items-center gap-3 justify-center w-full mt-1">
                                    <x-secondary-button
                                        wire:click="downloadQrCode('{{ $item->table_code }}', '{{ $item->branch_id }}')"
                                        class="text-xs cursor-pointer"
                                        data-tooltip-target="download-tooltip-toggle-{{ $item->id }}"
                                        type="button" data-tooltip-placement="top">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                    </x-secondary-button>

                                    <div id="download-tooltip-toggle-{{ $item->id }}" role="tooltip"
                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                        @lang('app.download')
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>

                                    <x-secondary-link target="_blank"
                                        href="{{ route('table_order', [$item->hash]) . '?hash=' . restaurant()->hash }}"
                                        class="text-xs"
                                        data-tooltip-target="visit-tooltip-toggle-{{ $item->id }}" type="button"
                                        data-tooltip-placement="top">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                    </x-secondary-link>

                                    <div id="visit-tooltip-toggle-{{ $item->id }}" role="tooltip"
                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                        @lang('app.visitLink')
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>

                                    <x-secondary-button wire:click="generateQrCode('{{ $item->id }}')" class="text-xs cursor-pointer"
                                        type="button"
                                        data-tooltip-target="generate-qr-code-tooltip-toggle-{{ $item->id }}"
                                        data-tooltip-placement="top">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                    </x-secondary-button>

                                    <div id="generate-qr-code-tooltip-toggle-{{ $item->id }}" role="tooltip"
                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                        @lang('modules.table.generateQrCode')
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </div>

                            </div>
                            {{-- End Card --}}
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
        {{-- End Card Section --}}

    </div>

</div>
