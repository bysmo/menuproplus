<?php

namespace App\Livewire\Onboarding;

use App\Models\OnboardingStep;
use App\Models\Area;
use App\Models\Table;
use App\Models\Menu;
use App\Models\ItemCategory;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;
use App\Models\OfflinePaymentMethod;
use App\Models\PaymentGatewayCredential;
use Illuminate\Support\Str;
use Livewire\Component;

class OnboardingSteps extends Component
{
    public $currentStep = 1;

    // Restaurant type selection
    public $restaurantType = '';

    // Type => allowed menu IDs mapping
    public $restaurantTypes = [
        'boulangerie'   => ['label' => 'Boulangerie / Café', 'icon' => '🥐', 'menus' => ['Pains & Viennoiseries', 'Pâtisseries & Tartes', 'Snacking & Restauration Rapide', 'Boissons']],
        'africain'      => ['label' => 'Restaurant Africain / Classique', 'icon' => '🍲', 'menus' => ['Plats Traditionnels', 'Grillades & Brochettes', 'Entrées & En-cas', 'Boissons', 'Formules & Menu du Jour']],
        'europeen'      => ['label' => 'Restaurant Européen', 'icon' => '🍽️', 'menus' => ['Snacking & Restauration Rapide', 'Pâtisseries & Tartes', 'Boissons', 'Formules & Menu du Jour']],
        'haut_de_gamme' => ['label' => 'Gastronomique / Haut de gamme', 'icon' => '⭐', 'menus' => ['Pâtisseries & Tartes', 'Grillades & Brochettes', 'Entrées & En-cas', 'Boissons', 'Formules & Menu du Jour']],
        'mixte'         => ['label' => 'Restaurant Mixte / Polyvalent', 'icon' => '🌍', 'menus' => ['Plats Traditionnels', 'Grillades & Brochettes', 'Snacking & Restauration Rapide', 'Boissons', 'Formules & Menu du Jour']],
        'complet'       => ['label' => 'Tout afficher', 'icon' => '📋', 'menus' => null], // null = all
    ];

    // Selections
    public $selectedZones = [];
    public $selectedMenus = [];
    public $selectedCategories = [];
    public $selectedItems = [];
    public $selectedPayments = [];

    // Custom counts
    public $zoneTablesCount = [];

    // Catalogs
    public $catalogZones = [
        ['id' => 'Salle Principale', 'name' => 'Salle Principale', 'desc' => 'Zone intérieure principale', 'tables' => 5],
        ['id' => 'Terrasse', 'name' => 'Terrasse', 'desc' => 'Zone extérieure avec vue', 'tables' => 4],
        ['id' => 'VIP', 'name' => 'VIP', 'desc' => 'Espace réservé et calme', 'tables' => 2],
    ];

    /**
     * Realistic food images (Unsplash stable photo IDs) per category.
     * Used in step 4 UI preview AND saved to MenuItem.image during generation.
     */
    public $categoryImages = [
        'Pains Traditionnels'      => 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400&h=280&fit=crop',
        'Pains Spéciaux'           => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=280&fit=crop',
        'Viennoiseries'            => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&h=280&fit=crop',
        'Gâteaux & Entremets'      => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&h=280&fit=crop',
        'Petits Gâteaux & Biscuits'=> 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&h=280&fit=crop',
        'Tartes (à la coupe)'      => 'https://images.unsplash.com/photo-1464305795204-6f5bbfc7fb81?w=400&h=280&fit=crop',
        'Sandwichs & Formules'     => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=280&fit=crop',
        'Salades & Plats Chauds'   => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=280&fit=crop',
        'Wraps & Paninis'          => 'https://images.unsplash.com/photo-1565299715199-866c917206bb?w=400&h=280&fit=crop',
        'Riz & Sauces'             => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400&h=280&fit=crop',
        'Riz Spéciaux'             => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=400&h=280&fit=crop',
        'Tô & Sauces'              => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=280&fit=crop',
        'Foutou & Sauces'          => 'https://images.unsplash.com/photo-1567364816519-cbc9c4ffe1eb?w=400&h=280&fit=crop',
        'Pâtes'                    => 'https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=400&h=280&fit=crop',
        'Volailles grillées'       => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=400&h=280&fit=crop',
        'Viandes grillées'         => 'https://images.unsplash.com/photo-1544025162-d76538b4c05c?w=400&h=280&fit=crop',
        'Poissons grillés'         => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400&h=280&fit=crop',
        'Accompagnements'          => 'https://images.unsplash.com/photo-1630384060421-cb20d0e0649d?w=400&h=280&fit=crop',
        'Entrées chaudes'          => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=400&h=280&fit=crop',
        'Entrées froides'          => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=400&h=280&fit=crop',
        'Boissons Traditionnelles' => 'https://images.unsplash.com/photo-1536935338788-846bb9981813?w=400&h=280&fit=crop',
        'Boissons Fraîches'        => 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=400&h=280&fit=crop',
        'Boissons Chaudes'         => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400&h=280&fit=crop',
        'Formule Déjeuner'         => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400&h=280&fit=crop',
        'Menu du Jour'             => 'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=400&h=280&fit=crop',
    ];

