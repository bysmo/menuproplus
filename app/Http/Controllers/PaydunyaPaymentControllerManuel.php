<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\PaymentGatewayCredential;
use App\Events\SendNewOrderReceived;
use App\Events\SendOrderBillEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PaydunyaPaymentControllerManuel
 *
 * Implémente le flux PAR (Paiement Avec Redirection) de PayDunya.
 *
 * FLUX :
 *  1. POST /paydunya/initiate-payment  → crée la facture sur PayDunya, retourne l'invoice_url
 *  2. Redirection client vers invoice_url (page PayDunya : Orange Money, MTN, Moov…)
 *  3. GET  /paydunya/callback?token=xxx → vérification et validation de la commande
 *  4. POST /paydunya/ipn               → notification serveur-à-serveur (IPN)
 *
 * Chaque restaurant utilise SES PROPRES clés API stockées dans payment_gateway_credentials.
 *
 * Ref documentation officielle : https://developers.paydunya.com/doc/FR/php
 */
class PaydunyaPaymentControllerManuel   extends Controller
{
    // ── Endpoints PayDunya ────────────────────────────────────────────────────
    private const API_TEST = 'https://app.paydunya.com/sandbox-api/v1';
    private const API_LIVE = 'https://app.paydunya.com/api/v1';

    // ── Clés chargées depuis la BDD pour le restaurant en cours ──────────────
    private string $masterKey  = '';
    private string $privateKey = '';
    private string $publicKey  = '';
    private string $token      = '';
    private string $mode       = 'test';

    // ═════════════════════════════════════════════════════════════════════════
    // CONFIGURATION PAR RESTAURANT
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Charge les clés API du restaurant depuis payment_gateway_credentials.
     * La configuration est STRICTEMENT liée au restaurant — jamais partagée.
     */
    private function setupForRestaurant(Restaurant $restaurant): void
    {
        $gw = PaymentGatewayCredential::withoutGlobalScopes()
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();

        if (! $gw->paydunya_status) {
            abort(403, 'PayDunya n\'est pas activé pour ce restaurant.');
        }

        $this->masterKey  = $gw->paydunya_master_key  ?? '';
        $this->privateKey = $gw->paydunya_private_key ?? '';
        $this->publicKey  = $gw->paydunya_public_key  ?? '';
        $this->token      = $gw->paydunya_token        ?? '';
        $this->mode       = $gw->paydunya_mode         ?? 'test';
    }

    /** URL de base selon le mode (test / live) */
    private function baseUrl(): string
    {
        return $this->mode === 'live' ? self::API_LIVE : self::API_TEST;
    }

