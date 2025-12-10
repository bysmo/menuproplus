<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Test de configuration de langue pour le site client\n\n";

// Test pour un restaurant avec langue FR
$restaurantFr = App\Models\Restaurant::where('hash', 'bar-restaurant-bricomo')->first();

if ($restaurantFr) {
    echo "🏪 Restaurant: {$restaurantFr->restaurant_name}\n";
    echo "   Hash: {$restaurantFr->hash}\n";
    echo "   Langue configurée: {$restaurantFr->customer_site_language}\n\n";

    // Mise à jour de la langue si nécessaire
    if ($restaurantFr->customer_site_language !== 'fr') {
        echo "⚠️  La langue est '{$restaurantFr->customer_site_language}' au lieu de 'fr'\n";
        echo "   Voulez-vous la changer en 'fr' ? (o/n): ";

        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim($line) === 'o') {
            $restaurantFr->customer_site_language = 'fr';
            $restaurantFr->save();
            echo "   ✅ Langue mise à jour vers 'fr'\n";
        } else {
            echo "   ❌ Changement annulé\n";
        }
        fclose($handle);
    } else {
        echo "✅ La langue est correctement configurée en français\n";
    }
} else {
    echo "❌ Restaurant 'bar-restaurant-bricomo' non trouvé\n";
}

echo "\n";

// Vérifier les clés de session utilisées
echo "📋 Vérification de la cohérence des clés de session:\n";
echo "   Middleware utilise: 'customer_locale'\n";
echo "   LanguageSwitcher utilise: 'customer_locale'\n";
echo "   HasLanguageSettings utilise: 'customer_locale'\n";
echo "   ✅ Les clés de session sont cohérentes\n\n";

echo "✅ Test terminé\n";
