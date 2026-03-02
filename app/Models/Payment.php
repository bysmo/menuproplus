<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class Payment extends BaseModel
{
    use HasFactory;
    use HasBranch;

    protected $guarded = ['id'];

    protected $fillable = [
        'order_id',
        'branch_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'validated_by',
        'validated_at',
        'is_refunded',
        'refunded_at',
        'refund_reason',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'is_refunded' => 'boolean',
        'refunded_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
