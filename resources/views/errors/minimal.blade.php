<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased min-h-screen flex items-center justify-center bg-gradient-to-br from-secondary to-skin-base">
    <main class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-2xl w-full">
            <div class="bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-2xl backdrop-blur-lg overflow-hidden border border-secondary/20">
                <div class="p-8 sm:p-12 space-y-8 text-center">
                    <!-- Error Code -->
                    <h1 class="text-9xl font-extrabold text-secondary dark:text-secondary/50">
                        @yield('code')
                    </h1>

                    <!-- Error Message -->
                    <div class="space-y-3">
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                            @yield('message')
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400">
                            @yield('description')
                        </p>
                    </div>

                    @if(trim($__env->yieldContent('help_title')))
                    <!-- Additional Help Text -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
                        <p class="text-base text-gray-600 dark:text-gray-300 font-medium">
                            @yield('help_title')
                        </p>
                        <ul class="mt-4 text-base text-gray-600 dark:text-gray-300 space-y-2">
                            <li class="flex items-center justify-center gap-2">
                                @yield('help_icon_1')
                                @yield('help_item_1')
                            </li>
                            <li class="flex items-center justify-center gap-2">
                                @yield('help_icon_2')
                                @yield('help_item_2')
                            </li>
                            <li class="flex items-center justify-center gap-2">
                                @yield('help_icon_3')
                                @yield('help_item_3')
                            </li>
                        </ul>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <a href="{{ url('/') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 text-white bg-secondary border-2 border-secondary rounded-md hover:bg-secondary/80 transition-colors duration-200">
                            @yield('home_icon')
                            Return Home
                        </a>

                        <button onclick="history.back()"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 text-white bg-secondary border-2 border-secondary rounded-md hover:bg-secondary/80 transition-colors duration-200">
                            @yield('back_icon')
                            Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
