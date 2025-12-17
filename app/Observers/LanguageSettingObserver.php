<?php

namespace App\Observers;

use App\Models\Contact;
use App\Models\FrontDetail;
use App\Models\FrontFaq;
use App\Models\FrontFeature;
use App\Models\FrontReviewSetting;
use App\Models\LanguageSetting;

class LanguageSettingObserver
{

    public function saved(LanguageSetting $languageSetting)
    {
        if ($languageSetting->active == 1) {
            if (FrontDetail::where('language_setting_id', $languageSetting->id)->first()) {
                return true;
            }

            $this->detail($languageSetting->id);
            $this->features($languageSetting->id);
            $this->review($languageSetting->id);
            $this->frontFaq($languageSetting->id);
        }
    }

    public function detail($languageId)
{
    $trFrontDetail = new FrontDetail();
    $trFrontDetail->language_setting_id = $languageId;

    $trFrontDetail->header_title = 'Logiciel de gestion pour restaurant, simple et efficace !';
    $trFrontDetail->header_description = 'Gérez facilement vos commandes, menus et tables en un seul endroit. Gagnez du temps, réduisez les erreurs et développez votre restaurant plus rapidement.';

    $trFrontDetail->feature_with_image_heading = 'Prenez le contrôle de votre restaurant à Ouaga';
    $trFrontDetail->feature_with_icon_heading = 'Des fonctionnalités puissantes pour booster votre restaurant';

    $trFrontDetail->review_heading = 'Ce que disent les restaurateurs au Burkina Faso';

    $trFrontDetail->price_heading = 'Des prix simples et transparents';
    $trFrontDetail->price_description = 'Tout ce dont vous avez besoin pour gérer votre restaurant avec un seul forfait abordable.';

    $trFrontDetail->faq_heading = 'Vos questions, nos réponses';
    $trFrontDetail->faq_description = 'Réponses aux questions les plus fréquentes des restaurateurs burkinabè.';

    $trFrontDetail->contact_heading = 'Contact';
    $trFrontDetail->footer_copyright_text = 'Copyright © Aladin Technologies Solutions 2025. Tous droits réservés';
    $trFrontDetail->save();
}

public function features($languageId)
{
    $features = [
        [
            'language_setting_id' => $languageId,
            'title' => 'Simplifiez la gestion des commandes',
            'description' => 'Ne perdez plus jamais une commande. Toutes vos commandes clients - sur place ou à emporter - sont organisées et accessibles en un seul endroit. Accélérez le service et gardez votre cuisine fluide.',
            'type' => 'image',
        ],
        [
            'language_setting_id' => $languageId,
            'title' => 'Optimisez les réservations de tables',
            'description' => "Maximisez l'efficacité de votre salle avec le suivi en temps réel des tables et des réservations. Réduisez les temps d'attente et assurez-vous qu'aucune table ne reste vide pendant les heures de pointe, améliorant l'expérience client et la rotation.",
            'type' => 'image',
        ],
        [
            'language_setting_id' => $languageId,
            'title' => 'Gestion de menu sans effort',
            'description' => 'Ajoutez, modifiez ou supprimez facilement des plats de votre menu en déplacement. Mettez en avant les spécialités, mettez à jour les prix et gardez tout synchronisé sur toutes les plateformes, pour que votre personnel et vos clients voient toujours les dernières offres.',
            'type' => 'image',
        ],
    ];

    FrontFeature::insert($features);

    $features = [
        [
            'title' => 'Menu QR Code',
            'language_setting_id' => $languageId,
            'description' => 'Commande sans contact, simple et rapide',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg " width="16" height="16" fill="currentColor" class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16"><path d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5M.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5M4 4h1v1H4z" /><path d="M7 2H2v5h5zM3 3h3v3H3zm2 8H4v1h1z" /><path d="M7 9H2v5h5zm-4 1h3v3H3zm8-6h1v1h-1z" /><path d="M9 2h5v5H9zm1 1v3h3V3zM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8zm2 2H9V9h1zm4 2h-1v1h-2v1h3zm-4 2v-1H8v1z" /><path d="M12 9h2V8h-2z" /></svg>',
            'type' => 'icon'
        ],
        [
            'title' => 'Intégration des paiements',
            'language_setting_id' => $languageId,
            'description' => 'Paiements rapides, sécurisés et flexibles via Mobile Money et cartes',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg " width="16" height="16" fill="currentColor"
                    class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                    <path
                        d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6.226 5.385c-.584 0-.937.164-.937.593 0 .468.607.674 1.36.93 1.228.415 2.844.963 2.851 2.993C11.5 11.868 9.924 13 7.63 13a7.7 7.7 0 0 1-3.009-.626V9.758c.926.506 2.095.88 3.01.88.617 0 1.058-.165 1.058-.671 0-.518-.658-.755-1.453-1.041C6.026 8.49 4.5 7.94 4.5 6.11 4.5 4.165 5.988 3 8.226 3a7.3 7.3 0 0 1 2.734.505v2.583c-.838-.45-1.896-.703-2.734-.703" />
                </svg>',
            'type' => 'icon'
        ],
        [
            'title' => 'Gestion du personnel',
            'language_setting_id' => $languageId,
            'description' => 'Connexion séparée pour chaque rôle du personnel avec différentes permissions.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg " width="16" height="16" fill="currentColor"
                    class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                    <path
                        d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                </svg>',
            'type' => 'icon'
        ],
        [
            'title' => 'Caisse enregistreuse (POS)',
            'language_setting_id' => $languageId,
            'description' => 'Intégration complète du système de caisse',
            'icon' => '<svg class="size-6 transition duration-75 text-skin-base dark:text-skin-base" fill="currentColor"
                    viewBox="0 -0.5 25 25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg ">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path fill-rule="evenodd"
                            d="M16,6 L20,6 C21.1045695,6 22,6.8954305 22,8 L22,16 C22,17.1045695 21.1045695,18 20,18 L16,18 L16,19.9411765 C16,21.0658573 15.1177541,22 14,22 L4,22 C2.88224586,22 2,21.0658573 2,19.9411765 L2,4.05882353 C2,2.93414267 2.88224586,2 4,2 L14,2 C15.1177541,2 16,2.93414267 16,4.05882353 L16,6 Z M20,11 L16,11 L16,16 L20,16 L20,11 Z M14,19.9411765 L14,4.05882353 C14,4.01396021 13.9868154,4 14,4 L4,4 C4.01318464,4 4,4.01396021 4,4.05882353 L4,19.9411765 C4,19.9860398 4.01318464,20 4,20 L14,20 C13.9868154,20 14,19.9860398 14,19.9411765 Z M5,19 L5,17 L7,17 L7,19 L5,19 Z M8,19 L8,17 L10,17 L10,19 L8,19 Z M11,19 L11,17 L13,17 L13,19 L11,19 Z M5,16 L5,14 L7,14 L7,16 L5,16 Z M8,16 L8,14 L10,14 L10,16 L8,16 Z M11,16 L11,14 L13,14 L13,16 L11,16 Z M13,5 L13,13 L5,13 L5,5 L13,5 Z M7,7 L7,11 L11,11 L11,7 L7,7 Z M20,9 L20,8 L16,8 L16,9 L20,9 Z">
                        </path>
                    </g>
                </svg>',
            'type' => 'icon'],
        [
            'title' => 'Plans de salle personnalisés',
            'language_setting_id' => $languageId,
            'description' => 'Concevez la disposition de votre restaurant.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg " width="16" height="16" fill="currentColor"
                    class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                    <path
                        d="M8.235 1.559a.5.5 0 0 0-.47 0l-7.5 4a.5.5 0 0 0 0 .882L3.188 8 .264 9.559a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882L12.813 8l2.922-1.559a.5.5 0 0 0 0-.882zm3.515 7.008L14.438 10 8 13.433 1.562 10 4.25 8.567l3.515 1.874a.5.5 0 0 0 .47 0zM8 9.433 1.562 6 8 2.567 14.438 6z" />
                </svg>',
            'type' => 'icon'],
        [
            'title' => 'Tickets de cuisine (KOT)',
            'language_setting_id' => $languageId,
            'description' => "Flux de travail en cuisine efficace.",
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg " width="16" height="16" fill="currentColor"
                    class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                    <path
                        d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5M11.5 4a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z" />
                    <path
                        d="M2.354.646a.5.5 0 0 0-.801.13l-.5 1A.5.5 0 0 0 1 2v13H.5a.5.5 0 0 0 0 1h15a.5.5 0 0 0 0-1H15V2a.5.5 0 0 0-.053-.224l-.5-1a.5.5 0 0 0-.8-.13L13 1.293l-.646-.647a.5.5 0 0 0-.708 0L11 1.293l-.646-.647a.5.5 0 0 0-.708 0L9 1.293 8.354.646a.5.5 0 0 0-.708 0L7 1.293 6.354.646a.5.5 0 0 0-.708 0L5 1.293 4.354.646a.5.5 0 0 0-.708 0L3 1.293zm-.217 1.198.51.51a.5.5 0 0 0 .707 0L4 1.707l.646.647a.5.5 0 0 0 .708 0L6 1.707l.646.647a.5.5 0 0 0 .708 0L8 1.707l.646.647a.5.5 0 0 0 .708 0L10 1.707l.646.647a.5.5 0 0 0 .708 0L12 1.707l.646.647a.5.5 0 0 0 .708 0l.509-.51.137.274V15H2V2.118z" />
                </svg>',
            'type' => 'icon'],
        [
            'title' => 'Impression des factures',
            'language_setting_id' => $languageId,
            'description' => 'Facturation rapide et précise.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg " width="16" height="16" fill="currentColor"
                    class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                    <path
                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                </svg>',
            'type' => 'icon'
        ],
        [
            'title' => 'Rapports et statistiques',
            'language_setting_id' => $languageId,
            'description' => 'Des décisions basées sur les données.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg " width="16" height="16" fill="currentColor"
                    class="bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                </svg>',
            'type' => 'icon'
        ],

    ];

    FrontFeature::insert($features);
}

public function review($languageId)
{
    $reviews = [
        [
            'reviewer_name' => 'Jean-Baptiste Sawadogo',
            'language_setting_id' => $languageId,
            'reviewer_designation' => 'Propriétaire du Bistro de Gounghin',
            'reviews' => '"Cela a complètement transformé notre façon de travailler. Gérer les commandes, les tables et le personnel depuis une seule plateforme a réduit notre charge de travail et tout fonctionne plus efficacement."',
        ],
        [
            'reviewer_name' => 'Mariam Ouédraogo',
            'language_setting_id' => $languageId,
            'reviewer_designation' => 'Gérante du Grill du Lac',
            'reviews' => '"Le menu QR Code et l\'intégration des paiements ont fait une grande différence pour nous, surtout après la pandémie. Les clients apprécient la facilité et nous avons une rotation des tables plus rapide."'
        ],
        [
            'reviewer_name' => 'Paul Kaboré',
            'language_setting_id' => $languageId,
            'reviewer_designation' => 'Propriétaire du Downtown Eats',
            'reviews' => '"Nous pouvons suivre chaque commande en temps réel, maintenir notre menu à jour et gérer rapidement les paiements. C\'est comme avoir une paire de mains supplémentaires dans le restaurant."',
        ],

    ];

    FrontReviewSetting::insert($reviews);
}

public function frontFaq($languageId)
{
    $client = [
        [
            'question' => 'Comment puis-je contacter le support client ?',
            'language_setting_id' => $languageId,
            'answer' => 'Notre équipe de support dédiée est disponible par email pour vous assister avec toutes vos questions ou problèmes techniques.'
        ],
        [
            'question' => 'Quels modes de paiement acceptez-vous au Burkina ?',
            'language_setting_id' => $languageId,
            'answer' => 'Nous acceptons Mobile Money (Orange Money, Moov Money), cartes bancaires et espèces.'
        ],
        [
            'question' => 'Puis-je utiliser ce système pour mon restaurant à Ouagadougou ?',
            'language_setting_id' => $languageId,
            'answer' => 'Oui, notre système est parfaitement adapté aux restaurants de Ouagadougou et fonctionne avec les paiements locaux.'
        ],
        [
            'question' => 'Y a-t-il une version d\'essai disponible ?',
            'language_setting_id' => $languageId,
            'answer' => 'Oui, nous offrons une période d\'essai gratuite de 14 jours pour tous les restaurants au Burkina Faso.'
        ],
        [
            'question' => 'Le système fonctionne-t-il sans connexion internet ?',
            'language_setting_id' => $languageId,
            'answer' => 'Une connexion internet est nécessaire, mais le système fonctionne même avec une connexion 3G faible.'
        ],
        [
            'question' => 'Proposez-vous une formation pour mon équipe ?',
            'language_setting_id' => $languageId,
            'answer' => 'Oui, nous fournissons une formation complète en français pour vous et votre équipe à Ouagadougou.'
        ],
    ];

    FrontFaq::insert($client);
}

public function contact($languageId)
{ 
    Contact::insert([
        'language_setting_id' => $languageId,
        'contact_company' => 'ALADIN Technologies Solutions',
        'address' => 'Ouaga 2000, Ouagadougou, Burkina Faso',
        'email' => 'infos@aladints.com',
        'phone' => '+226 76 66 66 66',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

}