    /** Unique photo per item ID — overrides the category fallback above */
    public $itemImages = [
        // --- Pains Traditionnels ---
        'Baguette'        => 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400&h=280&fit=crop',
        'PainCampagne400' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=280&fit=crop',
        'PainCampagne800' => 'https://images.unsplash.com/photo-1598373182133-52452f7691ef?w=400&h=280&fit=crop',
        'PainCereales'    => 'https://images.unsplash.com/photo-1574085733277-851d9d856a3a?w=400&h=280&fit=crop',
        'PainComplet'     => 'https://images.unsplash.com/photo-1484723091739-30990f88c8ec?w=400&h=280&fit=crop',
        'PainSeigle'      => 'https://images.unsplash.com/photo-1608198093002-ad4e005484ec?w=400&h=280&fit=crop',
        'Ficelle'         => 'https://images.unsplash.com/photo-1517824806704-9040b037703b?w=400&h=280&fit=crop',
        // --- Pains Spéciaux ---
        'PainNoix'        => 'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=400&h=280&fit=crop',
        'PainOlives'      => 'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=400&h=280&fit=crop',
        'PainFigues'      => 'https://images.unsplash.com/photo-1586444248834-b0e2e62f478a?w=400&h=280&fit=crop',
        'Fougasse'        => 'https://images.unsplash.com/photo-1600128522497-b9a4f4e91e61?w=400&h=280&fit=crop',
        'PainNordic'      => 'https://images.unsplash.com/photo-1544345581-5619a6bff853?w=400&h=280&fit=crop',
        // --- Viennoiseries ---
        'Croissant'       => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&h=280&fit=crop',
        'PainChoco'       => 'https://images.unsplash.com/photo-1603532648955-039310d9ed75?w=400&h=280&fit=crop',
        'ChaussonPommes'  => 'https://images.unsplash.com/photo-1621743478914-cc8a86d7e7b5?w=400&h=280&fit=crop',
        'PainRaisins'     => 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=400&h=280&fit=crop',
        'BriocheInd'      => 'https://images.unsplash.com/photo-1535912559178-9e0eca8a0050?w=400&h=280&fit=crop',
        'Kouign'          => 'https://images.unsplash.com/photo-1559622214-f8a9850965bb?w=400&h=280&fit=crop',
        // --- Gâteaux & Entremets ---
        'ParisBrest'      => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&h=280&fit=crop',
        'EclairChoco'     => 'https://images.unsplash.com/photo-1566843972142-a7fcb70de55a?w=400&h=280&fit=crop',
        'EclairCafe'      => 'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?w=400&h=280&fit=crop',
        'MilleFeuille'    => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&h=280&fit=crop',
        'Opera'           => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=280&fit=crop',
        'TarteFruitsRouges'=> 'https://images.unsplash.com/photo-1464305795204-6f5bbfc7fb81?w=400&h=280&fit=crop',
        'TarteCitron'     => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=400&h=280&fit=crop',
        // --- Petits Gâteaux & Biscuits ---
        'Macaron'         => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&h=280&fit=crop',
        'Financier'       => 'https://images.unsplash.com/photo-1600326145552-327c4df2c533?w=400&h=280&fit=crop',
        'Madeleine'       => 'https://images.unsplash.com/photo-1619954871828-40a5c5d38b87?w=400&h=280&fit=crop',
        'Canele'          => 'https://images.unsplash.com/photo-1604695573706-53170668f6a6?w=400&h=280&fit=crop',
        'Cookie'          => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=400&h=280&fit=crop',
        // --- Tartes ---
        'TartePomme'      => 'https://images.unsplash.com/photo-1621743478914-cc8a86d7e7b5?w=400&h=280&fit=crop&q=80',
        'TarteFragaises'  => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&h=280&fit=crop&q=80',
        'TarteNoix'       => 'https://images.unsplash.com/photo-1535920527002-b35e96722eb9?w=400&h=280&fit=crop',
        'QuicheLorraine'  => 'https://images.unsplash.com/photo-1604068549290-dea0e4a305ca?w=400&h=280&fit=crop',
        // --- Sandwichs & Formules ---
        'SandwichJambon'  => 'https://images.unsplash.com/photo-1553909489-cd47e0ef359f?w=400&h=280&fit=crop',
        'SandwichPoulet'  => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=280&fit=crop',
        'ClubSandwich'    => 'https://images.unsplash.com/photo-1528736235302-52922df5c122?w=400&h=280&fit=crop',
        'CroqueMonsieur'  => 'https://images.unsplash.com/photo-1528736235302-52922df5c122?w=400&h=280&fit=crop&sat=-20',
        'FormuleSandwich' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=280&fit=crop',
        // --- Salades & Plats Chauds ---
        'SaladeChevreChaud'=> 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=280&fit=crop',
        'QuicheJour'      => 'https://images.unsplash.com/photo-1604068549290-dea0e4a305ca?w=400&h=280&fit=crop&sat=10',
        'SoupeJour'       => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=400&h=280&fit=crop',
        // --- Wraps & Paninis ---
        'PaniniJambon'    => 'https://images.unsplash.com/photo-1571091655789-405eb7a3a3a8?w=400&h=280&fit=crop',
        'PaniniPoulet'    => 'https://images.unsplash.com/photo-1565299715199-866c917206bb?w=400&h=280&fit=crop',
        'WrapVege'        => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=400&h=280&fit=crop',
        // --- Riz & Sauces ---
        'RizArachide'     => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400&h=280&fit=crop',
        'RizFeuille'      => 'https://images.unsplash.com/photo-1516714435131-44d6b64dc6a2?w=400&h=280&fit=crop',
        'RizGombo'        => 'https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd?w=400&h=280&fit=crop',
        'RizGraine'       => 'https://images.unsplash.com/photo-1567364816519-cbc9c4ffe1eb?w=400&h=280&fit=crop',
        'RizLegumes'      => 'https://images.unsplash.com/photo-1546549032-9571cd6b27df?w=400&h=280&fit=crop',
        'RizClaire'       => 'https://images.unsplash.com/photo-1499028344343-cd173ffc68a9?w=400&h=280&fit=crop',
        'RizTomate'       => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=280&fit=crop&sat=20',
        'RizYassa'        => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=400&h=280&fit=crop&sat=-10',
        // --- Riz Spéciaux ---
        'RizGrasPoulet'   => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=400&h=280&fit=crop',
        'RizGrasBoeuf'    => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&h=280&fit=crop',
        'RizGrasPintade'  => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=400&h=280&fit=crop',
        'TchepPoisson'    => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400&h=280&fit=crop',
        'TchepPoulet'     => 'https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=400&h=280&fit=crop',
        // --- Tô & Sauces ---
        'ToArachide'      => 'https://images.unsplash.com/photo-1567364816519-cbc9c4ffe1eb?w=400&h=280&fit=crop&sat=5',
        'ToFeuille'       => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=280&fit=crop&sat=-5',
        'ToGombo'         => 'https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd?w=400&h=280&fit=crop&sat=-5',
        'ToGraine'        => 'https://images.unsplash.com/photo-1546549032-9571cd6b27df?w=400&h=280&fit=crop&sat=5',
        'ToClaire'        => 'https://images.unsplash.com/photo-1499028344343-cd173ffc68a9?w=400&h=280&fit=crop&sat=5',
        // --- Foutou & Sauces ---
        'FoutouBananeArachide'  => 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=400&h=280&fit=crop',
        'FoutouBananeGraine'    => 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=400&h=280&fit=crop&sat=10',
        'FoutouBananeGombo'     => 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=400&h=280&fit=crop&sat=-10',
        'FoutouIgnameArachide'  => 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?w=400&h=280&fit=crop',
        'FoutouIgnameGraine'    => 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?w=400&h=280&fit=crop&sat=10',
        'FoutouIgnameClaire'    => 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?w=400&h=280&fit=crop&sat=-10',
        // --- Pâtes ---
        'SpaghettiGrasPoulet'  => 'https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=400&h=280&fit=crop',
        'SpaghettiGrasBoeuf'   => 'https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb?w=400&h=280&fit=crop',
        'SpaghettiBoloP'       => 'https://images.unsplash.com/photo-1476124369491-e7addf5db371?w=400&h=280&fit=crop',
        'MacaroniGras'         => 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=400&h=280&fit=crop&sat=-30',
        // --- Volailles grillées ---
        'PouletBraiseEntier'  => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=400&h=280&fit=crop',
        'DemiPouletBraise'    => 'https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=400&h=280&fit=crop',
        'QuartPoulet'         => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=400&h=280&fit=crop',
        'PintadeEntiere'      => 'https://images.unsplash.com/photo-1548247416-ec66f4900b2e?w=400&h=280&fit=crop',
        'DemiPintade'         => 'https://images.unsplash.com/photo-1580554530778-ca36943938b2?w=400&h=280&fit=crop',
        'CuisseDinde'         => 'https://images.unsplash.com/photo-1574672280600-4accfa5b6f98?w=400&h=280&fit=crop',
        // --- Viandes grillées ---
        'BrochetteBoeuf'   => 'https://images.unsplash.com/photo-1544025162-d76538b4c05c?w=400&h=280&fit=crop',
        'BrochetteMouton'  => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400&h=280&fit=crop',
        'CotesMouton'      => 'https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?w=400&h=280&fit=crop',
        'CabriRoti'        => 'https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?w=400&h=280&fit=crop&sat=10',
        'FoieBoeuf'        => 'https://images.unsplash.com/photo-1558030006-450675393462?w=400&h=280&fit=crop',
        // --- Poissons grillés ---
        'CapitaineGrille'  => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400&h=280&fit=crop&sat=10',
        'TilapiaGrille'    => 'https://images.unsplash.com/photo-1534482421-64566f976cfa?w=400&h=280&fit=crop',
        'CarpeGrillee'     => 'https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?w=400&h=280&fit=crop',
        'PoissonsAuPiment' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=400&h=280&fit=crop',
        // --- Accompagnements ---
        'Alloco'           => 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=400&h=280&fit=crop&sat=30',
        'Attieke'          => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400&h=280&fit=crop&sat=-30',
        'FritesM'          => 'https://images.unsplash.com/photo-1630384060421-cb20d0e0649d?w=400&h=280&fit=crop',
        'RizBlanc'         => 'https://images.unsplash.com/photo-1536304993881-ff86e0c9437c?w=400&h=280&fit=crop',
        'SaucePiment'      => 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?w=400&h=280&fit=crop&sat=60',
        // --- Entrées chaudes ---
        'Samoussa'         => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=400&h=280&fit=crop',
        'Kosei'            => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&h=280&fit=crop&sat=-20',
        'SoupePoisson'     => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=400&h=280&fit=crop&sat=15',
        'SoupeBoeuf'       => 'https://images.unsplash.com/photo-1603073163308-9654c3fb70b5?w=400&h=280&fit=crop',
        // --- Entrées froides ---
        'SaladeLegumes'    => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=400&h=280&fit=crop',
        'SaladeAvocat'     => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=280&fit=crop&sat=-10',
        'SaladeMais'       => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=400&h=280&fit=crop',
        // --- Boissons Traditionnelles ---
        'JusBissap'        => 'https://images.unsplash.com/photo-1536935338788-846bb9981813?w=400&h=280&fit=crop',
        'JusGingembre'     => 'https://images.unsplash.com/photo-1603569283847-aa295f0d016a?w=400&h=280&fit=crop',
        'JusTamarin'       => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=400&h=280&fit=crop',
        'JusBouye'         => 'https://images.unsplash.com/photo-1623065422902-30a2d299bbe4?w=400&h=280&fit=crop',
        'JusDitakh'        => 'https://images.unsplash.com/photo-1497534446932-c925b458314e?w=400&h=280&fit=crop',
        'Dolo'             => 'https://images.unsplash.com/photo-1558642891-54be180ea339?w=400&h=280&fit=crop',
        // --- Boissons Fraîches ---
        'EauMinerale'      => 'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=400&h=280&fit=crop',
        'JusFraisPresses'  => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=400&h=280&fit=crop',
        'JusIndustriel'    => 'https://images.unsplash.com/photo-1622597467836-f3e46f0e49b4?w=400&h=280&fit=crop',
        'Soda'             => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=400&h=280&fit=crop',
        'Biere'            => 'https://images.unsplash.com/photo-1558642891-54be180ea339?w=400&h=280&fit=crop&sat=-20',
        // --- Boissons Chaudes ---
        'CafeNescafe'      => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400&h=280&fit=crop',
        'CafeAuLait'       => 'https://images.unsplash.com/photo-1534687941688-651ccaafbff8?w=400&h=280&fit=crop',
        'TheMenthe'        => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=400&h=280&fit=crop',
        'ChocolatChaud'    => 'https://images.unsplash.com/photo-1517578239113-b03992dcdd25?w=400&h=280&fit=crop',
        'CafeEspresso'     => 'https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=400&h=280&fit=crop',
        'Cappuccino'       => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&h=280&fit=crop',
        'LatteMacc'        => 'https://images.unsplash.com/photo-1561882468-9110d70d4f23?w=400&h=280&fit=crop',
        // --- Formules & Menu du Jour ---
        'Formule1'         => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400&h=280&fit=crop',
        'Formule2'         => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=280&fit=crop&sat=5',
        'Formule3'         => 'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=400&h=280&fit=crop',
        'PlatJour'         => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&h=280&fit=crop',
        'GrilladeJour'     => 'https://images.unsplash.com/photo-1544025162-d76538b4c05c?w=400&h=280&fit=crop&sat=5',
        'BoissonJour'      => 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=400&h=280&fit=crop',
    ];