    /**
     * En-têtes HTTP requis par PayDunya.
     *
     * Ref doc : Paydunya_Setup::setMasterKey / setPublicKey / setPrivateKey / setToken
     */
    private function headers(): array
    {
        return [
            'PAYDUNYA-MASTER-KEY'  => $this->masterKey,
            'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
            'PAYDUNYA-PUBLIC-KEY'  => $this->publicKey,
            'PAYDUNYA-TOKEN'       => $this->token,
            'Content-Type'         => 'application/json',
        ];
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 1. INITIER UN PAIEMENT — POST /paydunya/initiate-payment (PAR — Paiement Avec Redirection)
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Crée une facture PayDunya et retourne l'URL de paiement au front-end.
     *
     * Body JSON attendu :
     * {
     *   "order_id"        : 123,
     *   "restaurant_hash" : "abc123",
     *   "customer_name"   : "Koffi Amos",       (optionnel)
     *   "customer_phone"  : "0700000000",        (optionnel)
     *   "customer_email"  : "koffi@mail.com"     (optionnel)
     * }
     *
     * Réponse en succès :
     * { "success": true, "invoice_url": "https://app.paydunya.com/...", "token": "test_xxx" }
     *
     * Ref doc : $invoice->create() → header("Location: ".$invoice->getInvoiceUrl())
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'order_id'        => 'required|integer|exists:orders,id',
            'restaurant_hash' => 'required|string',
        ]);

        $restaurant = Restaurant::where('hash', $request->restaurant_hash)->firstOrFail();
        $this->setupForRestaurant($restaurant);

        $order = Order::with('items.menuItem', 'branch')->findOrFail($request->order_id);

        // ── Calcul du montant restant à payer ───────────────────────────────
        $amountToPay = (int) ($order->total - ($order->amount_paid ?? 0));

        if ($amountToPay <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande est déjà entièrement payée.',
            ], 422);
        }

        // ── Construction de la facture PayDunya ─────────────────────────────
        // Ref doc : $invoice->addItem(nom, qté, prix_unitaire, total, description_optionnelle)
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'name'        => $item->menuItem->item_name ?? 'Article',
                'quantity'    => $item->quantity,
                'unit_price'  => (int) $item->price,
                'total_price' => (int) ($item->price * $item->quantity),
            ];
        }

        // Si pas d'items (ex : paiement ardoise), on ajoute un item générique
        if (empty($items)) {
            $items[] = [
                'name'        => 'Commande #' . $order->order_number,
                'quantity'    => 1,
                'unit_price'  => $amountToPay,
                'total_price' => $amountToPay,
            ];
        }

        // ── Payload de création de facture ──────────────────────────────────
        $payload = [
            // Informations de la boutique (Ref: \Paydunya\Checkout\Store::setName)
            'store' => [
                'name'        => $restaurant->name,
                'website_url' => url('/restaurant/' . $restaurant->hash),
            ],

            // Articles de la facture (Ref: $invoice->addItem)
            'invoice' => [
                'items'        => $items,
                'total_amount' => $amountToPay,
                'description'  => 'Commande #' . $order->order_number . ' — ' . $restaurant->name,
            ],

            // URLs de redirection/notification (Ref: Store::setCallbackUrl, setReturnUrl, setCancelUrl)
            'actions' => [
                'callback_url' => route('paydunya.ipn'),        // IPN serveur-à-serveur
                'return_url'   => route('paydunya.callback'),   // Retour client après paiement
                'cancel_url'   => url('/restaurant/' . $restaurant->hash),
            ],

            // Données personnalisées restituées dans l'IPN
            'custom_data' => [
                'order_id'        => $order->id,
                'restaurant_hash' => $restaurant->hash,
                'branch_id'       => $order->branch_id,
            ],
        ];

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

        // ── Envoi de la requête à PayDunya ───────────────────────────────────
        // Ref doc : if($invoice->create()) { header("Location: ".$invoice->getInvoiceUrl()); }
        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->post($this->baseUrl() . '/checkout-invoice/create', $payload);

        $data = $response->json();

        Log::info('[PayDunya] Création facture', [
            'order_id'      => $order->id,
            'restaurant'    => $restaurant->name,
            'amount'        => $amountToPay,
            'mode'          => $this->mode,
            'response_code' => $data['response_code'] ?? null,
            'response_text' => $data['response_text'] ?? null,
        ]);

        // response_code = '00' → succès (Ref doc réponse PayDunya)
        if (($data['response_code'] ?? '') === '00') {
            $invoiceToken = $data['token'] ?? null;
            $invoiceUrl   = $data['invoice_url'] ?? null;

            // Enregistrement du paiement en statut "pending"
            Payment::create([
                'order_id'              => $order->id,
                'branch_id'             => $order->branch_id,
                'payment_method'        => 'paydunya',
                'amount'                => $amountToPay,
                'status'                => 'pending',
                'payment_date'          => now(),
                'transaction_reference' => $invoiceToken,
                'notes'                 => 'PayDunya PAR — mode: ' . $this->mode,
            ]);

            return response()->json([
                'success'     => true,
                'invoice_url' => $invoiceUrl,
                'token'       => $invoiceToken,
            ]);
        }

        // Échec de création de facture
        Log::error('[PayDunya] Échec création facture', $data ?? []);

        return response()->json([
            'success' => false,
            'message' => $data['response_text'] ?? 'Impossible de créer la facture PayDunya. Vérifiez vos clés API.',
        ], 422);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 2. CALLBACK — GET /paydunya/callback?token=xxx
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Le client est redirigé ici après avoir payé sur la page PayDunya.
     * PayDunya ajoute ?token=invoice_token à l'URL de retour.
     *
     * On vérifie le statut de la facture en interrogeant /checkout-invoice/confirm/{token}.
     * Ref doc : chunk 17 — statuts possibles : pending / cancelled / completed
     */
    public function handleCallback(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            Log::warning('[PayDunya] Callback sans token');
            return $this->flashAndRedirect('Token PayDunya manquant.', 'danger');
        }

        // Retrouver le paiement en base par son token
        $payment = Payment::where('transaction_reference', $token)
            ->where('payment_method', 'paydunya')
            ->first();

        if (! $payment) {
            Log::warning('[PayDunya] Callback : paiement introuvable', ['token' => $token]);
            return $this->flashAndRedirect('Paiement introuvable.', 'danger');
        }

        $order      = Order::find($payment->order_id);
        $restaurant = $order?->branch?->restaurant;

        // Si déjà traité (ex. par l'IPN), on redirige directement
        if ($payment->status === 'paid') {
            session()->flash('flash.banner', 'Paiement déjà confirmé !');
            session()->flash('flash.bannerStyle', 'success');
            return redirect()->route('order_success', $order->uuid ?? $order->id);
        }

        // Charger les clés du restaurant pour vérifier
        if ($restaurant) {
            try {
                $this->setupForRestaurant($restaurant);
            } catch (\Throwable $e) {
                Log::error('[PayDunya] Callback : impossible de charger les clés', ['error' => $e->getMessage()]);
            }
        }

        // Vérification du statut via l'API PayDunya
        // Ref doc : /checkout-invoice/confirm/{token}
        $verifyResponse = Http::withHeaders($this->headers())
            ->timeout(20)
            ->get($this->baseUrl() . '/checkout-invoice/confirm/' . $token);

        $data   = $verifyResponse->json();
        $status = $data['status'] ?? ($data['data']['status'] ?? 'pending');

        Log::info('[PayDunya] Callback verify', [
            'token'  => $token,
            'status' => $status,
            'data'   => $data,
        ]);

        if ($status === 'completed') {
            if ($order) {
                $this->markOrderPaid($payment, $order);
            }
            session()->flash('flash.banner', '✅ Paiement PayDunya réussi !');
            session()->flash('flash.bannerStyle', 'success');
        } else {
            $payment->update(['status' => ($status === 'cancelled') ? 'cancelled' : 'pending']);
            session()->flash('flash.banner', 'Paiement PayDunya en attente ou annulé (statut: ' . $status . ').');
            session()->flash('flash.bannerStyle', 'warning');
        }

        $redirectTarget = $order?->uuid
            ? route('order_success', $order->uuid)
            : route('dashboard');

        return redirect($redirectTarget);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 3. IPN — POST /paydunya/ipn
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Notification serveur-à-serveur (Instant Payment Notification).
     * PayDunya envoie une requête POST contenant les détails de la transaction.
     *
     * Structure de la réponse IPN (Ref doc chunk 10) :
     * {
     *   "data": {
     *     "response_code": "00",
     *     "response_text": "Transaction Found",
     *     "hash": "<sha512 pour vérification>",
     *     "invoice": {
     *       "token": "test_xxx",
     *       "total_amount": "42300",
     *       ...
     *     },
     *     "custom_data": { "order_id": 123, ... },
     *     "status": "completed"   // pending | cancelled | completed
     *   }
     * }
     *
     * Le hash SHA-512 est calculé sur : masterKey + token
     * et permet de vérifier l'authenticité de la notification.
     */
    public function handleIpn(Request $request)
    {
        $payload = $request->all();

        Log::info('[PayDunya] IPN reçu', ['payload' => $payload]);

        // ── Extraction des champs clés ────────────────────────────────────
        $token      = $payload['data']['invoice']['token'] ?? null;
        $status     = $payload['data']['status']           ?? null;
        $hash       = $payload['data']['hash']             ?? null;
        $customData = $payload['data']['custom_data']      ?? [];
        $orderId    = $customData['order_id'] ?? null;

        if (! $token) {
            Log::warning('[PayDunya] IPN : token manquant');
            return response()->json(['message' => 'Token manquant'], 400);
        }

        // ── Retrouver le paiement & la commande ───────────────────────────
        $payment = Payment::where('transaction_reference', $token)
            ->where('payment_method', 'paydunya')
            ->first();

        if (! $payment && $orderId) {
            // Fallback : chercher par order_id
            $payment = Payment::where('order_id', $orderId)
                ->where('payment_method', 'paydunya')
                ->where('status', 'pending')
                ->latest()
                ->first();
        }

        if (! $payment) {
            Log::warning('[PayDunya] IPN : paiement introuvable', ['token' => $token]);
            return response()->json(['message' => 'Paiement introuvable'], 404);
        }

        $order      = Order::find($payment->order_id);
        $restaurant = $order?->branch?->restaurant;

        // ── Vérification du hash (sécurité) ─────────────────────────────
        // hash_received = SHA-512(masterKey + token)
        if ($restaurant && $hash) {
            try {
                $this->setupForRestaurant($restaurant);
                $expectedHash = hash('sha512', $this->masterKey . $token);
                if (! hash_equals($expectedHash, $hash)) {
                    Log::error('[PayDunya] IPN : hash invalide', [
                        'token'     => $token,
                        'expected'  => substr($expectedHash, 0, 16) . '...',
                        'received'  => substr($hash, 0, 16) . '...',
                    ]);
                    return response()->json(['message' => 'Hash invalide'], 401);
                }
            } catch (\Throwable $e) {
                Log::warning('[PayDunya] IPN : impossible de vérifier le hash', ['error' => $e->getMessage()]);
                // On continue sans vérification si les clés ne sont pas disponibles
            }
        }

        // ── Traitement selon le statut ────────────────────────────────────
        Log::info('[PayDunya] IPN : traitement', [
            'token'      => $token,
            'status'     => $status,
            'order_id'   => $payment->order_id,
            'restaurant' => $restaurant?->name,
        ]);

        match ($status) {
            'completed' => $order
                ? $this->markOrderPaid($payment, $order)
                : $payment->update(['status' => 'paid']),
            'cancelled' => $payment->update(['status' => 'cancelled']),
            default     => null,    // 'pending' ou autre — on ne fait rien
        };

        return response()->json(['message' => 'IPN traité'], 200);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // MÉTHODES PRIVÉES
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Marque le paiement et la commande comme payés,
     * puis déclenche les événements de notification.
     */
    private function markOrderPaid(Payment $payment, Order $order): void
    {
        // Éviter les doubles traitements
        if ($payment->status === 'paid') {
            return;
        }

        $payment->update([
            'status'     => 'paid',
            'payment_date' => now(),
        ]);

        $order->amount_paid = ($order->amount_paid ?? 0) + $payment->amount;

        // Marquer comme payé uniquement si le total couvre la commande
        if ($order->amount_paid >= $order->total) {
            $order->status         = 'paid';
            $order->payment_status = 'paid';
        }

        $order->save();

        Log::info('[PayDunya] Commande payée', [
            'order_id'    => $order->id,
            'amount_paid' => $payment->amount,
            'order_total' => $order->total,
        ]);

        try {
            SendNewOrderReceived::dispatch($order);

            if ($order->customer_id) {
                SendOrderBillEvent::dispatch($order);
            }
        } catch (\Throwable $e) {
            Log::error('[PayDunya] Erreur dispatch événements', ['error' => $e->getMessage()]);
        }
    }

    /** Flash un message en session et redirige vers une route sûre. */
    private function flashAndRedirect(string $message, string $style, string $route = 'dashboard')
    {
        session()->flash('flash.banner', $message);
        session()->flash('flash.bannerStyle', $style);
        return redirect()->route($route);
    }
}
