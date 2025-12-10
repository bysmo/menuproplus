<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Test de création/récupération d'ardoise\n\n";

// Utiliser un restaurant existant
$restaurant = App\Models\Restaurant::first();
if (!$restaurant) {
    die("❌ Aucun restaurant trouvé dans la base de données\n");
}

$branch = $restaurant->branches()->first();
if (!$branch) {
    die("❌ Aucune branche trouvée pour ce restaurant\n");
}

$deviceUuid = "test-cookie-uuid-" . time();
$restaurantId = $restaurant->id;
$branchId = $branch->id;

echo "📋 UUID du cookie: {$deviceUuid}\n";
echo "🏪 Restaurant: {$restaurant->restaurant_name} (ID: {$restaurantId})\n";
echo "🏢 Branch: {$branch->branch_name} (ID: {$branchId})\n\n";

// Premier appel - devrait créer une nouvelle ardoise
echo "1️⃣  Premier appel à getOrCreateForDevice...\n";
$slate1 = App\Models\Slate::getOrCreateForDevice($deviceUuid, $restaurantId, $branchId);
echo "   Résultat: Ardoise {$slate1->code} (ID: {$slate1->id})\n";
echo "   Device UUID: {$slate1->device_uuid}\n";
echo "   Status: {$slate1->status}\n\n";

// Deuxième appel - devrait récupérer l'ardoise existante
echo "2️⃣  Deuxième appel avec le même UUID...\n";
$slate2 = App\Models\Slate::getOrCreateForDevice($deviceUuid, $restaurantId, $branchId);
echo "   Résultat: Ardoise {$slate2->code} (ID: {$slate2->id})\n";
echo "   Device UUID: {$slate2->device_uuid}\n";
echo "   Status: {$slate2->status}\n\n";

// Vérification
if ($slate1->id === $slate2->id) {
    echo "✅ SUCCÈS: La même ardoise a été récupérée!\n";
    echo "   Les deux appels ont retourné l'ardoise ID {$slate1->id}\n";
} else {
    echo "❌ ERREUR: Deux ardoises différentes ont été créées!\n";
    echo "   Premier appel: ID {$slate1->id}\n";
    echo "   Deuxième appel: ID {$slate2->id}\n";
}

echo "\n🧹 Nettoyage - suppression de l'ardoise de test...\n";
$slate1->delete();
echo "✅ Test terminé!\n";
