<div>
    <div class="bg-white dark:bg-gray-800 rounded-lg">
        <!-- Header Fixe -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        🔒 {{ __('modules.cashier.closeSession') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        #{{ $session->session_number }} • {{ $session->openedByUser->name }}
                    </p>
                </div>
                <!-- Résumé compact à droite -->
                <div class="flex gap-4 text-right">
                    <div>
                        <div class="text-xs text-gray-500">{{ __('modules.cashier.opening') }}</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ number_format($session->opening_balance, 0, ',', ' ') }} F
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">{{ __('modules.cashier.sales') }}</div>
                        <div class="text-sm font-semibold text-green-600">
                            +{{ number_format($session->total_sales, 0, ',', ' ') }} F
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">{{ __('modules.cashier.expected') }}</div>
                        <div class="text-sm font-semibold text-blue-600">
                            {{ number_format($session->expected_balance, 0, ',', ' ') }} F
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Body avec scroll interne -->
        <form wire:submit.prevent="closeSession">
            <div class="px-6 py-4 max-h-[60vh] overflow-y-auto">
                
                <!-- Alerte écart (fixe en haut du scroll) -->
                @if($showDiscrepancy)
                    <div class="mb-4 p-3 rounded-lg {{ $totalDiscrepancy > 0 ? 'bg-green-50 border border-green-200 dark:bg-green-900/20' : 'bg-red-50 border border-red-200 dark:bg-red-900/20' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">{{ $totalDiscrepancy > 0 ? '➕' : '➖' }}</span>
                                <div>
                                    <h5 class="font-medium {{ $totalDiscrepancy > 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                                        {{ $totalDiscrepancy > 0 ? __('modules.cashier.surplus') : __('modules.cashier.shortage') }}
                                    </h5>
                                    <p class="text-sm {{ $totalDiscrepancy > 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                        {{ number_format(abs($totalDiscrepancy), 0, ',', ' ') }} F
                                    </p>
                                </div>
                            </div>
                            <!-- Toggle justification -->
                            <button type="button" 
                                    x-data="{ open: false }" 
                                    @click="open = !open; $wire.set('showJustificationField', open)"
                                    class="text-sm font-medium {{ $totalDiscrepancy > 0 ? 'text-green-700 hover:text-green-800' : 'text-red-700 hover:text-red-800' }}">
                                <span x-show="!open">✏️ Justifier</span>
                                <span x-show="open">❌ Annuler</span>
                            </button>
                        </div>
                        
                        <!-- Champ justification collapsible -->
                        <div x-show="$wire.showJustificationField" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="mt-3">
                            <textarea wire:model="discrepancy_justification" rows="2"
                                      class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                      placeholder="{{ __('modules.cashier.explainDiscrepancy') }}"></textarea>
                            @error('discrepancy_justification') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                <!-- Grid 2 colonnes pour les moyens de paiement -->
                <div class="space-y-3">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white border-b pb-2">
                        💰 {{ __('modules.cashier.enterActualAmounts') }}
                    </h4>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Espèces -->
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-green-100 dark:bg-green-900/30 rounded">
                                <span class="text-lg">💵</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                    {{ __('modules.cashier.cash') }}
                                </label>
                                <input type="number" wire:model.live="cash" step="0.01"
                                       class="w-full text-sm px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <!-- Orange Money -->
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-orange-100 dark:bg-orange-900/30 rounded">
                                <span class="text-lg">🟠</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                    Orange Money
                                </label>
                                <input type="number" wire:model.live="mobile_money_orange" step="0.01"
                                       class="w-full text-sm px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <!-- Wave -->
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 rounded">
                                <span class="text-lg">💙</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                    Wave
                                </label>
                                <input type="number" wire:model.live="mobile_money_wave" step="0.01"
                                       class="w-full text-sm px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <!-- MTN -->
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-yellow-100 dark:bg-yellow-900/30 rounded">
                                <span class="text-lg">🟡</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                    MTN Money
                                </label>
                                <input type="number" wire:model.live="mobile_money_mtn" step="0.01"
                                       class="w-full text-sm px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <!-- Moov -->
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 rounded">
                                <span class="text-lg">🔵</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                    Moov Money
                                </label>
                                <input type="number" wire:model.live="mobile_money_moov" step="0.01"
                                       class="w-full text-sm px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-purple-100 dark:bg-purple-900/30 rounded">
                                <span class="text-lg">📱</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                    QR Code
                                </label>
                                <input type="number" wire:model.live="qr_code" step="0.01"
                                       class="w-full text-sm px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <!-- Carte bancaire -->
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/30 rounded">
                                <span class="text-lg">💳</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                    {{ __('modules.cashier.card') }}
                                </label>
                                <input type="number" wire:model.live="card" step="0.01"
                                       class="w-full text-sm px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <!-- Autre -->
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-900/30 rounded">
                                <span class="text-lg">🔄</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                                    {{ __('modules.cashier.other') }}
                                </label>
                                <input type="number" wire:model.live="other" step="0.01"
                                       class="w-full text-sm px-2 py-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes de fermeture (compact) -->
                <div class="mt-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        📝 {{ __('modules.cashier.closingNotes') }} <span class="text-gray-400">({{ __('modules.cashier.optional') }})</span>
                    </label>
                    <textarea wire:model="closing_notes" rows="2"
                              class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                              placeholder="{{ __('modules.cashier.addNotes') }}"></textarea>
                </div>
            </div>

            <!-- Footer Fixe -->
            <div class="flex justify-between items-center gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <!-- Total calculé -->
                <div class="text-left">
                    <div class="text-xs text-gray-500">{{ __('modules.cashier.totalCounted') }}</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ number_format((float)$cash + (float)$mobile_money_orange + (float)$mobile_money_wave + (float)$mobile_money_mtn + (float)$mobile_money_moov + (float)$qr_code + (float)$card + (float)$other, 0, ',', ' ') }} F
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" wire:click="$dispatch('closeModal')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        {{ __('modules.cashier.cancel') }}
                    </button>
                    <button type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 flex items-center gap-2">
                        <span>🔒</span>
                        <span>{{ __('modules.cashier.closeSession') }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @script
    <script>
        Alpine.data('justificationToggle', () => ({
            showJustificationField: false
        }));
    </script>
    @endscript
</div>
