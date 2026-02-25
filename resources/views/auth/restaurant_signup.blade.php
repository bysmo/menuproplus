<x-auth-layout>
    <link rel="stylesheet" href="{{ asset('css/aladin-theme.css') }}">
    
    <div class="aladin-split-layout">
        <!-- Left Side - Illustration -->
        <div class="aladin-split-left aladin-animate-fade-in hidden lg:flex">
            <div class="absolute inset-0 opacity-10">
                <x-aladin-pattern variant="dots" />
            </div>
            
            <div class="relative z-10 text-center text-white max-w-md">
                <!-- Logo MenuPro avec animation -->
                <div class="aladin-animate-float mb-8">
                    <div class="w-32 h-32 mx-auto bg-white/10 backdrop-blur-sm rounded-2xl p-6 border-2 border-yellow-400 flex items-center justify-center">
                        <img src="{{ restaurantOrGlobalSetting()->logo_url }}" class="w-full h-full object-contain" alt="MenuPro Logo" />
                    </div>
                </div>
                
                <!-- Titre MenuPro -->
                <h2 class="text-3xl font-bold mb-2">
                    {{ global_setting()->name ?? 'MenuPro+' }}
                </h2>
                
                <h3 class="text-xl font-semibold mb-4 text-yellow-400">{{ __('Inscription Restaurant') }}</h3>
                
                <p class="text-lg opacity-90 mb-8">
                    {{ __('Prenez le contrôle de votre restaurant') }}
                </p>
                
                <!-- Features -->
                <div class="space-y-4 text-left">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-1">{{ __('Rationaliser la gestion des commandes') }}</h4>
                            <p class="text-sm opacity-90">{{ __('Gérez efficacement toutes vos commandes') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-1">{{ __('Optimiser les réservations de table') }}</h4>
                            <p class="text-sm opacity-90">{{ __('Système de réservation intelligent') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-1">{{ __('Gestion de menu sans effort') }}</h4>
                            <p class="text-sm opacity-90">{{ __('Mettez à jour votre menu facilement') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="aladin-split-right aladin-animate-slide-in-right">
            <div class="w-full max-w-md">
                <!-- Card -->
                <div class="bg-white dark:bg-gray-800 backdrop-blur-sm border border-gray-200 dark:border-gray-700 border-t-4 border-t-yellow-400 rounded-xl p-6 shadow-lg aladin-animate-fade-in-up">
                    @livewire('forms.restaurantSignup')
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center text-sm text-gray-500">
                    <p>© {{ date('Y') }} MenuPro+</p>
                    <p class="mt-1">Powered by Aladin Technologies Solutions (ALTES)</p>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