    public $catalogMenus = [
        ['id' => 'Pains & Viennoiseries',            'icon' => '🥖', 'color' => 'amber',  'name' => 'Pains & Viennoiseries',            'desc' => 'Pains, baguettes et viennoiseries'],
        ['id' => 'Pâtisseries & Tartes',              'icon' => '🍰', 'color' => 'pink',   'name' => 'Pâtisseries & Tartes',              'desc' => 'Gâteaux, entremets et tartes'],
        ['id' => 'Snacking & Restauration Rapide',   'icon' => '🥪', 'color' => 'orange', 'name' => 'Snacking & Restauration Rapide',   'desc' => 'Sandwichs, salades et repas rapide'],
        ['id' => 'Plats Traditionnels',               'icon' => '🍲', 'color' => 'green',  'name' => 'Plats Traditionnels',               'desc' => 'Plats locaux et spécialités africaines'],
        ['id' => 'Grillades & Brochettes',            'icon' => '🍖', 'color' => 'red',    'name' => 'Grillades & Brochettes',            'desc' => 'Viandes, volailles et poissons grillés'],
        ['id' => 'Entrées & En-cas',                 'icon' => '🥗', 'color' => 'teal',   'name' => 'Entrées & En-cas',                 'desc' => 'Petits plats pour commencer'],
        ['id' => 'Boissons',                          'icon' => '🍹', 'color' => 'blue',   'name' => 'Boissons',                          'desc' => 'Boissons chaudes, fraîches et traditionnelles'],
        ['id' => 'Formules & Menu du Jour',           'icon' => '📋', 'color' => 'violet', 'name' => 'Formules & Menu du Jour',           'desc' => 'Formules repas et suggestions du chef'],
    ];

