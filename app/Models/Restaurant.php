<?php

namespace App\Models;

use App\Traits\FaviconTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Billable;
use App\Models\BaseModel;

class Restaurant extends BaseModel
{
    use HasFactory, Billable;

    protected $guarded = ['id'];

    const FAVICON_BASE_PATH_RESTAURANT = 'favicons/restaurant/';

    const ABOUT_US_DEFAULT_TEXT = '<p class="text-lg text-gray-600 mb-6">
          Bienvenue chez nous, là où la bonne cuisine et les bons moments se rencontrent ! Nous sommes un restaurant familial, ancré dans notre quartier, et nous adorons rassembler les gens autour de plats savoureux et d’instants inoubliables. Que vous veniez pour un petit creux, un dîner en famille ou une célébration, notre priorité est de rendre votre passage mémorable.
        </p>
        <p class="text-lg text-gray-600 mb-6">
          Notre carte regorge de plats préparés avec des ingrédients frais et de qualité, parce que nous croyons que la cuisine doit faire plaisir autant qu’elle nourrit. Entre nos spécialités phares et nos suggestions saisonnières, il y a toujours quelque chose pour éveiller vos papilles.
        </p>
        <p class="text-lg text-gray-600 mb-6">
          Mais nous ne sommes pas qu’un restaurant — nous sommes une communauté. Nous adorons retrouver nos habitués et accueillir les nouveaux visages. Notre équipe est chaleureuse et souriante, et elle met tout en œuvre pour que chaque visite vous donne l’impression d’être à la maison.
        </p>
        <p class="text-lg text-gray-600">
          Alors entrez, installez-vous, et laissez-nous nous occuper du reste. On a hâte de partager notre passion de la bonne cuisine avec vous !
        </p>
        <p class="text-lg text-gray-800 font-semibold mt-6">À très bientôt ! 🍽️✨</p>';

    protected $appends = [
        'logo_url',
    ];

    public function getFaviconBasePath(): string
    {
        return self::FAVICON_BASE_PATH_RESTAURANT . $this->hash . '/';
    }

    protected $casts = [
        'license_expire_on' => 'datetime',
        'trial_expire_on' => 'datetime',
        'license_updated_at' => 'datetime',
        'subscription_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'custom_delivery_options' => 'array',
        'is_active' => 'boolean',
        'enable_admin_reservation' => 'boolean',
        'enable_customer_reservation' => 'boolean',
        'restrict_qr_order_by_location' => 'boolean',
    ];

    public function logoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->logo ? asset_url_local_s3('logo/' . $this->logo) : global_setting()->logoUrl;
        });
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withoutGlobalScopes();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class)->withoutGlobalScopes();
    }

    public function paymentGateways(): HasOne
    {
        return $this->hasOne(PaymentGatewayCredential::class)->withoutGlobalScopes();
    }

    public function restaurantPayment(): HasMany
    {
        return $this->hasMany(RestaurantPayment::class)->where('status', 'paid  ')->orderByDesc('id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function currentInvoice(): HasOne
    {
        return $this->hasOne(GlobalInvoice::class)->latest();
    }

    public static function restaurantAdmin($restaurant)
    {
        return $restaurant->users()->orderBy('id')->first();
    }

    public function receiptSetting(): HasOne
    {
        return $this->hasOne(ReceiptSetting::class);
    }

    public function printerSettings(): HasMany
    {
        return $this->hasMany(Printer::class);
    }

    public function predefinedAmounts(): HasMany
    {
        return $this->hasMany(PredefinedAmount::class);
    }

    public function kotPlaces(): HasMany
    {
        return $this->hasMany(KotPlace::class);
    }

    public function orderPlaces(): HasMany
    {
        return $this->hasMany(MultipleOrder::class);
    }

    public function cartHeaderSetting(): HasOne
    {
        return $this->hasOne(CartHeaderSetting::class);
    }

    /**
     * Get URL for Android Chrome 192x192 favicon
     * Returns restaurant's custom favicon if available, otherwise falls back to global setting
     */
    public function uploadFavIconAndroidChrome192Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use restaurant's custom favicon if exists, otherwise use global setting
            return $this->upload_fav_icon_android_chrome_192
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_fav_icon_android_chrome_192)
                : global_setting()->upload_fav_icon_android_chrome_192_url;
        });
    }

    /**
     * Get URL for Android Chrome 512x512 favicon
     * Returns restaurant's custom favicon if available, otherwise falls back to global setting
     */
    public function uploadFavIconAndroidChrome512Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use restaurant's custom favicon if exists, otherwise use global setting
            return $this->upload_fav_icon_android_chrome_512
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_fav_icon_android_chrome_512)
                : global_setting()->upload_fav_icon_android_chrome_512_url;
        });
    }

    /**
     * Get URL for Apple Touch Icon (180x180)
     * Returns restaurant's custom icon if available, otherwise falls back to global setting
     */
    public function uploadFavIconAppleTouchIconUrl(): Attribute
    {
        return Attribute::get(function (): string {
            // Use restaurant's custom icon if exists, otherwise use global setting
            return $this->upload_fav_icon_apple_touch_icon
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_fav_icon_apple_touch_icon)
                : global_setting()->upload_fav_icon_apple_touch_icon_url;
        });
    }

    /**
     * Get URL for 16x16 favicon
     * Returns restaurant's custom favicon if available, otherwise falls back to global setting
     */
    public function uploadFavIcon16Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use restaurant's custom favicon if exists, otherwise use global setting
            return $this->upload_favicon_16
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_favicon_16)
                : global_setting()->upload_fav_icon_16_url;
        });
    }

    /**
     * Get URL for 32x32 favicon
     * Returns restaurant's custom favicon if available, otherwise falls back to global setting
     */
    public function uploadFavIcon32Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use restaurant's custom favicon if exists, otherwise use global setting
            return $this->upload_favicon_32
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_favicon_32)
                : global_setting()->upload_fav_icon_32_url;
        });
    }

    /**
     * Get URL for main favicon.ico file
     * Returns restaurant's custom favicon if available, otherwise falls back to global setting
     */
    public function faviconUrl(): Attribute
    {
        return Attribute::get(function (): string {
            // Use restaurant's custom favicon if exists, otherwise use global setting
            return $this->favicon
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->favicon)
                : global_setting()->favicon_url;
        });
    }

    /**
     * Get URL for webmanifest file (used for PWA support)
     * Returns restaurant's custom webmanifest if available, otherwise falls back to global setting
     */
    public function webmanifestUrl(): Attribute
    {
        return Attribute::get(function (): string {
            // Use restaurant's custom webmanifest if exists, otherwise use global setting
            return $this->webmanifest
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->webmanifest)
                : global_setting()->webmanifest_url;
        });
    }
}
