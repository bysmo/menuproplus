@extends('layouts.app')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('backend.cashier.index') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            {{ __('modules.cashier.title') }}
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">{{ __('modules.cashier.sessionDetails') }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                {{ __('modules.cashier.sessionDetails') }}
            </h1>
        </div>
    </div>
</div>

<div class="p-4 bg-gray-50 dark:bg-gray-900">
    <!-- Session Info Card -->
    <div class="mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('modules.cashier.sessionNumber') }}</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">#{{ $session->session_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('modules.cashier.openedBy') }}</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $session->openedByUser->name }}</p>
                    <p class="text-xs text-gray-400">{{ $session->opened_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($session->closed_at)
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('modules.cashier.closedBy') }}</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $session->closedByUser->name }}</p>
                    <p class="text-xs text-gray-400">{{ $session->closed_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('modules.cashier.status') }}</p>
                    @if($session->status == 'opened')
                        <span class="inline-flex px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                            {{ __('modules.cashier.sessionRunning') }}
                        </span>
                    @elseif($session->status == 'closed')
                        <span class="inline-flex px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">
                            {{ __('modules.cashier.closed') }}
                        </span>
                    @elseif($session->status == 'validated')
                        <span class="inline-flex px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                            {{ __('modules.cashier.validated') }}
                        </span>
                    @endif
                    
                    @if($session->validated_at)
                        <p class="text-xs text-gray-400 mt-1">
                            {{ __('modules.cashier.validated_by') }}: {{ $session->validatedByUser->name }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $session->validated_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary Card -->
    <div class="mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('modules.cashier.financialSummary') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
                    <p class="text-sm text-blue-600 dark:text-blue-400">{{ __('modules.cashier.openingBalance') }}</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                        {{ number_format($session->opening_balance, 0, ',', ' ') }} F
                    </p>
                </div>
                
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:border-green-800">
                    <p class="text-sm text-green-600 dark:text-green-400">{{ __('modules.cashier.totalSales') }}</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                        {{ number_format($session->total_sales, 0, ',', ' ') }} F
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ $session->total_transactions }} {{ __('modules.cashier.transactions') }}</p>
                </div>
                
                <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg dark:bg-purple-900/20 dark:border-purple-800">
                    <p class="text-sm text-purple-600 dark:text-purple-400">{{ __('modules.cashier.expectedBalance') }}</p>
                    <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">
                        {{ number_format($session->expected_balance, 0, ',', ' ') }} F
                    </p>
                </div>
                
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:border-yellow-800">
                    <p class="text-sm text-yellow-600 dark:text-yellow-400">{{ __('modules.cashier.realBalance') }}</p>
                    <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">
                        {{ number_format($session->closing_balance, 0, ',', ' ') }} F
                    </p>
                </div>
                
                <div class="p-4 {{ $session->discrepancy != 0 ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800' : 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' }} border rounded-lg">
                    <p class="text-sm {{ $session->discrepancy != 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ __('modules.cashier.discrepancy') }}
                    </p>
                    <p class="text-2xl font-bold {{ $session->discrepancy != 0 ? 'text-red-700 dark:text-red-300' : 'text-green-700 dark:text-green-300' }}">
                        {{ number_format($session->discrepancy, 0, ',', ' ') }} F
                    </p>
                </div>
            </div>

            @if($session->discrepancy_justification)
            <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('modules.cashier.discrepancyJustification') }}:</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $session->discrepancy_justification }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="p-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('modules.cashier.transactions') }}</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('modules.cashier.time') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('modules.cashier.order') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('modules.cashier.type') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('modules.cashier.method') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                {{ __('modules.cashier.amount') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($session->transactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                {{ $transaction->created_at->format('H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                #{{ $transaction->order->order_number ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($transaction->type == 'sale')
                                    <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                                        {{ __('modules.cashier.sale') }}
                                    </span>
                                @elseif($transaction->type == 'expense')
                                    <span class="px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300">
                                        {{ __('modules.cashier.expense') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                {{ __('modules.cashier.' . $transaction->payment_method) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">
                                {{ number_format($transaction->amount, 0, ',', ' ') }} F
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('modules.cashier.noTransactions') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        @if($session->status == 'closed')
        <form action="{{ route('backend.cashier.session.validate', $session->id) }}" method="POST" onsubmit="return confirm('{{ __('modules.cashier.confirmValidation') }}')">
            @csrf
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                {{ __('modules.cashier.validateSession') }}
            </button>
        </form>
        @endif
        
        <a href="{{ route('backend.cashier.print-opening', $session->id) }}" target="_blank" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
            {{ __('modules.cashier.printOpening') }}
        </a>
        
        @if($session->closed_at)
        <a href="{{ route('backend.cashier.print-closing', $session->id) }}" target="_blank" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
            {{ __('modules.cashier.printClosing') }}
        </a>
        @endif
    </div>
</div>
@endsection