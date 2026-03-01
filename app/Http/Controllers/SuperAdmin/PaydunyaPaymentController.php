<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Events\SendNewOrderReceived;
use App\Events\SendOrderBillEvent;
use App\Models\AdminPaydunyaPayment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Slate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PaydunyaPaymentController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Configuration SDK PayDunya par restaurant
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Initialise les clés API PayDunya depuis les credentials du restaurant.
     *
     * @param  string  $restaurantHash
     * @return Restaurant
     * @throws \Exception
     */
    private function setupPaydunya(string $restaurantHash): Restaurant
    {
        $restaurant = Restaurant::where('hash', $restaurantHash)->first();

        if (!$restaurant) {
            throw new \Exception('Restaurant introuvable pour le hash : ' . $restaurantHash);
        }

        $credential = $restaurant->paymentGateways;

        if (!$credential) {
            throw new \Exception('Aucune configuration de passerelle de paiement trouvée.');
        }

        if (
            empty($credential->paydunya_master_key) ||
            empty($credential->paydunya_public_key) ||
            empty($credential->paydunya_private_key) ||
            empty($credential->paydunya_token)
        ) {
            throw new \Exception('Les clés API PayDunya ne sont pas configurées pour ce restaurant.');
        }

        // Initialisation du SDK PayDunya (installation via Composer)
        \Paydunya\Setup::setMasterKey($credential->paydunya_master_key);
        \Paydunya\Setup::setPublicKey($credential->paydunya_public_key);
        \Paydunya\Setup::setPrivateKey($credential->paydunya_private_key);
        \Paydunya\Setup::setToken($credential->paydunya_token);
        \Paydunya\Setup::setMode($credential->paydunya_mode ?? 'test');

        return $restaurant;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. Initiation du paiement (PAR — Paiement Avec Redirection)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Crée une facture PayDunya et redirige le client vers la page de paiement.
     * Appelé en POST depuis le frontend (ex: bouton "Payer avec PayDunya").
     *
     * POST /paydunya/initiate-payment
     */
    public function initiatePayment(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        try {
            /** @var Order $order */
            $order = Order::with('branch.restaurant')->findOrFail($request->order_id);
            $restaurant = $order->branch->restaurant;

            // Initialisation des clés SDK
            $this->setupPaydunya($restaurant->hash);

            // ── Configuration du Store (informations du restaurant) ─────────
            \Paydunya\Checkout\Store::setName($restaurant->name);

            if (!empty($restaurant->phone)) {
                \Paydunya\Checkout\Store::setPhoneNumber($restaurant->phone);
            }
            if (!empty($restaurant->website)) {
                \Paydunya\Checkout\Store::setWebsiteUrl($restaurant->website);
            }
            if (!empty($restaurant->logo_url)) {
                \Paydunya\Checkout\Store::setLogoUrl($restaurant->logo_url);
            }

            // ── URLs de redirection ────────────────────────────────────────
            \Paydunya\Checkout\Store::setReturnUrl(route('paydunya.success'));
            \Paydunya\Checkout\Store::setCancelUrl(route('paydunya.failed'));
            \Paydunya\Checkout\Store::setCallbackUrl(route('paydunya.ipn'));

            // ── Création de la facture ──────────────────────────────────────
            $invoice = new \Paydunya\Checkout\CheckoutInvoice();

            // Ajout du/des article(s) de la commande
            $invoice->addItem(
                __('messages.order') . ' #' . $order->show_formatted_order_number,
                1,
                (float) $order->total_amount,
                (float) $order->total_amount,
                __('messages.restaurantOrder') . ' - ' . $restaurant->name
            );

            // Montant total (obligatoire — PayDunya facture CE montant)
            $invoice->setTotalAmount((float) $order->total_amount);

            // Description optionnelle
            $invoice->setDescription(
                'Paiement commande #' . $order->show_formatted_order_number .
                ' - ' . $restaurant->name
            );

            // Données personnalisées (récupérées dans le callback/IPN)
            $invoice->addCustomData('order_id', $order->id);
            $invoice->addCustomData('restaurant_hash', $restaurant->hash);
            $invoice->addCustomData('branch_id', $order->branch_id);

            // ── Envoi à PayDunya ────────────────────────────────────────────
            if ($invoice->create()) {
                $token      = $invoice->getToken();
                $invoiceUrl = $invoice->getInvoiceUrl();

                // Sauvegarde en base de données
                AdminPaydunyaPayment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'paydunya_token' => $token,
                        'invoice_url'    => $invoiceUrl,
                        'amount'         => $order->total_amount,
                        'currency'       => $restaurant->currency->currency_code ?? 'XOF',
                        'payment_status' => 'pending',
                    ]
                );

                Log::info('PayDunya: Facture créée', [
                    'order_id'   => $order->id,
                    'token'      => $token,
                    'invoice_url'=> $invoiceUrl,
                ]);

                return response()->json([
                    'status'      => 'success',
                    'redirect_url'=> $invoiceUrl,
                    'token'       => $token,
                ]);

            } else {
                Log::error('PayDunya: Échec de création de la facture', [
                    'order_id'      => $order->id,
                    'response_text' => $invoice->response_text,
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => $invoice->response_text ?? 'Impossible de créer la facture PayDunya.',
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('PayDunya: Exception lors de l\'initiation', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crée une facture PayDunya pour une Ardoise et redirige le client.
     * GET/POST /paydunya/slate-payment/{slate}
     */
    public function initiateSlatePayment(Slate $slate)
    {
        try {
            $restaurant = $slate->restaurant;

            if ($slate->remaining_amount <= 0) {
                return $this->flashAndRedirect('Cette ardoise est déjà payée.', 'warning');
            }

            // Initialisation des clés SDK
            $this->setupPaydunya($restaurant->hash);

            // ── Configuration du Store ─────────
            \Paydunya\Checkout\Store::setName($restaurant->name);

            if (!empty($restaurant->phone)) {
                \Paydunya\Checkout\Store::setPhoneNumber($restaurant->phone);
            }
            if (!empty($restaurant->website)) {
                \Paydunya\Checkout\Store::setWebsiteUrl($restaurant->website);
            }
            if (!empty($restaurant->logo_url)) {
                \Paydunya\Checkout\Store::setLogoUrl($restaurant->logo_url);
            }

            // ── URLs de redirection ────────────────────────────────────────
            \Paydunya\Checkout\Store::setReturnUrl(route('paydunya.success'));
            \Paydunya\Checkout\Store::setCancelUrl(route('paydunya.failed'));
            \Paydunya\Checkout\Store::setCallbackUrl(route('paydunya.ipn'));

            // ── Création de la facture ──────────────────────────────────────
            $invoice = new \Paydunya\Checkout\CheckoutInvoice();

            // Ajout du résumé de l'ardoise
            $invoice->addItem(
                'Paiement Ardoise #' . $slate->code,
                1,
                (float) $slate->remaining_amount,
                (float) $slate->remaining_amount,
                'Règlement complet de l\'ardoise - ' . $restaurant->name
            );

            // Montant total (obligatoire)
            $invoice->setTotalAmount((float) $slate->remaining_amount);

            $invoice->setDescription(
                'Paiement de l\'ardoise #' . $slate->code . ' - ' . $restaurant->name
            );

            // Données personnalisées pour l'IPN (Slate)
            $invoice->addCustomData('is_slate', true);
            $invoice->addCustomData('slate_id', $slate->id);
            $invoice->addCustomData('restaurant_hash', $restaurant->hash);
            $invoice->addCustomData('branch_id', $slate->branch_id);

            // ── Envoi à PayDunya ────────────────────────────────────────────
            if ($invoice->create()) {
                $token      = $invoice->getToken();
                $invoiceUrl = $invoice->getInvoiceUrl();

                // Sauvegarde en base de données pour la trace de l'ardoise
                AdminPaydunyaPayment::create([
                    'slate_id'       => $slate->id,
                    'paydunya_token' => $token,
                    'invoice_url'    => $invoiceUrl,
                    'amount'         => $slate->remaining_amount,
                    'currency'       => $restaurant->currency->currency_code ?? 'XOF',
                    'payment_status' => 'pending',
                ]);

                Log::info('PayDunya: Facture Ardoise créée', [
                    'slate_id'   => $slate->id,
                    'token'      => $token,
                    'invoice_url'=> $invoiceUrl,
                ]);

                return redirect()->away($invoiceUrl);

            } else {
                Log::error('PayDunya: Échec de création de la facture Ardoise', [
                    'slate_id'      => $slate->id,
                    'response_text' => $invoice->response_text,
                ]);

                return $this->flashAndRedirect($invoice->response_text ?? 'Impossible de créer la facture PayDunya pour l\'ardoise.', 'danger');
            }

        } catch (\Exception $e) {
            Log::error('PayDunya: Exception lors de l\'initiation Ardoise', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return $this->flashAndRedirect('Erreur: ' . $e->getMessage(), 'danger');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. Retour après paiement réussi (return_url)
    //    PayDunya ajoute automatiquement ?token=XXXX à l'URL
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /paydunya/callback  (return_url)
     * GET /paydunya/success   (alias)
     */
    public function handleCallback(Request $request): RedirectResponse
    {
        return $this->processReturnUrl($request);
    }

    public function paymentMainSuccess(Request $request): RedirectResponse
    {
        return $this->processReturnUrl($request);
    }

    /**
     * Logique commune pour le retour après paiement.
     */
    private function processReturnUrl(Request $request): RedirectResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return $this->flashAndRedirect('Aucun token de paiement fourni.', 'danger');
        }

        // Rechercher le paiement en base
        $paydunya = AdminPaydunyaPayment::where('paydunya_token', $token)->first();

        if (!$paydunya) {
            return $this->flashAndRedirect('Enregistrement de paiement introuvable.', 'danger');
        }

        if ($paydunya->slate_id) {
            $slate      = $paydunya->slate;
            $restaurant = $slate->restaurant;
            $orderUuid  = null;
        } else {
            $order      = $paydunya->order;
            $restaurant = $order->branch->restaurant;
            $orderUuid  = $order->uuid ?? null;
        }

        // Si déjà complété (ex: IPN traité avant la redirection)
        if ($paydunya->isCompleted()) {
            return $this->flashAndRedirect(
                __('messages.paymentDoneSuccessfully'),
                'success',
                $orderUuid
            );
        }

        // Vérification du statut via l'API PayDunya
        try {
            $this->setupPaydunya($restaurant->hash);

            $invoice = new \Paydunya\Checkout\CheckoutInvoice();

            if ($invoice->confirm($token)) {
                $status = $invoice->getStatus();

                if ($status === 'completed') {
                    // Mise à jour des informations client
                    $paydunya->update([
                        'payment_status'  => 'completed',
                        'customer_name'   => $invoice->getCustomerInfo('name'),
                        'customer_email'  => $invoice->getCustomerInfo('email'),
                        'customer_phone'  => $invoice->getCustomerInfo('phone'),
                        'receipt_url'     => $invoice->getReceiptUrl(),
                        'payment_response'=> [
                            'status'       => $status,
                            'total_amount' => $invoice->getTotalAmount(),
                            'receipt_url'  => $invoice->getReceiptUrl(),
                        ],
                    ]);

                    if ($paydunya->slate_id) {
                        $this->markSlateAsPaid($paydunya, $paydunya->slate);
                    } else {
                        $this->markOrderAsPaid($paydunya, $order);
                    }

                    return $this->flashAndRedirect(
                        __('messages.paymentDoneSuccessfully'),
                        'success',
                        $orderUuid
                    );

                } elseif ($status === 'pending') {
                    return $this->flashAndRedirect(
                        'Votre paiement est en cours de traitement. Veuillez patienter.',
                        'info',
                        $orderUuid
                    );

                } else {
                    // cancelled
                    $paydunya->update(['payment_status' => 'cancelled']);
                    return $this->flashAndRedirect(
                        'Le paiement a été annulé.',
                        'warning',
                        $orderUuid
                    );
                }
            } else {
                Log::warning('PayDunya confirm() a retourné false', [
                    'token'         => $token,
                    'response_text' => $invoice->response_text,
                    'response_code' => $invoice->response_code,
                ]);

                return $this->flashAndRedirect(
                    'Impossible de vérifier le statut du paiement.',
                    'danger',
                    $orderUuid
                );
            }

        } catch (\Exception $e) {
            Log::error('PayDunya: Erreur lors de la vérification', [
                'token'   => $token,
                'message' => $e->getMessage(),
            ]);

            return $this->flashAndRedirect(
                'Une erreur est survenue lors de la vérification du paiement.',
                'danger',
                $orderUuid
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. Redirection après annulation (cancel_url)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /paydunya/failed   (cancel_url)
     */
    public function paymentFailed(Request $request): RedirectResponse
    {
        $token = $request->query('token');

        if ($token) {
            $paydunya = AdminPaydunyaPayment::where('paydunya_token', $token)->first();

            if ($paydunya) {
                $paydunya->update([
                    'payment_status'       => 'cancelled',
                    'payment_error_response' => ['message' => 'Paiement annulé par l\'utilisateur.'],
                ]);

                $orderUuid = $paydunya->order->uuid ?? null;

                return $this->flashAndRedirect(
                    'Votre paiement PayDunya a été annulé.',
                    'warning',
                    $orderUuid
                );
            }
        }

        return $this->flashAndRedirect('Le paiement a été annulé.', 'warning');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. IPN — Notification Instantanée de Paiement (callback_url)
    //    PayDunya envoie une requête POST en arrière-plan
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * POST /paydunya/ipn
     *
     * Structure reçue :
     * $_POST['data']['hash']           => SHA-512(masterKey)
     * $_POST['data']['status']         => completed | pending | cancelled
     * $_POST['data']['invoice']['token']
     * $_POST['data']['invoice']['total_amount']
     * $_POST['data']['customer']['name']
     * $_POST['data']['customer']['phone']
     * $_POST['data']['customer']['email']
     * $_POST['data']['custom_data']['order_id']
     * $_POST['data']['custom_data']['restaurant_hash']
     */
    public function handleIpn(Request $request): Response
    {
        Log::info('PayDunya IPN reçu', ['payload' => $request->all()]);

        try {
            $data = $request->input('data');

            if (!is_array($data)) {
                Log::warning('PayDunya IPN: Structure invalide', ['raw' => $request->all()]);
                return response('Invalid payload structure', 400);
            }

            // ── Extraction des données ──────────────────────────────────────
            $receivedHash    = $data['hash']                        ?? null;
            $status          = $data['status']                      ?? null;
            $token           = $data['invoice']['token']            ?? null;
            $totalAmount     = $data['invoice']['total_amount']     ?? null;
            $customerName    = $data['customer']['name']            ?? null;
            $customerEmail   = $data['customer']['email']           ?? null;
            $customerPhone   = $data['customer']['phone']           ?? null;
            $restaurantHash  = $data['custom_data']['restaurant_hash'] ?? null;
            $receiptUrl      = $data['receipt_url']                 ?? null;

            // ── Vérification du token et du restaurant ──────────────────────
            if (!$token || !$restaurantHash) {
                Log::warning('PayDunya IPN: token ou restaurant_hash manquant');
                return response('Missing token or restaurant hash', 400);
            }

            $restaurant = Restaurant::where('hash', $restaurantHash)->first();
            if (!$restaurant) {
                Log::warning('PayDunya IPN: Restaurant introuvable', ['hash' => $restaurantHash]);
                return response('Restaurant not found', 404);
            }

            // ── Vérification du hash (sécurité — SHA-512 de la MasterKey) ───
            $credential  = $restaurant->paymentGateways;
            $masterKey   = $credential->paydunya_master_key ?? '';
            $expectedHash = hash('sha512', $masterKey);

            if ($receivedHash !== $expectedHash) {
                Log::error('PayDunya IPN: Hash invalide — requête suspecte', [
                    'received' => $receivedHash,
                    'expected' => $expectedHash,
                ]);
                return response('Invalid hash — unauthorized request', 403);
            }

            // ── Recherche du paiement en base ───────────────────────────────
            $paydunya = AdminPaydunyaPayment::where('paydunya_token', $token)->first();

            if (!$paydunya) {
                Log::warning('PayDunya IPN: Paiement introuvable en base', ['token' => $token]);
                return response('Payment record not found', 404);
            }

            // ── Idempotence : éviter le double traitement ───────────────────
            if ($paydunya->isCompleted()) {
                Log::info('PayDunya IPN: Paiement déjà traité', ['token' => $token]);
                return response('OK', 200);
            }

            // ── Traitement selon le statut ──────────────────────────────────
            if ($status === 'completed') {
                $paydunya->update([
                    'payment_status'  => 'completed',
                    'customer_name'   => $customerName,
                    'customer_email'  => $customerEmail,
                    'customer_phone'  => $customerPhone,
                    'receipt_url'     => $receiptUrl,
                    'payment_response'=> $data,
                ]);

                if ($paydunya->slate_id) {
                    $this->markSlateAsPaid($paydunya, $paydunya->slate);
                } else {
                    $this->markOrderAsPaid($paydunya, $paydunya->order);
                }

                Log::info('PayDunya IPN: Paiement complété', [
                    'token'    => $token,
                    'order_id' => $paydunya->order_id,
                    'slate_id' => $paydunya->slate_id,
                    'amount'   => $totalAmount,
                ]);

            } elseif ($status === 'cancelled') {
                $paydunya->update([
                    'payment_status'        => 'cancelled',
                    'payment_error_response'=> $data,
                ]);

                Log::info('PayDunya IPN: Paiement annulé', ['token' => $token]);

            } else {
                // pending ou autre
                $paydunya->update([
                    'payment_status'  => $status ?? 'pending',
                    'payment_response'=> $data,
                ]);
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('PayDunya IPN: Exception', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response('Internal Server Error', 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Méthodes utilitaires privées
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Marque la commande comme payée et crée l'enregistrement Payment.
     */
    private function markOrderAsPaid(AdminPaydunyaPayment $paydunya, Order $order): void
    {
        // Création / mise à jour de l'enregistrement Payment
        Payment::updateOrCreate(
            [
                'order_id'       => $paydunya->order_id,
                'payment_method' => 'due',
                'amount'         => $paydunya->amount,
            ],
            [
                'payment_method' => 'paydunya',
                'branch_id'      => $order->branch_id,
                'transaction_id' => $paydunya->paydunya_token,
            ]
        );

        // Mise à jour de la commande
        $order->amount_paid = $order->amount_paid + $paydunya->amount;
        $order->status      = 'paid';
        $order->save();

        // Dispatch des événements (notification temps réel + email client)
        SendNewOrderReceived::dispatch($order);

        if ($order->customer_id) {
            SendOrderBillEvent::dispatch($order);
        }
    }

    /**
     * Marque toutes les commandes d'une ardoise comme payées et crée les enregistrements Payment.
     */
    private function markSlateAsPaid(AdminPaydunyaPayment $paydunya, Slate $slate): void
    {
        $unpaidOrders = $slate->unpaidOrders()->get();
        
        foreach ($unpaidOrders as $order) {
            $remainingAmount = $order->total - ($order->amount_paid ?? 0);
            
            if ($remainingAmount <= 0) continue;

            Payment::create([
                'order_id'              => $order->id,
                'branch_id'             => $slate->branch_id,
                'payment_method'        => 'paydunya',
                'amount'                => $remainingAmount,
                'status'                => 'completed',
                'payment_date'          => now(),
                'transaction_reference' => $paydunya->paydunya_token,
                'notes'                 => 'Paiement en ligne via ardoise ' . $slate->code,
            ]);

            $order->amount_paid = ($order->amount_paid ?? 0) + $remainingAmount;
            $order->status = 'paid';
            $order->save();
        }

        $slate->recalculateAmounts();
    }

    /**
     * Flash un message et redirige l'utilisateur.
     */
    private function flashAndRedirect(
        string  $message,
        string  $style,
        ?string $orderUuid = null
    ): RedirectResponse {
        session()->flash('flash.banner', $message);
        session()->flash('flash.bannerStyle', $style);

        if ($orderUuid) {
            return redirect()->route('order_success', $orderUuid);
        }

        return redirect()->back();
    }
}