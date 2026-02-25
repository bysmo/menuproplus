@extends('layouts.app')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ __('modules.cashier.sessionValidation') }}</h1>
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('backend.cashier.index') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                            {{ __('modules.cashier.title') }}
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">{{ __('modules.cashier.sessionValidation') }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="p-4 bg-gray-50 dark:bg-gray-900">
    <!-- Sessions Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            {{ __('modules.cashier.sessionNumber') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            {{ __('modules.cashier.closedAt') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            {{ __('modules.cashier.cashier') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            {{ __('modules.cashier.totalSales') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            {{ __('modules.cashier.realBalance') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            {{ __('modules.cashier.discrepancy') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            {{ __('modules.cashier.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                #{{ $session->session_number }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900 dark:text-white">
                                {{ $session->closed_at->format('d/m/Y H:i') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900 dark:text-white">
                                {{ $session->openedByUser->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ number_format($session->total_sales, 0, ',', ' ') }} F
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ number_format($session->closing_balance, 0, ',', ' ') }} F
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            @if($session->discrepancy != 0)
                                <span class="text-sm font-bold text-red-600 dark:text-red-400">
                                    {{ number_format($session->discrepancy, 0, ',', ' ') }} F
                                </span>
                            @else
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                    0 F
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('backend.cashier.session', $session->id) }}" class="px-3 py-1.5 text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ __('modules.cashier.details') }}
                                </a>
                                <form action="{{ route('backend.cashier.session.validate', $session->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('modules.cashier.confirmValidation') }}')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-sm text-white bg-green-600 rounded hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                        {{ __('modules.cashier.validate') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">{{ __('modules.cashier.noSessionsToValidate') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sessions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection