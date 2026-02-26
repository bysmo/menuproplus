<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KotCancelReason;

class KotCancelReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($restaurant = null): void
    {
        $cancelReasons = [
            // Order cancellation reasons
            [
                'reason' => 'Le client a changé d\'avis',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],
            [
                'reason' => 'Le client a demandé d\'annuler',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],
            [
                'reason' => 'Problèmes de paiement',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],


            [
                'reason' => 'Le client ne veut plus de la commande',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],

            [
                'reason' => 'Ingrédient non disponible',
                'cancel_order' => false,
                'cancel_kot' => true,
            ],

            [
                'reason' => 'Temps de préparation trop long',
                'cancel_order' => false,
                'cancel_kot' => true,
            ],
            [
                'reason' => 'Problème de qualité des ingrédients',
                'cancel_order' => false,
                'cancel_kot' => true,
            ],

            // Both order and KOT cancellation reasons
            [
                'reason' => 'Erreur système/Problème technique',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],

            [
                'reason' => 'Restaurant ferme plus tôt',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],
              [
                'reason' => 'Autre',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],


        ];

        foreach ($cancelReasons as $reason) {
            $reason['restaurant_id'] = $restaurant->id ?? null;
            KotCancelReason::create($reason);
        }
    }
}
