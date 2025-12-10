<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧹 Nettoyage des doublons d'ardoises...\n\n";

$duplicates = \DB::select("
    SELECT device_uuid, restaurant_id, branch_id, COUNT(*) as count
    FROM slates
    GROUP BY device_uuid, restaurant_id, branch_id
    HAVING count > 1
");

if (empty($duplicates)) {
    echo "✅ Aucun doublon à nettoyer!\n";
    exit(0);
}

foreach ($duplicates as $dup) {
    echo "📋 Traitement des doublons pour:\n";
    echo "   Device UUID: {$dup->device_uuid}\n";
    echo "   Restaurant: {$dup->restaurant_id}, Branch: {$dup->branch_id}\n\n";

    // Récupérer toutes les ardoises en doublon, triées par date
    $slates = App\Models\Slate::where('device_uuid', $dup->device_uuid)
        ->where('restaurant_id', $dup->restaurant_id)
        ->where('branch_id', $dup->branch_id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Garder la plus récente
    $keepSlate = $slates->first();
    echo "   ✅ Garder: ID {$keepSlate->id} | Code: {$keepSlate->code} | Status: {$keepSlate->status}\n";

    // Traiter les autres
    foreach ($slates->skip(1) as $oldSlate) {
        echo "   🗑️  Supprimer: ID {$oldSlate->id} | Code: {$oldSlate->code} | Status: {$oldSlate->status}\n";

        // Transférer les commandes vers l'ardoise conservée
        $ordersCount = $oldSlate->orders()->count();
        if ($ordersCount > 0) {
            echo "      📦 Transfert de {$ordersCount} commande(s) vers l'ardoise {$keepSlate->code}\n";
            $oldSlate->orders()->update(['slate_id' => $keepSlate->id]);
        }

        // Supprimer l'ancienne ardoise
        $oldSlate->delete();
    }

    // Recalculer les montants de l'ardoise conservée
    $keepSlate->recalculateAmounts();
    echo "   💰 Montants recalculés pour l'ardoise {$keepSlate->code}\n\n";
}

echo "✅ Nettoyage terminé!\n";
