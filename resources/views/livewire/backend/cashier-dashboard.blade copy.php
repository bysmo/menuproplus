<div>
    {{-- Header Section --}}
<div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ __('modules.cashier.sessionDetails') }}</h1>
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
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">{{ __('modules.cashier.sessionDetails') }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
    
    @if($activeSession)
        {{-- Bandeau d'information de session active - VISIBLE PAR TOUS --}}
        <div class="px-4 pt-4 pb-2">
            <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 rounded-lg dark:from-blue-900/30 dark:to-blue-800/30 dark:border-blue-400">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        {{-- Avatar du caissier --}}
                        <div class="flex-shrink-0">
                            <img class="w-16 h-16 rounded-full border-2 border-blue-500 shadow-lg" 
                                 src="{{ $activeSession->openedByUser->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($activeSession->openedByUser->name) }}" 
                                 alt="{{ $activeSession->openedByUser->name }}">
                        </div>
                        
                        {{-- Informations de session --}}
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                                    <span class="w-2 h-2 mr-2 bg-green-500 rounded-full animate-pulse"></span>
                                    {{ __('modules.cashier.sessionRunning') }}
                                </span>
                                <span class="ml-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ __('modules.cashier.session') }} <strong class="text-blue-700 dark:text-blue-300">#{{ $activeSession->session_number }}</strong>
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                {{ __('modules.cashier.sessionOpenedBy') }}: {{ $activeSession->openedByUser->name }}
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('modules.cashier.openingDate') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $activeSession->opened_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('modules.cashier.openingTime') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $activeSession->opened_at->format('H:i:s') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('modules.cashier.sessionDuration') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $activeSession->opened_at->diffForHumans(['parts' => 2]) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Boutons d'action - CONDITIONNELS --}}
                    <div class="flex-shrink-0 ml-4">
                        @if($activeSession->opened_by === auth()->id())
                            {{-- L'utilisateur connecté a ouvert la session - PEUT FERMER --}}
                            <button 
                                wire:click="closeSessionModal"
                                class="flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                            >
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('modules.cashier.closeSession') }}
                            </button>
                        @else
                            {{-- Autre utilisateur - NE PEUT PAS FERMER --}}
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900/30 dark:border-yellow-800">
                                <div class="flex items-center text-yellow-800 dark:text-yellow-300">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-xs font-medium">{{ __('modules.cashier.onlySessionOwnerCanClose') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques de session - VISIBLE PAR TOUS --}}
        <div class="px-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Montant d'ouverture -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 dark:bg-green-900">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <h3 class="ml-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('modules.cashier.initialCash') }}</h3>
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($activeSession->opening_balance, 0, ',', ' ') }} F
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('modules.cashier.openingBalance') }}</p>
                </div>

                <!-- Total des ventes -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                                </svg>
                            </span>
                            <h3 class="ml-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('modules.cashier.totalSales') }}</h3>
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($activeSession->total_sales, 0, ',', ' ') }} F
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $activeSession->transactions()->where('type', 'sale')->count() }} {{ __('modules.cashier.transactions') }}
                    </p>
                </div>

                <!-- Total des dépenses -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-red-100 dark:bg-red-900">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <h3 class="ml-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('modules.cashier.totalExpenses') }}</h3>
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($expensesAmount, 0, ',', ' ') }} F
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('modules.cashier.expensesToday') }}</p>
                </div>

                <!-- Solde attendu -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <h3 class="ml-3 text-sm font-medium text-gray-900 dark:text-white">{{ __('modules.cashier.expectedBalance') }}</h3>
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($activeSession->expected_balance, 0, ',', ' ') }} F
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('modules.cashier.atSessionEnd') }}</p>
                </div>
            </div>
        </div>

        {{-- Filtres et actions --}}
        <div class="px-4 mb-4">
            <div class="sm:flex sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchTerm" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" 
                            placeholder="{{ __('modules.cashier.search') }}"
                        >
                    </div>
                
                    <div>
                        <select wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option value="pending">{{ __('modules.cashier.pending') }}</option>
                            <option value="completed">{{ __('modules.cashier.completed') }}</option>
                            <option value="all">{{ __('modules.cashier.all') }}</option>
                        </select>
                    </div>
                </div>
            
                <div class="flex space-x-2">
                    <a 
                        href="{{ route('backend.cashier.print-opening', $activeSession->id) }}" 
                        target="_blank"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center"
                    >
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('modules.cashier.print') }}
                    </a>
                    
                    @if($activeSession->opened_by === auth()->id())
                        <button 
                            wire:click="closeSessionModal"
                            class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800 flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('modules.cashier.closeSession') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Table des paiements en attente --}}
        <div class="px-4">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('modules.cashier.order') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('modules.cashier.customer') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('modules.cashier.amount') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('modules.cashier.paymentMethod') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('modules.cashier.status') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('modules.cashier.date') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('modules.cashier.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingPayments as $payment)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <strong class="text-primary-600 dark:text-primary-500">#{{ $payment->order->order_number }}</strong>
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->order->customer)
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $payment->order->customer->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->order->customer->phone }}</div>
                                        </div>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ __('modules.cashier.anonymousCustomer') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <strong>{{ number_format($payment->amount, 0, ',', ' ') }} F</strong>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                        {{ __('modules.cashier.' . $payment->payment_method) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">{{ __('modules.cashier.pending') }}</span>
                                    @elseif($payment->status === 'completed')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">{{ __('modules.cashier.completed') }}</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ $payment->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $payment->created_at->format('d/m/Y H:i') }}
                                    <br>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $payment->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->status === 'pending')
                                        <button 
                                            wire:click="selectPayment({{ $payment->id }})"
                                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-xs px-3 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 flex items-center" 
                                            title="{{ __('modules.cashier.collectPayment') }}"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('modules.cashier.collectPayment') }}
                                        </button>
                                    @else
                                        <button class="text-gray-400 bg-gray-100 border border-gray-200 font-medium rounded-lg text-xs px-3 py-2 cursor-not-allowed flex items-center" disabled>
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('modules.cashier.alreadyCollected') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center justify-center p-4">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">{{ __('modules.cashier.noPendingPayments') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="px-4 mt-4">
            {{ $pendingPayments->links() }}
        </div>
    @else
        {{-- Aucune session active --}}
        <div class="px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="text-center py-10 bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center justify-center mb-4">
                        <div class="p-4 bg-blue-100 rounded-full dark:bg-blue-900">
                            <svg class="w-16 h-16 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <h4 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">{{ __('modules.cashier.noActiveSession') }}</h4>
                    <p class="text-gray-500 mb-6 dark:text-gray-400">{{ __('modules.cashier.openNewSessionHelp') }}</p>
                    <button 
                        wire:click="openSessionModal"
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-lg px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 inline-flex items-center"
                    >
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('modules.cashier.openNewSession') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Historique des sessions --}}
        <div class="px-4 mb-8">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('modules.cashier.historySessions') }}</h5>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">{{ __('modules.cashier.sessionNumber') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('modules.cashier.cashier') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('modules.cashier.openedAt') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('modules.cashier.closedAt') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('modules.cashier.totalAmount') }}</th>
                                <th scope="col" class="px-6 py-3">{{ __('modules.cashier.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->previousSessions as $session)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4">
                                        <strong class="text-primary-600 dark:text-primary-500">#{{ $session->session_number }}</strong>
                                    </td>
                                    <td class="px-6 py-4">{{ $session->openedByUser->name }}</td>
                                    <td class="px-6 py-4">{{ $session->opened_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        @if($session->closed_at)
                                            {{ $session->closed_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">{{ __('modules.cashier.running') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <strong>{{ number_format($session->expected_balance, 0, ',', ' ') }} F</strong>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a 
                                            href="{{ route('backend.cashier.session', $session->id) }}" 
                                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 inline-flex items-center" 
                                            title="{{ __('modules.cashier.details') }}"
                                        >
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center justify-center p-4">
                                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400">{{ __('modules.cashier.noPreviousSession') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

   {{-- Modal d'ouverture de session --}}
@if($showOpenModal)
<div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900 bg-opacity-50" wire:click.self="$set('showOpenModal', false)">
    <div class="relative w-full max-w-2xl mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ __('modules.cashier.openNewSession') }}
            </h3>
            <button 
                wire:click="$set('showOpenModal', false)"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($paymentMethods as $method => $label)
                    <div>
                        <label for="opening_{{ $method }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $label }}
                        </label>
                        <input 
                            type="number" 
                            id="opening_{{ $method }}"
                            wire:model="openingAmounts.{{ $method }}"
                            step="0.01"
                            min="0"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="0.00"
                        >
                        @error('openingAmounts.' . $method) <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                @endforeach
            </div>
            
            <div>
                <label for="opening_notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('modules.cashier.notes') }} ({{ __('modules.cashier.optional') }})
                </label>
                <textarea 
                    id="opening_notes"
                    wire:model="openingNotes"
                    rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    placeholder="{{ __('modules.cashier.notesPlaceholder') }}"
                ></textarea>
            </div>
        </div>
        <!-- Modal Footer -->
        <div class="flex items-center justify-end p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
            <button 
                wire:click="$set('showOpenModal', false)"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
            >
                {{ __('modules.cashier.cancel') }}
            </button>
            <button 
                wire:click="openSession"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            >
                {{ __('modules.cashier.openSession') }}
            </button>
        </div>
    </div>
</div>
@endif

{{-- Modal de fermeture de session --}}
@if($showCloseModal)
<div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900 bg-opacity-50" wire:click.self="$set('showCloseModal', false)">
    <div class="relative w-full max-w-3xl mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ __('modules.cashier.closeSession') }}
            </h3>
            <button 
                wire:click="$set('showCloseModal', false)"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            {{-- Alerte d'écart si présente --}}
            @if($showDiscrepancyAlert)
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        <div>
                            <span class="font-medium">{{ __('modules.cashier.discrepancyDetected') }}</span>
                            <p class="mt-1">
                                {{ __('modules.cashier.expected') }}: <strong>{{ number_format($expectedCashAmount, 0, ',', ' ') }} F</strong><br>
                                {{ __('modules.cashier.counted') }}: <strong>{{ number_format($closingCashAmount, 0, ',', ' ') }} F</strong><br>
                                {{ __('modules.cashier.difference') }}: <strong class="{{ $discrepancyAmount > 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($discrepancyAmount, 0, ',', ' ') }} F</strong>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="discrepancy_justification" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('modules.cashier.justification') }} <span class="text-red-600">*</span>
                    </label>
                    <textarea 
                        id="discrepancy_justification"
                        wire:model="discrepancyJustification"
                        rows="3"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-red-300 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-red-600 dark:placeholder-gray-400 dark:text-white"
                        placeholder="{{ __('modules.cashier.justificationPlaceholder') }}"
                        required
                    ></textarea>
                    @error('discrepancyJustification') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
            @endif

            {{-- Récapitulatif de la session --}}
            <div class="p-4 bg-blue-50 rounded-lg dark:bg-blue-900/30">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('modules.cashier.sessionSummary') }}</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('modules.cashier.openingBalance') }}:</span>
                        <strong class="block text-gray-900 dark:text-white">{{ number_format($activeSession->opening_balance ?? 0, 0, ',', ' ') }} F</strong>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('modules.cashier.totalSales') }}:</span>
                        <strong class="block text-gray-900 dark:text-white">{{ number_format($activeSession->total_sales ?? 0, 0, ',', ' ') }} F</strong>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('modules.cashier.expenses') }}:</span>
                        <strong class="block text-gray-900 dark:text-white">{{ number_format($expensesAmount, 0, ',', ' ') }} F</strong>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('modules.cashier.expectedBalance') }}:</span>
                        <strong class="block text-lg text-blue-600 dark:text-blue-400">{{ number_format($expectedCashAmount, 0, ',', ' ') }} F</strong>
                    </div>
                </div>
            </div>

            {{-- Montants de fermeture --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('modules.cashier.closingAmounts') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($paymentMethods as $method => $label)
                        <div>
                            <label for="closing_{{ $method }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $label }}
                            </label>
                            <input 
                                type="number" 
                                id="closing_{{ $method }}"
                                wire:model.live="closingAmounts.{{ $method }}"
                                step="0.01"
                                min="0"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="0.00"
                            >
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div>
                <label for="closing_notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('modules.cashier.closingNotes') }} ({{ __('modules.cashier.optional') }})
                </label>
                <textarea 
                    id="closing_notes"
                    wire:model="closingNotes"
                    rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    placeholder="{{ __('modules.cashier.closingNotesPlaceholder') }}"
                ></textarea>
            </div>
        </div>
        <!-- Modal Footer -->
        <div class="flex items-center justify-end p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
            <button 
                wire:click="$set('showCloseModal', false)"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
            >
                {{ __('modules.cashier.cancel') }}
            </button>
            <button 
                wire:click="verifyClosingAmount"
                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
            >
                {{ __('modules.cashier.closeSession') }}
            </button>
        </div>
    </div>
</div>
@endif

{{-- Modal d'encaissement --}}
@if($showPaymentModal && $selectedPayment)
<div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900 bg-opacity-50" wire:click.self="$set('showPaymentModal', false)">
    <div class="relative w-full max-w-md mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ __('modules.cashier.collectPayment') }}
            </h3>
            <button 
                wire:click="$set('showPaymentModal', false)"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
        <!-- Modal Body -->
        <div class="p-6 space-y-4">
            <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('modules.cashier.order') }}</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">#{{ $selectedPayment->order->order_number }}</div>
                @if($selectedPayment->order->customer)
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        {{ $selectedPayment->order->customer->name }}
                    </div>
                @endif
            </div>

            <div>
                <label for="payment_amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('modules.cashier.amount') }} <span class="text-red-600">*</span>
                </label>
                <input 
                    type="number" 
                    id="payment_amount"
                    wire:model="paymentAmount"
                    step="0.01"
                    min="0"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-lg font-bold rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    required
                >
                @error('paymentAmount') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="payment_method" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('modules.cashier.paymentMethod') }} <span class="text-red-600">*</span>
                </label>
                <select 
                    id="payment_method"
                    wire:model="selectedPaymentMethod"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    required
                >
                    @foreach($paymentMethods as $method => $label)
                        <option value="{{ $method }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('selectedPaymentMethod') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>
        <!-- Modal Footer -->
        <div class="flex items-center justify-end p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
            <button 
                wire:click="$set('showPaymentModal', false)"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
            >
                {{ __('modules.cashier.cancel') }}
            </button>
            <button 
                wire:click="validatePayment"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
            >
                {{ __('modules.cashier.validate') }}
            </button>
        </div>
    </div>
</div>
@endif
    
    {{-- Indicateur de chargement --}}
    <div wire:loading class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div role="status">
            <svg aria-hidden="true" class="w-10 h-10 text-gray-200 animate-spin dark:text-gray-600 fill-primary-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
            </svg>
            <span class="sr-only">{{ __('modules.cashier.loading') }}</span>
        </div>
    </div>
</div>