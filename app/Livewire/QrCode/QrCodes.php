<?php

namespace App\Livewire\QrCode;

use App\Helper\Files;
use App\Models\Area;
use App\Models\Branch;
use App\Models\FileStorage;
use App\Models\Restaurant;
use App\Models\Table;
use Livewire\Component;
use Livewire\WithFileUploads;

class QrCodes extends Component
{
    use WithFileUploads;

    public $areaID = null;

    // ─── Customization panel state ──────────────────────────────────────
    public bool $showCustomizer = false;

    // Colors
    public string $foreground_color = '#000000';
    public string $background_color = '#FFFFFF';
    public bool   $sync_eye_color   = true;
    public string $eye_color        = '#000000';

    // Eye Shape ('square', 'rounded', 'circle', 'leaf')
    public string $eye_shape = 'square';

    // Logo
    public bool    $show_logo             = false;
    public int     $logo_size             = 20;   // % of QR size (5..30)
    public int     $logo_padding          = 6;    // Padding in pixels (0..20)
    public         $custom_logo           = null; // Livewire temp file upload
    public ?string $existing_custom_logo  = null; // Filename of already saved custom logo

    // Label / Text
    public string $label_text  = '';
    public string $label_color = '#000000';
    public int    $label_size  = 18;
    public string $label_font  = 'noto_sans'; // 'noto_sans', 'open_sans'

    // Branding Footer (ALTES / Menupro+)
    public bool   $show_branding     = true;
    public string $branding_text     = 'Menupro+, designed by ALTES';
    public string $branding_website  = 'https://menuproplus.aladints.com/';

    // Live preview base64 data URI
    public ?string $previewData = null;

    // ─── Lifecycle ─────────────────────────────────────────────────────

    public function mount(): void
    {
        try {
            $this->loadCustomization();
        } catch (\Throwable $e) {
            // Safe fallback
        }
    }

    private function loadCustomization(): void
    {
        $restaurantId = restaurant()?->id;
        if (! $restaurantId) {
            return;
        }

        // Always query the fresh model from database to avoid stale session cache
        $restaurant = Restaurant::find($restaurantId);
        if (! $restaurant) {
            return;
        }

        $c = $restaurant->qr_customization ?? [];

        $this->foreground_color = $c['foreground_color'] ?? '#000000';
        $this->background_color = $c['background_color'] ?? '#FFFFFF';
        $this->eye_shape        = $c['eye_shape'] ?? 'square';
        $this->sync_eye_color   = !isset($c['eye_color']) || $c['eye_color'] === ($c['foreground_color'] ?? '#000000');
        $this->eye_color        = $c['eye_color'] ?? $this->foreground_color;

        $this->show_logo            = (bool) ($c['show_logo'] ?? false);
        $this->logo_size            = (int) ($c['logo_size'] ?? 20);
        $this->logo_padding         = (int) ($c['logo_padding'] ?? 6);
        $this->existing_custom_logo = $c['custom_logo'] ?? null;
        $this->custom_logo          = null;

        $this->label_text  = $c['label_text'] ?? '';
        $this->label_color = $c['label_color'] ?? $this->foreground_color;
        $this->label_size  = (int) ($c['label_size'] ?? 18);
        $this->label_font  = $c['label_font'] ?? 'noto_sans';

        $this->show_branding    = isset($c['show_branding']) ? (bool) $c['show_branding'] : true;
        $this->branding_text    = $c['branding_text'] ?? 'Menupro+, designed by ALTES';
        $this->branding_website = $c['branding_website'] ?? 'https://menuproplus.aladints.com/';
    }

    // ─── Universal Livewire Property Watcher ───────────────────────────

    public function updated($property): void
    {
        if ($property === 'sync_eye_color' && $this->sync_eye_color) {
            $this->eye_color = $this->foreground_color;
        }

        if ($property === 'foreground_color' && $this->sync_eye_color) {
            $this->eye_color = $this->foreground_color;
        }

        if ($property === 'custom_logo' && $this->custom_logo) {
            $this->show_logo = true;
        }

        $this->refreshPreview();
    }

    // ─── Explicit User Action Handlers ─────────────────────────────────

    public function setEyeShape(string $shape): void
    {
        if (in_array($shape, ['square', 'rounded', 'circle', 'leaf'])) {
            $this->eye_shape = $shape;
            $this->refreshPreview();
        }
    }

    public function setColorPreset(string $fg, string $bg): void
    {
        $this->foreground_color = $fg;
        $this->background_color = $bg;
        if ($this->sync_eye_color) {
            $this->eye_color = $fg;
        }
        $this->refreshPreview();
    }

