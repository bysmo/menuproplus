<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Vérification des paramètres de langue des restaurants\n\n";

$restaurants = App\Models\Restaurant::all();

if ($restaurants->isEmpty()) {
    echo "❌ Aucun restaurant trouvé\n";
    exit(1);
}

foreach ($restaurants as $restaurant) {
    echo "🏪 Restaurant: {$restaurant->restaurant_name}\n";
    echo "   ID: {$restaurant->id}\n";
    echo "   Hash: {$restaurant->hash}\n";
    echo "   Langue du site client: " . ($restaurant->customer_site_language ?? 'NON DÉFINI') . "\n";

    if ($restaurant->customer_site_language) {
        $language = App\Models\LanguageSetting::where('language_code', $restaurant->customer_site_language)->first();
        if ($language) {
            echo "   Nom de la langue: {$language->language_name}\n";
            echo "   RTL: " . ($language->is_rtl ? 'Oui' : 'Non') . "\n";
        } else {
            echo "   ⚠️  Code langue non trouvé dans LanguageSetting!\n";
        }
    }
    echo "\n";
}

echo "✅ Vérification terminée\n";
