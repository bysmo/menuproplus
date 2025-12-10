<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|poppins:500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('css/aladin-theme.css') }}">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3 {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-yellow-50">
    <x-aladin-pattern variant="dots" />
    
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full text-center aladin-animate-fade-in-up">
            <!-- Logo MenuPro -->
            <div class="mb-8">
                <div class="w-24 h-24 mx-auto bg-white rounded-2xl p-4 shadow-lg border-2 border-yellow-400">
                    <img src="{{ restaurantOrGlobalSetting()->logo_url }}" class="w-full h-full object-contain" alt="MenuPro Logo" />
                </div>
            </div>
            
            <!-- Error Code -->
            <h1 class="aladin-error-code mb-4">
                @yield('code')
            </h1>
            
            <!-- Error Title -->
            <h2 class="aladin-error-title mb-4">
                @yield('message')
            </h2>
            
            <!-- Error Description -->
            <p class="aladin-error-description mb-8">
                @yield('description')
            </p>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ url('/') }}" class="aladin-btn inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    {{ __('Retour à l\'accueil') }}
                </a>
                
                <button onclick="history.back()" class="px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-all inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Page précédente') }}
                </button>
            </div>
            
            <!-- Help Section -->
            @hasSection('help')
                <div class="mt-12 p-6 bg-white/80 backdrop-blur-sm rounded-xl border border-blue-100 text-left">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ __('Besoin d\'aide ?') }}
                    </h3>
                    @yield('help')
                </div>
            @endif
            
            <!-- Footer -->
            <div class="mt-12 text-sm text-gray-500">
                <p>© {{ date('Y') }} Aladin Technologies Solutions</p>
                <p class="mt-1">Powered by MenuPro+</p>
            </div>
        </div>
    </div>
</body>
</html>
