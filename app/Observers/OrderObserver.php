<?php

namespace App\Observers;

use App\Models\Order;
use App\Events\OrderCancelled;
use App\Events\TodayOrdersUpdated;
use App\Models\Kot;
use App\Events\OrderUpdated;
use App\Events\OrderSuccessEvent;
use App\Events\NewOrderCreated;


class OrderObserver
{

    public function creating(Order $order)
    {
        if (branch() && $order->branch_id == null) {
            $order->branch_id = branch()->id;
        }
    }

    public function created(Order $order)
    {
        // Increment branch's count_orders (only for non-draft, non-canceled orders)
        if ($order->branch) {
            $order->branch->increment('count_orders');
            
            // Clear branch order stats cache
            cache()->forget('branch_' . $order->branch->id . '_order_stats');
        }

        $todayKotCount = Kot::join('orders', 'kots.order_id', '=', 'orders.id')
            ->whereDate('kots.created_at', '>=', now()->startOfDay()->toDateTimeString())
            ->whereDate('kots.created_at', '<=', now()->endOfDay()->toDateTimeString())
            ->where('orders.status', '<>', 'canceled')
            ->where('orders.status', '<>', 'draft')
            ->count();

        event(new OrderUpdated($order, 'created'));
        event(new TodayOrdersUpdated($todayKotCount));

        // Dispatch event for new order notification
        if ($order->status !== 'draft') {
            event(new NewOrderCreated($order));
        }
    }

    public function updated(Order $order)
    {
        if ($order->isDirty('status') && $order->status == 'canceled') {
            OrderCancelled::dispatch($order);
        }

        if ($order->isDirty('order_status')) {

            $statusMapping = [
            'preparing' => ['kot' => 'in_kitchen', 'item' => 'cooking'],
            'ready_for_pickup' => [
                'kot' => $order->order_type === 'pickup' ? 'food_ready' : 'served',
                'item' => $order->order_type === 'pickup' ? 'ready' : null
            ],
            'out_for_delivery' => ['kot' => 'served', 'item' => null],
            'served' => ['kot' => 'served', 'item' => null],
            'delivered' => ['kot' => 'served', 'item' => null],
            'cancelled' => ['kot' => 'cancelled', 'item' => 'cancelled'],
            ];

            $mapping = $statusMapping[$order->order_status->value] ?? ['kot' => 'pending_confirmation', 'item' => 'pending'];

            if ($mapping['kot']) {
                $order->kot->each(function ($kot) use ($mapping) {
                $kot->update(['status' => $mapping['kot']]);

                if ($mapping['item']) {
                $kot->items()->update(['status' => $mapping['item']]);
                }
            });
            }
        }

        $todayKotCount = Kot::join('orders', 'kots.order_id', '=', 'orders.id')
            ->whereDate('kots.created_at', '>=', now()->startOfDay()->toDateTimeString())
            ->whereDate('kots.created_at', '<=', now()->endOfDay()->toDateTimeString())
            ->where('orders.status', '<>', 'canceled')
            ->where('orders.status', '<>', 'draft')
            ->count();

        event(new OrderUpdated($order, 'updated'));
        event(new TodayOrdersUpdated($todayKotCount));

        event(new OrderSuccessEvent($order));
    }
}
