<?php

namespace App\Livewire\Dashboard;

use App\Events\TodayOrdersUpdated;
use App\Models\Kot;
use App\Models\Order;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class TodayOrders extends Component
{

    use LivewireAlert;

    public function render()
    {
        $tz = timezone();
        $start = Carbon::now($tz)->startOfDay()->setTimezone($tz)->toDateTimeString();
        $end = Carbon::now($tz)->endOfDay()->setTimezone($tz)->toDateTimeString();

        $count = Order::whereDate('orders.date_time', '>=', $start)
            ->whereDate('orders.date_time', '<=', $end)
            ->where('status', '<>', 'canceled')
            ->where('status', '<>', 'draft')
            ->count();

        $todayKotCount = Kot::join('orders', 'kots.order_id', '=', 'orders.id')
            ->whereDate('kots.created_at', '>=', $start)
            ->whereDate('kots.created_at', '<=', $end)
            ->where('orders.status', '<>', 'canceled')
            ->where('orders.status', '<>', 'draft')
            ->count();

        $playSound = false;

        if (session()->has('today_order_count') && session('today_order_count') < $todayKotCount) {
            $playSound = true;

            $this->alert('success', __('messages.newOrderReceived'), [
                'toast' => true,
                'position' => 'top-end'
            ]);

            // uncomment to show new order notification in orders list if needed
            // session(['new_order_notification_pending' => true]);
            $this->dispatch('refreshOrders');
        } elseif ($count > session('last_order_count', 0) && !session('new_order_notification_pending')) {
            session(['new_order_notification_pending' => true]);
        }

        session([
            'last_order_count' => $count,
            'today_order_count' => $todayKotCount
        ]);

        return view('livewire.dashboard.today-orders', [
            'count' => $count,
            'playSound' => $playSound,
        ]);
    }

    /**
     * Handle refresh from Pusher event
     */
    public function refreshOrders()
    {
        // This method will be called when Pusher sends data
        // The component will automatically re-render with fresh data
        $this->dispatch('$refresh');
    }
}
