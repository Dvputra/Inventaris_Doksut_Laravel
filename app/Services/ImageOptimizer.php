<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Optimasi dan kompres gambar (resize proporsional dan kompresi kualitas),
     * lalu simpan ke storage disk public sehingga ukuran akhir hanya berkisar puluhan/ratusan KB.
     *
     * @param  UploadedFile  $file  File gambar dari form upload (maks 3 MB)
     * @param  string  $directory  Direktori penyimpanan di storage/public (contoh: 'items' atau 'complaints')
     * @param  int  $maxDimension  Batas maksimal lebar/tinggi gambar (default: 1200 px)
     * @param  int  $quality  Kualitas output WebP/JPEG (1-100, default: 75)
     * @return string Path relatif file yang tersimpan pada disk public
     */
    public static function optimizeAndStore(
        UploadedFile $file,
        string $directory = 'items',
        int $maxDimension = 1000,
        int $quality = 70
    ): string {
        $realPath = $file->getRealPath();

        // Jika GD extension tidak tersedia atau getimagesize gagal, simpan file asli sebagai fallback
        if (! extension_loaded('gd') || ! file_exists($realPath)) {
            return $file->store($directory, 'public');
        }

        $imageInfo = @getimagesize($realPath);
        if ($imageInfo === false) {
            return $file->store($directory, 'public');
        }

        [$origWidth, $origHeight, $imageType] = $imageInfo;

        // Muat resource gambar sumber sesuai tipe aslinya
        $sourceImage = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($realPath),
            IMAGETYPE_PNG => @imagecreatefrompng($realPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($realPath) : null,
            default => null,
        };

        if (! $sourceImage) {
            return $file->store($directory, 'public');
        }

        // Hitung dimensi baru proporsional agar tidak melebihi $maxDimension
        $newWidth = $origWidth;
        $newHeight = $origHeight;

        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            if ($origWidth >= $origHeight) {
                $newWidth = $maxDimension;
                $newHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) round(($origWidth / $origHeight) * $maxDimension);
            }
        }

        // Buat canvas gambar baru
        $targetImage = imagecreatetruecolor($newWidth, $newHeight);

        // Pertahankan transparansi jika PNG / WebP
        if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_WEBP) {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $origWidth,
            $origHeight
        );

        // Buffer output kompresi (preferensi WebP untuk kompresi terbaik, fallback ke JPEG)
        ob_start();
        $useWebp = function_exists('imagewebp');
        if ($useWebp) {
            imagewebp($targetImage, null, $quality);
            $extension = 'webp';
        } else {
            imagejpeg($targetImage, null, $quality);
            $extension = 'jpg';
        }
        $compressedData = ob_get_clean();

        // Bersihkan memori GD
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        if (empty($compressedData)) {
            return $file->store($directory, 'public');
        }

        // Simpan data terkompresi ke public disk dengan nama unik
        $filename = Str::random(40).'.'.$extension;
        $path = trim($directory, '/').'/'.$filename;

        Storage::disk('public')->put($path, $compressedData);

        return $path;
    }
}
