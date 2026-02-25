<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDiscrepancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_session_id',
        'payment_method',
        'expected_amount',
        'actual_amount',
        'difference',
        'type',
        'justification',
        'approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'difference' => 'decimal:2',
        'approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relations
    public function cashSession()
    {
        return $this->belongsTo(CashSession::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Méthodes utilitaires
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cash' => '💵 Espèces',
            'mobile_money_orange' => '🟠 Orange Money',
            'mobile_money_wave' => '💙 Wave',
            'mobile_money_mtn' => '🟡 MTN Mobile Money',
            'mobile_money_moov' => '🔵 Moov Money',
            'qr_code' => '📱 QR Code',
            'card' => '💳 Carte Bancaire',
            'other' => '🔄 Autre',
        ];

        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            'surplus' => '➕ Excédent',
            'shortage' => '➖ Manquant',
            'balanced' => '✅ Équilibré',
        ];

        return $labels[$this->type] ?? $this->type;
    }

    public function getTypeColorAttribute()
    {
        $colors = [
            'surplus' => 'success',
            'shortage' => 'danger',
            'balanced' => 'info',
        ];

        return $colors[$this->type] ?? 'secondary';
    }

    public function isSurplus()
    {
        return $this->type === 'surplus';
    }

    public function isShortage()
    {
        return $this->type === 'shortage';
    }

    public function isBalanced()
    {
        return $this->type === 'balanced';
    }

    public function approve($userId = null)
    {
        $this->update([
            'approved' => true,
            'approved_by' => $userId ?? auth()->id(),
            'approved_at' => now(),
        ]);
    }
}
