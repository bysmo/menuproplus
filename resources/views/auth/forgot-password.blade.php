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
                
                <h3 class="text-xl font-semibold mb-4 text-yellow-400">{{ __('Mot de passe oublié') }}</h3>
                
                <p class="text-lg opacity-90 mb-8">
                    {{ __('Pas de problème ! Entrez votre email et nous vous enverrons un lien de réinitialisation.') }}
                </p>
                
                <div class="mt-12 p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-yellow-400/30">
                    <div class="flex items-center gap-3 text-left">
                        <svg class="w-8 h-8 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <div>
                            <h4 class="font-semibold mb-1">{{ __('Sécurisé') }}</h4>
                            <p class="text-sm opacity-90">{{ __('Le lien expire après 60 minutes') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="aladin-split-right aladin-animate-slide-in-right">
            <div class="w-full max-w-md">

                <!-- Card -->
                <x-aladin-card class="aladin-animate-fade-in-up border-t-4 border-yellow-400">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ __('app.forgotPassword') }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('app.forgotPasswordMessage') }}
                        </p>
                    </div>

                    @session('status')
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                        {{ $value }}
                    </div>
                    @endsession

                    <x-validation-errors class="mb-4"/>

                    <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                        @csrf

                        <!-- Email -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('app.email') }}
                            </label>
                            <x-aladin-input 
                                id="email" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                required 
                                autofocus 
                                autocomplete="username"
                                placeholder="votre@email.com"
                            />
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                            <a href="{{ route('login') }}" 
                               class="text-sm text-gray-600 dark:text-gray-400 hover:underline flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                {{ __('app.backToLogin') }}
                            </a>

                            <x-aladin-button 
                                type="submit" 
                                class="w-full sm:w-auto button"
                            >
                                {{ __('app.sendPasswordResetLink') }}
                            </x-aladin-button>
                        </div>
                    </form>
                </x-aladin-card>

                <!-- Footer -->
                <div class="mt-8 text-center text-sm text-gray-500">
                    <p>© {{ date('Y') }} MenuPro+</p>
                    <p class="mt-1">Powered by Aladin Technologies Solutions (ALTES)</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            const emailField = document.getElementById('email');
            const submitBtn = document.querySelector('.button');

            if (emailField.checkValidity() && emailField.value) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="inline w-5 h-5 mr-2 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    ${@json(__('app.loading'))}
                `;
            }
        });
    </script>
</x-auth-layout>
