<?php

namespace App\Livewire\Backend\Modals;

use LivewireUI\Modal\ModalComponent;
use App\Models\CashSession;
use App\Models\CashSessionDetail;
use Illuminate\Support\Facades\DB;

class OpenSessionModal extends ModalComponent
{
    // Montants d'ouverture par méthode de paiement
    public $cash = 0;
    public $mobile_money_orange = 0;
    public $mobile_money_wave = 0;
    public $mobile_money_mtn = 0;
    public $mobile_money_moov = 0;
    public $qr_code = 0;
    public $card = 0;
    public $other = 0;
    
    public $opening_notes = '';

    protected $rules = [
        'cash' => 'required|numeric|min:0',
        'mobile_money_orange' => 'nullable|numeric|min:0',
        'mobile_money_wave' => 'nullable|numeric|min:0',
        'mobile_money_mtn' => 'nullable|numeric|min:0',
        'mobile_money_moov' => 'nullable|numeric|min:0',
        'qr_code' => 'nullable|numeric|min:0',
        'card' => 'nullable|numeric|min:0',
        'other' => 'nullable|numeric|min:0',
        'opening_notes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'cash.required' => 'Le montant en espèces est obligatoire.',
        'cash.min' => 'Le montant ne peut pas être négatif.',
    ];

    public function mount()
    {
        // ✅ Vérifier qu'il n'y a pas déjà une session ouverte
        if (!branch()) {
            $this->closeModal();
            session()->flash('error', 'Aucune branche sélectionnée.');
            return redirect()->route('backend.cashier.index');
        }

        $activeSession = CashSession::open()
            ->forBranch(branch()->id)
            ->first();

        if ($activeSession) {
            $this->closeModal();
            session()->flash('error', 'Une session est déjà ouverte pour cette branche.');
            return redirect()->route('backend.cashier.index');
        }
    }

    public function openSession()
    {
        // Vérifier la permission
        if (!user_can('cashier.open_session')) {
            session()->flash('error', 'Vous n\'avez pas la permission d\'ouvrir une session.');
            $this->closeModal();
            return;
        }

        // ✅ Vérifier la branche
        if (!branch()) {
            session()->flash('error', 'Aucune branche sélectionnée.');
            $this->closeModal();
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Calculer le solde d'ouverture total
            $opening_balance = (float) $this->cash 
                             + (float) $this->mobile_money_orange
                             + (float) $this->mobile_money_wave
                             + (float) $this->mobile_money_mtn
                             + (float) $this->mobile_money_moov
                             + (float) $this->qr_code
                             + (float) $this->card
                             + (float) $this->other;

            // Générer le numéro de session
            $session_number = CashSession::generateSessionNumber(branch()->id);

            // Créer la session
            $session = CashSession::create([
                'branch_id' => branch()->id,
                'opened_by' => auth()->id(),
                'session_number' => $session_number,
                'status' => 'open',
                'opened_at' => now(),
                'opening_balance' => $opening_balance,
                'expected_balance' => $opening_balance,
                'opening_notes' => $this->opening_notes ?: null,
            ]);

            // Enregistrer les détails d'ouverture pour chaque moyen de paiement
            $paymentMethods = [
                'cash' => $this->cash,
                'mobile_money_orange' => $this->mobile_money_orange,
                'mobile_money_wave' => $this->mobile_money_wave,
                'mobile_money_mtn' => $this->mobile_money_mtn,
                'mobile_money_moov' => $this->mobile_money_moov,
                'qr_code' => $this->qr_code,
                'card' => $this->card,
                'other' => $this->other,
            ];

            foreach ($paymentMethods as $method => $amount) {
                if ($amount > 0) {
                    CashSessionDetail::create([
                        'cash_session_id' => $session->id,
                        'payment_method' => $method,
                        'type' => 'opening',
                        'amount' => $amount,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', 'Session ouverte avec succès !');
            
            // Fermer le modal et rafraîchir le composant parent
            $this->dispatch('sessionOpened');
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('error', 'Erreur lors de l\'ouverture de la session : ' . $e->getMessage());
            $this->closeModal();
        }
    }

    public function render()
    {
        return view('livewire.backend.modals.open-session-modal');
    }

    public static function modalMaxWidth(): string
    {
        return '4xl';
    }
}