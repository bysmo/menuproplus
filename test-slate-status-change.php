<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Test de changement de statut d'ardoise\n\n";

$restaurant = App\Models\Restaurant::first();
$branch = $restaurant->branches()->first();
$deviceUuid = "test-status-change-" . time();

echo "1️⃣  Création d'une nouvelle ardoise...\n";
$slate = App\Models\Slate::getOrCreateForDevice($deviceUuid, $restaurant->id, $branch->id);
echo "   Ardoise: {$slate->code} (ID: {$slate->id})\n";
echo "   Status initial: {$slate->status}\n\n";

echo "2️⃣  Changement de statut à 'paid'...\n";
$slate->status = 'paid';
$slate->save();
echo "   Status après save: {$slate->status}\n\n";

echo "3️⃣  Récupération de l'ardoise avec getOrCreateForDevice...\n";
$slate2 = App\Models\Slate::getOrCreateForDevice($deviceUuid, $restaurant->id, $branch->id);
echo "   Ardoise: {$slate2->code} (ID: {$slate2->id})\n";
echo "   Status: {$slate2->status}\n\n";

if ($slate->id === $slate2->id) {
    echo "✅ SUCCÈS: La même ardoise a été récupérée après changement de statut!\n";
} else {
    echo "❌ ERREUR: Une nouvelle ardoise a été créée!\n";
}

echo "\n🧹 Nettoyage...\n";
$slate->delete();
echo "✅ Test terminé!\n";
