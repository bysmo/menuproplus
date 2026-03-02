<?php

namespace App\Livewire\Backend\Cashier;

use App\Models\Order;
use App\Models\Payment;
use App\Models\CashSession;
use App\Models\CashTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CollectedOrders extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = ''; // 'paid', 'refunded'
    public $paymentMethodFilter = ''; // filter by payment method
    
    // Propriétés pour le modal de remboursement
    public $showRefundModal = false;
    public $orderToRefund = null;
    public $refundReason = '';

    protected $listeners = ['refreshOrders' => '$refresh'];

    public function boot()
    {
        if (session('locale')) {
            \Illuminate\Support\Facades\App::setLocale(session('locale'));
        } else {
            $user = auth()->user();
            if (isset($user)) {
                \Illuminate\Support\Facades\App::setLocale($user?->locale ?? 'fr');
            } else {
                try {
                    \Illuminate\Support\Facades\App::setLocale(session('locale') ?? global_setting()?->locale);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\App::setLocale('fr');
                }
            }
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPaymentMethodFilter()
    {
        $this->resetPage();
    }

    public function openRefundModal($orderId)
    {
        $this->orderToRefund = Order::with('payments')->find($orderId);
        $this->refundReason = '';
        
        if ($this->orderToRefund && $this->orderToRefund->status === 'paid') {
            $this->showRefundModal = true;
        } else {
            session()->flash('error', 'Cette commande ne peut pas être remboursée.');
        }
    }

    public function processRefund()
    {
        $this->validate([
            'refundReason' => 'required|string|min:5|max:500',
        ], [
            'refundReason.required' => 'Le motif de remboursement est obligatoire.',
            'refundReason.min' => 'Le motif doit faire au moins 5 caractères.',
        ]);

        if (!$this->orderToRefund || $this->orderToRefund->status !== 'paid') {
            return;
        }

        // Vérifier si une session de caisse est ouverte pour enregistrer la transaction de remboursement
        $activeSession = CashSession::open()->forBranch(branch()->id)->first();
        
        if (!$activeSession) {
            session()->flash('error', 'Vous devez avoir une session de caisse ouverte pour effectuer un remboursement.');
            $this->showRefundModal = false;
            return;
        }

        DB::beginTransaction();

        try {
            // 1. Marquer la commande comme annulée
            $this->orderToRefund->update([
                'status' => 'canceled',
                'order_status' => 'cancelled',
            ]);

            // 2. Marquer les paiements comme remboursés
            $payments = $this->orderToRefund->payments;
            
            foreach ($payments as $payment) {
                // Si le paiement a été effectué
                if (in_array($payment->status, ['paid', 'completed', 'successful']) && !$payment->is_refunded) {
                    $payment->update([
                        'is_refunded' => true,
                        'refunded_at' => now(),
                        'refund_reason' => $this->refundReason,
                    ]);

                    // 3. Ajouter une transaction négative à la session de caisse
                    $activeSession->transactions()->create([
                        'payment_id' => $payment->id,
                        'order_id' => $this->orderToRefund->id,
                        'user_id' => auth()->id(),
                        'transaction_number' => CashTransaction::generateTransactionNumber(),
                        'type' => 'refund',
                        'payment_method' => $payment->payment_method,
                        'amount' => -$payment->amount, // Montant négatif pour le remboursement
                        'description' => 'Remboursement de la commande #' . $this->orderToRefund->order_number . ' - ' . $this->refundReason,
                        'transaction_at' => now(),
                    ]);
                }
            }

            // 4. Mettre à jour les totaux de la session de caisse
            $activeSession->updateTotals();

            DB::commit();

            session()->flash('success', 'La commande a été remboursée avec succès.');
            $this->showRefundModal = false;
            $this->orderToRefund = null;
            $this->resetPage();
            
            // Dispatch un événement pour rafraîchir d'éventuels autres composants
            $this->dispatch('order-refunded');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors du remboursement : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $ordersQuery = Order::with(['customer', 'waiter', 'payments'])
            ->where('branch_id', branch()->id)
            ->where(function($query) {
                // Soit payée, soit remboursée (qui est passée en canceled avec un paiement remboursé)
                $query->where('status', 'paid')
                      ->orWhere(function($subq) {
                          $subq->where('status', 'canceled')
                               ->whereHas('payments', function($pq) {
                                   $pq->where('is_refunded', true);
                               });
                      });
            });

        // Application des filtres
        if ($this->search) {
            $ordersQuery->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('phone', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter === 'paid') {
            $ordersQuery->where('status', 'paid');
        } elseif ($this->statusFilter === 'refunded') {
            $ordersQuery->where('status', 'canceled')
                       ->whereHas('payments', function($pq) {
                           $pq->where('is_refunded', true);
                       });
        }

        if ($this->paymentMethodFilter) {
            $ordersQuery->whereHas('payments', function($pq) {
                $pq->where('payment_method', $this->paymentMethodFilter);
            });
        }

        $orders = $ordersQuery->latest('date_time')->paginate(10);

        return view('livewire.backend.cashier.collected-orders', [
            'orders' => $orders
        ])->extends('layouts.app')->section('content');
    }
}
