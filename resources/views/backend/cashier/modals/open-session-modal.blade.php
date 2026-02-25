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