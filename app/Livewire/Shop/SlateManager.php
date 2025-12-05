<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Slate;
use App\Models\Order;
use Livewire\Attributes\On;
use App\Models\PaymentGatewayCredential;


class SlateManager extends Component
{
    public $restaurant;
    public $shopBranch;
    public $deviceUuid;

    public $slate;
    public $slateOrders = [];
    public $showSlateModal = false;
    public $showJoinSlateModal = false;
    public $joinSlateCode = '';
    public $errorMessage = '';
    public $successMessage = '';
    public $enableQrPayment;
    public $showQrCode = false;
    public $showPaymentDetail = false;
    public $paymentGateway;
    public $offline_payment_status;
    public $paymentOrder;



    protected $listeners = ['refreshSlate' => '$refresh'];

    public function mount($restaurant, $shopBranch, $deviceUuid = null)
    {
        $this->restaurant = $restaurant;
        $this->shopBranch = $shopBranch;
        $this->deviceUuid = $deviceUuid;
        $this->paymentGateway = PaymentGatewayCredential::withoutGlobalScopes()->where('restaurant_id', $this->restaurant->id)->first();
        $this->offline_payment_status = $this->paymentGateway->offline_payment_status;
        $this->paymentOrder = null;

        if ($this->deviceUuid) {
            $this->loadOrCreateSlate();
        }
    }

    public function hydrate()
    {
        // Ne pas recharger depuis la session - éviter les conflits
    }

     public function toggleQrCode()
    {
        $this->showQrCode = !$this->showQrCode;
    }

    public function togglePaymenntDetail()
    {
        $this->showPaymentDetail = !$this->showPaymentDetail;
    }

    #[On('deviceUuidUpdated')]
    public function updateDeviceUuid($uuid)
    {
        $this->deviceUuid = $uuid;
        $this->loadOrCreateSlate();

        \Log::info('🔄 UUID appareil mis à jour', ['uuid' => $uuid]);
    }

    public function loadOrCreateSlate()
    {
        if (!$this->deviceUuid) {
            \Log::warning('⚠️ Impossible de charger l\'ardoise : UUID manquant');
            return;
        }

        //verifier si l'utilisateur est connecté et si c'est un client
        if(auth()->check() && auth()->user()->is_client){
            $customerId = auth()->id();
        }else{
            $customerId = null;
        }

        $this->slate = Slate::getOrCreateForDevice(
            $this->deviceUuid,
            $this->restaurant->id,
            $this->shopBranch->id,
            $customerId
        );

        $this->slate->checkExpiration();
        $this->slate->renewExpiration();
        $this->loadSlateOrders();

        \Log::info('📋 Ardoise chargée', [
            'slate_id' => $this->slate->id,
            'code' => $this->slate->code,
            'orders_count' => $this->slateOrders->count(),
        ]);
    }

    public function loadSlateOrders()
    {
        if ($this->slate) {
            $this->slateOrders = $this->slate->orders()
                ->with(['items.menuItem', 'table'])
                ->latest()
                ->get();
        }
    }

    public function openSlateModal()
    {
        if (!$this->deviceUuid) {
            // renvoyer le message dans la langue de l'utilisateur
            $this->errorMessage = __('modules.slate.deviceUuidMissingMessage');
            return;
        }

        $this->loadOrCreateSlate();
        $this->showSlateModal = true;
        $this->errorMessage = '';

        \Log::info('🔓 Modal ardoise ouvert', ['slate_code' => $this->slate?->code]);
    }

    public function closeSlateModal()
    {
        $this->showSlateModal = false;
    }

    public function openJoinSlateModal()
    {
        $this->showJoinSlateModal = true;
        $this->joinSlateCode = '';
        $this->errorMessage = '';
    }

    public function closeJoinSlateModal()
    {
        $this->showJoinSlateModal = false;
        $this->joinSlateCode = '';
    }

