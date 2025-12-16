<?php

namespace App\Livewire\Dashboard;

use App\Models\Restaurant;
use App\Models\RestaurantPayment;
use App\Models\GlobalSubscription;
use App\Models\User;
use Livewire\Component;

class SuperadminRecentActivity extends Component
{
    public $recentRestaurants;
    public $recentPayments;
    public $recentSubscriptions;
    public $recentUsers;
    public $recentActivities;

    public function mount()
    {
        $this->loadRecentData();
    }

    private function loadRecentData()
    {
        // Recent restaurant registrations
        $this->recentRestaurants = Restaurant::with(['package', 'country'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent restaurant payments
        $this->recentPayments = RestaurantPayment::with(['restaurant'])
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent subscription changes
        $this->recentSubscriptions = GlobalSubscription::with(['restaurant', 'package'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent user registrations
        $this->recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Combine all activities for timeline
        $this->recentActivities = collect();

        // Add new restaurants
        foreach ($this->recentRestaurants as $restaurant) {
            $this->recentActivities->push([
                'type' => 'restaurant',
                'title' => 'New Restaurant',
                //traduction dynamique de la description, avec __('modules.dashboard.xxx')
                'description' => "{$restaurant->name}" ." => ". __('modules.dashboard.joinedThePlatform'),
                'amount' => null,
                'time' => $restaurant->created_at,
                'icon' => 'building-storefront',
                'color' => 'purple'
            ]);
        }

        // Add payments
        foreach ($this->recentPayments as $payment) {
            $this->recentActivities->push([
                'type' => 'payment',
                'title' => 'Restaurant Payment',
                //traduction dynamique de la description, avec __('modules.dashboard.xxx')
                'description' => "{$payment?->restaurant?->name}" . " => " . __('modules.dashboard.paymentReceived'),
                'amount' => $payment->amount,
                'time' => $payment->created_at,
                'icon' => 'credit-card',
                'color' => 'green'
            ]);
        }

        // Add subscription changes
        foreach ($this->recentSubscriptions as $subscription) {
            $this->recentActivities->push([
                'type' => 'subscription',
                'title' => 'Subscription Update',
                //traduction dynamique de la description, avec __('modules.dashboard.xxx')
                'description' => "{$subscription?->restaurant?->name} - {$subscription?->package?->name}" . " => " . __('modules.dashboard.subscriptionUpdated'),
                'amount' => $subscription->amount ?? null,
                'time' => $subscription->created_at,
                'icon' => 'check-circle',
                'color' => 'blue'
            ]);
        }

        // Add new users
        foreach ($this->recentUsers as $user) {
            $this->recentActivities->push([
                'type' => 'user',
                'title' => 'New User',
                //traduction dynamique de la description, avec __('modules.dashboard.xxx')
                'description' => "{$user->name}" . " => " . __('modules.dashboard.newUser'),
                'amount' => null,
                'time' => $user->created_at,
                'icon' => 'user',
                'color' => 'indigo'
            ]);
        }

        // Sort by time and limit to 10 most recent
        $this->recentActivities = $this->recentActivities
            ->sortByDesc('time')
            ->take(10);
    }

    public function render()
    {
        return view('livewire.dashboard.superadmin-recent-activity');
    }
}
