<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CashSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'opened_by',
        'closed_by',
        'session_number',
        'status',
        'opened_at',
        'closed_at',
        'opening_balance',
        'expected_balance',
        'closing_balance',
        'discrepancy',
        'total_transactions',
        'total_sales',
        'total_cash',
        'total_mobile_money',
        'total_qr_code',
        'total_card',
        'total_other',
        'opening_notes',
        'closing_notes',
        'discrepancy_justification'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'discrepancy' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash' => 'decimal:2',
        'total_mobile_money' => 'decimal:2',
        'total_qr_code' => 'decimal:2',
        'total_card' => 'decimal:2',
        'total_other' => 'decimal:2',
    ];

    // Relations
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function openedByUser()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function details()
    {
        return $this->hasMany(CashSessionDetail::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function discrepancies()
    {
        return $this->hasMany(CashDiscrepancy::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('opened_at', today());
    }

    // Méthodes utilitaires
    public static function generateSessionNumber($branchId)
    {
        $date = now()->format('Ymd');
        $count = self::where('branch_id', $branchId)
            ->whereDate('opened_at', today())
            ->count();
        
        return sprintf('CS-%s-B%d-%03d', $date, $branchId, $count + 1);
    }

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function hasDiscrepancies()
    {
        return abs($this->discrepancy) > 100; // Tolérance de 0.01
    }

    public function addTransaction($paymentId, $amount, $paymentMethod, $type = 'sale')
    {
        $transaction = $this->transactions()->create([
            'payment_id' => $paymentId,
            'order_id' => Payment::find($paymentId)?->order_id,
            'user_id' => auth()->id(),
            'transaction_number' => CashTransaction::generateTransactionNumber(),
            'type' => $type,
            'payment_method' => $paymentMethod,
            'amount' => $amount,
            'transaction_at' => now(),
        ]);

        // Mise à jour des totaux
        $this->updateTotals();

        return $transaction;
    }

    public function updateTotals()
    {
        $transactions = $this->transactions()
            ->where('type', 'sale')
            ->selectRaw('
                payment_method,
                SUM(amount) as total
            ')
            ->groupBy('payment_method')
            ->get();

        $this->total_cash = $transactions->where('payment_method', 'cash')->sum('total');
        $this->total_mobile_money = $transactions->whereIn('payment_method', [
            'mobile_money_orange',
            'mobile_money_wave',
            'mobile_money_mtn',
            'mobile_money_moov'
        ])->sum('total');
        $this->total_qr_code = $transactions->where('payment_method', 'qr_code')->sum('total');
        $this->total_card = $transactions->where('payment_method', 'card')->sum('total');
        $this->total_other = $transactions->where('payment_method', 'other')->sum('total');

        $this->total_transactions = $this->transactions()->count();
        $this->total_sales = $transactions->sum('total');
        
        // Calcul du solde attendu
        $this->expected_balance = $this->opening_balance + $this->total_sales;

        $this->save();
    }

    public function closeSession($closingAmounts, $justification = null, $closingNotes = null)
    {
        DB::beginTransaction();
        try {
            // Mise à jour des totaux avant fermeture
            $this->updateTotals();

            $totalClosing = 0;

            // Enregistrement des montants de fermeture
            foreach ($closingAmounts as $paymentMethod => $amount) {
                $this->details()->create([
                    'payment_method' => $paymentMethod,
                    'type' => 'closing',
                    'amount' => $amount,
                ]);

                $totalClosing += $amount;

                // Calcul des écarts par moyen de paiement
                $openingAmount = $this->details()
                    ->where('payment_method', $paymentMethod)
                    ->where('type', 'opening')
                    ->sum('amount');

                $transactionsAmount = $this->transactions()
                    ->where('payment_method', $paymentMethod)
                    ->where('type', 'sale')
                    ->sum('amount');

                $expectedAmount = $openingAmount + $transactionsAmount;
                $difference = $amount - $expectedAmount;

                if (abs($difference) > 0.01) {
                    $type = $difference > 0 ? 'surplus' : 'shortage';
                    
                    $this->discrepancies()->create([
                        'payment_method' => $paymentMethod,
                        'expected_amount' => $expectedAmount,
                        'actual_amount' => $amount,
                        'difference' => $difference,
                        'type' => $type,
                        'justification' => $justification,
                    ]);
                }
            }

            // Fermeture de la session
            $this->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => auth()->id(),
                'closing_balance' => $totalClosing,
                'discrepancy' => $totalClosing - $this->expected_balance,
                'closing_notes' => $closingNotes,
                'discrepancy_justification' => $justification,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getOpeningAmountForPaymentMethod($paymentMethod)
    {
        return $this->details()
            ->where('payment_method', $paymentMethod)
            ->where('type', 'opening')
            ->sum('amount');
    }

    public function getClosingAmountForPaymentMethod($paymentMethod)
    {
        return $this->details()
            ->where('payment_method', $paymentMethod)
            ->where('type', 'closing')
            ->sum('amount');
    }

    public function getDuration()
    {
        if (!$this->closed_at) {
            return $this->opened_at->diffForHumans();
        }

        return $this->opened_at->diffForHumans($this->closed_at, true);
    }
}