    public function joinSlate()
    {
        $this->validate([
            'joinSlateCode' => 'required|string',
        ]);

        $foundSlate = Slate::findByCode($this->joinSlateCode, $this->shopBranch->id);

        if (!$foundSlate) {
            // renvoyer le message dans la langue de l'utilisateur
            $this->errorMessage = __('modules.slate.joinSlateInvalidCodeMessage');
            \Log::warning('❌ Code ardoise introuvable', ['code' => $this->joinSlateCode]);
            return;
        }

        // Remplacer l'UUID local par celui de l'ardoise partagée
        $this->deviceUuid = $foundSlate->device_uuid;
        $this->slate = $foundSlate;
        $this->loadSlateOrders();

        // Mettre à jour le cookie côté frontend
        $this->dispatch('updateDeviceUuid', uuid: $this->deviceUuid);

        // renvoyer le message dans la langue de l'utilisateur, avec le code de l'ardoise
        $this->successMessage = __('modules.slate.joinSlateSuccessMessage', ['code' => $foundSlate->code]);
        $this->closeJoinSlateModal();

        \Log::info('✅ Ardoise rejointe', [
            'code' => $foundSlate->code,
            'new_uuid' => $this->deviceUuid,
        ]);
    }

    public function copySlateCode()
    {
        if ($this->slate) {
            $this->dispatch('copyToClipboard', text: $this->slate->code);
            // renvoyer le message dans la langue de l'utilisateur, avec le code de l'ardoise
            $this->successMessage = __('modules.slate.copySlateCodeSuccessMessage', ['code' => $this->slate->code]);
        }
    }

    // Dans app/Livewire/Shop/SlateManager.php

    public function refreshSlateData()
    {
        if ($this->slate) {
            \Log::info('🔄 Actualisation ardoise demandée', [
                'slate_id' => $this->slate->id,
            ]);

            // Recharger depuis la base
            $this->slate = $this->slate->fresh();

            // Forcer le recalcul
            $this->slate->recalculateAmounts();

            // Recharger les commandes
            $this->loadSlateOrders();

            $this->successMessage = __('modules.slate.refreshSlateDataSuccessMessage');

            \Log::info('✅ Ardoise actualisée', [
                'slate_id' => $this->slate->id,
                'total' => $this->slate->total_amount,
                'remaining' => $this->slate->remaining_amount,
            ]);
        }
    }


    #[On('orderAddedToSlate')]
    public function handleOrderAdded($orderId)
    {
        $this->refreshSlateData();
        $this->successMessage = __('modules.slate.orderAddedToSlateSuccessMessage');

        \Log::info('🎉 Commande ajoutée à l\'ardoise (event)', ['order_id' => $orderId]);
    }

    public function render()
    {
        return view('livewire.shop.slate-manager');
    }

