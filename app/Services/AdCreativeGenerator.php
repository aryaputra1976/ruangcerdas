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

        imageantialias($canvas, true);

        $palette = $this->paletteForTemplate($templateKey);
        $cream = imagecolorallocate($canvas, ...$palette['cream']);
        $paper = imagecolorallocate($canvas, ...$palette['paper']);
        $ink = imagecolorallocate($canvas, ...$palette['ink']);
        $muted = imagecolorallocate($canvas, ...$palette['muted']);
        $red = imagecolorallocate($canvas, ...$palette['accent']);
        $softRed = imagecolorallocate($canvas, ...$palette['accent_soft']);
        $line = imagecolorallocate($canvas, ...$palette['line']);

        imagefilledrectangle($canvas, 0, 0, $width, $height, $cream);

        $lineSpacing = max(80, (int) round($height / 16));

        for ($y = 0; $y < $height; $y += $lineSpacing) {
            imageline($canvas, 0, $y, $width, $y, $line);
        }

        $scale = $height / 1920;
        $headerLeft = (int) round(80 * ($width / 1080));
        $headerTop = (int) round(90 * $scale);
        $headerRight = $width - $headerLeft;
        $headerBottom = (int) round(260 * $scale);
        $contentLeft = (int) round(110 * ($width / 1080));
        $contentTop = (int) round(330 * $scale);
        $contentRight = $width - $contentLeft;
        $contentBottom = (int) round(($height >= 1500 ? 1460 : ($height - 250)) * 1);

        imagefilledrectangle($canvas, $headerLeft, $headerTop, $headerRight, $headerBottom, $softRed);
        imagefilledrectangle($canvas, $contentLeft, $contentTop, $contentRight, $contentBottom, $paper);
        imagerectangle($canvas, $contentLeft, $contentTop, $contentRight, $contentBottom, $line);

        $productName = trim((string) ($product?->name ?? ''));
        $priceText = $product ? ('Mulai Rp' . number_format((int) $product->public_price, 0, ',', '.')) : null;

        $headlineMaxChars = $width >= 1080 && $height >= 1700 ? 54 : ($height <= 1200 ? 42 : 48);
        $bodyMaxChars = $height <= 1200 ? 3 : 4;
        $bodyLineChars = $height <= 1200 ? 30 : 30;
        $bulletLineChars = $height <= 1200 ? 30 : 28;
        $footerLineChars = $height <= 1200 ? 34 : 24;

        $cursorY = (int) round(120 * $scale);
        $cursorY = $this->drawWrappedText($canvas, Str::upper($title), $contentLeft, $cursorY, 5, $red, 28, 1, max(28, (int) round(38 * $scale)));
        $cursorY += (int) round(20 * $scale);
        $cursorY = $this->drawWrappedText($canvas, $headline, $contentLeft, $cursorY, 5, $ink, $headlineMaxChars, 2, max(42, (int) round(70 * $scale)));

        if ($productName !== '') {
            $cursorY += (int) round(22 * $scale);
            $cursorY = $this->drawWrappedText($canvas, $productName, $contentLeft, $cursorY, 4, $muted, 34, 1, max(30, (int) round(40 * $scale)));
        }

        $contentLabel = match ($templateKey) {
            'urgent_offer' => 'JANGAN LEWATKAN',
            'social_proof' => 'KENAPA BANYAK YANG SUKA',
            default => 'CATATAN PENTING',
        };

        $cursorY = (int) round(390 * $scale);
        $textLeft = (int) round(150 * ($width / 1080));
        $cursorY = $this->drawWrappedText($canvas, $contentLabel, $textLeft, $cursorY, 4, $red, 24, 1, max(26, (int) round(34 * $scale)));
        $cursorY += (int) round(12 * $scale);
        $cursorY = $this->drawWrappedText($canvas, $body, $textLeft, $cursorY, 4, $ink, $bodyLineChars, 1, max(32, (int) round(44 * $scale)));
        $cursorY += (int) round(26 * $scale);

        foreach ($bullets as $bullet) {
            imagefilledellipse($canvas, $textLeft + 5, $cursorY + max(12, (int) round(18 * $scale)), max(10, (int) round(14 * $scale)), max(10, (int) round(14 * $scale)), $red);
            $cursorY = $this->drawWrappedText($canvas, $bullet, $textLeft + max(24, (int) round(30 * ($width / 1080))), $cursorY, 4, $ink, $bulletLineChars, 1, max(28, (int) round(40 * $scale)));
            $cursorY += (int) round(16 * $scale);
        }

        if ($priceText) {
            $priceTop = (int) round(min($height - 290, 1180 * $scale));
            $priceBottom = $priceTop + max(70, (int) round(90 * $scale));
            $priceRight = min($contentRight - 40, $textLeft + max(300, (int) round(410 * ($width / 1080))));
            imagefilledrectangle($canvas, $textLeft, $priceTop, $priceRight, $priceBottom, $softRed);
            $this->drawWrappedText($canvas, $priceText, $textLeft + max(18, (int) round(28 * ($width / 1080))), $priceTop + max(18, (int) round(25 * $scale)), 5, $red, 32, 1, max(28, (int) round(38 * $scale)));
        }

        $ctaTop = (int) round($height - max(280, 415 * $scale));
        $ctaBottom = $ctaTop + max(110, (int) round(130 * $scale));
        imagefilledrectangle($canvas, $textLeft, $ctaTop, $contentRight - max(40, (int) round(40 * ($width / 1080))), $ctaBottom, $red);
        $this->drawWrappedText($canvas, Str::upper($ctaText), $textLeft + max(24, (int) round(50 * ($width / 1080))), $ctaTop + max(24, (int) round(35 * $scale)), 5, $paper, 34, 2, max(28, (int) round(38 * $scale)));

        $footerNote = match ($templateKey) {
            'urgent_offer' => 'Format ini cocok untuk promo cepat, flash sale, dan penawaran terbatas.',
            'social_proof' => 'Format ini cocok untuk edukasi, trust building, dan konten viral bergaya catatan.',
            default => 'Materi digital yang dibuat untuk bantu calon pembeli bergerak lebih cepat.',
        };

        $footerTop = $ctaBottom + max(26, (int) round(60 * $scale));
        $this->drawWrappedText($canvas, $footerNote, $textLeft, $footerTop, 4, $muted, $footerLineChars, 1, max(24, (int) round(34 * $scale)));
        $brandTop = $height - max(120, (int) round(130 * $scale));
        $this->drawWrappedText($canvas, $brandText, $textLeft, $brandTop, 5, $ink, 30, 1, max(28, (int) round(36 * $scale)));
        $this->drawWrappedText($canvas, 'Template ' . str_replace('_', ' ', $templateKey), max($textLeft, $width - max(380, (int) round(390 * ($width / 1080)))), $brandTop + 2, 3, $muted, 24, 1, 28);

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

    private function paletteForTemplate(string $templateKey): array
    {
        return match ($templateKey) {
            'urgent_offer' => [
                'cream' => [248, 236, 225],
                'paper' => [255, 249, 243],
                'ink' => [44, 31, 24],
                'muted' => [120, 93, 78],
                'accent' => [175, 34, 34],
                'accent_soft' => [247, 214, 206],
                'line' => [227, 202, 182],
            ],
            'social_proof' => [
                'cream' => [243, 237, 225],
                'paper' => [253, 250, 245],
                'ink' => [34, 38, 41],
                'muted' => [90, 94, 102],
                'accent' => [184, 55, 55],
                'accent_soft' => [244, 225, 225],
                'line' => [215, 206, 190],
            ],
            default => [
                'cream' => [247, 238, 223],
                'paper' => [255, 251, 245],
                'ink' => [39, 35, 31],
                'muted' => [108, 101, 92],
                'accent' => [193, 43, 43],
                'accent_soft' => [245, 217, 217],
                'line' => [224, 208, 187],
            ],
        };
    }
}
