<?php

namespace App\Services;

use App\Models\AdCreative;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AdCreativeGenerator
{
    public const SUPPORTED_FORMATS = ['png', 'jpg', 'jpeg', 'webp'];

    private const FONT_CANDIDATES = [
        'regular' => [
            'C:\Windows\Fonts\arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/dejavu/DejaVuSans.ttf',
        ],
        'bold' => [
            'C:\Windows\Fonts\arialbd.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
        ],
    ];

    public function generate(array $payload, ?Product $product = null): array
    {
        $templateKey = trim((string) ($payload['template_key'] ?? 'viral_note'));
        $title = trim((string) ($payload['title'] ?? 'Iklan Produk'));
        $headline = trim((string) ($payload['headline'] ?? 'Bantu pembeli paham manfaat produk Anda'));
        $body = trim((string) ($payload['body'] ?? 'Susun pesan yang singkat, jelas, dan mudah dibaca.'));
        $bullets = collect($payload['bullets'] ?? [])
            ->map(fn ($bullet) => trim((string) $bullet))
            ->filter()
            ->take(5)
            ->values()
            ->all();
        $ctaText = trim((string) ($payload['cta_text'] ?? 'Ambil Sekarang'));
        $brandText = trim((string) ($payload['brand_text'] ?? 'ruangcerdas.id'));
        $format = strtolower((string) ($payload['format'] ?? 'png'));
        $sizeKey = trim((string) ($payload['size_preset'] ?? 'story'));
        $sizePreset = AdCreative::SIZE_PRESETS[$sizeKey] ?? AdCreative::SIZE_PRESETS['story'];
        $width = (int) $sizePreset['width'];
        $height = (int) $sizePreset['height'];

        if ($format !== 'png') {
            throw new RuntimeException('Format iklan belum didukung selain PNG.');
        }

        $fileName = 'ad-creative-' . now()->format('Ymd-His') . '-' . Str::uuid() . '.png';
        $relativePath = 'ad-creatives/' . $fileName;
        $absolutePath = Storage::disk('public')->path($relativePath);
        $directory = dirname($absolutePath);

        if (! is_dir($directory) && ! @mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Folder penyimpanan iklan tidak dapat dibuat.');
        }

        if (class_exists(\Intervention\Image\ImageManager::class) && function_exists('imagecreatetruecolor')) {
            $this->renderWithGd($absolutePath, $templateKey, $title, $headline, $body, $bullets, $ctaText, $brandText, $product, $width, $height);
        } elseif (function_exists('imagecreatetruecolor')) {
            $this->renderWithGd($absolutePath, $templateKey, $title, $headline, $body, $bullets, $ctaText, $brandText, $product, $width, $height);
        } else {
            throw new RuntimeException('Server belum mendukung generator gambar PNG karena extension GD belum aktif.');
        }

        return [
            'image_path' => $relativePath,
            'format' => 'png',
            'width' => $width,
            'height' => $height,
        ];
    }

    public function export(string $sourcePath, string $format): array
    {
        $normalizedFormat = strtolower(trim($format));

        if (! in_array($normalizedFormat, self::SUPPORTED_FORMATS, true)) {
            throw new RuntimeException('Format export iklan tidak dikenali.');
        }

        if ($normalizedFormat === 'jpeg') {
            $normalizedFormat = 'jpg';
        }

        if ($normalizedFormat === 'png') {
            return [
                'binary' => file_get_contents($sourcePath),
                'mime' => 'image/png',
                'extension' => 'png',
            ];
        }

        if (! function_exists('imagecreatefrompng')) {
            throw new RuntimeException('Server belum mendukung konversi format iklan.');
        }

        $image = imagecreatefrompng($sourcePath);

        if ($image === false) {
            throw new RuntimeException('Gagal membaca file PNG sumber.');
        }

        ob_start();

        try {
            if ($normalizedFormat === 'jpg') {
                imagejpeg($image, null, 90);
                $mime = 'image/jpeg';
                $extension = 'jpg';
            } else {
                if (! function_exists('imagewebp')) {
                    throw new RuntimeException('Server belum mendukung export WebP.');
                }

                imagepalettetotruecolor($image);
                imagewebp($image, null, 90);
                $mime = 'image/webp';
                $extension = 'webp';
            }

            $binary = ob_get_clean();
        } catch (\Throwable $exception) {
            ob_end_clean();
            imagedestroy($image);

            throw $exception;
        }

        imagedestroy($image);

        if ($binary === false) {
            throw new RuntimeException('Gagal membuat file export iklan.');
        }

        return [
            'binary' => $binary,
            'mime' => $mime,
            'extension' => $extension,
        ];
    }

    private function renderWithGd(
        string $absolutePath,
        string $templateKey,
        string $title,
        string $headline,
        string $body,
        array $bullets,
        string $ctaText,
        string $brandText,
        ?Product $product,
        int $width,
        int $height
    ): void {
        $canvas = imagecreatetruecolor($width, $height);

        if ($canvas === false) {
            throw new RuntimeException('Gagal membuat canvas iklan.');
        }

        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);
        imageantialias($canvas, true);

        $palette = $this->paletteForTemplate($templateKey);
        $cream = imagecolorallocate($canvas, ...$palette['cream']);
        $paper = imagecolorallocate($canvas, ...$palette['paper']);
        $ink = imagecolorallocate($canvas, ...$palette['ink']);
        $muted = imagecolorallocate($canvas, ...$palette['muted']);
        $red = imagecolorallocate($canvas, ...$palette['accent']);
        $line = imagecolorallocate($canvas, ...$palette['line']);
        $shadow = imagecolorallocatealpha($canvas, 102, 72, 51, 108);
        $paperBorder = imagecolorallocatealpha($canvas, $palette['line'][0], $palette['line'][1], $palette['line'][2], 56);
        $softRedTint = imagecolorallocatealpha($canvas, $palette['accent'][0], $palette['accent'][1], $palette['accent'][2], 110);
        $priceTint = imagecolorallocatealpha($canvas, $palette['accent'][0], $palette['accent'][1], $palette['accent'][2], 102);
        $fontSet = $this->resolveFontSet();

        imagefilledrectangle($canvas, 0, 0, $width, $height, $cream);

        $lineSpacing = max(48, (int) round($height / 24));

        for ($y = 0; $y < $height; $y += $lineSpacing) {
            imageline($canvas, 0, $y, $width, $y, $line);
        }

        $ratioScale = min($width / 1080, $height / 1920);
        $shellInsetX = (int) round(56 * $ratioScale);
        $shellInsetY = (int) round(52 * $ratioScale);
        $shellRadius = max(26, (int) round(54 * $ratioScale));
        $frameRadius = max(24, (int) round(44 * $ratioScale));

        $this->fillRoundedRectangle(
            $canvas,
            $shellInsetX + max(8, (int) round(14 * $ratioScale)),
            $shellInsetY + max(14, (int) round(24 * $ratioScale)),
            $width - $shellInsetX + max(8, (int) round(14 * $ratioScale)),
            $height - $shellInsetY + max(16, (int) round(28 * $ratioScale)),
            $shellRadius,
            $shadow
        );
        $this->fillRoundedRectangle(
            $canvas,
            $shellInsetX,
            $shellInsetY,
            $width - $shellInsetX,
            $height - $shellInsetY,
            $shellRadius,
            $paper
        );

        $frameInset = max(22, (int) round(42 * $ratioScale));
        $frameLeft = $shellInsetX + $frameInset;
        $frameTop = $shellInsetY + $frameInset;
        $frameRight = $width - $shellInsetX - $frameInset;
        $frameBottom = $height - $shellInsetY - $frameInset;

        $this->fillRoundedRectangle($canvas, $frameLeft, $frameTop, $frameRight, $frameBottom, $frameRadius, $cream);

        $productName = trim((string) ($product?->name ?? ''));
        $priceText = $product ? ('Mulai Rp' . number_format((int) $product->public_price, 0, ',', '.')) : null;

        $contentLabel = match ($templateKey) {
            'urgent_offer' => 'JANGAN LEWATKAN',
            'social_proof' => 'KENAPA BANYAK YANG SUKA',
            default => 'CATATAN PENTING',
        };

        $headerLeft = $frameLeft + max(14, (int) round(18 * $ratioScale));
        $headerRight = $frameRight - max(14, (int) round(18 * $ratioScale));
        $headerTop = $frameTop + max(14, (int) round(18 * $ratioScale));
        $headerHeight = (int) round(($height >= 1600 ? 300 : ($height >= 1200 ? 250 : 220)) * $ratioScale);
        $headerBottom = $headerTop + $headerHeight;
        $headerRadius = max(18, (int) round(28 * $ratioScale));

        $this->fillRoundedRectangle($canvas, $headerLeft, $headerTop, $headerRight, $headerBottom, $headerRadius, $softRedTint);

        $headerPaddingX = max(20, (int) round(28 * $ratioScale));
        $headerPaddingY = max(20, (int) round(26 * $ratioScale));
        $titleSize = max(18, (int) round(28 * $ratioScale));
        $headlineSize = max(30, (int) round(($height >= 1600 ? 54 : 42) * $ratioScale));
        $productSize = max(20, (int) round(24 * $ratioScale));
        $bodySize = max(22, (int) round(26 * $ratioScale));
        $bodyLineHeight = max(32, (int) round($bodySize * 1.55));
        $bulletSize = max(20, (int) round(24 * $ratioScale));
        $bulletLineHeight = max(30, (int) round($bulletSize * 1.5));
        $ctaSize = max(24, (int) round(30 * $ratioScale));
        $footerSize = max(18, (int) round(22 * $ratioScale));
        $metaSize = max(16, (int) round(18 * $ratioScale));

        $headerTextWidth = ($headerRight - $headerLeft) - (2 * $headerPaddingX);
        $titleLineHeight = max(22, (int) round($titleSize * 1.2));
        $productLineHeight = max(26, (int) round($productSize * 1.35));
        $availableHeadlineHeight = $headerHeight - (2 * $headerPaddingY) - $titleLineHeight - max(10, (int) round(10 * $ratioScale));

        if ($productName !== '') {
            $availableHeadlineHeight -= $productLineHeight + max(8, (int) round(8 * $ratioScale));
        }

        $headlineSize = $this->fitTextBlockFontSize(
            $headline,
            $fontSet['bold'],
            $headerTextWidth,
            max(52, $availableHeadlineHeight),
            $headlineSize,
            max(24, (int) round(28 * $ratioScale)),
            1.25
        );
        $headlineLineHeight = max(38, (int) round($headlineSize * 1.25));
        $cursorY = $headerTop + $headerPaddingY;
        $cursorY = $this->drawTextBlock($canvas, Str::upper($title), $headerLeft + $headerPaddingX, $cursorY, [
            'max_width' => $headerTextWidth,
            'font_size' => $titleSize,
            'line_height' => $titleLineHeight,
            'color' => $red,
            'font' => $fontSet['bold'],
            'uppercase' => true,
            'letter_spacing' => max(1, (int) round(2 * $ratioScale)),
        ]);
        $cursorY += max(10, (int) round(10 * $ratioScale));
        $cursorY = $this->drawTextBlock($canvas, $headline, $headerLeft + $headerPaddingX, $cursorY, [
            'max_width' => $headerTextWidth,
            'font_size' => $headlineSize,
            'line_height' => $headlineLineHeight,
            'color' => $ink,
            'font' => $fontSet['bold'],
        ]);

        if ($productName !== '') {
            $cursorY += max(8, (int) round(8 * $ratioScale));
            $cursorY = $this->drawTextBlock($canvas, $productName, $headerLeft + $headerPaddingX, $cursorY, [
                'max_width' => $headerTextWidth,
                'font_size' => $productSize,
                'line_height' => $productLineHeight,
                'color' => $muted,
                'font' => $fontSet['bold'],
            ]);
        }

        $noteLeft = $frameLeft + max(34, (int) round(44 * $ratioScale));
        $noteRight = $frameRight - max(34, (int) round(44 * $ratioScale));
        $noteTop = $headerBottom + max(26, (int) round(34 * $ratioScale));
        $noteBottom = $frameBottom - max(112, (int) round(140 * $ratioScale));
        $noteRadius = max(20, (int) round(30 * $ratioScale));

        $this->fillRoundedRectangle($canvas, $noteLeft, $noteTop, $noteRight, $noteBottom, $noteRadius, $paper);
        $this->strokeRoundedRectangle($canvas, $noteLeft, $noteTop, $noteRight, $noteBottom, $noteRadius, $paperBorder);

        $textLeft = $noteLeft + max(26, (int) round(34 * $ratioScale));
        $textRight = $noteRight - max(26, (int) round(34 * $ratioScale));
        $textWidth = $textRight - $textLeft;
        $cursorY = $noteTop + max(28, (int) round(36 * $ratioScale));
        $cursorY = $this->drawTextBlock($canvas, $contentLabel, $textLeft, $cursorY, [
            'max_width' => $textWidth,
            'font_size' => max(18, (int) round(22 * $ratioScale)),
            'line_height' => max(22, (int) round(26 * $ratioScale)),
            'color' => $red,
            'font' => $fontSet['bold'],
            'uppercase' => true,
            'letter_spacing' => max(1, (int) round(1.5 * $ratioScale)),
        ]);
        $cursorY += max(10, (int) round(10 * $ratioScale));
        $cursorY = $this->drawTextBlock($canvas, $body, $textLeft, $cursorY, [
            'max_width' => $textWidth,
            'font_size' => $bodySize,
            'line_height' => $bodyLineHeight,
            'color' => $ink,
            'font' => $fontSet['regular'],
        ]);
        $cursorY += max(18, (int) round(20 * $ratioScale));

        foreach ($bullets as $bullet) {
            $bulletDot = max(10, (int) round(12 * $ratioScale));
            imagefilledellipse(
                $canvas,
                $textLeft + max(8, (int) round(10 * $ratioScale)),
                $cursorY + max(12, (int) round(14 * $ratioScale)),
                $bulletDot,
                $bulletDot,
                $red
            );
            $cursorY = $this->drawTextBlock($canvas, $bullet, $textLeft + max(22, (int) round(30 * $ratioScale)), $cursorY, [
                'max_width' => $textWidth - max(22, (int) round(30 * $ratioScale)),
                'font_size' => $bulletSize,
                'line_height' => $bulletLineHeight,
                'color' => $ink,
                'font' => $fontSet['regular'],
            ]);
            $cursorY += max(10, (int) round(10 * $ratioScale));
        }

        if ($priceText) {
            $priceY = min($cursorY + max(4, (int) round(6 * $ratioScale)), $noteBottom - max(180, (int) round(230 * $ratioScale)));
            $priceWidth = min($textWidth, max(280, (int) round(360 * $ratioScale)));
            $priceHeight = max(58, (int) round(72 * $ratioScale));
            $this->fillRoundedRectangle(
                $canvas,
                $textLeft,
                $priceY,
                $textLeft + $priceWidth,
                $priceY + $priceHeight,
                max(24, (int) round(36 * $ratioScale)),
                $priceTint
            );
            $this->drawTextBlock($canvas, $priceText, $textLeft + max(16, (int) round(22 * $ratioScale)), $priceY + max(14, (int) round(18 * $ratioScale)), [
                'max_width' => $priceWidth - max(30, (int) round(44 * $ratioScale)),
                'font_size' => max(24, (int) round(28 * $ratioScale)),
                'line_height' => max(30, (int) round(34 * $ratioScale)),
                'color' => $red,
                'font' => $fontSet['bold'],
            ]);
            $cursorY = $priceY + $priceHeight;
        }

        $ctaTop = $noteBottom - max(118, (int) round(150 * $ratioScale));
        $ctaBottom = $ctaTop + max(92, (int) round(112 * $ratioScale));
        $this->fillRoundedRectangle(
            $canvas,
            $textLeft,
            $ctaTop,
            $textRight,
            $ctaBottom,
            max(24, (int) round(34 * $ratioScale)),
            $red
        );
        $this->drawTextBlock($canvas, Str::upper($ctaText), $textLeft + max(20, (int) round(28 * $ratioScale)), $ctaTop + max(22, (int) round(28 * $ratioScale)), [
            'max_width' => $textWidth - max(40, (int) round(56 * $ratioScale)),
            'font_size' => $ctaSize,
            'line_height' => max(30, (int) round(36 * $ratioScale)),
            'color' => $paper,
            'font' => $fontSet['bold'],
            'uppercase' => true,
            'align' => 'center',
        ]);

        $brandTop = $frameBottom - max(40, (int) round(54 * $ratioScale));
        $this->drawTextBlock($canvas, $brandText, $frameLeft + max(24, (int) round(30 * $ratioScale)), $brandTop, [
            'max_width' => (int) round(($frameRight - $frameLeft) * 0.45),
            'font_size' => $footerSize,
            'line_height' => max(24, (int) round(28 * $ratioScale)),
            'color' => $muted,
            'font' => $fontSet['bold'],
        ]);
        $sizeLabel = $this->sizeLabelFromDimensions($width, $height);
        $this->drawTextBlock($canvas, str_replace('_', ' ', $templateKey), $frameRight - max(190, (int) round(220 * $ratioScale)), $brandTop + max(18, (int) round(18 * $ratioScale)), [
            'max_width' => max(150, (int) round(170 * $ratioScale)),
            'font_size' => $metaSize,
            'line_height' => max(18, (int) round(22 * $ratioScale)),
            'color' => $muted,
            'font' => $fontSet['regular'],
            'uppercase' => true,
            'align' => 'right',
        ]);
        $this->drawTextBlock($canvas, $sizeLabel, $frameRight - max(190, (int) round(220 * $ratioScale)), $brandTop - max(8, (int) round(6 * $ratioScale)), [
            'max_width' => max(150, (int) round(170 * $ratioScale)),
            'font_size' => $metaSize,
            'line_height' => max(18, (int) round(22 * $ratioScale)),
            'color' => $muted,
            'font' => $fontSet['regular'],
            'uppercase' => true,
            'align' => 'right',
        ]);

        if (! imagepng($canvas, $absolutePath)) {
            imagedestroy($canvas);

            throw new RuntimeException('Gagal menyimpan file PNG iklan.');
        }

        imagedestroy($canvas);
    }

    private function drawWrappedText(
        \GdImage $canvas,
        string $text,
        int $x,
        int $y,
        int $font,
        int $color,
        int $maxChars,
        int $fontScale = 1,
        int $lineHeight = 28
    ): int {
        $cleanText = trim(preg_replace('/\s+/', ' ', $text) ?? '');

        if ($cleanText === '') {
            return $y;
        }

        $lines = preg_split('/\r\n|\r|\n/', wordwrap($cleanText, $maxChars, "\n", true)) ?: [];

        foreach ($lines as $line) {
            for ($scale = 0; $scale < max(1, $fontScale); $scale++) {
                imagestring($canvas, $font, $x, $y + $scale, $line, $color);
            }

            $y += $lineHeight;
        }

        return $y;
    }

    private function drawTextBlock(\GdImage $canvas, string $text, int $x, int $y, array $options): int
    {
        $cleanText = trim((string) preg_replace('/\s+/', ' ', $text));

        if ($cleanText === '') {
            return $y;
        }

        $font = $options['font'] ?? null;

        if (! is_string($font) || ! function_exists('imagettftext')) {
            return $this->drawWrappedText(
                $canvas,
                $cleanText,
                $x,
                $y,
                5,
                $options['color'],
                max(16, (int) floor(($options['max_width'] ?? 320) / 12)),
                1,
                (int) ($options['line_height'] ?? 28)
            );
        }

        $fontSize = (int) ($options['font_size'] ?? 24);
        $lineHeight = (int) ($options['line_height'] ?? ($fontSize + 8));
        $maxWidth = (int) ($options['max_width'] ?? 320);
        $align = $options['align'] ?? 'left';
        $letterSpacing = (int) ($options['letter_spacing'] ?? 0);
        $lines = $this->wrapTextForTtf($cleanText, $font, $fontSize, $maxWidth, $letterSpacing);

        foreach ($lines as $line) {
            $lineWidth = $this->textWidth($font, $fontSize, $line, $letterSpacing);
            $drawX = match ($align) {
                'center' => $x + (int) floor(($maxWidth - $lineWidth) / 2),
                'right' => $x + max(0, $maxWidth - $lineWidth),
                default => $x,
            };

            $this->drawTtfString($canvas, $font, $fontSize, $drawX, $y + $fontSize, $options['color'], $line, $letterSpacing);
            $y += $lineHeight;
        }

        return $y;
    }

    private function drawTtfString(\GdImage $canvas, string $font, int $fontSize, int $x, int $baselineY, int $color, string $text, int $letterSpacing = 0): void
    {
        if ($letterSpacing <= 0) {
            imagettftext($canvas, $fontSize, 0, $x, $baselineY, $color, $font, $text);

            return;
        }

        $cursorX = $x;

        foreach (mb_str_split($text) as $character) {
            imagettftext($canvas, $fontSize, 0, $cursorX, $baselineY, $color, $font, $character);
            $cursorX += $this->textWidth($font, $fontSize, $character, 0) + $letterSpacing;
        }
    }

    private function wrapTextForTtf(string $text, string $font, int $fontSize, int $maxWidth, int $letterSpacing = 0): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;

            if ($this->textWidth($font, $fontSize, $candidate, $letterSpacing) <= $maxWidth) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $lines[] = $current;
                $current = '';
            }

            if ($this->textWidth($font, $fontSize, $word, $letterSpacing) <= $maxWidth) {
                $current = $word;
                continue;
            }

            $segments = $this->splitLongWord($word, $font, $fontSize, $maxWidth, $letterSpacing);
            $lines = [...$lines, ...array_slice($segments, 0, -1)];
            $current = end($segments) ?: '';
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private function splitLongWord(string $word, string $font, int $fontSize, int $maxWidth, int $letterSpacing = 0): array
    {
        $segments = [];
        $current = '';

        foreach (mb_str_split($word) as $character) {
            $candidate = $current . $character;

            if ($current !== '' && $this->textWidth($font, $fontSize, $candidate, $letterSpacing) > $maxWidth) {
                $segments[] = $current;
                $current = $character;
                continue;
            }

            $current = $candidate;
        }

        if ($current !== '') {
            $segments[] = $current;
        }

        return $segments;
    }

    private function textWidth(string $font, int $fontSize, string $text, int $letterSpacing = 0): int
    {
        $box = imagettfbbox($fontSize, 0, $font, $text);

        if ($box === false) {
            return 0;
        }

        $width = (int) abs($box[2] - $box[0]);

        if ($letterSpacing > 0) {
            $length = max(0, mb_strlen($text) - 1);
            $width += $length * $letterSpacing;
        }

        return $width;
    }

    private function fitTextBlockFontSize(
        string $text,
        ?string $font,
        int $maxWidth,
        int $maxHeight,
        int $startSize,
        int $minSize,
        float $lineHeightRatio
    ): int {
        if (! is_string($font) || $font === '' || ! function_exists('imagettfbbox')) {
            return $startSize;
        }

        for ($fontSize = $startSize; $fontSize >= $minSize; $fontSize--) {
            $lineHeight = max($fontSize + 6, (int) round($fontSize * $lineHeightRatio));
            $lines = $this->wrapTextForTtf($text, $font, $fontSize, $maxWidth);
            $height = count($lines) * $lineHeight;

            if ($height <= $maxHeight) {
                return $fontSize;
            }
        }

        return $minSize;
    }

    private function sizeLabelFromDimensions(int $width, int $height): string
    {
        $sizeKey = AdCreative::sizePresetKeyFromDimensions($width, $height);

        return $sizeKey ? str_replace('_', ' ', $sizeKey) : ($width . ' x ' . $height);
    }

    private function resolveFontSet(): array
    {
        return [
            'regular' => $this->findFontPath(self::FONT_CANDIDATES['regular']),
            'bold' => $this->findFontPath(self::FONT_CANDIDATES['bold']),
        ];
    }

    private function findFontPath(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function fillRoundedRectangle(\GdImage $canvas, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        $radius = max(0, min($radius, (int) floor(min($x2 - $x1, $y2 - $y1) / 2)));

        imagefilledrectangle($canvas, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($canvas, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
        imagefilledellipse($canvas, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($canvas, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($canvas, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($canvas, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    private function strokeRoundedRectangle(\GdImage $canvas, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        $radius = max(0, min($radius, (int) floor(min($x2 - $x1, $y2 - $y1) / 2)));

        imageline($canvas, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
        imageline($canvas, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);
        imageline($canvas, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
        imageline($canvas, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);
        imagearc($canvas, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
        imagearc($canvas, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color);
        imagearc($canvas, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
        imagearc($canvas, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color);
    }

    private function paletteForTemplate(string $templateKey): array
    {
        $template = AdCreative::templateDefinitions()[$templateKey] ?? AdCreative::templateDefinitions()['viral_note'];
        $palette = $template['palette'];

        return [
            'cream' => $palette['cream'],
            'paper' => $palette['paper'],
            'ink' => $palette['ink'],
            'muted' => $palette['muted'],
            'accent' => $palette['accent'],
            'accent_soft' => array_map(
                fn (int $channel) => min(255, (int) round($channel + ((255 - $channel) * 0.72))),
                $palette['accent']
            ),
            'line' => $palette['line'],
        ];
    }
}
