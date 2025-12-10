<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Recherche des doublons d'ardoises...\n\n";

$duplicates = \DB::select("
    SELECT device_uuid, restaurant_id, branch_id, COUNT(*) as count
    FROM slates
    GROUP BY device_uuid, restaurant_id, branch_id
    HAVING count > 1
");

if (empty($duplicates)) {
    echo "✅ Aucun doublon trouvé!\n";
} else {
    echo "❌ Doublons trouvés:\n\n";

    foreach ($duplicates as $dup) {
        echo "Device UUID: {$dup->device_uuid}\n";
        echo "Restaurant ID: {$dup->restaurant_id}\n";
        echo "Branch ID: {$dup->branch_id}\n";
        echo "Nombre de doublons: {$dup->count}\n";

        // Récupérer les ardoises en doublon
        $slates = App\Models\Slate::where('device_uuid', $dup->device_uuid)
            ->where('restaurant_id', $dup->restaurant_id)
            ->where('branch_id', $dup->branch_id)
            ->orderBy('created_at', 'asc')
            ->get();

        echo "Détails:\n";
        foreach ($slates as $slate) {
            echo "  - ID: {$slate->id} | Code: {$slate->code} | Status: {$slate->status} | Créée: {$slate->created_at}\n";
        }
        echo "\n";
    }
}