    public function setLabelPreset(string $text): void
    {
        $this->label_text = $text;
        $this->refreshPreview();
    }

    public function clearLabelText(): void
    {
        $this->label_text = '';
        $this->refreshPreview();
    }

    public function toggleSyncEyeColor(): void
    {
        $this->sync_eye_color = !$this->sync_eye_color;
        if ($this->sync_eye_color) {
            $this->eye_color = $this->foreground_color;
        }
        $this->refreshPreview();
    }

    public function toggleShowLogo(): void
    {
        $this->show_logo = !$this->show_logo;
        $this->refreshPreview();
    }

    public function toggleShowBranding(): void
    {
        $this->show_branding = !$this->show_branding;
        $this->refreshPreview();
    }

    public function removeCustomLogo(): void
    {
        $this->custom_logo = null;
        $this->existing_custom_logo = null;
        $this->refreshPreview();
    }

    // ─── Download actions ──────────────────────────────────────────────

    public function downloadQrCode($tableCode, $branchId)
    {
        $branch = Branch::find($branchId);

        if (!$branch || (user()->branch_id && (int) user()->branch_id !== (int) $branch->id)) {
            abort(403);
        }

        $filename = 'qrcode-' . $branchId . '-' . str()->slug($tableCode, '-', (auth()->user() ? auth()->user()->locale : 'en')) . '.png';

        $file = FileStorage::where('filename', $filename)->first();

        return download_local_s3($file, 'qrcodes/' . $filename);
    }

    public function downloadBranchQrCode()
    {
        $branch = branch();

        $filename = 'qrcode-branch-' . $branch->id . '-' . $branch->restaurant->id . '.png';

        $file = FileStorage::where('filename', $filename)->first();

        return download_local_s3($file, 'qrcodes/' . $filename);
    }

    // ─── Generate actions ──────────────────────────────────────────────

    public function generateQrCode($tableId = null)
    {
        if ($tableId) {
            $table = Table::find($tableId);
        } else {
            $table = branch();
        }

        $table->generateQrCode();

        $this->redirect(route('qrcodes.index'));
    }

    // ─── Customization modal controls ─────────────────────────────────

    public function openCustomizer(): void
    {
        $this->loadCustomization();
        $this->showCustomizer = true;
        $this->refreshPreview();
    }

    public function closeCustomizer(): void
    {
        $this->showCustomizer = false;
        $this->previewData    = null;
        $this->custom_logo    = null;
    }

    public function refreshPreview(): void
    {
        $sampleUrl = route('qr.short', ['sample']);
        $options = $this->buildOptionsArray(forPreview: true);

        $branch = branch();

        try {
            $png = $branch->buildQrCodePreview($sampleUrl, $options);
            $this->previewData = 'data:image/png;base64,' . base64_encode($png);
        } catch (\Throwable $e) {
            $this->previewData = null;
        }
    }

    public function saveCustomization(): void
    {
        $this->validate([
            'foreground_color'  => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color'  => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'eye_color'         => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'eye_shape'         => ['required', 'in:square,rounded,circle,leaf'],
            'show_logo'         => ['boolean'],
            'logo_size'         => ['integer', 'min:5', 'max:30'],
            'logo_padding'      => ['integer', 'min:0', 'max:25'],
            'label_text'        => ['nullable', 'string', 'max:100'],
            'label_color'       => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'label_size'        => ['integer', 'min:10', 'max:36'],
            'label_font'        => ['required', 'in:noto_sans,open_sans'],
            'show_branding'     => ['boolean'],
            'branding_text'     => ['nullable', 'string', 'max:120'],
            'branding_website'  => ['nullable', 'string', 'max:150'],
        ]);

        $customization = [
            'foreground_color'  => $this->foreground_color,
            'background_color'  => $this->background_color,
            'eye_shape'         => $this->eye_shape,
            'eye_color'         => $this->sync_eye_color ? $this->foreground_color : $this->eye_color,
            'show_logo'         => $this->show_logo,
            'logo_size'         => $this->logo_size,
            'logo_padding'      => $this->logo_padding,
            'label_text'        => $this->label_text ?: null,
            'label_color'       => $this->label_color,
            'label_size'        => $this->label_size,
            'label_font'        => $this->label_font,
            'show_branding'     => $this->show_branding,
            'branding_text'     => $this->branding_text,
            'branding_website'  => $this->branding_website,
        ];

        // Handle custom logo upload
        if ($this->custom_logo) {
            $ext = $this->custom_logo->getClientOriginalExtension() ?: 'png';
            $filename = 'qr-custom-logo-' . restaurant()->id . '-' . time() . '.' . $ext;
            $destDir  = public_path('user-uploads/logo');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $targetPath = $destDir . '/' . $filename;
            // Use copy from real path to avoid Symfony is_uploaded_file restriction on Livewire temp files
            copy($this->custom_logo->getRealPath(), $targetPath);

            $customization['custom_logo'] = $filename;
            $this->existing_custom_logo = $filename;
        } elseif ($this->existing_custom_logo) {
            $customization['custom_logo'] = $this->existing_custom_logo;
        }

        // Persist settings directly on Restaurant model
        $restaurant = Restaurant::find(restaurant()->id);
        if ($restaurant) {
            $restaurant->qr_customization = $customization;
            $restaurant->save();
            session(['restaurant' => $restaurant]);
        }

        // Regenerate all QR codes
        $this->regenerateAllQrCodes();

        $this->showCustomizer = false;
        $this->previewData    = null;
        $this->custom_logo    = null;

        session()->flash('success', __('modules.table.qrCustomizationSaved'));
        $this->redirect(route('qrcodes.index'));
    }

