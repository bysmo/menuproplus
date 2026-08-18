<?php

namespace App\Traits;

use App\Helper\Files;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;

trait GeneratesQrCode
{
    /**
     * Parse a hex color string (#RRGGBB or RRGGBB) into a Color object.
     */
    private function parseColor(string $hex, int $alpha = 0): Color
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (strlen($hex) !== 6) {
            $hex = '000000';
        }

        return new Color(
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
            $alpha
        );
    }

    /**
     * Build and apply custom eye shapes (finder patterns) on GD image.
     */
    private function applyCustomEyes($gdImage, $matrix, string $eyeShape, string $eyeColorHex, string $bgColorHex): void
    {
        if (!extension_loaded('gd') || !$gdImage) {
            return;
        }

        $count = $matrix->getBlockCount();
        $innerSize = $matrix->getInnerSize();
        $bSize = (int) round($innerSize / $count);
        $margin = $matrix->getMarginLeft();

        $bg = $this->parseColor($bgColorHex);
        $eye = $this->parseColor($eyeColorHex);

        $bgGd = imagecolorallocate($gdImage, $bg->getRed(), $bg->getGreen(), $bg->getBlue());
        $eyeGd = imagecolorallocate($gdImage, $eye->getRed(), $eye->getGreen(), $eye->getBlue());

        $eyePositions = [
            [0, 0],              // Top-Left
            [$count - 7, 0],      // Top-Right
            [0, $count - 7],      // Bottom-Left
        ];

        foreach ($eyePositions as $pos) {
            $col = $pos[0];
            $row = $pos[1];

            $x0 = $margin + $col * $bSize;
            $y0 = $margin + $row * $bSize;
            $eyePx = 7 * $bSize;
            $x1 = $x0 + $eyePx - 1;
            $y1 = $y0 + $eyePx - 1;

            // Clear the 7x7 area with background color
            imagefilledrectangle($gdImage, $x0, $y0, $x1, $y1, $bgGd);

            // Dimensions
            $gapX0 = $x0 + $bSize;
            $gapY0 = $y0 + $bSize;
            $gapX1 = $x1 - $bSize;
            $gapY1 = $y1 - $bSize;
            $gapPx = 5 * $bSize;

            $dotX0 = $x0 + 2 * $bSize;
            $dotY0 = $y0 + 2 * $bSize;
            $dotX1 = $x1 - 2 * $bSize;
            $dotY1 = $y1 - 2 * $bSize;
            $dotPx = 3 * $bSize;

            if ($eyeShape === 'circle') {
                imagefilledellipse($gdImage, (int)($x0 + $eyePx / 2), (int)($y0 + $eyePx / 2), $eyePx, $eyePx, $eyeGd);
                imagefilledellipse($gdImage, (int)($x0 + $eyePx / 2), (int)($y0 + $eyePx / 2), $gapPx, $gapPx, $bgGd);
                imagefilledellipse($gdImage, (int)($x0 + $eyePx / 2), (int)($y0 + $eyePx / 2), $dotPx, $dotPx, $eyeGd);
            } elseif ($eyeShape === 'rounded') {
                $this->drawFilledRoundedRect($gdImage, $x0, $y0, $x1, $y1, (int)($bSize * 1.8), $eyeGd);
                $this->drawFilledRoundedRect($gdImage, $gapX0, $gapY0, $gapX1, $gapY1, (int)($bSize * 1.2), $bgGd);
                $this->drawFilledRoundedRect($gdImage, $dotX0, $dotY0, $dotX1, $dotY1, (int)($bSize * 0.8), $eyeGd);
            } elseif ($eyeShape === 'leaf') {
                $this->drawFilledLeafRect($gdImage, $x0, $y0, $x1, $y1, (int)($bSize * 2.2), $eyeGd);
                $this->drawFilledLeafRect($gdImage, $gapX0, $gapY0, $gapX1, $gapY1, (int)($bSize * 1.5), $bgGd);
                $this->drawFilledLeafRect($gdImage, $dotX0, $dotY0, $dotX1, $dotY1, (int)($bSize * 1.0), $eyeGd);
            } else {
                imagefilledrectangle($gdImage, $x0, $y0, $x1, $y1, $eyeGd);
                imagefilledrectangle($gdImage, $gapX0, $gapY0, $gapX1, $gapY1, $bgGd);
                imagefilledrectangle($gdImage, $dotX0, $dotY0, $dotX1, $dotY1, $eyeGd);
            }
        }
    }

    /**
     * Overlay logo at the center of QR with customizable padding and alpha transparency.
     */
    private function applyCustomLogoWithPadding($gdImage, $matrix, string $logoPath, int $logoSizePct, int $logoPadding, string $bgColorHex): void
    {
        if (!extension_loaded('gd') || !$gdImage || !file_exists($logoPath)) {
            return;
        }

        $rawLogo = @file_get_contents($logoPath);
        if (!$rawLogo) {
            return;
        }

        $srcLogo = @imagecreatefromstring($rawLogo);
        if (!$srcLogo) {
            return;
        }

        $srcW = imagesx($srcLogo);
        $srcH = imagesy($srcLogo);

        if ($srcW <= 0 || $srcH <= 0) {
            imagedestroy($srcLogo);
            return;
        }

        $qrOuterSize = $matrix->getOuterSize();
        $logoSizePct = min(30, max(5, $logoSizePct));
        $logoPadding = max(0, min(25, $logoPadding));

        // Calculate proportional dimensions
        $logoW = (int) round($qrOuterSize * $logoSizePct / 100);
        $logoH = (int) round($srcH * ($logoW / $srcW));

        $boxW = $logoW + $logoPadding * 2;
        $boxH = $logoH + $logoPadding * 2;

        $centerX = (int) round($qrOuterSize / 2);
        $centerY = (int) round($qrOuterSize / 2);

        $boxX0 = (int) round($centerX - $boxW / 2);
        $boxY0 = (int) round($centerY - $boxH / 2);
        $boxX1 = $boxX0 + $boxW - 1;
        $boxY1 = $boxY0 + $boxH - 1;

        $bg = $this->parseColor($bgColorHex);
        $bgGd = imagecolorallocate($gdImage, $bg->getRed(), $bg->getGreen(), $bg->getBlue());

        // Clear and draw background separation box around logo (with slight rounded corners)
        if ($logoPadding > 0) {
            $this->drawFilledRoundedRect($gdImage, $boxX0, $boxY0, $boxX1, $boxY1, min(6, (int)($logoPadding * 0.8)), $bgGd);
        } else {
            imagefilledrectangle($gdImage, $boxX0, $boxY0, $boxX1, $boxY1, $bgGd);
        }

        // Resample logo with full alpha blending
        imagealphablending($gdImage, true);
        imagesavealpha($gdImage, true);
        imagecopyresampled(
            $gdImage,
            $srcLogo,
            $boxX0 + $logoPadding,
            $boxY0 + $logoPadding,
            0, 0,
            $logoW, $logoH,
            $srcW, $srcH
        );

        imagedestroy($srcLogo);
    }

    /**
     * Draw a filled rectangle with 4 rounded corners using GD.
     */
    private function drawFilledRoundedRect($img, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        $radius = max(1, min($radius, (int)(($x2 - $x1) / 2), (int)(($y2 - $y1) / 2)));
        imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($img, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
        imagefilledellipse($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    /**
     * Draw a filled rectangle with 2 opposite rounded corners (leaf style) using GD.
     */
    private function drawFilledLeafRect($img, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        $radius = max(1, min($radius, (int)(($x2 - $x1) / 2), (int)(($y2 - $y1) / 2)));
        imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color);
        imagefilledellipse($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    /**
     * Build a QR code with the given URL and options.
     *
     * @param string      $qrUrl    The URL to encode
     * @param string|null $label    Optional label text shown below the QR
     * @param array       $options  Customization options
     */
    public function createQrCode(string $qrUrl, ?string $label = null, array $options = [])
    {
        $fileName = $this->getQrCodeFileName();
        $filePath = public_path(Files::UPLOAD_FOLDER . '/qrcodes/' . $fileName);

        $pngData = $this->generateQrPngData($qrUrl, $label, $options);

        Files::createDirectoryIfNotExist('qrcodes');
        file_put_contents($filePath, $pngData);

        Files::fileStore(
            new File($filePath),
            'qrcodes',
            $fileName,
            uploaded: false,
            restaurantId: $this->getRestaurantId()
        );

        // Move file to cloud storage if configured
        if (config('filesystems.default') !== 'local') {
            $contents = FileFacade::get($filePath);
            Storage::disk(config('filesystems.default'))->put('qrcodes/' . $fileName, $contents);
            unlink($filePath);
        }
    }

    /**
     * Build a QR code in memory and return raw PNG bytes (for preview and saving).
     */
    public function buildQrCodePreview(string $qrUrl, array $options = []): string
    {
        return $this->generateQrPngData($qrUrl, null, $options);
    }

    /**
     * Internal unified method to build the QR code image with all styling options.
     */
    private function generateQrPngData(string $qrUrl, ?string $defaultLabel = null, array $options = []): string
    {
        $fgHex = $options['foreground_color'] ?? '#000000';
        $bgHex = $options['background_color'] ?? '#FFFFFF';
        $eyeColorHex = $options['eye_color'] ?? $fgHex;
        $eyeShape = $options['eye_shape'] ?? 'square';

        $foreground = $this->parseColor($fgHex);
        $background = $this->parseColor($bgHex);

        $size = (int) ($options['size'] ?? 300);
        $margin = (int) ($options['margin'] ?? 12);

        $builder = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($qrUrl)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size($size)
            ->margin($margin)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->foregroundColor($foreground)
            ->backgroundColor($background)
            ->validateResult(false);

        // --- Label / Text Integration ---
        $labelText = $options['label_text'] ?? $defaultLabel;
        if (!empty($labelText)) {
            $labelSize = (int) ($options['label_size'] ?? 18);
            $labelColorHex = $options['label_color'] ?? $fgHex;
            $labelColor = $this->parseColor($labelColorHex);

            $fontFamily = $options['label_font'] ?? 'noto_sans';
            $vendorPath = dirname(__DIR__, 2) . '/vendor/endroid/qr-code/assets/';
            $fontPath = ($fontFamily === 'open_sans')
                ? $vendorPath . 'open_sans.ttf'
                : $vendorPath . 'noto_sans.otf';

            if (file_exists($fontPath)) {
                $font = new Font($fontPath, $labelSize);
            } else {
                $font = new NotoSans($labelSize);
            }

            $labelObj = Label::create($labelText)
                ->setFont($font)
                ->setTextColor($labelColor)
                ->setAlignment(LabelAlignment::Center);

            $builder->labelText($labelObj->getText())
                ->labelFont($labelObj->getFont())
                ->labelTextColor($labelObj->getTextColor())
                ->labelAlignment($labelObj->getAlignment());
        }

        $result = $builder->build();
        $gdImage = $result->getImage();
        $matrix = $result->getMatrix();

        // 1. Apply custom eye shape / color
        $this->applyCustomEyes($gdImage, $matrix, $eyeShape, $eyeColorHex, $bgHex);

        // 2. Apply custom logo with customizable padding and alpha transparency
        $logoPath = $options['logo_path'] ?? null;
        if ($logoPath && file_exists($logoPath)) {
            $logoSizePct = (int) ($options['logo_size'] ?? 20);
            $logoPadding = (int) ($options['logo_padding'] ?? 6);
            $this->applyCustomLogoWithPadding($gdImage, $matrix, $logoPath, $logoSizePct, $logoPadding, $bgHex);
        }

        // 3. Apply branding footer (Menupro+, designed by ALTES + Logo ALTES + Website)
        $gdImage = $this->applyBrandingFooter($gdImage, $options, $bgHex, $fgHex);

        ob_start();
        imagepng($gdImage);
        $rawPng = ob_get_clean();
        imagedestroy($gdImage);

        return $rawPng;
    }

    /**
     * Calculate relative perceived brightness / luminance of a hex color.
     */
    private function getLuminance(string $hex): float
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) {
            return 255.0;
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return ($r * 0.299 + $g * 0.587 + $b * 0.114);
    }

    /**
     * Apply the ALTES / Menupro+ branding footer onto the QR code image.
     */
    private function applyBrandingFooter($gdImage, array $options, string $bgHex, string $fgHex)
    {
        $showBranding = $options['show_branding'] ?? true;
        if (!$showBranding) {
            return $gdImage;
        }

        $brandingText = $options['branding_text'] ?? 'Menupro+, designed by ALTES';
        $brandingUrl  = $options['branding_website'] ?? 'https://menuproplus.aladints.com/';
        $logoPath     = $options['altes_logo_path'] ?? (function_exists('public_path') && app()->has('path.public') ? public_path('img/altes-logo.png') : dirname(__DIR__, 2) . '/public/img/altes-logo.png');

        if (!extension_loaded('gd') || !$gdImage) {
            return $gdImage;
        }

        $w = imagesx($gdImage);
        $h = imagesy($gdImage);

        // Proportional scale factor based on standard 360px QR code width
        $scale = max(0.65, min(3.5, $w / 360));

        $footerH = (int) round(70 * $scale);
        $newH = $h + $footerH;

        $canvas = imagecreatetruecolor($w, $newH);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        // Fill background
        $bg = $this->parseColor($bgHex);
        $bgGd = imagecolorallocate($canvas, $bg->getRed(), $bg->getGreen(), $bg->getBlue());
        imagefilledrectangle($canvas, 0, 0, $w, $newH, $bgGd);

        // Copy QR code onto top of canvas
        imagecopy($canvas, $gdImage, 0, 0, 0, 0, $w, $h);
        imagedestroy($gdImage);

        // Determine contrasting colors based on background luminance
        $lum = $this->getLuminance($bgHex);
        $isDarkBg = ($lum < 130);

        if ($isDarkBg) {
            $sepColor     = imagecolorallocate($canvas, 71, 85, 105);        // Slate 600
            $primaryColor = imagecolorallocate($canvas, 251, 191, 36);       // Gold/Yellow #FBBF24
            $urlColor     = imagecolorallocate($canvas, 147, 197, 253);       // Blue 300 #93C5FD
        } else {
            $sepColor     = imagecolorallocate($canvas, 226, 232, 240);       // Slate 200 #E2E8F0
            $primaryColor = imagecolorallocate($canvas, 30, 64, 175);        // Aladin Blue Primary #1E40AF
            $urlColor     = imagecolorallocate($canvas, 100, 116, 139);       // Slate 500 #64748B
        }

        // Draw thin separator line
        $marginPad = (int) round(20 * $scale);
        $sepY = $h + (int) round(4 * $scale);
        imageline($canvas, $marginPad, $sepY, $w - $marginPad, $sepY, $sepColor);

        // Resolve fonts
        $basePath = dirname(__DIR__, 2);
        $fontBold = $basePath . '/vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf';
        if (!file_exists($fontBold)) {
            $fontBold = $basePath . '/vendor/endroid/qr-code/assets/noto_sans.otf';
        }

        $fontReg = $basePath . '/vendor/endroid/qr-code/assets/noto_sans.otf';
        if (!file_exists($fontReg)) {
            $fontReg = $fontBold;
        }

        // 1. Line 1: Logo ALTES + "Menupro+, designed by ALTES"
        $t1Size = max(7, (int) round(10 * $scale));
        $b1 = imagettfbbox($t1Size, 0, $fontBold, $brandingText);
        $t1W = abs($b1[2] - $b1[0]);

        $hasLogo = file_exists($logoPath);
        $targetLogoW = 0;
        $targetLogoH = (int) round(22 * $scale);
        $gap = (int) round(7 * $scale);
        $srcLogo = null;

        if ($hasLogo) {
            $rawLogo = @file_get_contents($logoPath);
            if ($rawLogo) {
                $srcLogo = @imagecreatefromstring($rawLogo);
                if ($srcLogo) {
                    $srcW = imagesx($srcLogo);
                    $srcH = imagesy($srcLogo);
                    $targetLogoW = (int) round($srcW * ($targetLogoH / $srcH));
                }
            }
        }

        $totalLine1W = $t1W + ($hasLogo && $targetLogoW > 0 ? $targetLogoW + $gap : 0);
        $line1StartX = max(4, (int) round(($w - $totalLine1W) / 2));

        if ($hasLogo && $srcLogo && $targetLogoW > 0) {
            $logoX = $line1StartX;
            $logoY = $h + (int) round(13 * $scale);
            imagecopyresampled($canvas, $srcLogo, $logoX, $logoY, 0, 0, $targetLogoW, $targetLogoH, $srcW, $srcH);
            imagedestroy($srcLogo);
            $t1X = $logoX + $targetLogoW + $gap;
        } else {
            $t1X = $line1StartX;
        }

        $t1Y = $h + (int) round(29 * $scale);
        imagettftext($canvas, $t1Size, 0, $t1X, $t1Y, $primaryColor, $fontBold, $brandingText);

        // 2. Line 2: Website URL
        $t2Size = max(6, (int) round(8 * $scale));
        $b2 = imagettfbbox($t2Size, 0, $fontReg, $brandingUrl);
        $t2W = abs($b2[2] - $b2[0]);
        $t2X = max(4, (int) round(($w - $t2W) / 2));
        $t2Y = $h + (int) round(52 * $scale);
        imagettftext($canvas, $t2Size, 0, $t2X, $t2Y, $urlColor, $fontReg, $brandingUrl);

        return $canvas;
    }

    abstract protected function getQrCodeFileName(): string;
    abstract protected function getRestaurantId(): int;
}
