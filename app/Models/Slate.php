<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Slate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'device_uuid',
        'restaurant_id',
        'branch_id',
        'customer_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'expires_at',
        'last_activity_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    // Relations
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->where('is_on_slate', true);
    }


    public function unpaidOrders()
    {
        return $this->orders()
            ->where(function($query) {
                $query->whereColumn('total', '>', 'amount_paid')
                    ->orWhereNull('amount_paid');
            })
            ->whereNotIn('status', ['paid', 'canceled', 'draft']);
    }


    public function paidOrders()
    {
        // paid if orders.total <= orders.amount_paid and status  in ['paid']
        return $this->orders()->whereColumn('total', '<=', 'amount_paid')->where('status', 'paid');
    }

    public function getPaidOrdersCountAttribute(): int
    {
        return $this->paidOrders()->count();
    }

    public function canceledOrders()
    {
        // canceled if status  in ['cancelled']
        return $this->orders()->where('status', 'cancelled');
    }

    public function remainingOrders()
    {
        // remaining if orders.total > orders.amount_paid and status not in ['paid', 'cancelled','draft','pending_verification','pending_payment']
        return $this->orders()->where('total', '>', 'amount_paid')->whereNotIn('status', ['paid', 'cancelled','draft','pending_verification','pending_payment']);
    }

    // Génération du code incrémental par branche
    public static function generateCode($branchId)
    {
        $prefix = 'ARD';

        // Récupérer le dernier code pour cette branche
        $lastSlate = self::where('branch_id', $branchId)
            ->where('code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSlate && preg_match('/^' . $prefix . '(\d+)$/', $lastSlate->code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    // Créer ou récupérer une ardoise pour un UUID appareil
    public static function getOrCreateForDevice($deviceUuid, $restaurantId, $branchId, $customerId = null)
    {
        // Chercher une ardoise active pour cet appareil
        $slate = self::where('device_uuid', $deviceUuid)
            ->where('branch_id', $branchId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if (!$slate) {
            $slate = self::create([
                'code' => self::generateCode($branchId),
                'device_uuid' => $deviceUuid,
                'restaurant_id' => $restaurantId,
                'branch_id' => $branchId,
                'customer_id' => $customerId,
                'expires_at' => now()->addMonths(3),
                'last_activity_at' => now(),
            ]);

            \Log::info('🆕 Nouvelle ardoise créée', [
                'slate_id' => $slate->id,
                'code' => $slate->code,
                'device_uuid' => $deviceUuid,
            ]);
        }

        return $slate;
    }

    // Récupérer une ardoise par code
    public static function findByCode($code, $branchId)
    {
        return self::where('code', $code)
            ->where('branch_id', $branchId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();
    }

    // Recalculer les montants de l'ardoise
    public function recalculateAmounts()
    {
        $unpaidOrders = $this->unpaidOrders;
        $paidOrders = $this->paidOrders;

        $this->total_amount = $unpaidOrders->sum('total') + $paidOrders->sum('total');
        $this->paid_amount = $paidOrders->sum('total');
        $this->remaining_amount = $unpaidOrders->sum('total');
        $this->last_activity_at = now();

        // Si tout est payé, marquer comme payée mais garder active
        if ($this->remaining_amount <= 0 && $this->total_amount > 0) {
            $this->status = 'paid';
        } else {
            $this->status = 'active';
        }

        $this->save();

        \Log::info('💰 Montants ardoise recalculés', [
            'slate_id' => $this->id,
            'code' => $this->code,
            'total' => $this->total_amount,
            'paid' => $this->paid_amount,
            'remaining' => $this->remaining_amount,
        ]);

        return $this;
    }

    // Ajouter une commande à l'ardoise
    public function addOrder(Order $order)
    {
        $order->slate_id = $this->id;
        $order->is_on_slate = true;
        $order->save();

        $this->recalculateAmounts();

        \Log::info('➕ Commande ajoutée à l\'ardoise', [
            'slate_code' => $this->code,
            'order_id' => $order->id,
        ]);

        return $this;
    }

    // Vérifier si l'ardoise a expiré
    public function checkExpiration()
    {
        if ($this->expires_at && $this->expires_at->isPast()) {
            $this->status = 'expired';
            $this->save();
            return true;
        }
        return false;
    }

    // Renouveler l'expiration (3 mois à partir de maintenant)
    public function renewExpiration()
    {
        $this->expires_at = now()->addMonths(3);
        $this->last_activity_at = now();
        $this->save();
    }
}
