<x-auth-layout>
    <div class="w-full sm:max-w-md mt-8 px-8 py-6 bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg shadow-lg rounded-2xl border border-secondary/20 mx-auto">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-secondary">Inscription</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Créez votre compte</p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('app.name') }}" />
                <x-input id="name" class="block mt-1 w-full focus:ring-secondary focus:border-secondary" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('app.email') }}" />
                <x-input id="email" class="block mt-1 w-full focus:ring-secondary focus:border-secondary" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('app.password') }}" />
                <x-input-password id="password" class="block mt-1 w-full focus:ring-secondary focus:border-secondary" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('modules.profile.confirmPassword') }}" />
                <x-input-password id="password_confirmation" class="block mt-1 w-full focus:ring-secondary focus:border-secondary" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-secondary hover:text-secondary/80" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="button bg-secondary hover:bg-secondary/80">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </div>
</x-auth-layout>
