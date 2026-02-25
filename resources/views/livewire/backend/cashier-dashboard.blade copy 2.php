
                        <!-- Actions -->
                        <div>
                            @if($activeSession->opened_by === auth()->id())
                                <button wire:click="$dispatch('openModal', { component: 'backend.modals.close-session-modal', arguments: { sessionId: {{ $activeSession->id }} }})"
                                        class="btn btn-danger btn-sm">
                                    <i class="fas fa-lock me-1"></i>
                                    {{ __('modules.cashier.closeSession') }}
                                </button>
                            @else
                                <div class="alert alert-warning mb-0 py-2 px-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    {{ __('modules.cashier.onlyOwnerCanClose') }}
                                </div>
                            @endif
                        </div>
                   

                            <!-- Close Session -->
                            @if($activeSession->opened_by === auth()->id())
                                <button wire:click="$dispatch('openModal', { component: 'backend.modals.close-session-modal', arguments: { sessionId: {{ $activeSession->id }} }})"
                                        class="btn btn-danger btn-sm">
                                    <i class="fas fa-lock me-1"></i>
                                    {{ __('modules.cashier.closeSession') }}
                                </button>
                            @endif

                    <button wire:click="$dispatch('openModal', { component: 'backend.modals.open-session-modal' })"
                            class="btn btn-primary btn-lg">
                        <i class="fas fa-unlock me-2"></i>
                        {{ __('modules.cashier.openNewSession') }}
                    </button>
                