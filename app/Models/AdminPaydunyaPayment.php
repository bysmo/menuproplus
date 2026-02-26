<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPaydunyaPayment extends Model
{
    use HasFactory;

    protected $table = 'admin_paydunya_payments';

    protected $fillable = [
        'order_id',
        'paydunya_token',
        'invoice_url',
        'amount',
        'currency',
        'payment_status',
        'transaction_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'receipt_url',
        'payment_response',
        'payment_error_response',
        'status'
    ];

    protected $casts = [
        'payment_response'       => 'array',
        'payment_error_response' => 'array',
        'amount'                 => 'decimal:2',
    ];

    /**
     * Relation : commande associée
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Vérifie si le paiement est complété
     */
    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Vérifie si le paiement est en attente
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }
}