    public function resetCustomization(): void
    {
        $this->foreground_color = '#000000';
        $this->background_color = '#FFFFFF';
        $this->sync_eye_color   = true;
        $this->eye_color        = '#000000';
        $this->eye_shape        = 'square';
        $this->show_logo        = false;
        $this->logo_size        = 20;
        $this->logo_padding     = 6;
        $this->custom_logo      = null;
        $this->existing_custom_logo = null;
        $this->label_text       = '';
        $this->label_color      = '#000000';
        $this->label_size       = 18;
        $this->label_font       = 'noto_sans';
        $this->show_branding    = true;
        $this->branding_text    = 'Menupro+, designed by ALTES';
        $this->branding_website = 'https://menuproplus.aladints.com/';

        $this->refreshPreview();
    }

    private function buildOptionsArray(bool $forPreview = false): array
    {
        $options = [
            'foreground_color'  => $this->foreground_color,
            'background_color'  => $this->background_color,
            'eye_shape'         => $this->eye_shape,
            'eye_color'         => $this->sync_eye_color ? $this->foreground_color : $this->eye_color,
            'label_text'        => $this->label_text ?: null,
            'label_color'       => $this->label_color,
            'label_size'        => $this->label_size,
            'label_font'        => $this->label_font,
            'logo_size'         => $this->logo_size,
            'logo_padding'      => $this->logo_padding,
            'show_branding'     => $this->show_branding,
            'branding_text'     => $this->branding_text,
            'branding_website'  => $this->branding_website,
            'altes_logo_path'   => public_path('img/altes-logo.png'),
        ];

        // Resolve logo path for preview or generation
        if ($this->show_logo) {
            $logoPath = null;

            if ($this->custom_logo) {
                // Livewire temporary file upload
                try {
                    $logoPath = $this->custom_logo->getRealPath();
                } catch (\Throwable $e) {
                    $logoPath = null;
                }
            } elseif ($this->existing_custom_logo) {
                $p = public_path('user-uploads/logo/' . $this->existing_custom_logo);
                if (file_exists($p)) {
                    $logoPath = $p;
                }
            } elseif (restaurant()?->logo) {
                $p = public_path('user-uploads/logo/' . restaurant()->logo);
                if (file_exists($p)) {
                    $logoPath = $p;
                }
            }

            if ($logoPath && file_exists($logoPath)) {
                $options['logo_path'] = $logoPath;
            }
        }

        return $options;
    }

    private function regenerateAllQrCodes(): void
    {
        $branch = branch();
        if (! $branch) {
            $branch = Branch::withoutGlobalScopes()->where('restaurant_id', restaurant()->id)->first();
        }

        if ($branch) {
            $branch->refresh();
            if ($branch->restaurant) {
                $branch->restaurant->refresh();
            }

            // Branch QR
            $branch->generateQrCode();

            // Tables QRs (fetch all tables regardless of area scoping)
            $tables = Table::withoutGlobalScopes()->where('branch_id', $branch->id)->get();

            foreach ($tables as $table) {
                $table->generateQrCode();
            }
        }
    }

    // ─── Render ───────────────────────────────────────────────────────

    public function render()
    {
        $query = Area::with('tables');

        if (!is_null($this->areaID)) {
            $query = $query->where('id', $this->areaID);
        }

        $query = $query->get();

        return view('livewire.qr-code.qr-codes', [
            'tables' => $query,
            'areas'  => Area::get(),
        ]);
    }
}
