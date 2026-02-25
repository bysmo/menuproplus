<?php

namespace App\Livewire\Backend\Modals;

use LivewireUI\Modal\ModalComponent;
use App\Models\CashSession;
use Illuminate\Support\Facades\DB;

class CloseSessionModal extends ModalComponent
{
    public $sessionId;
    public $session;
    
    // Montants de fermeture par méthode de paiement
    public $cash = 0;
    public $mobile_money_orange = 0;
    public $mobile_money_wave = 0;
    public $mobile_money_mtn = 0;
    public $mobile_money_moov = 0;
    public $qr_code = 0;
    public $card = 0;
    public $other = 0;
    
    public $closing_notes = '';
    public $discrepancy_justification = '';
    
    public $showDiscrepancy = false;
    public $showJustificationField = false; // ✅ Nouveau : contrôle l'affichage du champ
    public $totalDiscrepancy = 0;

    protected $rules = [
        'cash' => 'required|numeric|min:0',
        'mobile_money_orange' => 'nullable|numeric|min:0',
        'mobile_money_wave' => 'nullable|numeric|min:0',
        'mobile_money_mtn' => 'nullable|numeric|min:0',
        'mobile_money_moov' => 'nullable|numeric|min:0',
        'qr_code' => 'nullable|numeric|min:0',
        'card' => 'nullable|numeric|min:0',
        'other' => 'nullable|numeric|min:0',
        'closing_notes' => 'nullable|string|max:500',
        'discrepancy_justification' => 'nullable|string|max:500',
    ];

    public function mount($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->session = CashSession::with(['openedByUser', 'details'])
            ->findOrFail($sessionId);
            
        // ✅ Vérifier que la session appartient à la branche active
        if (branch() && $this->session->branch_id !== branch()->id) {
            $this->closeModal();
            session()->flash('error', 'Cette session n\'appartient pas à la branche active.');
            return redirect()->route('backend.cashier.index');
        }

        // Pré-remplir avec les montants attendus
        $this->cash = $this->session->getOpeningAmountForPaymentMethod('cash') 
                    + $this->session->total_cash;
        $this->mobile_money_orange = $this->session->getOpeningAmountForPaymentMethod('mobile_money_orange');
        $this->mobile_money_wave = $this->session->getOpeningAmountForPaymentMethod('mobile_money_wave');
        $this->mobile_money_mtn = $this->session->getOpeningAmountForPaymentMethod('mobile_money_mtn');
        $this->mobile_money_moov = $this->session->getOpeningAmountForPaymentMethod('mobile_money_moov');
        $this->qr_code = $this->session->getOpeningAmountForPaymentMethod('qr_code') 
                       + $this->session->total_qr_code;
        $this->card = $this->session->getOpeningAmountForPaymentMethod('card') 
                    + $this->session->total_card;
        $this->other = $this->session->getOpeningAmountForPaymentMethod('other') 
                     + $this->session->total_other;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->calculateDiscrepancy();
    }

    public function calculateDiscrepancy()
    {
        $closingAmounts = [
            'cash' => (float) $this->cash,
            'mobile_money_orange' => (float) $this->mobile_money_orange,
            'mobile_money_wave' => (float) $this->mobile_money_wave,
            'mobile_money_mtn' => (float) $this->mobile_money_mtn,
            'mobile_money_moov' => (float) $this->mobile_money_moov,
            'qr_code' => (float) $this->qr_code,
            'card' => (float) $this->card,
            'other' => (float) $this->other,
        ];

        $expectedAmounts = [
            'cash' => $this->session->getOpeningAmountForPaymentMethod('cash') + $this->session->total_cash,
            'mobile_money_orange' => $this->session->getOpeningAmountForPaymentMethod('mobile_money_orange'),
            'mobile_money_wave' => $this->session->getOpeningAmountForPaymentMethod('mobile_money_wave'),
            'mobile_money_mtn' => $this->session->getOpeningAmountForPaymentMethod('mobile_money_mtn'),
            'mobile_money_moov' => $this->session->getOpeningAmountForPaymentMethod('mobile_money_moov'),
            'qr_code' => $this->session->getOpeningAmountForPaymentMethod('qr_code') + $this->session->total_qr_code,
            'card' => $this->session->getOpeningAmountForPaymentMethod('card') + $this->session->total_card,
            'other' => $this->session->getOpeningAmountForPaymentMethod('other') + $this->session->total_other,
        ];

        $this->totalDiscrepancy = 0;
        foreach ($closingAmounts as $method => $actualAmount) {
            $expected = $expectedAmounts[$method];
            $difference = $actualAmount - $expected;
            $this->totalDiscrepancy += $difference;
        }

        $this->showDiscrepancy = abs($this->totalDiscrepancy) > 0.01;
        
        // ✅ Réinitialiser le champ de justification si plus d'écart
        if (!$this->showDiscrepancy) {
            $this->showJustificationField = false;
            $this->discrepancy_justification = '';
        }
    }

    public function closeSession()
    {
        // Vérifier la permission
        if (!user_can('cashier.close_session')) {
            session()->flash('error', 'Vous n\'avez pas la permission de fermer une session.');
            $this->closeModal();
            return;
        }

        // Vérifier que l'utilisateur est bien celui qui a ouvert la session
        if ($this->session->opened_by !== auth()->id()) {
            session()->flash('error', 'Seul l\'utilisateur qui a ouvert la session peut la fermer.');
            $this->closeModal();
            return;
        }

        // ✅ Si écart > 0 ET justification activée, exiger la justification
        if ($this->showDiscrepancy && $this->showJustificationField && empty($this->discrepancy_justification)) {
            $this->addError('discrepancy_justification', 'Une justification est requise en cas d\'écart.');
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            $closingAmounts = [
                'cash' => (float) $this->cash,
                'mobile_money_orange' => (float) $this->mobile_money_orange,
                'mobile_money_wave' => (float) $this->mobile_money_wave,
                'mobile_money_mtn' => (float) $this->mobile_money_mtn,
                'mobile_money_moov' => (float) $this->mobile_money_moov,
                'qr_code' => (float) $this->qr_code,
                'card' => (float) $this->card,
                'other' => (float) $this->other,
            ];

            // Utiliser la méthode du modèle pour fermer la session
            $this->session->closeSession(
                $closingAmounts,
                $this->discrepancy_justification ?: null,
                $this->closing_notes ?: null
            );

            DB::commit();

            session()->flash('success', 'Session fermée avec succès !');
            
            // Fermer le modal et rafraîchir le composant parent
            $this->dispatch('sessionClosed');
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('error', 'Erreur lors de la fermeture de la session : ' . $e->getMessage());
            $this->closeModal();
        }
    }

    public function render()
    {
        return view('livewire.backend.modals.close-session-modal');
    }

    public static function modalMaxWidth(): string
    {
        return '4xl'; // ✅ Large pour 2 colonnes
    }
}