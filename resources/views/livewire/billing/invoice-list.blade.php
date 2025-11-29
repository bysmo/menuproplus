<div>

        <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
                <div class="mb-4">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('menu.billing')</h1>
                </div>
                <div class="items-center justify-between block sm:flex ">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="#" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <x-input id="menu_name" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.searchPayments') }}" wire:model.live.debounce.500ms="search"  />
                            </div>
                        </form>
                    </div>

                    {{-- <a href="{{ route('payments.export') }}" class="inline-flex items-center justify-center w-1/2 px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path></svg>
                        @lang('app.export')
                    </a> --}}
                </div>
            </div>
        </div>

        @if(isset($earningsStats) && $earningsStats['total_invoices'] > 0)
            <div class="px-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('modules.dashboard.totalRevenue')</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Total Earnings -->
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('modules.dashboard.totalRevenue')</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ global_currency_format($earningsStats['total_amount'], global_setting()->default_currency_id) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Month Earnings -->
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('modules.dashboard.salesThisMonth')</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ global_currency_format($earningsStats['current_month_earnings'], global_setting()->default_currency_id) }}
                                </p>
                                @if($earningsStats['last_month_earnings'] > 0)
                                    @php
                                        $monthlyChange = (($earningsStats['current_month_earnings'] - $earningsStats['last_month_earnings']) / $earningsStats['last_month_earnings']) * 100;
                                    @endphp
                                    <p class="text-xs mt-1 {{ $monthlyChange >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $monthlyChange >= 0 ? '+' : '' }}{{ number_format($monthlyChange, 1) }}% @lang('modules.dashboard.sincePreviousMonth')
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Total Invoices -->
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('modules.billing.total')</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $earningsStats['total_invoices'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">@lang('modules.billing.receipt')</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Restaurants -->
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('modules.dashboard.totalRestaurants')</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $earningsStats['total_restaurants'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">@lang('modules.dashboard.totalPaidRestaurantCount')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <livewire:billing.invoice-table :search='$search' key='payment-table-{{ microtime() }}' />

</div>