    public $catalogCategories = [
        ['id' => 'Pains Traditionnels',      'icon' => '🥖', 'menu_ref' => 'Pains & Viennoiseries',            'name' => 'Pains Traditionnels',      'desc' => 'Baguettes et pains classiques'],
        ['id' => 'Pains Spéciaux',          'icon' => '🌾', 'menu_ref' => 'Pains & Viennoiseries',            'name' => 'Pains Spéciaux',          'desc' => 'Pains aux noix, olives, céréales'],
        ['id' => 'Viennoiseries',            'icon' => '🥐', 'menu_ref' => 'Pains & Viennoiseries',            'name' => 'Viennoiseries',            'desc' => 'Croissants, pains au chocolat'],
        ['id' => 'Gâteaux & Entremets',     'icon' => '🎂', 'menu_ref' => 'Pâtisseries & Tartes',              'name' => 'Gâteaux & Entremets',     'desc' => 'Paris-Brest, éclairs, mille-feuille'],
        ['id' => 'Petits Gâteaux & Biscuits','icon' => '🍪','menu_ref' => 'Pâtisseries & Tartes',              'name' => 'Petits Gâteaux & Biscuits','desc' => 'Macarons, madeleines, cookies'],
        ['id' => 'Tartes (à la coupe)',      'icon' => '🥧', 'menu_ref' => 'Pâtisseries & Tartes',              'name' => 'Tartes (à la coupe)',      'desc' => 'Tartes aux fruits et quiches'],
        ['id' => 'Sandwichs & Formules',     'icon' => '🥪', 'menu_ref' => 'Snacking & Restauration Rapide',   'name' => 'Sandwichs & Formules',     'desc' => 'Sandwichs, baguettes'],
        ['id' => 'Salades & Plats Chauds',   'icon' => '🥗', 'menu_ref' => 'Snacking & Restauration Rapide',   'name' => 'Salades & Plats Chauds',   'desc' => 'Salades et plats'],
        ['id' => 'Wraps & Paninis',          'icon' => '🌯', 'menu_ref' => 'Snacking & Restauration Rapide',   'name' => 'Wraps & Paninis',          'desc' => 'Wraps et paninis'],
        ['id' => 'Riz & Sauces',             'icon' => '🍚', 'menu_ref' => 'Plats Traditionnels',               'name' => 'Riz & Sauces',             'desc' => 'Plats de riz et sauces'],
        ['id' => 'Riz Spéciaux',            'icon' => '✨',  'menu_ref' => 'Plats Traditionnels',               'name' => 'Riz Spéciaux',            'desc' => 'Riz gras, tchep'],
        ['id' => 'Tô & Sauces',             'icon' => '🌡️', 'menu_ref' => 'Plats Traditionnels',               'name' => 'Tô & Sauces',             'desc' => 'Tô et sauces'],
        ['id' => 'Foutou & Sauces',          'icon' => '🍌', 'menu_ref' => 'Plats Traditionnels',               'name' => 'Foutou & Sauces',          'desc' => 'Foutou et sauces'],
        ['id' => 'Pâtes',                   'icon' => '🍝', 'menu_ref' => 'Plats Traditionnels',               'name' => 'Pâtes',                   'desc' => 'Plats de pâtes'],
        ['id' => 'Volailles grillées',      'icon' => '🍗', 'menu_ref' => 'Grillades & Brochettes',            'name' => 'Volailles grillées',      'desc' => 'Poulet et pintade'],
        ['id' => 'Viandes grillées',        'icon' => '🥩', 'menu_ref' => 'Grillades & Brochettes',            'name' => 'Viandes grillées',        'desc' => 'Brochettes, mouton'],
        ['id' => 'Poissons grillés',        'icon' => '🐟', 'menu_ref' => 'Grillades & Brochettes',            'name' => 'Poissons grillés',        'desc' => 'Poissons braisés'],
        ['id' => 'Accompagnements',          'icon' => '🍟', 'menu_ref' => 'Grillades & Brochettes',            'name' => 'Accompagnements',          'desc' => 'Alloco, frites, attiéké'],
        ['id' => 'Entrées chaudes',         'icon' => '🌮', 'menu_ref' => 'Entrées & En-cas',                 'name' => 'Entrées chaudes',         'desc' => 'Samoussas, soupes'],
        ['id' => 'Entrées froides',         'icon' => '🌱', 'menu_ref' => 'Entrées & En-cas',                 'name' => 'Entrées froides',         'desc' => 'Salades, avocat'],
        ['id' => 'Boissons Traditionnelles', 'icon' => '🌵', 'menu_ref' => 'Boissons',                          'name' => 'Boissons Traditionnelles', 'desc' => 'Bissap, gingembre, dolo'],
        ['id' => 'Boissons Fraîches',       'icon' => '🍼', 'menu_ref' => 'Boissons',                          'name' => 'Boissons Fraîches',       'desc' => 'Jus, eaux, sodas'],
        ['id' => 'Boissons Chaudes',         'icon' => '☕',  'menu_ref' => 'Boissons',                          'name' => 'Boissons Chaudes',         'desc' => 'Cafés, thés, infusions'],
        ['id' => 'Formule Déjeuner',        'icon' => '🍽️', 'menu_ref' => 'Formules & Menu du Jour',           'name' => 'Formule Déjeuner',        'desc' => 'Plats du midi'],
        ['id' => 'Menu du Jour',             'icon' => '📝', 'menu_ref' => 'Formules & Menu du Jour',           'name' => 'Menu du Jour',             'desc' => 'Plat du jour'],
    ];

