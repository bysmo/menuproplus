<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\QrShortLink;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QrRedirectController extends Controller
{
    /**
     * Resolve and safely redirect a short QR link.
     */
    public function redirectShortUrl(string $code): RedirectResponse
    {
        $code = trim($code);

        // 1. Look for existing short link record
        $shortLink = QrShortLink::where('code', $code)->first();

        if ($shortLink) {
            // Track scan stats
            $shortLink->increment('scan_count');
            $shortLink->update(['last_scanned_at' => now()]);

            // Validate target model still exists
            if ($shortLink->target_type === 'table') {
                $table = Table::find($shortLink->target_id);
                if ($table && $table->hash) {
                    return redirect()->to(route('table_order', [$table->hash]));
                }
            } elseif ($shortLink->target_type === 'branch') {
                $branch = Branch::withoutGlobalScopes()->find($shortLink->target_id);
                if ($branch) {
                    $restaurant = $branch->restaurant;
                    $url = route('table_order', [$branch->restaurant_id]) . '?branch=' . $branch->unique_hash . '&hash=' . ($restaurant?->hash ?? '') . '&from_qr=1';
                    return redirect()->to($url);
                }
            }

            if (!empty($shortLink->destination_url)) {
                return redirect()->to($shortLink->destination_url);
            }
        }

        // 2. Fallback: Check if $code is directly a table hash
        $table = Table::where('hash', $code)->first();
        if ($table) {
            return redirect()->to(route('table_order', [$table->hash]));
        }

        // 3. Fallback: Check if $code is a branch unique_hash
        $branch = Branch::withoutGlobalScopes()->where('unique_hash', $code)->first();
        if ($branch) {
            $restaurant = $branch->restaurant;
            $url = route('table_order', [$branch->restaurant_id]) . '?branch=' . $branch->unique_hash . '&hash=' . ($restaurant?->hash ?? '') . '&from_qr=1';
            return redirect()->to($url);
        }

        // 4. Fallback: Check if $code is a restaurant hash
        $restaurant = Restaurant::where('hash', $code)->first();
        if ($restaurant) {
            return redirect()->to(route('shop_restaurant', [$restaurant->hash]));
        }

        abort(404, 'Lien QR code invalide ou introuvable.');
    }
}
