<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_session_id',
        'order_id',
        'payment_id',
        'user_id',
        'transaction_number',
        'type',
        'payment_method',
        'amount',
        'description',
        'metadata',
        'transaction_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'transaction_at' => 'datetime',
    ];

    // Relations
    public function cashSession()
    {
        return $this->belongsTo(CashSession::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utilitaires
    public static function generateTransactionNumber()
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('transaction_at', today())->count();
        
        return sprintf('TXN-%s-%04d', $date, $count + 1);
    }

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
            'sale' => '💰 Vente',
            'refund' => '↩️ Remboursement',
            'adjustment' => '⚙️ Ajustement',
            'other' => '🔄 Autre',
        ];

        return $labels[$this->type] ?? $this->type;
    }
}