    /**
     * Payer/solder toutes les commandes non payées de l'ardoise
     */
    public function paySlate()
    {
        try {
            if (!$this->slate) {
                $this->errorMessage = 'Aucune ardoise à payer.';
                return;
            }

            // Recharger l'ardoise pour avoir les données les plus récentes
            $this->slate->refresh();

            // Vérifier qu'il y a des commandes à payer
            if ($this->slate->remaining_amount <= 0) {
                $this->errorMessage = 'Aucune commande à payer sur cette ardoise.';
                return;
            }

            \Log::info('💳 Début du paiement de l\'ardoise', [
                'slate_id' => $this->slate->id,
                'slate_code' => $this->slate->code,
                'remaining_amount' => $this->slate->remaining_amount,
            ]);

            // Récupérer toutes les commandes non payées
            $unpaidOrders = $this->slate->unpaidOrders()->get();

            if ($unpaidOrders->isEmpty()) {
                $this->errorMessage = 'Aucune commande à payer.';
                \Log::warning('⚠️ Aucune commande non payée trouvée', [
                    'slate_id' => $this->slate->id,
                ]);
                return;
            }

            \Log::info('📋 Commandes à traiter', [
                'count' => $unpaidOrders->count(),
                'order_ids' => $unpaidOrders->pluck('id')->toArray(),
            ]);

            $totalPaid = 0;
            $paidOrdersCount = 0;
            $failedOrders = [];

            // Parcourir toutes les commandes non payées
            foreach ($unpaidOrders as $order) {
                try {
                    \Log::info('💰 Traitement commande', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'total' => $order->total,
                        'amount_paid' => $order->amount_paid,
                        'current_status' => $order->status,
                    ]);

                    // Calculer le montant restant à payer pour cette commande
                    $remainingAmount = $order->total - ($order->amount_paid ?? 0);

                    if ($remainingAmount <= 0) {
                        \Log::info('⏭️ Commande déjà payée', ['order_id' => $order->id]);
                        continue;
                    }

                    // Créer un paiement pour cette commande
                    \App\Models\Payment::create([
                        'order_id' => $order->id,
                        'branch_id' => $this->shopBranch->id,
                        'payment_method' => 'offline', // Paiement hors ligne par défaut
                        'amount' => $remainingAmount,
                        'status' => 'pending', // En attente de vérification
                        'payment_date' => now(),
                        'transaction_reference' => 'SLATE_' . $this->slate->code . '_' . $order->id,
                        'notes' => 'Paiement via ardoise ' . $this->slate->code,
                    ]);

                    // Mettre à jour le montant payé de la commande
                    $order->amount_paid = ($order->amount_paid ?? 0) + $remainingAmount;

                    // Mettre à jour le statut de la commande
                    $order->status = 'pending_verification'; // En attente de vérification manuelle
                    $order->save();

                    $totalPaid += $remainingAmount;
                    $paidOrdersCount++;

                    \Log::info('✅ Commande traitée avec succès', [
                        'order_id' => $order->id,
                        'paid_amount' => $remainingAmount,
                        'new_status' => $order->status,
                    ]);

                } catch (\Exception $orderException) {
                    \Log::error('❌ Erreur lors du traitement de la commande', [
                        'order_id' => $order->id,
                        'error' => $orderException->getMessage(),
                        'trace' => $orderException->getTraceAsString(),
                    ]);

                    $failedOrders[] = [
                        'order_id' => $order->id,
                        'error' => $orderException->getMessage(),
                    ];
                }
            }

            // Recalculer les montants de l'ardoise
            $this->slate->recalculateAmounts();

            // Recharger les commandes
            $this->loadSlateOrders();

            // Préparer le message de succès
            if ($paidOrdersCount > 0) {
                $this->successMessage = sprintf(
                    '%d commande(s) soldée(s) pour un total de %s. En attente de vérification.',
                    $paidOrdersCount,
                    currency_format($totalPaid, $this->restaurant->currency_id)
                );

                \Log::info('🎉 Paiement de l\'ardoise terminé avec succès', [
                    'slate_id' => $this->slate->id,
                    'slate_code' => $this->slate->code,
                    'paid_orders_count' => $paidOrdersCount,
                    'total_paid' => $totalPaid,
                    'failed_orders_count' => count($failedOrders),
                ]);

                // Dispatcher un événement pour notifier le restaurant
                event(new \App\Events\SlatePaymentReceived($this->slate, $unpaidOrders));

            } else {
                $this->errorMessage = 'Aucune commande n\'a pu être traitée.';
            }

            // Afficher les erreurs s'il y en a
            if (count($failedOrders) > 0) {
                $this->errorMessage .= sprintf(
                    ' %d commande(s) n\'ont pas pu être traitées.',
                    count($failedOrders)
                );

                \Log::warning('⚠️ Certaines commandes ont échoué', [
                    'failed_orders' => $failedOrders,
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('❌ Erreur fatale lors du paiement de l\'ardoise', [
                'slate_id' => $this->slate->id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->errorMessage = 'Une erreur est survenue lors du paiement. Veuillez réessayer.';
        }
    }

    // function to remove old paid orders from slate
    public function cleanSlateOrders()
    {
        $this->slate->paidOrders()->delete();
        //clean also canceled orders
        $this->slate->canceledOrders()->delete();
        $this->successMessage = __('modules.slate.cleanSlateOrdersSuccessMessage');
    }

}
