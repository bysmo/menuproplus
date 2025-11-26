<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LivewirePreflight
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cible uniquement les URLs Livewire
        if ($request->is('livewire/*')) {

            // Si ce n'est pas un POST (donc GET / HEAD / OPTIONS / etc.)
            if (!$request->isMethod('POST')) {
                // On répond "OK, rien à faire" => plus de 405
                return response()->noContent(Response::HTTP_NO_CONTENT);
            }
        }

        return $next($request);
    }
}
