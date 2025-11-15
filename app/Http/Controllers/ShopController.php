<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\LanguageSetting;
use App\Traits\HasLanguageSettings;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    use HasLanguageSettings;
    /**
     * Constructor to handle language and RTL settings
     */
    public function __construct()
    {
        $this->applyLanguageSettings();
    }

    /**
     * Get the branch for the shop based on request or default to first branch
     */
    private function getShopBranch(Restaurant $restaurant): Branch
    {
        if (request()->filled('branch')) {
            $branchParam = request('branch');

            // Try to find by unique_hash first, then by ID
            $branch = Branch::withoutGlobalScopes()->where('unique_hash', $branchParam)->first();

            if (!$branch) {
                $branch = Branch::withoutGlobalScopes()->find($branchParam);
            }

            return $branch;
        }

        return $restaurant->branches->first();
    }

    /**
     * Get enabled package modules and features for the restaurant
     */
    private function getPackageModules(?Restaurant $restaurant): array
    {
        if (!$restaurant?->package) {
            return [];
        }

        $modules = $restaurant->package->modules->pluck('name')->toArray();
        $additionalFeatures = json_decode($restaurant->package->additional_features ?? '[]', true);

        return array_merge($modules, $additionalFeatures);
    }

    /**
     * Show shopping cart page
     */
    public function cart(string $hash)
    {

        $restaurant = Restaurant::with('currency')->where('hash', $hash)->firstOrFail();
        $shopBranch = $this->getShopBranch($restaurant);

        $packageModules = $this->getPackageModules($restaurant);

        $this->redirectIfSubdomainIsEnabled($restaurant);

        return view('shop.index', [
            'restaurant' => $restaurant,
            'shopBranch' => $shopBranch,
            'getTable' => $restaurant->table_required,
            'canCreateOrder' => in_array('Order', $packageModules)
        ]);
    }

    /**
     * Show order success page
     */
    public function orderSuccess(string $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        $id = $order->id;

        if ($order->status === 'draft') {
            abort(404);
        }

        $shopBranch = request()->filled('branch')
            ? Branch::withoutGlobalScopes()->find(request('branch'))
            : $order->branch;

        $restaurant = $order->branch->restaurant;

        return view('shop.order_success', [
            'restaurant' => $restaurant,
            'id' => $id,
            'shopBranch' => $shopBranch
        ]);
    }

    /**
     * Show table booking page
     */
    public function bookTable(string $hash)
    {
        $restaurant = Restaurant::with('currency')->where('hash', $hash)->firstOrFail();
        $shopBranch = $this->getShopBranch($restaurant);
        $packageModules = $this->getPackageModules($restaurant);

        $this->redirectIfSubdomainIsEnabled($restaurant);

        abort_if(!in_array('Table Reservation', $packageModules), 403);

        return view('shop.book_a_table', compact('restaurant', 'shopBranch'));
    }

    /**
     * Show user's bookings page
     */
    public function myBookings(string $hash)
    {
        $restaurant = Restaurant::with('currency')->where('hash', $hash)->firstOrFail();
        $shopBranch = $this->getShopBranch($restaurant);
        $packageModules = $this->getPackageModules($restaurant);

        $this->redirectIfSubdomainIsEnabled($restaurant);

        abort_if(!in_array('Table Reservation', $packageModules), 403);

        return view('shop.bookings', compact('restaurant', 'shopBranch'));
    }

    /**
     * Show user's addresses page
     */
    public function myAddresses(string $hash)
    {
        $restaurant = Restaurant::with('currency')->where('hash', $hash)->firstOrFail();
        $shopBranch = $this->getShopBranch($restaurant);

        $this->redirectIfSubdomainIsEnabled($restaurant);

        return view('shop.addresses', compact('restaurant', 'shopBranch'));
    }

    /**
     * Show user profile page
     */
    public function profile(string $hash)
    {
        $restaurant = Restaurant::with('currency')->where('hash', $hash)->firstOrFail();
        $shopBranch = $this->getShopBranch($restaurant);

        $this->redirectIfSubdomainIsEnabled($restaurant);

        return view('shop.profile', compact('restaurant', 'shopBranch'));
    }

    /**
     * Show user's orders page
     */
    public function myOrders(string $hash)
    {
        $restaurant = Restaurant::with('currency')->where('hash', $hash)->firstOrFail();
        $shopBranch = $this->getShopBranch($restaurant);

        $this->redirectIfSubdomainIsEnabled($restaurant);

        return view('shop.orders', compact('restaurant', 'shopBranch'));
    }

    /**
     * Show about page
     */
    public function about(string $hash)
    {
        $restaurant = Restaurant::with('currency')->where('hash', $hash)->firstOrFail();
        $shopBranch = $this->getShopBranch($restaurant);

        $this->redirectIfSubdomainIsEnabled($restaurant);

        return view('shop.about', compact('restaurant', 'shopBranch'));
    }

    /**
     * Show contact page
     */
    public function contact(string $hash)
    {
        $restaurant = Restaurant::with('currency')->where('hash', $hash)->firstOrFail();
        $shopBranch = $this->getShopBranch($restaurant);

        $this->redirectIfSubdomainIsEnabled($restaurant);

        return view('shop.contact', compact('restaurant', 'shopBranch'));
    }

    /**
     * Show table order page
     */
    public function tableOrder(string $table)
{
    // 1) Résoudre la table par hash OU par id numérique
    $tableModel = Table::query()
        ->when(is_numeric($table), fn($q) => $q->orWhere('id', $table))
        ->orWhere('hash', $table)
        ->first();

    if ($tableModel) {
        // Cas 1 : on a une table -> on déduit tout du contexte "table"
        $shopBranch = $tableModel->branch;
        $restaurant = $shopBranch->restaurant->load('currency');
        $tableHash  = $tableModel->hash; // pour la vue
        $getTable   = false;
    } else {
        // Cas 2 : aucune table trouvée -> on attend un hash de restaurant en query
        $restaurantHash = request('hash'); // ex: belle-etoile
        abort_unless($restaurantHash, 404, 'Restaurant hash is required');

        $restaurant = Restaurant::with('currency')
            ->where('hash', $restaurantHash)
            ->firstOrFail();

        $shopBranch = $this->getShopBranch($restaurant);
        $tableHash  = null;
        $getTable   = true;
    }

    // Redirection sous-domaine si activée
    //$this->redirectIfSubdomainIsEnabled($restaurant);

    $packageModules = $this->getPackageModules($restaurant);

    return view('shop.index', [
        'tableHash'      => $tableHash,
        'restaurant'     => $restaurant,
        'shopBranch'     => $shopBranch,
        'getTable'       => $getTable,
        'canCreateOrder' => in_array('Order', $packageModules),
    ]);
}

public function redirectIfSubdomainIsEnabled(Restaurant $restaurant): void
{
    if (!module_enabled('Subdomain')) {
        return;
    }

    $restaurantDomain = getRestaurantBySubDomain();

    if (is_null($restaurantDomain)) {
        $target = 'https://' . $restaurant->sub_domain . request()->getRequestUri();

        // Utiliser 301 (Permanent) ou 302 (Temporary) avec préservation de la méthode GET
        redirect()->withoutRedirecting()->away($target, 301)->send();
        exit; // Important pour arrêter l'exécution
    }
}
}
