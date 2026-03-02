<div>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <nav class="flex mb-5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="#" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                {{ __('modules.cashier.title') }}
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">{{ __('modules.cashier.collectedOrders') }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ __('modules.cashier.collectedOrders') }}</h1>
                </div>
            </div>
            <div class="sm:flex">
                <div class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                    <form class="lg:pr-3" action="#" method="GET">
                        <label for="search" class="sr-only">{{ __('modules.cashier.search') }}</label>
                        <div class="relative mt-1 lg:w-64 xl:w-96">
                            <input type="text" wire:model.live.debounce.500ms="search" id="search" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="{{ __('modules.cashier.search') }}">
                        </div>
                    </form>
                    <div class="flex pl-0 mt-3 space-x-1 sm:pl-2 sm:mt-0">
                        <select wire:model.live="paymentMethodFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 mr-2">
                            <option value="">{{ __('modules.cashier.allPaymentMethods') }}</option>
                            <option value="cash">💵 {{ __('modules.cashier.cash') }}</option>
                            <option value="offline_payment">💵 {{ __('modules.cashier.offline') }}</option>
                            <option value="qr_code">📱 {{ __('modules.cashier.qr_code') }}</option>
                            <option value="paydunya">🟠 PayDunya</option>
                            <option value="mobile_money_orange">🟠 Orange Money</option>
                            <option value="mobile_money_wave">💙 Wave</option>
                            <option value="mobile_money_moov">🔵 Moov Money</option>
                            <option value="card">💳 {{ __('modules.cashier.card') }}</option>
                        </select>
                        <select wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">{{ __('modules.cashier.all') }}</option>
                            <option value="paid">{{ __('modules.cashier.paid') }}</option>
                            <option value="refunded">{{ __('modules.cashier.refunded') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    {{ __('modules.cashier.orderNumber') }}
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    {{ __('modules.cashier.date') }}
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    {{ __('modules.cashier.customer') }}
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    {{ __('modules.cashier.amount') }}
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    {{ __('modules.cashier.status') }}
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    {{ __('modules.cashier.paymentMethod') }}
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    {{ __('modules.cashier.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse($orders as $order)
                                @php
                                    $payment = $order->payments->last();
                                    $method = $payment ? $payment->payment_method : null;
                                    
                                    $paymentMethods = [
                                        'cash' => '💵 ' . __('modules.cashier.cash'),
                                        'mobile_money_orange' => '🟠 Orange Money',
                                        'mobile_money_wave' => '💙 Wave',
                                        'mobile_money_moov' => '🔵 Moov Money',
                                        'qr_code' => '📱 ' . __('modules.cashier.qr_code'),
                                        'card' => '💳 ' . __('modules.cashier.card'),
                                        'paydunya' => '🟠 PayDunya',
                                        'offline_payment' => '💵 ' . __('modules.cashier.offline'),
                                        'stripe' => '💳 Stripe',
                                        'paypal' => '💳 PayPal',
                                        'razorpay' => '💳 Razorpay',
                                    ];
                                    
                                    $methodDisplay = $method ? ($paymentMethods[$method] ?? ucfirst(str_replace('_', ' ', $method))) : '--';
                                    $isRefunded = $order->status === 'canceled' && $payment && $payment->is_refunded;
                                @endphp
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $order->show_formatted_order_number }}
                                    </td>
                                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $order->date_time->timezone(timezone())->translatedFormat('d/m/Y H:i') }}
                                    </td>
                                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $order->customer ? ($order->customer->name ?: __('modules.customer.walkin')) : __('modules.cashier.anonymousCustomer') }}
                                    </td>
                                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ currency_format($order->total, restaurant()->currency_id) }}
                                    </td>
                                    <td class="p-4 text-base font-normal text-gray-900 whitespace-nowrap dark:text-white">
                                        @if($isRefunded)
                                            <span class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-md border border-red-100 dark:bg-gray-700 dark:border-red-500 dark:text-red-400">{{ __('modules.cashier.refunded') }}</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-md border border-green-100 dark:bg-gray-700 dark:border-green-500 dark:text-green-400">{{ __('modules.order.paid') }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-base font-normal text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $methodDisplay }}
                                    </td>
                                    <td class="p-4 space-x-2 whitespace-nowrap">
                                        @if($order->status === 'paid' && !$isRefunded)
                                            <button wire:click="openRefundModal({{ $order->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                {{ __('modules.cashier.refund') }}
                                            </button>
                                        @elseif($isRefunded)
                                            <span class="text-sm text-gray-500 italic" title="{{ $payment->refund_reason ?? '' }}">
                                                {{ $payment->refunded_at ? $payment->refunded_at->timezone(timezone())->translatedFormat('d/m H:i') : '' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('modules.cashier.noData') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        {{ $orders->links(data: ['scrollTo' => false]) }}
    </div>

    <!-- Modal de Remboursement -->
    @if($showRefundModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900 bg-opacity-50">
            <div class="relative w-full max-w-md p-4 h-auto">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ __('modules.cashier.refundOrderPrefix') }} {{ $orderToRefund ? $orderToRefund->show_formatted_order_number : '' }}
                        </h3>
                        <button wire:click="$set('showRefundModal', false)" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-6">
                        @if($orderToRefund)
                            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('modules.cashier.amountToRefund') }} <strong class="text-gray-900 dark:text-white">{{ currency_format($orderToRefund->total, restaurant()->currency_id) }}</strong>
                            </div>
                            <form>
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="refundReason" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ __('modules.cashier.refundReasonLabel') }} <span class="text-red-500">*</span>
                                    </label>
                                    <textarea wire:model="refundReason" id="refundReason" rows="4" class="shadow-sm border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="{{ __('modules.cashier.refundReasonPlaceholder') }}"></textarea>
                                    @error('refundReason') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </form>
                            <div class="mt-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                                <strong>{{ __('modules.cashier.refundWarningTitle') }}</strong> {{ __('modules.cashier.refundWarningMessage') }}
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-700">
                        <button wire:click="processRefund" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                            {{ __('modules.cashier.confirmRefund') }}
                        </button>
                        <button wire:click="$set('showRefundModal', false)" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            {{ __('modules.cashier.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
