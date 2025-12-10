<div>
    {{-- Header Section --}}
    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Gestion des Ardoises</h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                Tableau de bord
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Ardoises</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            {{-- Filters Section --}}
            <div class="sm:flex">
                <div class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                    <form class="lg:pr-3" action="#" method="GET">
                        <label for="slates-search" class="sr-only">Rechercher</label>
                        <div class="relative mt-1 lg:w-64 xl:w-96">
                            <x-input
                                id="slates-search"
                                class="block mt-1 w-full"
                                type="text"
                                placeholder="Rechercher (code, UUID, client...)"
                                wire:model.live.debounce.300ms="search"
                            />
                        </div>
                    </form>
                </div>

                <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                    {{-- Status Filter --}}
                    <select wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="all">Tous les statuts</option>
                        <option value="active">Actif</option>
                        <option value="paid">Payé</option>
                        <option value="expired">Expiré</option>
                    </select>

                    {{-- Date From --}}
                    <x-input
                        type="date"
                        wire:model.live="dateFrom"
                        placeholder="Date début"
                    />

                    {{-- Date To --}}
                    <x-input
                        type="date"
                        wire:model.live="dateTo"
                        placeholder="Date fin"
                    />

                    {{-- Reset Button --}}
                    <x-secondary-button wire:click="resetFilters">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                        </svg>
                        Réinitialiser
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 px-4 pt-4 xl:grid-cols-4 2xl:grid-cols-4">
        {{-- Total Ardoises --}}
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="flex items-center justify-center w-12 h-12 text-lg font-bold text-blue-600 bg-blue-100 rounded-lg dark:bg-blue-900 dark:text-blue-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Ardoises</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $slates->total() }}</p>
                </div>
            </div>
        </div>

        {{-- Payées --}}
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="flex items-center justify-center w-12 h-12 text-lg font-bold text-green-600 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Payées</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $slates->where('status', 'paid')->count() }}</p>
                </div>
            </div>
        </div>

        {{-- En attente --}}
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="flex items-center justify-center w-12 h-12 text-lg font-bold text-yellow-600 bg-yellow-100 rounded-lg dark:bg-yellow-900 dark:text-yellow-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">En attente</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $slates->where('status', 'pending_verification')->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Montant Total --}}
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="flex items-center justify-center w-12 h-12 text-lg font-bold text-purple-600 bg-purple-100 rounded-lg dark:bg-purple-900 dark:text-purple-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                </div>
                <div class="flex-1 w-0 ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Montant Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ currency_format($slates->sum('total_amount'), restaurant()->currency_id) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="flex flex-col px-4 pt-4">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    Code
                                </th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    Client
                                </th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    Commandes
                                </th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    Total
                                </th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    Payé
                                </th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    En attente
                                </th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    Reste
                                </th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    Statut
                                </th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    Date
                                </th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse ($slates as $slate)
                                @php
                                    $paymentStatus = $this->getPaymentStatus($slate);
                                @endphp
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="py-2.5 px-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="font-semibold text-primary-600 dark:text-primary-500">#{{ $slate->code }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($slate->device_uuid, 12) }}</div>
                                    </td>
                                    <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                        @if($slate->customer)
                                            <div class="font-medium">{{ $slate->customer->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $slate->customer->phone }}</div>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                                Anonyme
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-sm whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                            {{ $slate->orders->count() }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 text-sm font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ currency_format($slate->total_amount, restaurant()->currency_id) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-sm font-medium text-green-600 whitespace-nowrap dark:text-green-400">
                                        {{ currency_format($slate->paid_amount, restaurant()->currency_id) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-sm font-medium text-yellow-600 whitespace-nowrap dark:text-yellow-400">
                                        {{ currency_format($slate->pending_payment, restaurant()->currency_id) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-sm font-medium text-red-600 whitespace-nowrap dark:text-red-400">
                                        {{ currency_format($slate->remaining_amount, restaurant()->currency_id) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-sm whitespace-nowrap">
                                        @if($paymentStatus['class'] == 'bg-success')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                                                {{ $paymentStatus['label'] }}
                                            </span>
                                        @elseif($paymentStatus['class'] == 'bg-warning')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-300">
                                                {{ $paymentStatus['label'] }}
                                            </span>
                                        @elseif($paymentStatus['class'] == 'bg-danger')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300">
                                                {{ $paymentStatus['label'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                                {{ $paymentStatus['label'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                        <div>{{ $slate->created_at->format('d/m/Y H:i') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $slate->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                        {{-- Détails --}}
                                        <x-secondary-button-table
                                            wire:click.prevent="$dispatch('show-slate-details', { slateId: {{ $slate->id }} })"
                                            title="Détails"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Détails
                                        </x-secondary-button-table>

                                        {{-- Imprimer --}}
                                        <x-secondary-button-table
                                            onclick="window.open('{{ route('slates.print-invoice', $slate->id) }}', '_blank')"
                                            title="Imprimer la facture"
                                        >
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </x-secondary-button-table>
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="py-8 px-4 text-center text-gray-900 dark:text-gray-400" colspan="10">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-base font-medium">Aucune ardoise trouvée</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center mb-4 sm:mb-0 w-full">
            {{ $slates->links() }}
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 z-50">
            <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    <span class="font-medium">Succès!</span> {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 z-50">
            <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    <span class="font-medium">Erreur!</span> {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    {{-- Loading Indicator --}}
    <div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="px-5 py-3 text-white bg-gray-800 rounded-lg">
            <svg class="inline w-8 h-8 mr-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
            </svg>
            <span class="text-lg font-medium">Chargement...</span>
        </div>
    </div>

    {{-- Slate Details Modal Component --}}
    @livewire('shop.slate-details')
</div>
