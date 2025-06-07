<div
    class="mx-4 p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">

    <x-cron-message :modal="false" :showModal="false" />

    <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.appSettings')</h3>
    <form wire:submit.prevent="submitForm">
        <div class="grid gap-6">
            <div class="grid lg:grid-cols-3 gap-6">

                <div>
                    <x-label for="appName" value="{{ __('modules.settings.appName') }}" />
                    <x-input id="appName" class="block mt-1 w-full" type="text" autofocus wire:model='appName' />
                    <x-input-error for="appName" class="mt-2" />
                </div>


                <div>
                    <x-label for="defaultLanguage" value="{{ __('modules.settings.defaultLanguage') }}" />
                    <x-select id="defaultLanguage" class="block mt-1 w-full" wire:model='defaultLanguage'>
                        @foreach ($languageSettings as $item)
                            <option value="{{ $item->language_code }}">{{  isset(\App\Models\LanguageSetting::LANGUAGES_TRANS[$item->language_code]) ? \App\Models\LanguageSetting::LANGUAGES_TRANS[$item->language_code] . ' (' . $item->language_name . ')' : $item->language_name }}</option>
                        @endforeach
                    </x-select>

                    <x-input-error for="defaultLanguage" class="mt-2" />
                </div>

                <div>
                    <x-label for="defaultCurrency" value="{{ __('modules.settings.defaultCurrency') }}" />
                    <x-select id="defaultCurrency" class="block mt-1 w-full" wire:model='defaultCurrency'>
                        @foreach ($globalCurrencies as $item)
                            <option value="{{ $item->id }}">{{ $item->currency_name . ' (' . $item->currency_code . ')' }}</option>
                        @endforeach
                    </x-select>

                    <x-input-error for="defaultCurrency" class="mt-2" />
                </div>
            </div>

            <div>
                <x-label for="mapApiKey" :value="__('modules.delivery.mapApiKey')" />
                <x-input id="mapApiKey" class="block mt-1 w-full" type="text" wire:model='mapApiKey' placeholder="{{ __('placeholders.enterGoogleMapApiKey')}}" />
                <x-input-error for="mapApiKey" class="mt-2" />
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    @lang('modules.settings.getGoogleMapApiKeyHelp') 
                    <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" class="text-skin-base hover:text-skin-base/[.8] dark:text-skin-base dark:hover:text-skin-base/[.8]">
                        @lang('modules.settings.learnMore')
                    </a>
                </p>
            </div>

            <div >
                <x-label for="requiresApproval">
                    <div class="flex items-start space-x-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors duration-200 dark:border-gray-700 dark:hover:bg-gray-700/50">
                        <div class="flex-shrink-0">
                            <x-checkbox 
                                class="mt-1 h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700" 
                                name="requiresApproval" 
                                id="requiresApproval" 
                                wire:model='requiresApproval' 
                            />
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                @lang('modules.settings.restaurantRequiresApproval')
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @lang('modules.settings.restaurantRequiresApprovalInfo')
                            </p>
                        </div>
                    </div>
                </x-label>
            </div>

            <div>
                <x-button>@lang('app.save')</x-button>
            </div>
        </div>
    </form>

</div>
