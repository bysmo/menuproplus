<x-auth-layout>
    <div class="w-full sm:max-w-md mt-8 px-8 py-6 bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg shadow-lg rounded-2xl border border-secondary/20 mx-auto">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-secondary">Inscription Restaurant</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Rejoignez-nous et gérez votre établissement</p>
        </div>
        <x-validation-errors class="mb-4"/>

        @session('status')
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ $value }}
        </div>
        @endsession

        @livewire('forms.restaurantSignup')

    </div>

    @if (languages()->count() > 1)
    <div class="mt-4">
        @livewire('shop.languageSwitcher')
    </div>
    @endif

</x-auth-layout>
