<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasRestaurant;
use App\Traits\GeneratesQrCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasBranch;
use App\Models\OrderType;
use Spatie\LaravelPackageTools\Concerns\Package\HasServiceProviders;

class Branch extends BaseModel
{
    use HasFactory;
    use GeneratesQrCode;
    use HasRestaurant;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'restaurant_id',
        'is_active',
        'unique_hash',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function getQrCodeFileName(): string
    {
        return 'qrcode-branch-' . $this->id . '-' . $this->restaurant->id . '.png';
    }

    public function getRestaurantId(): int
    {
        return $this->restaurant_id;
    }

    public function getQrOptions(): array
    {
        $restaurant = $this->restaurant;
        $customization = $restaurant?->qr_customization ?? [];

        $options = [];

        if (!empty($customization['foreground_color'])) {
            $options['foreground_color'] = $customization['foreground_color'];
        }
        if (!empty($customization['background_color'])) {
            $options['background_color'] = $customization['background_color'];
        }
        if (!empty($customization['eye_color'])) {
            $options['eye_color'] = $customization['eye_color'];
        }
        if (!empty($customization['eye_shape'])) {
            $options['eye_shape'] = $customization['eye_shape'];
        }
        if (!empty($customization['label_text'])) {
            $options['label_text'] = $customization['label_text'];
        }
        if (!empty($customization['label_color'])) {
            $options['label_color'] = $customization['label_color'];
        }
        if (!empty($customization['label_size'])) {
            $options['label_size'] = (int) $customization['label_size'];
        }
        if (!empty($customization['label_font'])) {
            $options['label_font'] = $customization['label_font'];
        }
        if (!empty($customization['logo_size'])) {
            $options['logo_size'] = (int) $customization['logo_size'];
        }
        if (isset($customization['logo_padding'])) {
            $options['logo_padding'] = (int) $customization['logo_padding'];
        }

        // Resolve logo path
        if (!empty($customization['show_logo']) && $customization['show_logo']) {
            $logoPath = null;
            if (!empty($customization['custom_logo'])) {
                $customPath = public_path('user-uploads/logo/' . $customization['custom_logo']);
                if (file_exists($customPath)) {
                    $logoPath = $customPath;
                }
            }
            if (!$logoPath && $restaurant?->logo) {
                $restLogoPath = public_path('user-uploads/logo/' . $restaurant->logo);
                if (file_exists($restLogoPath)) {
                    $logoPath = $restLogoPath;
                }
            }
            if ($logoPath) {
                $options['logo_path'] = $logoPath;
            }
        }

        return $options;
    }

    public function generateQrCode()
    {
        // Generate a new unique_hash to invalidate old QR code links
        $this->generateUniqueHash();
        $this->save();

        // Get shortened secure URL
        $shortUrl = \App\Services\QrShortLinkService::getShortUrlForBranch($this);

        $this->createQrCode(
            $shortUrl,
            null,
            $this->getQrOptions()
        );
    }

    public function deliverySetting()
    {
        return $this->hasOne(BranchDeliverySetting::class, 'branch_id');
    }

    public function deliveryFeeTiers()
    {
        return $this->hasMany(DeliveryFeeTier::class);
    }

    public function qRCodeUrl(): Attribute
    {
        return Attribute::get(fn(): string => asset_url_local_s3('qrcodes/' . $this->getQrCodeFileName()));
    }

    public function printerSettings(): HasMany
    {
        return $this->hasMany(Printer::class);
    }

    public function kotPlaces(): HasMany
    {
        return $this->hasMany(KotPlace::class);
    }

    public function orderPlaces(): HasMany
    {
        return $this->hasMany(MultipleOrder::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function kotSetting(): HasOne
    {
        return $this->hasOne(KotSetting::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class)->withoutGlobalScopes();
    }

    public function modifierGroups()
    {
        return $this->hasMany(ModifierGroup::class)->withoutGlobalScopes();
    }

    public function itemCategories()
    {
        return $this->hasMany(ItemCategory::class)->withoutGlobalScopes();
    }

    public function orderTypes()
    {
        return $this->hasMany(OrderType::class)->withoutGlobalScopes();
    }

    public function generateKotSetting()
    {
        $this->kotSetting()->create([
            'branch_id' => $this->id,
            'default_status' => 'pending',
            'enable_item_level_status' => true,
        ]);
    }

    /**
     * Generate a unique hash for this branch
     */
    public function generateUniqueHash()
    {
        $baseString = $this->id . '_' . ($this->name ?? 'branch') . '_' . time();
        $this->unique_hash = substr(hash('sha256', $baseString), 0, 20);
    }
}
