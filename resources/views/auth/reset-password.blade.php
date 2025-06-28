<x-auth-layout>
    <div class="w-full sm:max-w-md mt-8 px-8 py-6 bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg shadow-lg rounded-2xl border border-secondary/20 mx-auto">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-secondary">Réinitialiser le mot de passe</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Entrez et confirmez votre nouveau mot de passe</p>
        </div>
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-label for="email" value="{{ __('app.email') }}" />
                <x-input id="email" class="block mt-1 w-full focus:ring-secondary focus:border-secondary" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('app.password') }}" />
                <x-input id="password" class="block mt-1 w-full focus:ring-secondary focus:border-secondary" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('modules.profile.confirmPassword') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full focus:ring-secondary focus:border-secondary" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="button bg-secondary hover:bg-secondary/80">
                    {{ __('Reset Password') }}
                </x-button>
            </div>
        </form>
    </div>
</x-auth-layout>
