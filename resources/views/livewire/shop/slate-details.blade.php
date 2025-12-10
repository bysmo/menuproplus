<div>
    @if($isOpen && $slate)
        <!-- Modal Backdrop -->
        <div
            x-data="{ show: @entangle('isOpen') }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 dark:bg-opacity-80"
            @click="$wire.closeModal()"
        ></div>

        <!-- Sliding Panel from Right -->
        <div
            x-data="{ show: @entangle('isOpen') }"
            x-show="show"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 z-50 h-full w-full max-w-2xl bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto"
            @click.away="$wire.closeModal()"
        >
            <!-- Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Détails de l'ardoise #{{ $slate->code }}
                </h3>
                <button
                    wire:click="closeModal"
                    type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6">
                <!-- Informations générales -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Informations générales</h4>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Code</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">#{{ $slate->code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Statut</p>
                            @php $status = $this->getPaymentStatus(); @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Branche</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $slate->branch?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Date de création</p>
                            <p class="text-base font-medium text-gray-900 dark:text-white">{{ $slate->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($slate->customer)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Client</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $slate->customer->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $slate->customer->phone }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">UUID de l'appareil</p>
                        <p class="text-xs font-mono text-gray-600 dark:text-gray-300 break-all">{{ $slate->device_uuid }}</p>
                    </div>
                </div>

                <!-- Résumé financier -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-4 space-y-3">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Résumé financier</h4>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Total</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ currency_format($slate->total_amount, restaurant()->currency_id) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Payé (confirmé)</span>
                            <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{ currency_format($slate->paid_amount, restaurant()->currency_id) }}</span>
                        </div>
                        @if($slate->pending_payment > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">En attente de vérification</span>
                                <span class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ currency_format($slate->pending_payment, restaurant()->currency_id) }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-300 dark:border-gray-500 pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-medium text-gray-700 dark:text-gray-200">Reste à payer</span>
                                <span class="text-xl font-bold text-red-600 dark:text-red-400">{{ currency_format($slate->remaining_amount, restaurant()->currency_id) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des commandes -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        Commandes ({{ $slate->orders->count() }})
                    </h4>

                    @if($slate->orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($slate->orders as $order)
                                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <!-- En-tête de la commande -->
                                    <div class="mb-3 pb-3 border-b border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <h5 class="text-base font-semibold text-gray-900 dark:text-white">Commande #{{ $order->order_number }}</h5>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                            <div class="text-right">
                                                @php
                                                    $orderStatusClass = match($order->status) {
                                                        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                        'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                        'pending_verification' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                                    };
                                                @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $orderStatusClass }}">
                                                    {{ __(ucfirst($order->status)) }}
                                                </span>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">{{ currency_format($order->total, restaurant()->currency_id) }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between mt-2">
                                            <div>
                                                @php
                                                    $paymentStatusClass = match($order->payment_status) {
                                                        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                        'pending_verification' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                        'pending' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                                        'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                                    };
                                                    $paymentStatusLabel = match($order->payment_status) {
                                                        'paid' => 'Payé',
                                                        'pending_verification' => 'En attente de vérification',
                                                        'pending' => 'En attente',
                                                        'failed' => 'Échoué',
                                                        default => ucfirst($order->payment_status ?? 'En attente')
                                                    };
                                                @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $paymentStatusClass }}">
                                                    {{ $paymentStatusLabel }}
                                                </span>
                                            </div>

                                            @if($order->status === 'pending_verification')
                                                <button
                                                    wire:click="confirmOrderPayment({{ $order->id }})"
                                                    type="button"
                                                    class="text-xs text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg px-3 py-1.5 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800"
                                                >
                                                    Confirmer
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Articles de la commande -->
                                    <div class="space-y-2">
                                        @foreach($order->items as $item)
                                            <div class="text-sm">
                                                <div class="flex justify-between items-center">
                                                    <p class="text-gray-900 dark:text-white">
                                                        <span class="font-semibold">{{ $item->quantity }}x</span>
                                                        {{ $item->menuItem->item_name ?? 'Article inconnu' }}
                                                        @if($item->menuItemVariation)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">({{ $item->menuItemVariation->name }})</span>
                                                        @endif
                                                        <span class="text-gray-600 dark:text-gray-400">x {{ currency_format($item->price, restaurant()->currency_id) }}</span>
                                                    </p>
                                                    <span class="text-gray-900 dark:text-white font-semibold">{{ currency_format($item->price * $item->quantity, restaurant()->currency_id) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Aucune commande pour cette ardoise</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="sticky bottom-0 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 px-6 py-4">
                <div class="flex justify-between items-center gap-3">
                    <button
                        wire:click="closeModal"
                        type="button"
                        class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                    >
                        Fermer
                    </button>

                    <div class="flex gap-2">
                        @php
                            $hasPendingPayments = $slate->orders->where('status', 'pending_verification')->count() > 0;
                        @endphp

                        @if($hasPendingPayments)
                            <button
                                wire:click="confirmAllPayments"
                                type="button"
                                class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800"
                            >
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Confirmer tous les paiements
                            </button>
                        @endif

                        <a
                            href="{{ route('slates.print-invoice', $slate->id) }}"
                            target="_blank"
                            class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                        >
                            <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"></path>
                            </svg>
                            Imprimer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
