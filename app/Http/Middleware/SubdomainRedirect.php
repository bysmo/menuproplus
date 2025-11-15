<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubdomainRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si le module Subdomain est activé
        if (!function_exists('module_enabled') || !module_enabled('Subdomain')) {
            return $next($request);
        }

        // Vérifier si on est déjà sur le bon domaine
        $restaurantDomain = function_exists('getRestaurantBySubDomain')
            ? getRestaurantBySubDomain()
            : null;

        if (is_null($restaurantDomain)) {
            // Extraire le hash du restaurant depuis la route
            $hash = $request->route('hash');

            if ($hash) {
                $restaurant = \App\Models\Restaurant::where('hash', $hash)->first();

                if ($restaurant && $restaurant->sub_domain) {
                    $target = 'https://' . $restaurant->sub_domain . $request->getRequestUri();

                    // ✅ Retourner une vue HTML avec redirection JS
                    return response()->view('redirect', ['target' => $target], 200);
                }
            }
        }

        return $next($request);
    }
}
