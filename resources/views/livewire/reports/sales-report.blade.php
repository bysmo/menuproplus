<div>
    @php
        $paymentGateway = \App\Models\PaymentGatewayCredential::select('stripe_status', 'razorpay_status')
            ->where('restaurant_id', restaurant()->id)
            ->first();
    @endphp
    <!-- Header Section -->
    <div class="p-4 bg-white dark:bg-gray-800">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('menu.salesReport')</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@lang('modules.report.salesReportMessage')</p>
        </div>
        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Total Sales Card -->
            <div class="p-4 bg-skin-base/10 rounded-xl shadow-sm dark:bg-skin-base/10 border border-skin-base/30 dark:border-skin-base/40">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-skin-base dark:text-skin-base">@lang('modules.report.totalSales')</h3>
                <div class="p-2 bg-skin-base/10 rounded-lg dark:bg-skin-base/10">
                    <svg class="w-4 h-4 text-skin-base dark:text-skin-base" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 13.75c0 .97.75 1.75 1.67 1.75h1.88c.8 0 1.45-.68 1.45-1.53 0-.91-.4-1.24-.99-1.45l-3.01-1.05c-.59-.21-.99-.53-.99-1.45 0-.84.65-1.53 1.45-1.53h1.88c.92 0 1.67.78 1.67 1.75M12 7.5v9"/><path d="M22 12c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2m10 4V2h-4m-1 5 5-5"/></g></svg>
                </div>
            </div>
            <p class="text-3xl break-words font-bold text-skin-base dark:text-skin-base">
                {{ currency_format($menuItems->sum('total_amount')) }}
            </p>
            <p class="mt-2 text-sm text-skin-base dark:text-skin-base">
                {{ $menuItems->sum('total_orders') }} @lang('modules.report.orders')
            </p>
            </div>

            <!-- Total Charges Card -->
            <div class="p-4 bg-blue-50 rounded-xl shadow-sm dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-blue-600 dark:text-blue-400">@lang('modules.report.totalCharges')</h3>
                    <div class="p-2 bg-blue-100 rounded-lg dark:bg-blue-900/50">
                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0"/></svg>
                    </div>
                </div>
                <p class="text-3xl break-words font-bold text-gray-800 dark:text-gray-100">
                    {{ currency_format($charges->sum(fn($charge) => $menuItems->sum(fn($item) => $item['charges'][$charge->charge_name] ?? 0))) }}
                </p>
            </div>

            <!-- Total Taxes Card -->
            <div class="p-4 bg-red-50 rounded-xl shadow-sm dark:bg-red-900/10 border border-red-100 dark:border-red-800">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-red-600 dark:text-red-400">@lang('modules.report.totalTaxes')</h3>
                    <div class="p-2 bg-red-100 rounded-lg dark:bg-red-900/50">
                        <svg class="w-4 h-4 text-red-500 dark:text-red-400"  width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 5 5 19m2-9a3 3 0 1 0 0-6 3 3 0 0 0 0 6m10 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
                <p class="text-3xl break-words font-bold text-gray-800 dark:text-gray-100">
                    {{ currency_format($menuItems->sum(fn($item) => collect($item['taxes'])->sum())) }}
                </p>
            </div>

            <!-- Cash Payments Card -->
            <div class="p-4 bg-emerald-50 rounded-xl shadow-sm dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-emerald-600 dark:text-emerald-400">@lang('modules.report.cashPayments')</h3>
                <div class="p-2 bg-emerald-100 rounded-lg dark:bg-emerald-900/50">
                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2m2 4h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2m7-5a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/></svg>
                </div>
            </div>
            <p class="text-3xl break-words font-bold text-gray-800 dark:text-gray-100">
                {{ currency_format($menuItems->sum('cash_amount')) }}
            </p>
            </div>

            <!-- Digital Payments Card -->
            <div class="p-4 bg-violet-50 rounded-xl shadow-sm dark:bg-violet-900/10 border border-violet-100 dark:border-violet-800">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-violet-600 dark:text-violet-400">@lang('modules.report.digitalPayments')</h3>
                <div class="p-2 bg-violet-100 rounded-lg dark:bg-violet-900/50">
                    <svg class="w-4 h-4 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3"/></svg>
                </div>
            </div>
            <p class="text-3xl break-words font-bold text-gray-800 dark:text-gray-100">
                {{ currency_format($menuItems->sum('card_amount') + $menuItems->sum('upi_amount')) }}
            </p>
            <p class="mt-2 text-sm text-violet-400">
                @lang('modules.order.upi'): {{ currency_format($menuItems->sum('upi_amount')) }} + @lang('modules.order.card'): {{ currency_format($menuItems->sum('card_amount')) }}
            </p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="flex flex-wrap justify-between items-center gap-4 p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
            <div class="lg:flex items-center mb-4 sm:mb-0">
                <form class="sm:pr-3" action="#" method="GET">

                    <div class="lg:flex gap-2 items-center">
                        <x-select id="dateRangeType" class="block w-full sm:w-fit mb-2 lg:mb-0" wire:model="dateRangeType" wire:change="setDateRange">
                            <option value="today">@lang('app.today')</option>
                            <option value="currentWeek">@lang('app.currentWeek')</option>
                            <option value="lastWeek">@lang('app.lastWeek')</option>
                            <option value="last7Days">@lang('app.last7Days')</option>
                            <option value="currentMonth">@lang('app.currentMonth')</option>
                            <option value="lastMonth">@lang('app.lastMonth')</option>
                            <option value="currentYear">@lang('app.currentYear')</option>
                            <option value="lastYear">@lang('app.lastYear')</option>
                        </x-select>

                        <div id="date-range-picker" date-rangepicker class="flex items-center w-full">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20zM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2"/></svg>
                                </div>
                                <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.change='startDate' placeholder="@lang('app.selectStartDate')">
                            </div>
                            <span class="mx-4 text-gray-500 dark:text-gray-100">@lang('app.to')</span>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20zM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2"/></svg>
                                </div>
                                <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.live='endDate' placeholder="@lang('app.selectEndDate')">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <a href="javascript:;" wire:click='exportReport'
                class="inline-flex items-center  w-1/2 px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.414A2 2 0 0 0 15.414 6L12 2.586A2 2 0 0 0 10.586 2zm5 6a1 1 0 1 0-2 0v3.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l3-3a1 1 0 0 0-1.414-1.414L11 11.586z" clip-rule="evenodd"/></svg>
                @lang('app.export')
            </a>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 p-4">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
                <th class="p-4 text-xs font-medium tracking-wider text-left text-gray-600 uppercase dark:text-gray-300">
                @lang('app.date')
                </th>
                <th class="p-4 text-xs font-medium tracking-wider text-center text-gray-600 uppercase dark:text-gray-300">
                @lang('modules.report.totalOrders')
                </th>

                <!-- Charges Column Group -->
                @if(count($charges) > 0)
                <th colspan="{{ count($charges) }}" class="p-4 text-xs font-medium tracking-wider text-center text-gray-600 uppercase dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">
                    @lang('modules.order.extraCharges')
                </th>
                @endif

                <!-- Taxes Column Group -->
                @if(count($taxes) > 0)
                <th colspan="{{ count($taxes) }}" class="p-4 text-xs font-medium tracking-wider text-center text-gray-600 uppercase dark:text-gray-300 bg-red-50 dark:bg-red-900/20">
                    @lang('modules.order.taxes')
                </th>
                @endif

                <!-- Payment Methods Column Group -->
                <th colspan="{{ 3 + (isset($paymentGateway) && $paymentGateway->stripe_status ? 1 : 0) + (isset($paymentGateway) && $paymentGateway->razorpay_status ? 1 : 0) }}" class="p-4 text-xs font-medium tracking-wider text-center text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                    @lang('modules.report.paymentMethods')
                </th>

                <th   class="p-4 text-xs font-bold tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                @lang('modules.order.tip')
                </th>
                <th class="p-4 text-xs font-bold tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                @lang('modules.order.total')
                </th>
            </tr>
            <tr>
                <th></th>
                <th></th>

                <!-- Charges Subheaders -->
                @foreach ($charges as $charge)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">
                    {{ $charge->charge_name }}
                </th>
                @endforeach

                <!-- Taxes Subheaders -->
                @foreach ($taxes as $tax)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-red-50 dark:bg-red-900/20">
                    {{ $tax->tax_name }} ({{ $tax->tax_percent }}%)
                </th>
                @endforeach
                <!-- Payment Methods Subheaders -->
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                @lang('modules.order.cash')
                </th>
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                @lang('modules.order.upi')
                </th>
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                @lang('modules.order.card')
                </th>
                @if($paymentGateway->razorpay_status)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                    @lang('modules.order.razorpay')
                </th>
                @endif
                @if($paymentGateway->stripe_status)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                    @lang('modules.order.stripe')
                </th>
                @endif
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
            @forelse ($menuItems as $item)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-4 text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                {{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}
                </td>
                <td class="p-4 text-sm text-center text-gray-900 dark:text-white">
                {{ $item['total_orders'] }}
                </td>

                @foreach ($charges as $charge)
                <td class="p-4 text-sm font-normal text-right text-gray-900 dark:text-gray-100 bg-blue-50/50 dark:bg-blue-900/10">
                {{ currency_format($item['charges'][$charge->charge_name] ?? 0) }}
                </td>
                @endforeach

                @foreach ($taxes as $tax)
                <td class="p-4 text-sm font-normal text-right text-gray-900 dark:text-gray-100 bg-red-50/50 dark:bg-red-900/10">
                {{ currency_format($item['taxes'][$tax->tax_name] ?? 0) }}
                </td>
                @endforeach

                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                {{ currency_format($item['cash_amount']) }}
                </td>
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                {{ currency_format($item['upi_amount']) }}
                </td>
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                {{ currency_format($item['card_amount']) }}
                </td>
                @if($paymentGateway->razorpay_status)
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                    {{ currency_format($item['razorpay_amount']) }}
                </td>
                @endif
                @if($paymentGateway->stripe_status)
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                    {{ currency_format($item['stripe_amount']) }}
                </td>
                @endif
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                {{ currency_format($item['tip_amount']) }}
                </td>
                <td class="p-4 text-sm font-bold text-right text-gray-900 dark:text-white">
                {{ currency_format($item['total_amount']) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="p-4 text-sm text-center text-gray-500 dark:text-gray-400">
                @lang('messages.noItemAdded')
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @script
    <script>
        const datepickerEl1 = document.getElementById('datepicker-range-start');

        datepickerEl1.addEventListener('changeDate', (event) => {
            $wire.dispatch('setStartDate', { start: datepickerEl1.value });
        });

        const datepickerEl2 = document.getElementById('datepicker-range-end');

        datepickerEl2.addEventListener('changeDate', (event) => {
            $wire.dispatch('setEndDate', { end: datepickerEl2.value });
        });
    </script>
    @endscript
</div>
