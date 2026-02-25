<?php

namespace App\Livewire\Backend;

use App\Models\CashSession;
use App\Models\Order;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class CashierDashboard extends Component
{
    use WithPagination;

    // Session active
    public $activeSession;
    
    // ✅ NOUVEAU : Branche sélectionnée
    public $selectedBranchId;

    // Méthodes de paiement disponibles
    public $paymentMethods = [
        'cash' => '💵 Espèces',
        'mobile_money_orange' => '🟠 Orange Money',
        'mobile_money_wave' => '💙 Wave',
        'mobile_money_mtn' => '🟡 MTN Money',
        'mobile_money_moov' => '🔵 Moov Money',
        'qr_code' => '📱 QR Code',
        'card' => '💳 Carte Bancaire',
    ];

    // Modal ouverture
    public $showOpenModal = false;
    public $openingAmounts = [];
    public $openingNotes = '';

    // Modal fermeture
    public $showCloseModal = false;
    public $closingAmounts = [];
    public $closingNotes = '';
    public $discrepancyJustification = '';
    public $closingCashAmount = 0;
    public $expectedCashAmount = 0;
    public $expensesAmount = 0;
    public $discrepancyAmount = 0;
    public $showDiscrepancyAlert = false;

    // Modal encaissement
    public $showPaymentModal = false;
    public $selectedPayment = null;
    public $paymentAmount = 0;
    public $selectedPaymentMethod = 'cash';

    // Filtres
    public $searchTerm = '';
    public $statusFilter = '';

    protected $listeners = [
        'refreshDashboard' => '$refresh',
        'branchChanged' => 'onBranchChanged', // ✅ NOUVEAU : Écouter le changement de branche
    ];

    public function mount()
    {
        $this->selectedBranchId = $this->getCurrentBranchId();
        $this->loadActiveSession();
        $this->initializeAmounts();
    }

    /**
     * ✅ CORRECTION PRINCIPALE : Récupérer l'ID de la branche sélectionnée
     * 
     * Cette méthode gère 3 cas :
     * 1. Utilisateur lié à une branche fixe (caissiers, serveurs)
     * 2. Admin avec branche sélectionnée en session
     * 3. Admin avec branche sélectionnée en cookie
     */
    protected function getCurrentBranchId()
    {
        // Cas 1 : Utilisateur lié à une branche fixe
        if (auth()->user()->branch_id) {
            return auth()->user()->branch_id;
        }

        // Cas 2 : Admin avec branche sélectionnée en session
        if (session()->has('selected_branch_id')) {
            return session('selected_branch_id');
        }

        // Cas 3 : Admin avec branche sélectionnée via helper/cookie
        // Adaptez selon votre implémentation
        if (function_exists('current_branch_id')) {
            return current_branch_id();
        }

        // Cas 4 : Fallback - première branche du restaurant
        $user = auth()->user();
        if ($user->restaurant_id) {
            $firstBranch = \App\Models\Branch::where('restaurant_id', $user->restaurant_id)
                ->first();
            return $firstBranch?->id;
        }

        return null;
    }

    /**
     * ✅ NOUVEAU : Gérer le changement de branche
     * Cette méthode est appelée quand l'admin change de branche
     */
    public function onBranchChanged($branchId)
    {
        $this->selectedBranchId = $branchId;
        $this->loadActiveSession();
        $this->resetPage(); // Reset pagination
    }

    /**
     * ✅ CORRECTION : Utiliser la branche sélectionnée
     * au lieu de auth()->user()->branch_id
     */
    public function loadActiveSession()
    {
        if (!$this->selectedBranchId) {
            $this->activeSession = null;
            return;
        }

        // ✅ Utiliser $this->selectedBranchId au lieu de auth()->user()->branch_id
        $this->activeSession = CashSession::open()
            ->forBranch($this->selectedBranchId) // ✅ Branche sélectionnée
            ->with(['details', 'transactions', 'openedByUser', 'discrepancies'])
            ->first();
        
        // Debug optionnel
        // \Log::info('Active session loaded:', [
        //     'found' => $this->activeSession ? 'YES' : 'NO',
        //     'selected_branch_id' => $this->selectedBranchId,
        //     'user_branch_id' => auth()->user()->branch_id,
        //     'is_admin' => auth()->user()->branch_id === null,
        // ]);
    }

    public function initializeAmounts()
    {
        foreach ($this->paymentMethods as $key => $label) {
            $this->openingAmounts[$key] = 0;
            $this->closingAmounts[$key] = 0;
        }
    }

    public function openSessionModal()
    {
        if (!$this->selectedBranchId) {
            session()->flash('error', __('modules.cashier.noBranchSelected'));
            return;
        }

        // Vérifier qu'il n'y a pas déjà une session ouverte
        $existingSession = CashSession::open()
            ->forBranch($this->selectedBranchId)
            ->first();

        if ($existingSession) {
            session()->flash('error', __('modules.cashier.sessionAlreadyOpen'));
            return;
        }

        $this->showOpenModal = true;
    }

    /**
     * ✅ SÉCURITÉ : Vérifier que seul le propriétaire peut fermer
     */
    public function closeSessionModal()
    {
        if (!$this->activeSession) {
            session()->flash('error', __('modules.cashier.noActiveSessionToClose'));
            return;
        }

        if ($this->activeSession->opened_by !== auth()->id()) {
            session()->flash('error', __('modules.cashier.onlySessionOwnerCanClose'));
            return;
        }

        $this->calculateClosingData();
        $this->showCloseModal = true;
    }

    public function calculateClosingData()
    {
        if (!$this->activeSession) return;

        $this->expectedCashAmount = $this->activeSession->expected_balance;
        $this->expensesAmount = $this->activeSession->transactions()
            ->where('type', 'expense')
            ->sum('amount');
    }

    public function verifyClosingAmount()
    {
        $this->closingCashAmount = collect($this->closingAmounts)->sum();
        $this->discrepancyAmount = $this->closingCashAmount - $this->expectedCashAmount;

        if (abs($this->discrepancyAmount) > 0.01) {
            $this->showDiscrepancyAlert = true;
        } else {
            $this->showDiscrepancyAlert = false;
            $this->closeSession();
        }
    }

    public function openSession()
    {
        if (!$this->selectedBranchId) {
            session()->flash('error', __('modules.cashier.noBranchSelected'));
            return;
        }

        $this->validate([
            'openingAmounts.cash' => 'required|numeric|min:0',
            'openingNotes' => 'nullable|string|max:500',
        ]);

        $totalOpening = collect($this->openingAmounts)->sum();

        // ✅ Utiliser la branche sélectionnée
        $sessionNumber = CashSession::generateSessionNumber($this->selectedBranchId);

        $session = CashSession::create([
            'branch_id' => $this->selectedBranchId, // ✅ Branche sélectionnée
            'opened_by' => auth()->id(),
            'session_number' => $sessionNumber,
            'opened_at' => now(),
            'opening_balance' => $totalOpening,
            'expected_balance' => $totalOpening,
            'opening_notes' => $this->openingNotes,
            'status' => 'open',
        ]);

        // Enregistrer les détails d'ouverture
        foreach ($this->openingAmounts as $method => $amount) {
            if ($amount > 0) {
                $session->details()->create([
                    'payment_method' => $method,
                    'type' => 'opening',
                    'amount' => $amount,
                ]);
            }
        }

        session()->flash('success', __('modules.cashier.sessionOpenedSuccessfully'));
        $this->showOpenModal = false;
        $this->loadActiveSession();
        $this->initializeAmounts();
    }

    public function closeSession()
    {
        if (!$this->activeSession || $this->activeSession->opened_by !== auth()->id()) {
            session()->flash('error', __('modules.cashier.unauthorizedSessionClose'));
            $this->showCloseModal = false;
            return;
        }

        $this->validate([
            'closingCashAmount' => 'required|numeric|min:0',
            'discrepancyJustification' => 'required_if:showDiscrepancyAlert,true|string|max:500',
            'closingNotes' => 'nullable|string|max:500',
        ]);

        try {
            $this->activeSession->closeSession(
                $this->closingAmounts,
                $this->discrepancyJustification,
                $this->closingNotes
            );

            session()->flash('success', __('modules.cashier.sessionClosedSuccessfully'));
            $this->showCloseModal = false;
            $this->activeSession = null;
            $this->initializeAmounts();
            
        } catch (\Exception $e) {
            session()->flash('error', __('modules.cashier.sessionCloseError') . ': ' . $e->getMessage());
        }
    }

    public function selectPayment($paymentId)
    {
        $this->selectedPayment = Payment::with('order.customer')->findOrFail($paymentId);
        $this->paymentAmount = $this->selectedPayment->amount;
        $this->showPaymentModal = true;
    }

    public function validatePayment()
    {
        if (!$this->activeSession) {
            session()->flash('error', __('modules.cashier.noActiveSession'));
            return;
        }

        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'selectedPaymentMethod' => 'required|in:' . implode(',', array_keys($this->paymentMethods)),
        ]);

        $this->selectedPayment->update([
            'status' => 'paid',
            'payment_method' => $this->selectedPaymentMethod,
            'paid_at' => now(),
        ]);

        $this->activeSession->addTransaction(
            $this->selectedPayment->id,
            $this->paymentAmount,
            $this->selectedPaymentMethod,
            'sale'
        );

        session()->flash('success', __('modules.cashier.paymentCollectedSuccessfully'));
        $this->showPaymentModal = false;
        $this->reset(['selectedPayment', 'paymentAmount', 'selectedPaymentMethod']);
        
        $this->loadActiveSession();
    }

    public function printSession()
    {
        if (!$this->activeSession) {
            return;
        }
        
        $this->dispatch('print-session', ['sessionId' => $this->activeSession->id]);
    }

    /**
     * ✅ CORRECTION : Utiliser la branche sélectionnée
     */
    public function getPreviousSessionsProperty()
    {
        if (!$this->selectedBranchId) {
            return collect(); // Collection vide
        }

        // ✅ Utiliser $this->selectedBranchId
        return CashSession::forBranch($this->selectedBranchId)
            ->closed()
            ->with(['openedByUser', 'closedByUser', 'discrepancies'])
            ->latest('closed_at')
            ->paginate(10);
    }

    public function render()
    {
        // ✅ Utiliser la branche sélectionnée pour les paiements
        $pendingPayments = $this->selectedBranchId 
            ? Payment::query()
                ->where('status', 'pending')
                ->whereHas('order', function ($query) {
                    $query->where('branch_id', $this->selectedBranchId); // ✅ Branche sélectionnée
                })
                ->with(['order.customer'])
                ->when($this->searchTerm, function ($query) {
                    $query->whereHas('order', function ($q) {
                        $q->where('reference', 'like', '%' . $this->searchTerm . '%');
                    })->orWhereHas('order.customer', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
                })
                ->latest()
                ->paginate(10)
            : collect(); // Collection vide si pas de branche

        return view('livewire.backend.cashier-dashboard', [
            'pendingPayments' => $pendingPayments,
            'previousSessions' => $this->previousSessions,
        ]);
    }
}