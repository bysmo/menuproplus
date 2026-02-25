<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashSessionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_session_id',
        'payment_method',
        'type',
        'amount',
        'details',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'details' => 'array',
    ];

    // Relations
    public function cashSession()
    {
        return $this->belongsTo(CashSession::class);
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

    public function isOpening()
    {
        return $this->type === 'opening';
    }

    public function isClosing()
    {
        return $this->type === 'closing';
    }
}
