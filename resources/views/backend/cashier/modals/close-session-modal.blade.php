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
                                {{ __('modules.cashier.expected') }}: <strong>{{ currency_format($expectedCashAmount, restaurant()->currency_id) }}</strong><br>
                                {{ __('modules.cashier.counted') }}: <strong>{{ currency_format($closingCashAmount, restaurant()->currency_id) }}</strong><br>
                                {{ __('modules.cashier.difference') }}: <strong class="{{ $discrepancyAmount > 0 ? 'text-green-600' : 'text-red-600' }}">{{ currency_format($discrepancyAmount, restaurant()->currency_id) }}</strong>
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
                        <strong class="block text-gray-900 dark:text-white">{{ currency_format($activeSession->opening_balance ?? 0, restaurant()->currency_id) }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('modules.cashier.totalSales') }}:</span>
                        <strong class="block text-gray-900 dark:text-white">{{ currency_format($activeSession->total_sales ?? 0, restaurant()->currency_id) }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('modules.cashier.expenses') }}:</span>
                        <strong class="block text-gray-900 dark:text-white">{{ currency_format($expensesAmount, restaurant()->currency_id) }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('modules.cashier.expectedBalance') }}:</span>
                        <strong class="block text-lg text-blue-600 dark:text-blue-400">{{ currency_format($expectedCashAmount, restaurant()->currency_id) }}</strong>
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