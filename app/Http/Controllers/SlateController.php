<?php

namespace App\Http\Controllers;

use App\Models\Slate;
use App\Models\Order;
use App\Models\Payment;
use App\Models\RestaurantTax;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class SlateController extends Controller
{
    /**
     * Afficher la liste des ardoises
     */
    public function index()
    {
        abort_if(!in_array('Order', restaurant_modules()), 403);
        abort_if(!user_can('Show Order'), 403);

        return view('slates.index');
    }

    /**
     * Confirmer le paiement d'une ardoise
     */
    public function confirmPayment(Request $request, $slateId)
    {
        abort_if(!user_can('Edit Order'), 403);

        try {
            $slate = Slate::with('orders')->findOrFail($slateId);

            // Vérifier que l'ardoise appartient au restaurant courant
            if ($slate->restaurant_id !== restaurant()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ardoise non trouvée pour ce restaurant'
                ], 403);
            }

            // Mettre à jour tous les ordres impayés
            $unpaidOrders = $slate->orders()
                ->whereIn('status', ['pending_verification', 'pending', 'accepted', 'preparing', 'ready', 'kot'])
                ->get();

            if ($unpaidOrders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune commande impayée à confirmer'
                ], 400);
            }

            // Confirmer le paiement de chaque commande
            foreach ($unpaidOrders as $order) {
                $order->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'is_paid' => true
                ]);

                // Mettre à jour le paiement associé s'il existe
                $payment = Payment::where('order_id', $order->id)->first();
                if ($payment && $payment->status !== 'paid') {
                    $payment->update([
                        'status' => 'paid',
                        'paid_at' => now()
                    ]);
                }
            }

            // Recalculer les montants de l'ardoise
            $slate->recalculateAmounts();

            // Mettre à jour le statut de l'ardoise
            if ($slate->remaining_amount <= 0) {
                $slate->update(['status' => 'paid']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement confirmé avec succès',
                'slate' => $slate->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur confirmation paiement ardoise', [
                'slate_id' => $slateId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la confirmation du paiement'
            ], 500);
        }
    }

    /**
     * Imprimer la facture d'une ardoise
     */
    public function printInvoice($slateId)
    {
        abort_if(!user_can('Show Order'), 403);

        try {
            $slate = Slate::with([
                'orders.items.menuItem',
                'orders.payments',
                'orders.table',
                'restaurant',
                'branch'
            ])->findOrFail($slateId);

            // Vérifier que l'ardoise appartient au restaurant courant
            if ($slate->restaurant_id !== restaurant()->id) {
                abort(403, 'Accès non autorisé');
            }

            $restaurant = $slate->restaurant;
            $branch = $slate->branch;
            $taxDetails = RestaurantTax::where('restaurant_id', $restaurant->id)->get();
            $receiptSettings = $restaurant->receiptSetting;

            // Calculer les totaux
            $subtotal = 0;
            $totalTaxAmount = 0;
            $totalDiscount = 0;

            foreach ($slate->orders as $order) {
                $subtotal += $order->sub_total ?? 0;
                $totalTaxAmount += $order->total_tax_amount ?? 0;
                $totalDiscount += $order->discount ?? 0;
            }

            $data = [
                'slate' => $slate,
                'restaurant' => $restaurant,
                'branch' => $branch,
                'taxDetails' => $taxDetails,
                'receiptSettings' => $receiptSettings,
                'subtotal' => $subtotal,
                'totalTaxAmount' => $totalTaxAmount,
                'totalDiscount' => $totalDiscount,
                'grandTotal' => $slate->total_amount
            ];

            return view('slates.print-invoice', $data);

        } catch (\Exception $e) {
            \Log::error('Erreur impression facture ardoise', [
                'slate_id' => $slateId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erreur lors de l\'impression de la facture');
        }
    }

    /**
     * Télécharger la facture en PDF
     */
    public function downloadInvoice($slateId)
    {
        abort_if(!user_can('Show Order'), 403);

        try {
            $slate = Slate::with([
                'orders.items.menuItem',
                'orders.payments',
                'orders.table',
                'restaurant',
                'branch'
            ])->findOrFail($slateId);

            if ($slate->restaurant_id !== restaurant()->id) {
                abort(403, 'Accès non autorisé');
            }

            $restaurant = $slate->restaurant;
            $branch = $slate->branch;
            $taxDetails = RestaurantTax::where('restaurant_id', $restaurant->id)->get();
            $receiptSettings = $restaurant->receiptSetting;

            // Calculer les totaux
            $subtotal = 0;
            $totalTaxAmount = 0;
            $totalDiscount = 0;

            foreach ($slate->orders as $order) {
                $subtotal += $order->sub_total ?? 0;
                $totalTaxAmount += $order->total_tax_amount ?? 0;
                $totalDiscount += $order->discount ?? 0;
            }

            $data = [
                'slate' => $slate,
                'restaurant' => $restaurant,
                'branch' => $branch,
                'taxDetails' => $taxDetails,
                'receiptSettings' => $receiptSettings,
                'subtotal' => $subtotal,
                'totalTaxAmount' => $totalTaxAmount,
                'totalDiscount' => $totalDiscount,
                'grandTotal' => $slate->total_amount
            ];

            $pdf = Pdf::loadView('slates.print-invoice', $data);

            return $pdf->download('facture-ardoise-' . $slate->code . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Erreur téléchargement facture ardoise', [
                'slate_id' => $slateId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erreur lors du téléchargement de la facture');
        }
    }
}
