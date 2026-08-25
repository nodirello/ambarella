<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Validated, GD-based image uploads to the "public" disk with a safe random name.
 */
class ImageService
{
    private const ALLOWED = ['jpg', 'jpeg', 'png', 'webp'];
    private const MAX_KB = 4096;

    public function upload(?UploadedFile $file, string $directory, int $maxWidth = 1600): ?string
    {
        if (! $file) {
            return null;
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, self::ALLOWED, true)) {
            abort(422, 'Rasm formati qo‘llab-quvvatlanmaydi (jpg, png, webp).');
        }

        if ($file->getSize() > self::MAX_KB * 1024) {
            abort(422, 'Rasm hajmi '.self::MAX_KB.' KB dan oshmasligi kerak.');
        }

        $source = match ($extension) {
            'png' => @imagecreatefrompng($file->getRealPath()),
            'webp' => @imagecreatefromwebp($file->getRealPath()),
            default => @imagecreatefromjpeg($file->getRealPath()),
        };

        if (! $source) {
            abort(422, 'Rasmni o‘qib bo‘lmadi — fayl buzilgan.');
        }

        $width = imagesx($source);
        if ($width > $maxWidth) {
            $ratio = $maxWidth / $width;
            $scaled = imagescale($source, $maxWidth, (int) (imagesy($source) * $ratio), IMG_BICUBIC);
            imagedestroy($source);
            $source = $scaled;
        }

        $name = $directory.'/'.date('Y/m').'/'.Str::random(32).'.'.$extension;
        $tmp = tempnam(sys_get_temp_dir(), 'img');

        match ($extension) {
            'png' => imagepng($source, $tmp, 8),
            'webp' => imagewebp($source, $tmp, 82),
            default => imagejpeg($source, $tmp, 82),
        };

        imagedestroy($source);

        $file->storeAs('public', $name);
        copy($tmp, storage_path('app/public/'.$name));
        @unlink($tmp);

        return $name;
    }
}
