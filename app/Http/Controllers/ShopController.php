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
    private function OldgetPackageModules(?Restaurant $restaurant): array
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


    public function tableOrderLast(string $table)
{
    try {
        \Log::info('=== DEBUT TABLE ORDER ===', [
            'table_raw' => $table,
            'method' => request()->method(),
            'url' => request()->fullUrl(),
            'timestamp' => now()->toDateTimeString()
        ]);

        $table = trim($table);

        \Log::info('Après trim', ['table' => $table]);

        // 1) Résoudre la table par hash OU par id numérique
        $tableModel = Table::query()
            ->when(is_numeric($table), function($q) use ($table) {
                \Log::info('Recherche par ID numérique', ['id' => $table]);
                return $q->orWhere('id', $table);
            })
            ->orWhere('hash', $table)
            ->first();

        \Log::info('Résultat recherche table', [
            'table_trouvee' => $tableModel ? 'OUI' : 'NON',
            'table_id' => $tableModel->id ?? null,
            'table_hash' => $tableModel->hash ?? null
        ]);

        if ($tableModel) {
            \Log::info('Cas 1: Table trouvée');
            $shopBranch = $tableModel->branch;
            $restaurant = $shopBranch->restaurant->load('currency');
            $tableHash = $tableModel->hash;
            $getTable = false;

            // VÉRIFIER L'ÉTAT DE LA LICENCE
            \Log::info('Vérification licence restaurant', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'status' => $restaurant->status,
                'license_expire_on' => $restaurant->license_expire_on,
                'is_license_expired' => $restaurant->status === 'license_expired'
            ]);

        } else {
            \Log::info('Cas 2: Table NON trouvée - recherche par hash restaurant');
            $restaurantHash = request('hash');

            if (!$restaurantHash) {
                \Log::error('Restaurant hash manquant');
                abort(404, 'Restaurant hash is required');
            }

            $restaurant = Restaurant::with('currency')
                ->where('hash', $restaurantHash)
                ->firstOrFail();

            $shopBranch = $this->getShopBranch($restaurant);
            $tableHash = null;
            $getTable = true;
        }

        \Log::info('Avant check subdomain redirect', [
            'module_subdomain_enabled' => module_enabled('Subdomain')
        ]);

        // La redirection est commentée - mais gardons le log
        \Log::info('Redirection subdomain commentée - passage direct');

        $packageModules = $this->getPackageModules($restaurant);

        \Log::info('Modules package récupérés', [
            'modules' => $packageModules,
            'can_create_order' => in_array('Order', $packageModules)
        ]);

        \Log::info('AVANT RETURN VIEW', [
            'restaurant_id' => $restaurant->id,
            'shop_branch_id' => $shopBranch->id,
            'table_hash' => $tableHash,
            'get_table' => $getTable
        ]);

        $viewData = [
            'tableHash' => $tableHash,
            'restaurant' => $restaurant,
            'shopBranch' => $shopBranch,
            'getTable' => $getTable,
            'canCreateOrder' => in_array('Order', $packageModules),
        ];

        \Log::info('Données pour la vue', $viewData);

        \Log::info('=== GENERATION DE LA VUE ===');

        return view('shop.index', $viewData);

    } catch (\Exception $e) {
        \Log::error('=== ERREUR FATALE DANS TABLE ORDER ===', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);

        // Relancer l'exception pour voir l'erreur 405
        throw $e;
    }
}


public function tableOrderNew(string $code)
{
    $code = trim($code);

    \Log::info('QR Scan Debug', [
        'method' => request()->method(),
        'url' => request()->fullUrl(),
        'code' => $code,
    ]);

    // 1) Tenter d'abord table par hash
    $table = Table::where('hash', $code)->first();

    if ($table) {
        // QR lié à une table précise
        $shopBranch = $table->branch;
        $restaurant = $shopBranch->restaurant->load('currency');
        $tableHash  = $table->hash;
        $getTable   = false;
    } else {
        // QR "global" : code est un id de restaurant (ou un hash de restaurant)
        $restaurantQuery = Restaurant::with('currency');

        if (request()->has('hash')) {
            // hash du restaurant via query (?hash=...)
            $restaurant = $restaurantQuery->where('hash', request('hash'))->firstOrFail();
        } else {
            // fallback : id numérique
            $restaurant = $restaurantQuery->findOrFail($code);
        }

        $shopBranch = $this->getShopBranch($restaurant);
        $tableHash  = null;
        $getTable   = true;
    }

    $packageModules = $this->getPackageModules($restaurant);

    return view('shop.index', [
        'tableHash'      => $tableHash,
        'restaurant'     => $restaurant,
        'shopBranch'     => $shopBranch,
        'getTable'       => $getTable,
        'canCreateOrder' => in_array('Order', $packageModules),
    ]);
}
/**
 * AJOUTER AUSSI DES LOGS DANS getPackageModules()
 */

private function getPackageModules(?Restaurant $restaurant): array
{
    \Log::info('getPackageModules appelé', [
        'restaurant_id' => $restaurant->id ?? null,
        'has_package' => $restaurant?->package ? 'OUI' : 'NON'
    ]);

    if (!$restaurant?->package) {
        \Log::info('Pas de package - retour tableau vide');
        return [];
    }

    $modules = $restaurant->package->modules->pluck('name')->toArray();
    $additionalFeatures = json_decode($restaurant->package->additional_features ?? '[]', true);

    $result = array_merge($modules, $additionalFeatures);

    \Log::info('Modules récupérés', [
        'modules' => $modules,
        'additional_features' => $additionalFeatures,
        'result' => $result
    ]);

    return $result;
}



    /**
     * Show table order page
     */
    public function tableOrder(string $hash)
    {

        $table = Table::where('hash', $hash)->first();

        if ($table) {
            $shopBranch = $table->branch;
            $restaurant = $table->branch->restaurant->load('currency');
            $getTable = false;
        } else {
            $restaurant = Restaurant::with('currency')->where('id', $hash)->firstOrFail();
            $shopBranch = $this->getShopBranch($restaurant);
            $hash = null;
            $getTable = true;
        }

        $this->redirectIfSubdomainIsEnabled($restaurant);

        $packageModules = $this->getPackageModules($restaurant);

        return view('shop.index', [
            'tableHash' => $hash,
            'restaurant' => $restaurant,
            'shopBranch' => $shopBranch,
            'getTable' => $getTable,
            'canCreateOrder' => in_array('Order', $packageModules)
        ]);
    }

public function redirectIfSubdomainIsEnabled(Restaurant $restaurant): void
{
    if (!module_enabled('Subdomain')) {
        // logger ici que le module n'est pas activé
        \Log::info('Subdomain Module is not enabled', [
            'module' => 'Subdomain',
            'restaurant' => $restaurant->toArray(),
            'request' => [
                'method' => request()->method(),
                'url' => request()->url(),
                'user_agent' => request()->userAgent(),
                'headers' => request()->headers->all()
            ]
        ]);
        return;
    }

    $restaurantDomain = getRestaurantBySubDomain();

    \Log::info('Subdomain Domain Found', [
        'restaurant_domain' => $restaurantDomain ? $restaurantDomain->toArray() : null,
    ]);

    if (is_null($restaurantDomain)) {
        $target = 'https://' . $restaurant->sub_domain . request()->getRequestUri();

        // Utiliser 301 (Permanent) ou 302 (Temporary) avec préservation de la méthode GET
        //redirect()->withoutRedirecting()->away($target, 301)->send();
        redirect()->away($target, 302)->send();
        exit; // Important pour arrêter l'exécution
    }
}
}
