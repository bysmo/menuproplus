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
        'pending_payment',
        'remaining_amount',
        'status',
        'expires_at',
        'last_activity_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'pending_payment' => 'decimal:2',
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
            ->whereNotIn('status', ['paid', 'canceled', 'draft', 'pending_verification']);
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
        // remaining if orders.total > orders.amount_paid and status not in ['paid', 'cancelled','draft']
        return $this->orders()->where('total', '>', 'amount_paid')->whereNotIn('status', ['paid', 'cancelled','draft']);
    }

    // Génération du code incrémental par branche
    public static function generateCode($branchId)
    {
        $prefix = 'ARD';
        $maxAttempts = 10;
        $attempt = 0;

        do {
            // Récupérer le dernier code pour cette branche
            $lastSlate = self::where('branch_id', $branchId)
                ->where('code', 'LIKE', $prefix . '%')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastSlate && preg_match('/^' . $prefix . '(\d+)$/', $lastSlate->code, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }

            $code = $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Vérifier si le code existe déjà
            $exists = self::where('code', $code)->exists();

            if (!$exists) {
                return $code;
            }

            $attempt++;

            // Si le code existe, forcer le numéro suivant
            if ($attempt < $maxAttempts) {
                // Trouver le plus grand numéro utilisé
                $maxSlate = self::where('branch_id', $branchId)
                    ->where('code', 'LIKE', $prefix . '%')
                    ->orderByRaw('CAST(SUBSTRING(code, 4) AS UNSIGNED) DESC')
                    ->first();

                if ($maxSlate && preg_match('/^' . $prefix . '(\d+)$/', $maxSlate->code, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                    $code = $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                    if (!self::where('code', $code)->exists()) {
                        return $code;
                    }
                }
            }

        } while ($attempt < $maxAttempts);

        // Si après plusieurs tentatives on ne trouve pas de code unique, utiliser un timestamp
        return $prefix . time();
    }

    // Créer ou récupérer une ardoise pour un UUID appareil
    public static function getOrCreateForDevice($deviceUuid, $restaurantId, $branchId, $customerId = null)
    {
        // Chercher une ardoise pour cet appareil (UUID), ce restaurant ET cette branche
        // On cherche d'abord une ardoise active, sinon n'importe quelle ardoise non expirée
        $slate = self::where('device_uuid', $deviceUuid)
            ->where('restaurant_id', $restaurantId)
            ->where('branch_id', $branchId)
            ->where('expires_at', '>', now())
            ->orderByRaw("FIELD(status, 'active', 'pending_verification', 'paid')")
            ->first();

        if (!$slate) {
            // Utiliser une transaction pour éviter les doublons
            $slate = \DB::transaction(function () use ($deviceUuid, $restaurantId, $branchId, $customerId) {
                // Vérifier à nouveau dans la transaction (sans filtre sur le status)
                $existingSlate = self::where('device_uuid', $deviceUuid)
                    ->where('restaurant_id', $restaurantId)
                    ->where('branch_id', $branchId)
                    ->where('expires_at', '>', now())
                    ->lockForUpdate()
                    ->first();

                if ($existingSlate) {
                    \Log::info('✅ Ardoise existante trouvée', [
                        'slate_id' => $existingSlate->id,
                        'code' => $existingSlate->code,
                        'device_uuid' => $deviceUuid,
                        'restaurant_id' => $restaurantId,
                        'branch_id' => $branchId,
                        'status' => $existingSlate->status,
                    ]);
                    return $existingSlate;
                }

                $maxRetries = 3;
                $retryCount = 0;

                while ($retryCount < $maxRetries) {
                    try {
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

                        return $slate;
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Si c'est une erreur de doublon de code, réessayer
                        if ($e->errorInfo[1] == 1062 && strpos($e->getMessage(), 'slates_code_unique') !== false) {
                            $retryCount++;
                            \Log::warning("Tentative {$retryCount}/{$maxRetries} - Code en doublon détecté, nouvelle génération...");

                            if ($retryCount >= $maxRetries) {
                                throw new \Exception("Impossible de générer un code unique après {$maxRetries} tentatives");
                            }

                            // Attendre un court instant avant de réessayer
                            usleep(100000); // 100ms
                            continue;
                        }

                        // Pour toute autre erreur, la relancer
                        throw $e;
                    }
                }

                throw new \Exception("Erreur inattendue lors de la création de l'ardoise");
            });
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
        // Récupérer toutes les commandes de l'ardoise (sauf annulées et brouillons)
        $activeOrders = $this->orders()
            ->whereNotIn('status', ['canceled', 'draft'])
            ->get();

        // Total = somme de toutes les commandes actives
        $this->total_amount = $activeOrders->sum('total');

        // Séparer les commandes payées, en attente et non payées
        $paidOrders = $activeOrders->where('status', 'paid');
        $pendingOrders = $activeOrders->where('status', 'pending_verification');

        // Payé = somme des montants confirmés (status = paid)
        $this->paid_amount = $paidOrders->sum('amount_paid');

        // En attente = somme des montants en attente de vérification
        $this->pending_payment = $pendingOrders->sum('amount_paid');

        // Reste à payer = Total - Payé - En attente
        $this->remaining_amount = $this->total_amount - $this->paid_amount - $this->pending_payment;

        $this->last_activity_at = now();

        // Déterminer le statut de l'ardoise
        if ($this->remaining_amount <= 0 && $this->pending_payment <= 0 && $this->total_amount > 0) {
            // Tout est payé et confirmé
            $this->status = 'paid';
        } else {
            // Partiellement payé, non payé, ou en attente de vérification
            $this->status = 'active';
        }

        $this->save();

        \Log::info('💰 Montants ardoise recalculés', [
            'slate_id' => $this->id,
            'code' => $this->code,
            'total' => $this->total_amount,
            'paid' => $this->paid_amount,
            'pending' => $this->pending_payment,
            'remaining' => $this->remaining_amount,
            'status' => $this->status,
            'orders_count' => $activeOrders->count(),
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
