<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Restaurant;
use App\Models\LanguageSetting;

use App\Models\Table;

class CustomerSiteMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hash = $request->route('hash');
        $code = $request->route('code');
        $restaurant = null;

        // 1. Try to resolve from Subdomain (if function exists)
        if (function_exists('getRestaurantBySubDomain')) {
            $subdomainRestaurant = getRestaurantBySubDomain();
            if ($subdomainRestaurant) {
                $restaurant = $subdomainRestaurant;
            }
        }

        // 2. Fallback to Route Parameters if not resolved
        if (!$restaurant) {
            if ($hash) {
                $restaurant = Restaurant::where('hash', $hash)->first();
            } elseif ($code) {
                // Try to find table first
                $table = Table::where('hash', $code)->first();
                
                if ($table) {
                    $restaurant = $table->branch->restaurant;
                } else {
                    // Determine if code is hash or id
                    $restaurant = Restaurant::where('hash', $code)->first();
                    if (!$restaurant && is_numeric($code)) {
                        $restaurant = Restaurant::find($code); 
                    }
                }
            }
        }

        if ($restaurant && $restaurant->customer_site_language) {
            // If session has customer_locale (from language switcher), use it
            if (session()->has('customer_locale')) {
                $locale = session('customer_locale');
                // Get RTL from the selected language
                $language = LanguageSetting::where('language_code', $locale)->first();
                $rtl = $language?->is_rtl ?? false;
                // Update session with correct RTL
                session(['customer_is_rtl' => $rtl]);
                session()->forget('isRtl'); // Clear admin session
            } else {
                // First visit - use restaurant's customer_site_language directly
                $locale = $restaurant->customer_site_language;

                // Get is_rtl from language settings
                $language = LanguageSetting::where('language_code', $locale)->first();
                $rtl = $language?->is_rtl ?? false;

                // Set session for consistency
                session([
                    'customer_locale' => $locale,
                    'locale' => $locale, // Fallback for some systems
                    'customer_site_language' => $locale,
                    'customer_is_rtl' => $rtl,
                ]);
                session()->forget('isRtl'); // Clear admin session
            }

            App::setLocale($locale);
        }

        return $next($request);
    }
}
