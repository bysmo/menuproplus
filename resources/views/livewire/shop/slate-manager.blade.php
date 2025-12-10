<div>
    {{-- Bouton pour ouvrir l'ardoise --}}
    <button
        wire:click="openSlateModal"
        class="relative p-2 text-gray-700 hover:text-gray-900 focus:outline-none"
        title="@lang('modules.slate.title')"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>

        @if($slate && $slate->remaining_amount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ currency_format($slate->remaining_amount, $restaurant->currency_id) }}
            </span>
        @endif
    </button>

    {{-- Modal principal de l'ardoise --}}
    @if($showSlateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="slate-modal">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                     wire:click="closeSlateModal"></div>

                {{-- Contenu du modal --}}
                <div class="inline-block w-full max-w-2xl px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:p-6">

                    {{-- En-tête --}}
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-900">
                            @lang('modules.slate.title')
                        </h3>
                        <button wire:click="closeSlateModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @if($slate)
                        {{-- Informations de l'ardoise --}}
                        <div class="p-4 mb-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">@lang('modules.slate.code') :</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-blue-600">{{ $slate->code }}</span>
                                    <button
                                        wire:click="copySlateCode"
                                        class="p-1 text-blue-600 hover:text-blue-800"
                                        title="@lang('modules.slate.copyCode')"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-4 gap-4 mt-3 text-center">
                                <div>
                                    <p class="text-xs text-gray-600">@lang('modules.slate.total')</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ currency_format($slate->total_amount, $restaurant->currency_id) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">@lang('modules.slate.paid')</p>
                                    <p class="text-lg font-bold text-green-600">
                                        {{ currency_format($slate->paid_amount, $restaurant->currency_id) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">En attente</p>
                                    <p class="text-lg font-bold text-yellow-600">
                                        {{ currency_format($slate->pending_payment, $restaurant->currency_id) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">@lang('modules.slate.remaining')</p>
                                    <p class="text-lg font-bold text-red-600">
                                        {{ currency_format($slate->remaining_amount, $restaurant->currency_id) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-4">
                                <button
                                    wire:click="openJoinSlateModal"
                                    class="flex-1 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200"
                                >
                                    📝 @lang('modules.slate.joinSlate')
                                </button>
                                <button
                                    wire:click="refreshSlateData"
                                    class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                                >
                                    🔄 @lang('modules.slate.refresh')
                                </button>
                                <button
                                    wire:click="cleanSlateOrders"
                                    class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-red-100 rounded-lg hover:bg-red-200"
                                >
                                    🗑️ @lang('modules.slate.cleanSlateOrders')
                                </button>
                            </div>
                        </div>

                        {{-- Messages --}}
                        @if($successMessage)
                            <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                                {{-- renvoyer le message dans la langue de l'utilisateur --}}
                                {{ $successMessage }}
                            </div>
                        @endif

                        @if($errorMessage)
                            <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                                {{ $errorMessage }}
                            </div>
                        @endif

                        {{-- Liste des commandes --}}
                        <div class="max-h-96 overflow-y-auto">
                            @if($slateOrders->count() > 0)
                                <h4 class="mb-3 text-lg font-semibold text-gray-800">
                                    @lang('modules.slate.ordersOnSlate')
                                </h4>

                                @foreach($slateOrders as $order)
                                    <div class="p-4 mb-3 border border-gray-200 rounded-lg bg-gray-50">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    @lang('modules.slate.order') #{{ $order->id }}
                                                    @if($order->table)
                                                        <span class="text-gray-600">- @lang('modules.slate.table') {{ $order->table->name }}</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($order->order_type) }}
                                            </span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($order->order_status->name) }}
                                            </span>
                                             <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                             <span class="px-2 py-1 text-xs font-semibold rounded-full
                                             // case when pour mettre les couleurs en fonction du statut de la commande : paid, kot, billed, pending_verification, canceled
                                             @if($order->status === 'paid') bg-green-100 text-green-800
                                             @elseif($order->status === 'kot') bg-pink-100 text-pink-800
                                             @elseif($order->status === 'billed') bg-yellow-100 text-yellow-800
                                             @elseif($order->status === 'pending_verification') bg-orange-100 text-orange-800
                                             @elseif($order->status === 'canceled') bg-red-100 text-red-800
                                             @endif
                                            ">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>

                                        {{-- Articles de la commande --}}
                                        <div class="pl-3 mt-2 space-y-1 border-l-2 border-gray-300">
                                            @foreach($order->items as $item)
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-700">
                                                        {{ $item->menuItem->item_name }}
                                                    </span>
                                                    <span class="text-gray-700">
                                                        {{ number_format($item->quantity, 0, ',', ' ') }}x {{ currency_format($item->price, $restaurant->currency_id) }}
                                                    </span>
                                                    <span class="font-medium text-gray-900">
                                                        {{ currency_format($item->price * $item->quantity, $restaurant->currency_id) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="flex justify-between pt-2 mt-2 border-t border-gray-300">
                                            <span class="text-sm font-bold text-gray-900"> @lang('modules.slate.total') : </span>
                                            <span class="text-sm font-bold text-gray-900">{{ currency_format($order->total, $restaurant->currency_id) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="py-8 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium"> @lang('modules.slate.noOrders') </p>
                                    <p class="text-sm">@lang('modules.slate.noOrdersDescription')</p>
                                </div>
                            @endif
                        </div>

                        {{-- Bouton de paiement --}}
                        @if($slate->remaining_amount > 0)
                            <div class="mt-6">

                                @if ($showQrCode || $showPaymentDetail)
                                    <x-secondary-button wire:click="{{ $showQrCode ? 'toggleQrCode' : 'togglePaymenntDetail' }}">
                                        <span class="ml-2">@lang('modules.billing.showOtherPaymentOption')</span>
                                    </x-secondary-button>

                                    <div class="flex items-center mt-2">
                                        @if ($showQrCode)
                                            <img src="{{ $paymentGateway->qr_code_image_url }}" alt="QR Code Preview"
                                                class="object-cover rounded-md h-30 w-30">
                                        @else
                                            <span class="p-3 font-bold text-gray-700 rounded">@lang('modules.billing.accountDetails')</span>
                                            <span>{{ $paymentGateway->offline_payment_detail }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="grid items-center w-full grid-cols-1 gap-6 mt-4 md:grid-cols-2">

                                        @if ($paymentGateway->is_qr_payment_enabled && $paymentGateway->qr_code_image_url)
                                            <!-- Button -->
                                            <x-secondary-button wire:click="toggleQrCode">
                                                <span class="inline-flex items-center">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g stroke-width="0" />
                                                        <g stroke-linecap="round" stroke-linejoin="round" />
                                                        <path fill="none" d="M0 0h24v24H0z" />
                                                        <path
                                                            d="M16 17v-1h-3v-3h3v2h2v2h-1v2h-2v2h-2v-3h2v-1zm5 4h-4v-2h2v-2h2zM3 3h8v8H3zm2 2v4h4V5zm8-2h8v8h-8zm2 2v4h4V5zM3 13h8v8H3zm2 2v4h4v-4zm13-2h3v2h-3zM6 6h2v2H6zm0 10h2v2H6zM16 6h2v2h-2z" />
                                                    </svg>
                                                    <span class="ml-2">@lang('modules.billing.paybyQr')</span>
                                                </span>
                                            </x-secondary-button>
                                        @endif

                                        @if ($paymentGateway->is_offline_payment_enabled && $paymentGateway->offline_payment_detail)
                                            <!-- Button -->
                                            <x-secondary-button wire:click="togglePaymenntDetail">
                                                <span class="inline-flex items-center">
                                                    <svg class="w-4 h-4" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12 15V17M6 7H18C18.5523 7 19 7.44772 19 8V16C19 16.5523 18.5523 17 18 17H6C5.44772 17 5 16.5523 5 16V8C5 7.44772 5.44772 7 6 7ZM6 7L18 7C18.5523 7 19 6.55228 19 6V4C19 3.44772 18.5523 3 18 3H6C5.44772 3 5 3.44772 5 4V6C5 6.55228 5.44772 7 6 7Z"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M12 11C13.1046 11 14 10.1046 14 9C14 7.89543 13.1046 7 12 7C10.8954 7 10 7.89543 10 9C10 10.1046 10.8954 11 12 11Z"
                                                            stroke="currentColor" stroke-width="2" />
                                                    </svg>

                                                    <span class="ml-2">@lang('modules.billing.bankTransfer')</span>
                                                </span>
                                            </x-secondary-button>
                                        @endif

                                        @if ($paymentGateway->is_cash_payment_enabled)
                                            <x-secondary-button wire:click="paySlate" wire:loading.attr="disabled"
                                                class="w-full px-6 py-3 text-lg font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                                <span class="inline-flex items-center">
                                                    <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                                            d="M8 7V6a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1M3 18v-7a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                                    </svg>
                                                    <span class="ml-2" wire:loading.remove wire:target="paySlate">
                                                        💳 @lang('modules.order.payViaCash')
                                                    </span>
                                                </span>
                                                <span wire:loading wire:target="paySlate">
                                                    <svg class="inline w-5 h-5 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Traitement en cours...
                                                </span>
                                            </x-secondary-button>
                                            <p class="text-xs text-center text-gray-500">
                                                Le paiement sera soumis pour vérification manuelle par le restaurant
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if ($showQrCode)
                                    <x-button class="ml-3"
                                        wire:click="paySlate"
                                        wire:loading.attr="disabled">@lang('modules.billing.paymentDone')</x-button>

                                @elseif ($showPaymentDetail)
                                    <x-button class="ml-3"
                                        wire:click="paySlate"
                                        wire:loading.attr="disabled">@lang('modules.billing.paymentDone')</x-button>
                                @endif

                            </div>
                        @endif
                    @else
                        <p class="text-center text-gray-500"> @lang('modules.slate.loadingSlate') </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal pour rejoindre une ardoise --}}
    @if($showJoinSlateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="join-slate-modal">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                     wire:click="closeJoinSlateModal"></div>

                <div class="inline-block w-full max-w-md px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:p-6">
                    <h3 class="mb-4 text-xl font-bold text-gray-900">@lang('modules.slate.joinSlate')</h3>

                    @if($errorMessage)
                        <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            @lang('modules.slate.joinSlateCode')
                        </label>
                        <input
                            type="text"
                            wire:model="joinSlateCode"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ex: ARD000001"
                        />
                    </div>

                    <div class="flex gap-3">
                        <button
                            wire:click="joinSlate"
                            class="flex-1 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                        >
                            @lang('modules.slate.joinSlate')
                        </button>
                        <button
                            wire:click="closeJoinSlateModal"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300"
                        >
                            @lang('modules.slate.close')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Fonctions de gestion des cookies pour l'UUID du device
    function getDeviceUUID() {
        const restaurantId = {{ $restaurant->id }};
        const branchId = {{ $shopBranch->id ?? 0 }};
        const cookieName = `menupro_device_${restaurantId}_${branchId}`;

        let uuid = getCookie(cookieName);

        if (!uuid) {
            uuid = generateUUID();
            setCookie(cookieName, uuid, 90);
            console.log('🆕 Nouveau UUID généré pour ardoise:', uuid, 'Restaurant:', restaurantId, 'Branch:', branchId);
        } else {
            console.log('✅ UUID existant trouvé pour ardoise:', uuid, 'Restaurant:', restaurantId, 'Branch:', branchId);
        }

        return uuid;
    }

    function setDeviceUUID(uuid) {
        const restaurantId = {{ $restaurant->id }};
        const branchId = {{ $shopBranch->id ?? 0 }};
        const cookieName = `menupro_device_${restaurantId}_${branchId}`;

        setCookie(cookieName, uuid, 90);
        console.log('🔄 UUID mis à jour pour ardoise:', uuid, 'Restaurant:', restaurantId, 'Branch:', branchId);
    }

    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
    }

    // Copier dans le presse-papiers
    Livewire.on('copyToClipboard', (event) => {
        navigator.clipboard.writeText(event.text).then(() => {
            console.log('✅ Code copié:', event.text);
        });
    });

    // Mettre à jour le cookie UUID
    Livewire.on('updateDeviceUuid', (event) => {
        setDeviceUUID(event.uuid);
        console.log('🔄 UUID mis à jour depuis événement:', event.uuid);
    });

    // ⚠️ IMPORTANT : Envoyer l'UUID à Livewire au chargement pour SlateManager
    document.addEventListener('DOMContentLoaded', function() {
        const uuid = getDeviceUUID();
        console.log('📤 Envoi UUID SlateManager à Livewire:', uuid);

        if (window.Livewire) {
            Livewire.dispatch('deviceUuidUpdated', { uuid: uuid });
        }
    });

    // ⚠️ IMPORTANT : Également envoyer après l'initialisation de Livewire
    document.addEventListener('livewire:initialized', () => {
        const uuid = getDeviceUUID();
        console.log('📤 Livewire initialisé - Envoi UUID SlateManager:', uuid);
        Livewire.dispatch('deviceUuidUpdated', { uuid: uuid });
    });
</script>
@endpush