    public $catalogItems = [
        // ===== BOULANGERIE =====
        // Pains Traditionnels
        ['id' => 'Baguette', 'cat_id' => 'Pains Traditionnels', 'name' => 'Baguette tradition', 'price' => 200],
        ['id' => 'PainCampagne400', 'cat_id' => 'Pains Traditionnels', 'name' => 'Pain de campagne 400g', 'price' => 500],
        ['id' => 'PainCampagne800', 'cat_id' => 'Pains Traditionnels', 'name' => 'Pain de campagne 800g', 'price' => 900],
        ['id' => 'PainCereales', 'cat_id' => 'Pains Traditionnels', 'name' => 'Pain aux céréales', 'price' => 600],
        ['id' => 'PainComplet', 'cat_id' => 'Pains Traditionnels', 'name' => 'Pain complet', 'price' => 600],
        ['id' => 'PainSeigle', 'cat_id' => 'Pains Traditionnels', 'name' => 'Pain au seigle', 'price' => 700],
        ['id' => 'Ficelle', 'cat_id' => 'Pains Traditionnels', 'name' => 'Ficelle', 'price' => 150],
        // Pains Spéciaux
        ['id' => 'PainNoix', 'cat_id' => 'Pains Spéciaux', 'name' => 'Pain aux noix', 'price' => 900],
        ['id' => 'PainOlives', 'cat_id' => 'Pains Spéciaux', 'name' => 'Pain aux olives', 'price' => 900],
        ['id' => 'PainFigues', 'cat_id' => 'Pains Spéciaux', 'name' => 'Pain aux figues et raisins', 'price' => 1000],
        ['id' => 'Fougasse', 'cat_id' => 'Pains Spéciaux', 'name' => 'Fougasse (lardons/olives)', 'price' => 1200],
        ['id' => 'PainNordic', 'cat_id' => 'Pains Spéciaux', 'name' => 'Pain nordic (lin, tournesol)', 'price' => 1100],
        // Viennoiseries
        ['id' => 'Croissant', 'cat_id' => 'Viennoiseries', 'name' => 'Croissant au beurre', 'price' => 300],
        ['id' => 'PainChoco', 'cat_id' => 'Viennoiseries', 'name' => 'Pain au chocolat', 'price' => 400],
        ['id' => 'ChaussonPommes', 'cat_id' => 'Viennoiseries', 'name' => 'Chausson aux pommes', 'price' => 500],
        ['id' => 'PainRaisins', 'cat_id' => 'Viennoiseries', 'name' => 'Pain aux raisins', 'price' => 400],
        ['id' => 'BriocheInd', 'cat_id' => 'Viennoiseries', 'name' => 'Brioche individuelle', 'price' => 400],
        ['id' => 'Kouign', 'cat_id' => 'Viennoiseries', 'name' => 'Kouign-amann', 'price' => 700],
        // Gâteaux & Entremets
        ['id' => 'ParisBrest', 'cat_id' => 'Gâteaux & Entremets', 'name' => 'Paris-Brest', 'price' => 1500],
        ['id' => 'EclairChoco', 'cat_id' => 'Gâteaux & Entremets', 'name' => 'Éclair au chocolat', 'price' => 1000],
        ['id' => 'EclairCafe', 'cat_id' => 'Gâteaux & Entremets', 'name' => 'Éclair au café', 'price' => 1000],
        ['id' => 'MilleFeuille', 'cat_id' => 'Gâteaux & Entremets', 'name' => 'Mille-feuille', 'price' => 1500],
        ['id' => 'Opera', 'cat_id' => 'Gâteaux & Entremets', 'name' => 'Opéra', 'price' => 1500],
        ['id' => 'TarteFruitsRouges', 'cat_id' => 'Gâteaux & Entremets', 'name' => 'Tarte aux fruits rouges', 'price' => 1500],
        ['id' => 'TarteCitron', 'cat_id' => 'Gâteaux & Entremets', 'name' => 'Tarte au citron meringuée', 'price' => 1500],
        // Petits Gâteaux & Biscuits
        ['id' => 'Macaron', 'cat_id' => 'Petits Gâteaux & Biscuits', 'name' => 'Macaron (assortiment)', 'price' => 600],
        ['id' => 'Financier', 'cat_id' => 'Petits Gâteaux & Biscuits', 'name' => 'Financier', 'price' => 400],
        ['id' => 'Madeleine', 'cat_id' => 'Petits Gâteaux & Biscuits', 'name' => 'Madeleine', 'price' => 300],
        ['id' => 'Canele', 'cat_id' => 'Petits Gâteaux & Biscuits', 'name' => 'Canelé bordelais', 'price' => 500],
        ['id' => 'Cookie', 'cat_id' => 'Petits Gâteaux & Biscuits', 'name' => 'Cookie chocolat noisette', 'price' => 400],
        // Tartes
        ['id' => 'TartePomme', 'cat_id' => 'Tartes (à la coupe)', 'name' => 'Tarte aux pommes', 'price' => 1000],
        ['id' => 'TarteFragaises', 'cat_id' => 'Tartes (à la coupe)', 'name' => 'Tarte aux fraises', 'price' => 1200],
        ['id' => 'TarteNoix', 'cat_id' => 'Tartes (à la coupe)', 'name' => 'Tarte à la noix', 'price' => 1000],
        ['id' => 'QuicheLorraine', 'cat_id' => 'Tartes (à la coupe)', 'name' => 'Quiche lorraine', 'price' => 1200],
        // Sandwichs & Formules
        ['id' => 'SandwichJambon', 'cat_id' => 'Sandwichs & Formules', 'name' => 'Sandwich jambon-beurre', 'price' => 1500],
        ['id' => 'SandwichPoulet', 'cat_id' => 'Sandwichs & Formules', 'name' => 'Sandwich poulet-crudités', 'price' => 1800],
        ['id' => 'ClubSandwich', 'cat_id' => 'Sandwichs & Formules', 'name' => 'Club sandwich (toasté)', 'price' => 2500],
        ['id' => 'CroqueMonsieur', 'cat_id' => 'Sandwichs & Formules', 'name' => 'Croque-monsieur', 'price' => 1200],
        ['id' => 'FormuleSandwich', 'cat_id' => 'Sandwichs & Formules', 'name' => 'Formule sandwich + boisson + dessert', 'price' => 3500],
        // Salades & Plats Chauds
        ['id' => 'SaladeChevreChaud', 'cat_id' => 'Salades & Plats Chauds', 'name' => 'Salade (chèvre chaud, noix)', 'price' => 2500],
        ['id' => 'QuicheJour', 'cat_id' => 'Salades & Plats Chauds', 'name' => 'Quiche du jour (part)', 'price' => 1500],
        ['id' => 'SoupeJour', 'cat_id' => 'Salades & Plats Chauds', 'name' => 'Soupe du jour (saison)', 'price' => 1500],
        // Wraps & Paninis
        ['id' => 'PaniniJambon', 'cat_id' => 'Wraps & Paninis', 'name' => 'Panini jambon-fromage', 'price' => 2000],
        ['id' => 'PaniniPoulet', 'cat_id' => 'Wraps & Paninis', 'name' => 'Panini poulet-pesto', 'price' => 2000],
        ['id' => 'WrapVege', 'cat_id' => 'Wraps & Paninis', 'name' => 'Wrap végétarien (houmous)', 'price' => 1800],

        // ===== RESTAURANT CLASSIQUE (BURKINA FASO) =====
        // Riz & Sauces
        ['id' => 'RizArachide', 'cat_id' => 'Riz & Sauces', 'name' => 'Riz + Sauce arachide', 'price' => 2500],
        ['id' => 'RizFeuille', 'cat_id' => 'Riz & Sauces', 'name' => 'Riz + Sauce feuille', 'price' => 2500],
        ['id' => 'RizGombo', 'cat_id' => 'Riz & Sauces', 'name' => 'Riz + Sauce gombo', 'price' => 2500],
        ['id' => 'RizGraine', 'cat_id' => 'Riz & Sauces', 'name' => 'Riz + Sauce graine', 'price' => 3000],
        ['id' => 'RizLegumes', 'cat_id' => 'Riz & Sauces', 'name' => 'Riz + Sauce légumes', 'price' => 2000],
        ['id' => 'RizClaire', 'cat_id' => 'Riz & Sauces', 'name' => 'Riz + Sauce claire', 'price' => 2000],
        ['id' => 'RizTomate', 'cat_id' => 'Riz & Sauces', 'name' => 'Riz + Sauce tomate', 'price' => 2000],
        ['id' => 'RizYassa', 'cat_id' => 'Riz & Sauces', 'name' => 'Riz + Yassa', 'price' => 3000],
        // Riz Spéciaux
        ['id' => 'RizGrasPoulet', 'cat_id' => 'Riz Spéciaux', 'name' => 'Riz gras au poulet', 'price' => 2500],
        ['id' => 'RizGrasBoeuf', 'cat_id' => 'Riz Spéciaux', 'name' => 'Riz gras au bœuf', 'price' => 2500],
        ['id' => 'RizGrasPintade', 'cat_id' => 'Riz Spéciaux', 'name' => 'Riz gras à la pintade', 'price' => 3000],
        ['id' => 'TchepPoisson', 'cat_id' => 'Riz Spéciaux', 'name' => 'Tchep au poisson (thiéboudienne)', 'price' => 3000],
        ['id' => 'TchepPoulet', 'cat_id' => 'Riz Spéciaux', 'name' => 'Tchep au poulet', 'price' => 2500],
        // Tô & Sauces
        ['id' => 'ToArachide', 'cat_id' => 'Tô & Sauces', 'name' => 'Tô + Sauce arachide', 'price' => 1500],
        ['id' => 'ToFeuille', 'cat_id' => 'Tô & Sauces', 'name' => 'Tô + Sauce feuille', 'price' => 1500],
        ['id' => 'ToGombo', 'cat_id' => 'Tô & Sauces', 'name' => 'Tô + Sauce gombo', 'price' => 1500],
        ['id' => 'ToGraine', 'cat_id' => 'Tô & Sauces', 'name' => 'Tô + Sauce graine', 'price' => 2000],
        ['id' => 'ToClaire', 'cat_id' => 'Tô & Sauces', 'name' => 'Tô + Sauce claire', 'price' => 1500],
        // Foutou & Sauces
        ['id' => 'FoutouBananeArachide', 'cat_id' => 'Foutou & Sauces', 'name' => 'Foutou banane + Sauce arachide', 'price' => 3000],
        ['id' => 'FoutouBananeGraine', 'cat_id' => 'Foutou & Sauces', 'name' => 'Foutou banane + Sauce graine', 'price' => 3000],
        ['id' => 'FoutouBananeGombo', 'cat_id' => 'Foutou & Sauces', 'name' => 'Foutou banane + Sauce gombo', 'price' => 3000],
        ['id' => 'FoutouIgnameArachide', 'cat_id' => 'Foutou & Sauces', 'name' => 'Foutou igname + Sauce arachide', 'price' => 3500],
        ['id' => 'FoutouIgnameGraine', 'cat_id' => 'Foutou & Sauces', 'name' => 'Foutou igname + Sauce graine', 'price' => 3500],
        ['id' => 'FoutouIgnameClaire', 'cat_id' => 'Foutou & Sauces', 'name' => 'Foutou igname + Sauce claire', 'price' => 3000],
        // Pâtes
        ['id' => 'SpaghettiGrasPoulet', 'cat_id' => 'Pâtes', 'name' => 'Spaghetti gras au poulet', 'price' => 2000],
        ['id' => 'SpaghettiGrasBoeuf', 'cat_id' => 'Pâtes', 'name' => 'Spaghetti gras au bœuf', 'price' => 2000],
        ['id' => 'SpaghettiBoloP', 'cat_id' => 'Pâtes', 'name' => 'Spaghetti bolognaise poulet', 'price' => 2500],
        ['id' => 'MacaroniGras', 'cat_id' => 'Pâtes', 'name' => 'Macaroni gras', 'price' => 2000],
        // Volailles grillées
        ['id' => 'PouletBraiseEntier', 'cat_id' => 'Volailles grillées', 'name' => 'Poulet braisé entier', 'price' => 6000],
        ['id' => 'DemiPouletBraise', 'cat_id' => 'Volailles grillées', 'name' => 'Demi-poulet braisé', 'price' => 3500],
        ['id' => 'QuartPoulet', 'cat_id' => 'Volailles grillées', 'name' => 'Quart de poulet braisé', 'price' => 2000],
        ['id' => 'PintadeEntiere', 'cat_id' => 'Volailles grillées', 'name' => 'Pintade braisée entière', 'price' => 7000],
        ['id' => 'DemiPintade', 'cat_id' => 'Volailles grillées', 'name' => 'Demi-pintade braisée', 'price' => 4000],
        ['id' => 'CuisseDinde', 'cat_id' => 'Volailles grillées', 'name' => 'Cuisse de dinde grillée', 'price' => 3500],
        // Viandes grillées
        ['id' => 'BrochetteBoeuf', 'cat_id' => 'Viandes grillées', 'name' => 'Brochettes de bœuf (4 pcs)', 'price' => 1500],
        ['id' => 'BrochetteMouton', 'cat_id' => 'Viandes grillées', 'name' => 'Brochettes de mouton (4 pcs)', 'price' => 2000],
        ['id' => 'CotesMouton', 'cat_id' => 'Viandes grillées', 'name' => 'Côtelettes de mouton grillées', 'price' => 3000],
        ['id' => 'CabriRoti', 'cat_id' => 'Viandes grillées', 'name' => 'Cabri rôti (portion)', 'price' => 3500],
        ['id' => 'FoieBoeuf', 'cat_id' => 'Viandes grillées', 'name' => 'Foie de bœuf grillé', 'price' => 2000],
        // Poissons grillés
        ['id' => 'CapitaineGrille', 'cat_id' => 'Poissons grillés', 'name' => 'Capitaine grillé entier (500g)', 'price' => 5000],
        ['id' => 'TilapiaGrille', 'cat_id' => 'Poissons grillés', 'name' => 'Tilapia grillé entier', 'price' => 4000],
        ['id' => 'CarpeGrillee', 'cat_id' => 'Poissons grillés', 'name' => 'Carpe grillée entière', 'price' => 4500],
        ['id' => 'PoissonsAuPiment', 'cat_id' => 'Poissons grillés', 'name' => 'Poisson braisé sauce pimentée', 'price' => 4000],
        // Accompagnements
        ['id' => 'Alloco', 'cat_id' => 'Accompagnements', 'name' => 'Alloco (banane plantain frite)', 'price' => 500],
        ['id' => 'Attieke', 'cat_id' => 'Accompagnements', 'name' => 'Attiéké (couscous de manioc)', 'price' => 500],
        ['id' => 'FritesM', 'cat_id' => 'Accompagnements', 'name' => 'Frites maison', 'price' => 500],
        ['id' => 'RizBlanc', 'cat_id' => 'Accompagnements', 'name' => 'Riz blanc nature', 'price' => 500],
        ['id' => 'SaucePiment', 'cat_id' => 'Accompagnements', 'name' => 'Piment frais / sauce pimentée', 'price' => 200],
        // Entrées chaudes
        ['id' => 'Samoussa', 'cat_id' => 'Entrées chaudes', 'name' => 'Samoussas viande/légumes (4 pcs)', 'price' => 1000],
        ['id' => 'Kosei', 'cat_id' => 'Entrées chaudes', 'name' => 'Beignets de haricots (kosei)', 'price' => 500],
        ['id' => 'SoupePoisson', 'cat_id' => 'Entrées chaudes', 'name' => 'Soupe de poisson locale', 'price' => 1500],
        ['id' => 'SoupeBoeuf', 'cat_id' => 'Entrées chaudes', 'name' => 'Soupe de bœuf aux légumes', 'price' => 2000],
        // Entrées froides / Salades
        ['id' => 'SaladeLegumes', 'cat_id' => 'Entrées froides', 'name' => 'Salade de légumes frais', 'price' => 1500],
        ['id' => 'SaladeAvocat', 'cat_id' => 'Entrées froides', 'name' => 'Salade d\'avocat (tomate, oignon)', 'price' => 2000],
        ['id' => 'SaladeMais', 'cat_id' => 'Entrées froides', 'name' => 'Salade de maïs', 'price' => 1500],
        // Boissons Traditionnelles & Locales
        ['id' => 'JusBissap', 'cat_id' => 'Boissons Traditionnelles', 'name' => 'Jus de bissap (hibiscus)', 'price' => 500],
        ['id' => 'JusGingembre', 'cat_id' => 'Boissons Traditionnelles', 'name' => 'Jus de gingembre (gnamakoudji)', 'price' => 500],
        ['id' => 'JusTamarin', 'cat_id' => 'Boissons Traditionnelles', 'name' => 'Jus de tamarin', 'price' => 500],
        ['id' => 'JusBouye', 'cat_id' => 'Boissons Traditionnelles', 'name' => 'Jus de bouye (baobab)', 'price' => 500],
        ['id' => 'JusDitakh', 'cat_id' => 'Boissons Traditionnelles', 'name' => 'Jus de ditakh', 'price' => 500],
        ['id' => 'Dolo', 'cat_id' => 'Boissons Traditionnelles', 'name' => 'Dolo (bière de mil)', 'price' => 300],
        // Boissons Fraîches
        ['id' => 'EauMinerale', 'cat_id' => 'Boissons Fraîches', 'name' => 'Eau minérale (plate/gazeuse)', 'price' => 500],
        ['id' => 'JusFraisPresses', 'cat_id' => 'Boissons Fraîches', 'name' => 'Jus de fruits frais pressés', 'price' => 1000],
        ['id' => 'JusIndustriel', 'cat_id' => 'Boissons Fraîches', 'name' => 'Jus Vitalait / Solani', 'price' => 500],
        ['id' => 'Soda', 'cat_id' => 'Boissons Fraîches', 'name' => 'Sodas (Coca, Fanta, Sprite)', 'price' => 700],
        ['id' => 'Biere', 'cat_id' => 'Boissons Fraîches', 'name' => 'Bière (Brakina, So.B.B, Castel)', 'price' => 800],
        // Boissons Chaudes
        ['id' => 'CafeNescafe', 'cat_id' => 'Boissons Chaudes', 'name' => 'Café Nescafé', 'price' => 300],
        ['id' => 'CafeAuLait', 'cat_id' => 'Boissons Chaudes', 'name' => 'Café au lait', 'price' => 500],
        ['id' => 'TheMenthe', 'cat_id' => 'Boissons Chaudes', 'name' => 'Thé à la menthe', 'price' => 300],
        ['id' => 'ChocolatChaud', 'cat_id' => 'Boissons Chaudes', 'name' => 'Chocolat chaud (Milo/Nescao)', 'price' => 500],
        ['id' => 'CafeEspresso', 'cat_id' => 'Boissons Chaudes', 'name' => 'Café espresso', 'price' => 500],
        ['id' => 'Cappuccino', 'cat_id' => 'Boissons Chaudes', 'name' => 'Cappuccino', 'price' => 700],
        ['id' => 'LatteMacc', 'cat_id' => 'Boissons Chaudes', 'name' => 'Latte macchiato', 'price' => 800],
        // Formules & Menu du Jour
        ['id' => 'Formule1', 'cat_id' => 'Formule Déjeuner', 'name' => 'Formule 1 : Plat + Boisson', 'price' => 3000],
        ['id' => 'Formule2', 'cat_id' => 'Formule Déjeuner', 'name' => 'Formule 2 : Plat + Dessert + Boisson', 'price' => 4000],
        ['id' => 'Formule3', 'cat_id' => 'Formule Déjeuner', 'name' => 'Formule 3 : Entrée + Plat + Boisson', 'price' => 4500],
        ['id' => 'PlatJour', 'cat_id' => 'Menu du Jour', 'name' => 'Plat du jour (riz ou tô + sauce chef)', 'price' => 2000],
        ['id' => 'GrilladeJour', 'cat_id' => 'Menu du Jour', 'name' => 'Grillade du jour', 'price' => 3000],
        ['id' => 'BoissonJour', 'cat_id' => 'Menu du Jour', 'name' => 'Boisson du jour incluse', 'price' => 0],
    ];

