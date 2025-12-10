<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Test de la logique des cookies et ardoises\n\n";

$deviceUuid = "test-uuid-12345";
$restaurantId = 1;
$branchId = 1;

echo "📋 Recherche d'ardoise existante...\n";
$slate = App\Models\Slate::where("device_uuid", $deviceUuid)
    ->where("restaurant_id", $restaurantId)
    ->where("branch_id", $branchId)
    ->where("status", "active")
    ->where("expires_at", ">", now())
    ->first();

if ($slate) {
    echo "✅ Ardoise trouvée: {$slate->code} (ID: {$slate->id})\n";
    echo "   Device UUID: {$slate->device_uuid}\n";
    echo "   Restaurant: {$slate->restaurant_id}\n";
    echo "   Branch: {$slate->branch_id}\n";
} else {
    echo "❌ Aucune ardoise trouvée pour cet UUID\n";
    echo "   UUID recherché: {$deviceUuid}\n";
    echo "   Restaurant: {$restaurantId}\n";
    echo "   Branch: {$branchId}\n";
}

echo "\n📊 Liste de toutes les ardoises actives:\n";
$activeSlates = App\Models\Slate::where("status", "active")
    ->where("expires_at", ">", now())
    ->get(["id", "code", "device_uuid", "restaurant_id", "branch_id", "status"]);

foreach ($activeSlates as $s) {
    echo "  - {$s->code} | UUID: {$s->device_uuid} | Restaurant: {$s->restaurant_id} | Branch: {$s->branch_id}\n";
}

echo "\n✅ Test terminé!\n";
