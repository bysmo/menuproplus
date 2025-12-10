<?php

namespace App\Livewire\Shop;

use App\Models\Order;
use App\Models\Slate;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class SlateDetails extends Component
{
    public $isOpen = false;
    public $slate = null;
    public $slateId = null;

    #[On('show-slate-details')]
    public function showDetails($slateId)
    {
        $this->slateId = $slateId;
        $this->loadSlate();
        $this->isOpen = true;
    }

    public function loadSlate()
    {
        if ($this->slateId) {
            $this->slate = Slate::with([
                'orders' => function($q) {
                    $q->with([
                        'items.menuItem',
                        'items.menuItemVariation',
                        'items.modifierOptions'
                    ])
                    ->orderBy('created_at', 'desc');
                },
                'customer',
                'branch',
                'restaurant'
            ])->find($this->slateId);
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->slate = null;
        $this->slateId = null;
    }

    public function getPaymentStatus()
    {
        if (!$this->slate) return null;

        if ($this->slate->status === 'paid') {
            return [
                'label' => 'Payé',
                'class' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            ];
        }

        if ($this->slate->status === 'pending_verification') {
            return [
                'label' => 'En attente de vérification',
                'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            ];
        }


        if ($this->slate->paid_amount > 0 && $this->slate->remaining_amount > 0) {
            return [
                'label' => 'Partiellement payé',
                'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            ];
        }

        if ($this->slate->remaining_amount > 0) {
            return [
                'label' => 'Non payé',
                'class' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            ];
        }

        return [
            'label' => 'Actif',
            'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        ];
    }

    public function confirmOrderPayment($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            // Vérifier que la commande appartient bien à cette ardoise
            if ($order->slate_id !== $this->slate->id) {
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => 'Cette commande n\'appartient pas à cette ardoise.'
                ]);
                return;
            }

            // verifier si la commande est en attente de verification
            if ($order->status !== 'pending_verification') {
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => 'Cette commande n\'est pas en attente de vérification.'
                ]);
                return;
            }

            // Mettre à jour le statut de paiement et s'assurer que amount_paid = total
            $order->update([
                'status' => 'paid',
                'amount_paid' => $order->total
            ]);

            // Recalculer les montants de l'ardoise
            $this->slate->recalculateAmounts();

            // Recharger l'ardoise
            $this->loadSlate();

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Paiement de la commande confirmé avec succès.'
            ]);

            // Rafraîchir la liste des ardoises
            $this->dispatch('slate-updated');

        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Erreur lors de la confirmation du paiement: ' . $e->getMessage()
            ]);
        }
    }

    public function confirmAllPayments()
    {
        try {
            DB::beginTransaction();

            // Récupérer toutes les commandes avec paiement en attente
            $pendingOrders = $this->slate->orders()
                ->where('status', 'pending_verification')
                ->get();

            if ($pendingOrders->isEmpty()) {
                $this->dispatch('alert', [
                    'type' => 'info',
                    'message' => 'Aucun paiement en attente de confirmation.'
                ]);
                DB::rollBack();
                return;
            }

            // Mettre à jour tous les statuts
            foreach ($pendingOrders as $order) {
                $order->update([
                    'status' => 'paid',
                    'amount_paid' => $order->total
                ]);
            }

            // Recalculer les montants de l'ardoise
            $this->slate->recalculateAmounts();

            DB::commit();

            // Recharger l'ardoise
            $this->loadSlate();

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => count($pendingOrders) . ' paiement(s) confirmé(s) avec succès.'
            ]);

            // Rafraîchir la liste des ardoises
            $this->dispatch('slate-updated');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Erreur lors de la confirmation des paiements: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.shop.slate-details');
    }
}