    public $catalogPayments = [
        ['id' => 'Espèces',    'icon' => '💵', 'name' => 'Espèces',    'desc' => 'Paiement basique en espèces'],
        ['id' => 'Hors ligne', 'icon' => '📵', 'name' => 'Hors ligne', 'desc' => 'Autre mode de paiement manuel'],
        ['id' => 'QrCode',     'icon' => '📱', 'name' => 'QrCode',     'desc' => 'Paiement via scan de QR Code'],
        ['id' => 'PayDunya',   'icon' => '📲', 'name' => 'PayDunya',   'desc' => 'Mobile Money (Orange, MTN, Moov) via PayDunya'],
    ];

    public function mount()
    {
        // Pre-select all by default for a smoother experience
        $this->selectedZones    = array_column($this->catalogZones,    'id');
        $this->selectedMenus    = array_column($this->catalogMenus,    'id');
        $this->selectedCategories = array_column($this->catalogCategories, 'id');
        $this->selectedItems    = array_column($this->catalogItems,    'id');
        $this->selectedPayments = array_column($this->catalogPayments, 'id');

        // Init table counts
        foreach ($this->catalogZones as $zone) {
            $this->zoneTablesCount[$zone['id']] = $zone['tables'];
        }
    }

    /**
     * When the restaurant type changes, rebuild the selections
     * so only menus/categories/items relevant to that type are selected.
     */
    public function updatedRestaurantType()
    {
        if (!$this->restaurantType || !isset($this->restaurantTypes[$this->restaurantType])) {
            // No type = keep all
            $this->selectedMenus      = array_column($this->catalogMenus,      'id');
            $this->selectedCategories = array_column($this->catalogCategories, 'id');
            $this->selectedItems      = array_column($this->catalogItems,      'id');
            return;
        }

        $allowedMenus = $this->restaurantTypes[$this->restaurantType]['menus'];

        if ($allowedMenus === null) {
            // "Tout afficher" — select everything
            $this->selectedMenus      = array_column($this->catalogMenus,      'id');
            $this->selectedCategories = array_column($this->catalogCategories, 'id');
            $this->selectedItems      = array_column($this->catalogItems,      'id');
            return;
        }

        // Filter menus
        $this->selectedMenus = array_values(array_filter(
            array_column($this->catalogMenus, 'id'),
            fn($id) => in_array($id, $allowedMenus)
        ));

        // Filter categories to only those whose menu_ref is in allowed menus
        $this->selectedCategories = array_values(array_filter(
            array_column($this->catalogCategories, 'id'),
            function ($id) use ($allowedMenus) {
                $cat = collect($this->catalogCategories)->firstWhere('id', $id);
                return $cat && in_array($cat['menu_ref'] ?? '', $allowedMenus);
            }
        ));

        // Filter items to only those whose category is still selected
        $this->selectedItems = array_values(array_filter(
            array_column($this->catalogItems, 'id'),
            function ($id) {
                $item = collect($this->catalogItems)->firstWhere('id', $id);
                return $item && in_array($item['cat_id'], $this->selectedCategories);
            }
        ));
    }

