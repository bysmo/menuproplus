<div x-data="{ activeTab: 'settings' }" class="mx-4">

    <!-- Tabs Navigation -->
    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4">
        <button @click="activeTab = 'settings'"
            :class="activeTab === 'settings'
                ?
                'border-b-2 border-blue-600 text-blue-600 dark:text-blue-500 dark:border-blue-500 font-semibold' :
                'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>@lang('modules.settings.disableLandingSite')</span>
            </div>
        </button>

        <button @click="activeTab = 'showdynamicPage'"
            :class="activeTab === 'showdynamicPage'
                ?
                'border-b-2 border-blue-600 text-blue-600 dark:text-blue-500 dark:border-blue-500 font-semibold' :
                'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                <span>@lang('modules.settings.showMoreWebPage')</span>
            </div>
        </button>
    </div>

    <!-- Settings Tab -->
    <div x-show="activeTab === 'settings'"
        class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.disableLandingSite')</h3>
        <x-help-text class="mb-6">@lang('modules.settings.disableLandingSiteHelp')</x-help-text>
        <form wire:submit="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div class="border p-4 rounded-lg dark:border-gray-500 space-y-4">
                    <div>
                        <x-label for="disableLandingSite">
                            <div class="flex items-center cursor-pointer">
                                <x-checkbox name="disableLandingSite" id="disableLandingSite"
                                    wire:model.live='disableLandingSite' />
                                <div class="ms-2">
                                    @lang('modules.settings.disableLandingSite')
                                </div>
                            </div>
                        </x-label>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            @lang('modules.settings.disableLandingSiteHelpDescription')
                        </p>
                    </div>
                    @if (!$disableLandingSite)
                        <div>
                            <x-label for="landingSiteType" :value="__('modules.settings.landingSiteType')" />
                            <x-select id="landingSiteType" class="mt-1 block w-full" wire:model.live="landingSiteType">
                                <option value="theme">@lang('modules.settings.theme')</option>
                                <option value="custom">@lang('modules.settings.custom')</option>
                            </x-select>
                            <x-input-error for="landingSiteType" class="mt-2" />
                        </div>

                        @if ($landingSiteType == 'custom')
                            <div>
                                <x-label for="landingSiteUrl" value="Landing Site URL" />
                                <x-input id="landingSiteUrl" class="block mt-1 w-full" type="text"
                                    wire:model='landingSiteUrl' />
                                <x-input-error for="landingSiteUrl" class="mt-2" />
                            </div>
                        @endif

                        @if ($landingSiteType == 'theme')
                            <div>
                                <x-label for="facebook" value="{{ __('modules.settings.facebook_link') }}" />
                                <x-input id="facebook" class="block mt-1 w-full" type="url"
                                    placeholder="{{ __('placeholders.facebookPlaceHolder') }}" autofocus
                                    wire:model='facebook' />
                                <x-input-error for="facebook" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="instagram" value="{{ __('modules.settings.instagram_link') }}" />
                                <x-input id="instagram" class="block mt-1 w-full" type="url"
                                    placeholder="{{ __('placeholders.instagramPlaceHolder') }}" autofocus
                                    wire:model='instagram' />
                                <x-input-error for="instagram" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="twitter" value="{{ __('modules.settings.twitter_link') }}" />
                                <x-input id="twitter" class="block mt-1 w-full" type="url"
                                    placeholder="{{ __('placeholders.twitterPlaceHolder') }}" autofocus
                                    wire:model='twitter' />
                                <x-input-error for="twitter" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="yelp" value="{{ __('modules.settings.yelp_link') }}" />
                                <x-input id="yelp" class="block mt-1 w-full" type="url"
                                    placeholder="{{ __('placeholders.yelpPlaceHolder') }}" autofocus
                                    wire:model='yelp' />
                                <x-input-error for="yelp" class="mt-2" />
                            </div>
                        @endif
                    @endif
                </div>

                <div class="border p-4 rounded-lg dark:border-gray-500 space-y-4">
                    <div>
                        <x-label for="metaTitle" value="{{ __('modules.settings.metaTitle') }}" />
                        <x-input id="metaTitle" class="block mt-1 w-full" type="text"
                            placeholder="{{ __('placeholders.metaTtilePlaceHolder') }}" autofocus
                            wire:model='metaTitle' />
                        <x-input-error for="metaTitle" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="metaKeyword" value="{{ __('modules.settings.metaKeyword') }}" />
                        <x-input id="metaKeyword" class="block mt-1 w-full" type="text"
                            placeholder="{{ __('placeholders.metaKeywordPlaceHolder') }}" autofocus
                            wire:model='metaKeyword' />
                        <x-input-error for="metaKeyword" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="metaDescription" value="{{ __('modules.settings.metaDescription') }}" />
                        <x-textarea id="metaDescription" class="block mt-1 w-full"
                            placeholder="{{ __('placeholders.metaDescriptionPlaceHolder') }}" autofocus
                            wire:model='metaDescription'></x-textarea>
                        <x-input-error for="metaDescription" class="mt-2" />
                    </div>
                </div>

                <div class="md:col-span-2 mt-4">
                    <x-button>@lang('app.save')</x-button>
                </div>
            </div>
        </form>
    </div>
    {{-- End --}}

    <!-- Dynamic Web Page Tab -->
    <div x-show="activeTab === 'showdynamicPage'"
        class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold dark:text-white">@lang('modules.settings.showMoreWebPage')</h3>
            <x-button class="m-3" type="button" wire:click="$toggle('addDyanamicMenuModal')">
                @lang('modules.settings.addDyanamicMenu')
            </x-button>
        </div>

        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th
                                class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.menuName')
                            </th>
                            <th
                                class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.menuSlug')
                            </th>

                            <th
                                class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.isActive')
                            </th>
                            <th
                                class="py-3 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                @lang('app.action')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse ($customMenu as $menu)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 even:bg-gray-50 dark:even:bg-gray-700">
                                <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $menu->menu_name }}
                                    <a href="{{ route('customMenu', $menu->menu_slug) }}" target="_blank" class="text-blue-600 hover:text-blue-700 dark:text-blue-500 dark:hover:text-blue-400 font-medium">
                                        <svg class="inline-block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $menu->menu_slug }}
                                </td>


                                <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="isActive-{{ $menu->id }}"
                                            wire:click="toggleMenuStatus({{ $menu->id }})"
                                            @if ($menu->is_active) checked @endif
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">

                                        <label for="isActive-{{ $menu->id }}"
                                            class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            @lang('app.active')
                                        </label>
                                    </div>
                                </td>


                                <td class="py-3 px-4 space-x-2 whitespace-nowrap text-right">
                                    <x-secondary-button-table wire:click='showEditDynamicMenu({{ $menu->id }})'
                                        wire:key='menu-edit-{{ $menu->id . microtime() }}'>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                            </path>
                                            <path fill-rule="evenodd"
                                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        @lang('app.update')
                                    </x-secondary-button-table>


                                    <button type="button"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        wire:click="confirmDeleteMenu({{ $menu->id }})">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        @lang('app.delete')
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="py-3 px-4 space-x-6 dark:text-gray-400" colspan="4">
                                    @lang('messages.noCustomMenu')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>


        <div wire:key='custom-menu-paginate-{{ microtime() }}'
            class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center mb-4 sm:mb-0 w-full">
                {{ $customMenu->links() }}
            </div>
        </div>

    </div>
    {{-- End --}}

    <x-right-modal wire:model.live="addDyanamicMenuModal">
        <x-slot name="title">
            {{ __('modules.settings.addDynamicMenu') }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.add-dyanamic-menu')
        </x-slot>

        {{-- <x-slot name="footer">
            <x-secondary-button wire:click="hideAddDyanamicMenu" wire:loading.attr="disabled">
                {{ __('app.close') }}
            </x-secondary-button>
        </x-slot> --}}
    </x-right-modal>

    <x-right-modal wire:model.live="showEditDynamicMenuModal">
        <x-slot name="title">
            {{ __('modules.settings.editDynamicMenu') }}
        </x-slot>

        <x-slot name="content">
            @if ($menuId)
                @livewire('forms.edit-dyanamic-menu', ['menuId' => $menuId], key(str()->random(50)))
            @endif
        </x-slot>

        {{-- <x-slot name="footer">
            <x-secondary-button wire:click="$set('closeEditMenu', null)" wire:loading.attr="disabled">
                {{ __('app.close') }}
            </x-secondary-button>
        </x-slot> --}}
    </x-right-modal>


    <x-dialog-modal wire:model="menuIdToDelete">
        <x-slot name="title">
            @lang('modules.settings.deleteDyanamicMenu')
        </x-slot>

        <x-slot name="content">
            @lang('messages.confrmDeleteDyanamicMenu')
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <x-secondary-button wire:click="$set('menuIdToDelete', null)" class="px-4 py-2 text-sm">
                    @lang('app.cancel')
                </x-secondary-button>

                <x-danger-button wire:click="deleteMenu" class="px-4 py-2 text-sm">
                    @lang('app.delete')
                </x-danger-button>
            </div>
        </x-slot>
    </x-dialog-modal>



</div>
