<?php

namespace App\Observers;

use App\Models\Kot;
use App\Models\KotSetting;
use App\Events\KotUpdated;

class KotObserver
{
    public function creating(Kot $kot)
    {
        $kotSettings = KotSetting::first();

        if (branch() && $kot->branch_id == null) {
            $kot->branch_id = branch()->id;
        }

        if ($kot->order?->order_status->value === 'placed' || $kotSettings->default_status == 'pending') {
            $kot->status = 'pending_confirmation';
        } elseif ($kotSettings->default_status == 'cooking') {
            $kot->status = 'in_kitchen';
        }
    }

    public function updated(Kot $kot)
    {
        if (!$kot->wasChanged('status') || !$kot->order) {
            return;
        }

        $order = $kot->order;
        $orderType = $order->order_type;
        
        $pendingStatus = ($order->placed_via === 'pos')
            ? 'confirmed' 
            : (restaurant()->auto_confirm_orders ? 'confirmed' : 'placed');

        $statusMap = [
            'in_kitchen' => 'preparing',
            'food_ready' => match($orderType) {
                'pickup' => 'ready_for_pickup',
                default => 'preparing',
            },
            'served' => match($orderType) {
                'pickup' => 'ready_for_pickup',
                'delivery' => 'preparing',
                default => 'served',
            },
            'pending_confirmation' => $pendingStatus,
        ];

        if (isset($statusMap[$kot->status])) {
            $order->updateQuietly(['order_status' => $statusMap[$kot->status]]);
        }
    }

    public function saved(Kot $kot)
    {

        event(new KotUpdated($kot));
    }
}