    public function nextStep()
    {
        if ($this->currentStep < 5) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function generateSelectedData()
    {
        $restaurantId = auth()->user()->restaurant_id;
        $branchId = auth()->user()->branch_id;
        $onboardingStep = OnboardingStep::where('branch_id', $branchId)->first();

        // 1. Generate Zones & Tables
        if (!empty($this->selectedZones)) {
            foreach ($this->catalogZones as $zoneDef) {
                if (in_array($zoneDef['id'], $this->selectedZones)) {
                    $area = Area::create([
                        'area_name' => $zoneDef['name'],
                        'branch_id' => $branchId,
                        'restaurant_id' => $restaurantId,
                    ]);

                    $tableCount = $this->zoneTablesCount[$zoneDef['id']] ?? $zoneDef['tables'];
                    for ($i = 1; $i <= $tableCount; $i++) {
                        Table::create([
                            'table_code' => substr($zoneDef['name'], 0, 1) . '-' . $i,
                            'available_status' => 'available',
                            'status' => 'active',
                            'seating_capacity' => 4,
                            'hash' => Str::random(10),
                            'area_id' => $area->id,
                            'branch_id' => $branchId,
                            'restaurant_id' => $restaurantId,
                        ]);
                    }
                }
            }
            if ($onboardingStep) {
                $onboardingStep->add_area_completed = 1;
                $onboardingStep->add_table_completed = 1;
            }
        }

        // 2, 3, 4. Menus, Categories and Items
        $createdMenus = [];
        if (!empty($this->selectedMenus)) {
            foreach ($this->catalogMenus as $menuDef) {
                if (in_array($menuDef['id'], $this->selectedMenus)) {
                    $createdMenus[$menuDef['id']] = Menu::create([
                        'menu_name' => $menuDef['name'],
                        'branch_id' => $branchId,
                        'restaurant_id' => $restaurantId,
                    ]);
                }
            }
            if ($onboardingStep) $onboardingStep->add_menu_completed = 1;
        }

        $createdCategories = [];
        if (!empty($this->selectedCategories)) {
            foreach ($this->catalogCategories as $catDef) {
                if (in_array($catDef['id'], $this->selectedCategories)) {
                    $createdCategories[$catDef['id']] = ItemCategory::create([
                        'category_name' => $catDef['name'],
                        'branch_id' => $branchId,
                        'restaurant_id' => $restaurantId,
                    ]);
                }
            }
        }

        if (!empty($this->selectedItems) && !empty($createdMenus)) {
            // Assign to first menu created as default
            $defaultMenuId = array_values($createdMenus)[0]->id;

            // Map categories to menu references
            $catToMenuRef = [];
            foreach ($this->catalogCategories as $cDef) {
                $catToMenuRef[$cDef['id']] = $cDef['menu_ref'] ?? null;
            }

            foreach ($this->catalogItems as $itemDef) {
                if (in_array($itemDef['id'], $this->selectedItems)) {
                    // Only create if its category was selected and created
                    if (isset($createdCategories[$itemDef['cat_id']])) {
                        $menuId = $defaultMenuId;
                        $menuRef = $catToMenuRef[$itemDef['cat_id']] ?? null;
                        if ($menuRef && isset($createdMenus[$menuRef])) {
                            $menuId = $createdMenus[$menuRef]->id;
                        }

                        // Resolve the image URL (per-item first, then category fallback)
                        $imageUrl = $this->itemImages[$itemDef['id']]
                            ?? $this->categoryImages[$itemDef['cat_id']]
                            ?? null;

                        // Download and store locally → returns bare filename for the image column
                        $storedImage = $imageUrl ? $this->downloadItemImage($imageUrl) : null;

                        $menuItem = MenuItem::create([
                            'item_name'        => $itemDef['name'],
                            'description'      => 'Un classique : ' . $itemDef['name'],
                            'price'            => $itemDef['price'],
                            'type'             => 'veg',
                            'is_available'     => 1,
                            'menu_id'          => $menuId,
                            'item_category_id' => $createdCategories[$itemDef['cat_id']]->id,
                            'branch_id'        => $branchId,
                            'restaurant_id'    => $restaurantId,
                            'image'            => $storedImage,
                        ]);

                        if (in_array($itemDef['cat_id'], ['Boissons'])) {
                            MenuItemVariation::create([
                                'menu_item_id' => $menuItem->id,
                                'variation' => 'Petit',
                                'price' => $itemDef['price'],
                                'restaurant_id' => $restaurantId
                            ]);
                            MenuItemVariation::create([
                                'menu_item_id' => $menuItem->id,
                                'variation' => 'Grand',
                                'price' => $itemDef['price'] + 1000,
                                'restaurant_id' => $restaurantId
                            ]);
                        }
                    }
                }
            }
            if ($onboardingStep) $onboardingStep->add_menu_items_completed = 1;
        }

        // 5. Payment Methods
        if (!empty($this->selectedPayments)) {
            // Enable the correct gateway flags based on the user's selection
            $gatewayUpdates = [];

            foreach ($this->catalogPayments as $payDef) {
                if (!in_array($payDef['id'], $this->selectedPayments)) continue;

                // Create the OfflinePaymentMethod entry
                OfflinePaymentMethod::firstOrCreate([
                    'name'          => $payDef['name'],
                    'restaurant_id' => $restaurantId,
                ], [
                    'description' => $payDef['desc'],
                    'status'      => 'active',
                ]);

                // Map catalog IDs to PaymentGatewayCredential flag columns
                match ($payDef['id']) {
                    'Espèces'    => $gatewayUpdates['is_cash_payment_enabled']    = 1,
                    'Hors ligne' => $gatewayUpdates['is_offline_payment_enabled'] = 1,
                    'QrCode'     => $gatewayUpdates['is_qr_payment_enabled']      = 1,
                    'PayDunya'   => $gatewayUpdates['paydunya_status']            = 1,
                    default      => null,
                };
            }

            // Apply all flag changes at once
            if (!empty($gatewayUpdates)) {
                $gateway = PaymentGatewayCredential::first();
                if ($gateway) {
                    $gateway->update($gatewayUpdates);
                }
            }
        }

        if ($onboardingStep) {
            $onboardingStep->save();
        }

        session()->flash('message', 'Données générées avec succès ! Vous êtes prêt.');
        $this->redirect(route('dashboard'));
    }

    /**
     * Download a remote food image and store it in storage/app/item/.
     * Returns the bare filename that goes into MenuItem.image column,
     * or null if the download fails (generation continues without image).
     */
    private function downloadItemImage(string $url): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(8)
                ->withHeaders(['User-Agent' => 'MenuPro/1.0'])
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            $ext      = 'jpeg';
            $filename = md5($url . microtime()) . '.' . $ext;
            $disk     = \Illuminate\Support\Facades\Storage::disk('local');
            $disk->put('item/' . $filename, $response->body());

            return $filename;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function render()
    {
        $onboardingSteps = OnboardingStep::where('branch_id', auth()->user()->branch_id)->first();

        return view('livewire.onboarding.onboarding-steps', [
            'onboardingSteps' => $onboardingSteps
        ]);
    }
}

