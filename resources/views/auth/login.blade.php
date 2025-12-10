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
                
                <h3 class="text-xl font-semibold mb-4 text-yellow-400">{{ __('Connexion') }}</h3>
                
                <p class="text-lg opacity-90 mb-8">
                    {{ __('La solution complète de gestion de restaurant par Aladin Technologies Solutions') }}
                </p>
                
                <div class="mt-12 flex items-center justify-center gap-8">
                    <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-yellow-400/30">
                        <div class="text-4xl font-bold text-yellow-400">500+</div>
                        <div class="text-sm mt-1">{{ __('Restaurants') }}</div>
                    </div>
                    <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-yellow-400/30">
                        <div class="text-4xl font-bold text-yellow-400">50K+</div>
                        <div class="text-sm mt-1">{{ __('Commandes/jour') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="aladin-split-right aladin-animate-slide-in-right">
            <div class="w-full max-w-md">

                <!-- Card -->
                <x-aladin-card class="aladin-animate-fade-in-up border-t-4 border-yellow-400">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ __('app.login') }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('Connectez-vous à votre compte') }}
                        </p>
                    </div>

                    <x-validation-errors class="mb-4"/>

                    @session('status')
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                        {{ $value }}
                    </div>
                    @endsession

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Email -->
                        <div class="mb-4">
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

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('app.password') }}
                            </label>
                            <x-input-password 
                                id="password" 
                                name="password" 
                                required 
                                class="aladin-input"
                            />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mb-6">
                            <label for="remember_me" class="flex items-center cursor-pointer">
                                <x-checkbox id="remember_me" name="remember"/>
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('app.rememberMe') }}
                                </span>
                            </label>

                            <a href="{{ route('password.request') }}" 
                               class="text-sm font-medium hover:underline"
                               style="color: var(--aladin-blue-primary);">
                                {{ __('app.forgotPassword') }}
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <x-aladin-button 
                            type="submit" 
                            class="w-full mb-4" 
                            id="submitBtn"
                        >
                            {{ __('app.login') }}
                        </x-aladin-button>

                        <!-- Register Link -->
                        @if(!module_enabled('Subdomain'))
                        <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                            @lang('auth.areYouNew', ['appName' => global_setting()->name])
                            <a href="{{ route('restaurant_signup') }}" 
                               class="font-medium hover:underline"
                               style="color: var(--aladin-blue-primary);">
                                @lang('auth.createAccount')
                            </a>
                        </div>
                        @endif

                        <!-- Home Link -->
                        @if(!module_enabled('Subdomain') && !global_setting()->disable_landing_site)
                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" 
                               class="text-sm text-gray-500 hover:underline flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                @lang('auth.goHome')
                            </a>
                        </div>
                        @endif
                    </form>
                </x-aladin-card>

                <!-- Footer -->
                <div class="mt-8 text-center text-sm text-gray-500">
                    <p>© {{ date('Y') }} Aladin Technologies Solutions</p>
                    <p class="mt-1">Powered by MenuPro+</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            const submitBtn = document.getElementById('submitBtn');

            if (emailField.checkValidity() && passwordField.checkValidity() && 
                emailField.value && passwordField.value) {
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
