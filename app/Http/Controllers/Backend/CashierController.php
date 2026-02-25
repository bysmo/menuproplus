<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CashierController extends Controller
{

    /**
     * ✅ Afficher la page principale du module caisse
     */
    public function index()
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        // ✅ Vérifier que la branche est disponible
        if (!branch()) {
            return back()->with('error', 'Aucune branche sélectionnée. Veuillez sélectionner une branche.');
        }

        // ✅ Utiliser branch()->id au lieu de auth()->user()->branch_id
        $activeSession = CashSession::open()
            ->forBranch(branch()->id) // ✅ CORRECTION
            ->with(['openedByUser', 'details', 'transactions'])
            ->first();

        return view('backend.cashier.index', compact('activeSession'));
    }

    /**
     * ✅ Ouvrir une nouvelle session de caisse
     */
    public function openSession(Request $request)
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        // ✅ Vérifier que la branche est disponible
        if (!branch()) {
            return back()->with('error', 'Aucune branche sélectionnée. Veuillez sélectionner une branche.');
        }

        $request->validate([
            'opening_amounts' => 'required|array',
            'opening_amounts.*' => 'required|numeric|min:0',
            'opening_notes' => 'nullable|string|max:1000',
        ]);

        // ✅ Utiliser branch()->id
        $branchId = branch()->id;

        // Vérifier qu'il n'y a pas déjà une session ouverte pour cette succursale
        $existingSession = CashSession::open()
            ->forBranch($branchId)
            ->exists();

        if ($existingSession) {
            return back()->with('error', 'Une session de caisse est déjà ouverte dans cette agence. Elle doit être fermée avant d\'en ouvrir une nouvelle.');
        }

        DB::beginTransaction();
        try {
            // Créer la session
            $session = CashSession::create([
                'branch_id' => $branchId, // ✅ CORRECTION
                'opened_by' => auth()->id(),
                'session_number' => CashSession::generateSessionNumber($branchId),
                'status' => 'open',
                'opened_at' => now(),
                'opening_balance' => array_sum($request->opening_amounts),
                'opening_notes' => $request->opening_notes,
            ]);

            // Enregistrer les montants par moyen de paiement
            foreach ($request->opening_amounts as $paymentMethod => $amount) {
                if ($amount > 0) {
                    $session->details()->create([
                        'payment_method' => $paymentMethod,
                        'type' => 'opening',
                        'amount' => $amount,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('backend.cashier.session', $session->id)
                ->with('success', 'Session de caisse ouverte avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'ouverture de la session : ' . $e->getMessage());
        }
    }

    /**
     * ✅ Afficher les détails d'une session
     */
    public function showSession($id)
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        // ✅ Vérifier que la branche est disponible
        if (!branch()) {
            abort(403, 'Aucune branche sélectionnée.');
        }

        $session = CashSession::with([
            'openedByUser',
            'closedByUser',
            'branch',
            'details',
            'transactions.order',
            'transactions.user',
            'discrepancies'
        ])->findOrFail($id);

        // ✅ Vérifier que l'utilisateur a accès à cette session
        // Admin peut voir toutes les sessions de toutes les branches
        // Utilisateur normal ne peut voir que les sessions de sa branche
        if (auth()->user()->branch_id && $session->branch_id !== branch()->id) {
            abort(403, 'Accès non autorisé à cette session.');
        }

        return view('backend.cashier.session', compact('session'));
    }

    /**
     * ✅ Encaisser un paiement
     */
    public function processPayment(Request $request)
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'session_id' => 'required|exists:cash_sessions,id',
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $session = CashSession::findOrFail($request->session_id);

        // ✅ Vérifier que la session appartient à la branche active
        if (branch() && $session->branch_id !== branch()->id) {
            return back()->with('error', 'Cette session n\'appartient pas à la branche active.');
        }

        if ($session->isClosed()) {
            return back()->with('error', 'Cette session est déjà fermée.');
        }

        DB::beginTransaction();
        try {
            $payment = Payment::findOrFail($request->payment_id);
            
            // Marquer le paiement comme validé
            $payment->update([
                'status' => 'completed',
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);

            // Enregistrer la transaction
            $session->addTransaction(
                $payment->id,
                $request->amount,
                $request->payment_method,
                'sale'
            );

            DB::commit();

            return back()->with('success', 'Paiement encaissé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'encaissement : ' . $e->getMessage());
        }
    }

    /**
     * ✅ Fermer une session de caisse
     */
    public function closeSession(Request $request, $id)
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'closing_amounts' => 'required|array',
            'closing_amounts.*' => 'required|numeric|min:0',
            'discrepancy_justification' => 'nullable|string|max:1000',
            'closing_notes' => 'nullable|string|max:1000',
        ]);

        $session = CashSession::findOrFail($id);

        // ✅ Vérifier que la session appartient à la branche active
        if (branch() && $session->branch_id !== branch()->id) {
            return back()->with('error', 'Cette session n\'appartient pas à la branche active.');
        }

        if ($session->isClosed()) {
            return back()->with('error', 'Cette session est déjà fermée.');
        }

        // ✅ Vérifier que seul le propriétaire peut fermer
        if ($session->opened_by !== auth()->id()) {
            return back()->with('error', 'Seul le caissier qui a ouvert cette session peut la fermer.');
        }

        try {
            $session->closeSession(
                $request->closing_amounts,
                $request->discrepancy_justification,
                $request->closing_notes
            );

            return redirect()
                ->route('backend.cashier.session', $session->id)
                ->with('success', 'Session de caisse fermée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la fermeture : ' . $e->getMessage());
        }
    }

    /**
     * ✅ Imprimer le bordereau d'ouverture
     */
    public function printOpening($id)
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        $session = CashSession::with([
            'openedByUser',
            'branch',
            'details' => fn($q) => $q->where('type', 'opening')
        ])->findOrFail($id);

        // ✅ Vérification de la branche (optionnelle pour l'impression)
        // Les admins peuvent imprimer toutes les sessions
        if (auth()->user()->branch_id && $session->branch_id !== branch()->id) {
            abort(403, 'Accès non autorisé à cette session.');
        }

        return view('backend.cashier.print-opening', compact('session'));
    }

    /**
     * ✅ Imprimer le bordereau de fermeture
     */
    public function printClosing($id)
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        $session = CashSession::with([
            'openedByUser',
            'closedByUser',
            'branch',
            'details',
            'transactions',
            'discrepancies'
        ])->findOrFail($id);

        if ($session->isOpen()) {
            return back()->with('error', 'Cette session n\'est pas encore fermée.');
        }

        // ✅ Vérification de la branche (optionnelle pour l'impression)
        if (auth()->user()->branch_id && $session->branch_id !== branch()->id) {
            abort(403, 'Accès non autorisé à cette session.');
        }

        return view('backend.cashier.print-closing', compact('session'));
    }

    /**
     * ✅ Télécharger le bordereau en PDF
     */
    public function downloadPdf($id, $type = 'closing')
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        $session = CashSession::with([
            'openedByUser',
            'closedByUser',
            'branch',
            'details',
            'transactions',
            'discrepancies'
        ])->findOrFail($id);

        // ✅ Vérification de la branche
        if (auth()->user()->branch_id && $session->branch_id !== branch()->id) {
            abort(403, 'Accès non autorisé à cette session.');
        }

        $view = $type === 'opening' 
            ? 'backend.cashier.print-opening' 
            : 'backend.cashier.print-closing';

        $pdf = Pdf::loadView($view, compact('session'));
        
        $filename = sprintf(
            'bordereau-%s-%s.pdf',
            $type,
            $session->session_number
        );

        return $pdf->download($filename);
    }

    /**
     * ✅ Liste des commandes en attente de paiement
     */
    public function pendingOrders()
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        // ✅ Vérifier que la branche est disponible
        if (!branch()) {
            return back()->with('error', 'Aucune branche sélectionnée. Veuillez sélectionner une branche.');
        }

        // ✅ Utiliser branch()->id
        $orders = Order::with(['customer', 'payments'])
            ->where('branch_id', branch()->id) // ✅ CORRECTION
            ->whereIn('status', ['pending', 'processing', 'completed'])
            ->whereHas('payments', function($q) {
                $q->where('status', 'pending');
            })
            ->latest()
            ->paginate(20);

        return view('backend.cashier.pending-orders', compact('orders'));
    }

    /**
     * ✅ Liste des sessions en attente de validation
     */
    public function validation()
    {
        if (!user_can('validate_cash_session')) {
            abort(403, 'Accès non autorisé');
        }

        // ✅ Vérifier que la branche est disponible
        if (!branch()) {
            return back()->with('error', 'Aucune branche sélectionnée. Veuillez sélectionner une branche.');
        }

        // ✅ Utiliser branch()->id
        $sessions = CashSession::with(['openedByUser', 'closedByUser'])
            ->forBranch(branch()->id) // ✅ CORRECTION
            ->whereNotNull('closed_at')
            ->whereNull('validated_by')
            ->latest('closed_at')
            ->paginate(20);

        return view('backend.cashier.validation', compact('sessions'));
    }

    /**
     * ✅ Valider une session fermée
     */
    public function validateSession($id)
    {
        if (!user_can('validate_cash_session')) {
            abort(403, 'Accès non autorisé');
        }

        $session = CashSession::findOrFail($id);

        // ✅ Vérifier que la session appartient à la branche active
        if (branch() && $session->branch_id !== branch()->id) {
            return back()->with('error', 'Cette session n\'appartient pas à la branche active.');
        }

        if ($session->status !== 'closed') {
            return back()->with('error', 'Seules les sessions fermées peuvent être validées.');
        }

        if ($session->validated_by) {
            return back()->with('error', 'Cette session est déjà validée.');
        }

        // Empêcher l'auto-validation par celui qui a fermé
        if ($session->closed_by === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas valider une session que vous avez fermée vous-même.');
        }

        $session->update([
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Session validée avec succès.');
    }
    
    /**
     * ✅ Historique des sessions
     */
    public function history(Request $request)
    {
        if (!user_can('manage_cashier')) {
            abort(403, 'Accès non autorisé');
        }

        // ✅ Vérifier que la branche est disponible
        if (!branch()) {
            return back()->with('error', 'Aucune branche sélectionnée. Veuillez sélectionner une branche.');
        }

        // ✅ Utiliser branch()->id
        $query = CashSession::with(['openedByUser', 'closedByUser'])
            ->forBranch(branch()->id); // ✅ CORRECTION

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('opened_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('opened_at', '<=', $request->to_date);
        }

        if ($request->filled('user_id')) {
            $query->where('opened_by', $request->user_id);
        }

        $sessions = $query->latest('opened_at')->paginate(20);

        return view('backend.cashier.history', compact('sessions'));
    }
}