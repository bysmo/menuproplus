<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\QrShortLink;
use App\Models\Table;

class QrShortLinkService
{
    /**
     * Get or create a short URL for a Table.
     */
    public static function getShortUrlForTable(Table $table): string
    {
        $targetType = 'table';
        $targetId   = $table->id;
        $restaurantId = $table->branch?->restaurant_id;

        // The actual destination URL in the application
        $destinationUrl = route('table_order', [$table->hash]);

        $shortLink = QrShortLink::firstOrCreate(
            [
                'target_type' => $targetType,
                'target_id'   => $targetId,
            ],
            [
                'code'            => QrShortLink::generateUniqueCode(6),
                'restaurant_id'   => $restaurantId,
                'destination_url' => $destinationUrl,
            ]
        );

        // Always ensure destination_url matches current hash
        if ($shortLink->destination_url !== $destinationUrl) {
            $shortLink->update(['destination_url' => $destinationUrl]);
        }

        return route('qr.short', [$shortLink->code]);
    }

    /**
     * Get or create a short URL for a Branch (Restaurant main QR).
     */
    public static function getShortUrlForBranch(Branch $branch): string
    {
        $targetType = 'branch';
        $targetId   = $branch->id;
        $restaurant = $branch->restaurant;
        $restaurantId = $branch->restaurant_id;

        // Destination URL for branch ordering
        $destinationUrl = route('table_order', [$branch->restaurant_id]) . '?branch=' . $branch->unique_hash . '&hash=' . ($restaurant?->hash ?? '') . '&from_qr=1';

        $shortLink = QrShortLink::firstOrCreate(
            [
                'target_type' => $targetType,
                'target_id'   => $targetId,
            ],
            [
                'code'            => QrShortLink::generateUniqueCode(6),
                'restaurant_id'   => $restaurantId,
                'destination_url' => $destinationUrl,
            ]
        );

        if ($shortLink->destination_url !== $destinationUrl) {
            $shortLink->update(['destination_url' => $destinationUrl]);
        }

        return route('qr.short', [$shortLink->code]);
    }
}
