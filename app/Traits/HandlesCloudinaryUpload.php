<?php

namespace App\Traits;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesCloudinaryUpload
{
    /**
     * Upload photo to Cloudinary with compression
     *
     * @param UploadedFile $file
     * @param string $folder Folder in Cloudinary (e.g., 'complaints/photos')
     * @param int $maxWidth Maximum width for resizing
     * @param int $quality Image quality (1-100)
     * @return array ['path' => 'cloudinary_public_id', 'url' => 'cloudinary_url']
     */
    protected function uploadToCloudinary(
        UploadedFile $file,
        string $folder = 'complaints',
        int $maxWidth = 1920,
        int $quality = 85
    ): array {
        $extension = strtolower($file->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);

        // Compress image before upload if it's an image
        if ($isImage) {
            $image = Image::read($file->getRealPath());

            // Resize if larger than max width
            if ($image->width() > $maxWidth) {
                $image->scale(width: $maxWidth);
            }

            // Encode based on format
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $encodedImage = $image->toJpeg(quality: $quality);
            } elseif ($extension === 'webp') {
                $encodedImage = $image->toWebp(quality: $quality);
            } elseif ($extension === 'png') {
                $encodedImage = $image->toPng();
            } else {
                $encodedImage = $image->encode();
            }

            // Create temporary file for Cloudinary upload
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.' . $extension;
            file_put_contents($tempPath, (string) $encodedImage);

            try {
                // Upload to Cloudinary
                $uploadedFile = Cloudinary::upload($tempPath, [
                    'folder' => $folder,
                    'resource_type' => 'image',
                    'transformation' => [
                        'quality' => 'auto:good',
                        'fetch_format' => 'auto',
                    ]
                ]);
            } finally {
                // Clean up temp file
                @unlink($tempPath);
            }
        } else {
            // Upload non-image files directly
            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'raw',
            ]);
        }

        return [
            'path' => $uploadedFile->getPublicId(),
            'url' => $uploadedFile->getSecurePath(),
            'size' => $uploadedFile->getReadableSize(),
        ];
    }

    /**
     * Delete file from Cloudinary
     *
     * @param string $publicId Cloudinary public ID
     * @return bool
     */
    protected function deleteFromCloudinary(string $publicId): bool
    {
        try {
            Cloudinary::destroy($publicId);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to delete from Cloudinary: ' . $e->getMessage(), [
                'public_id' => $publicId
            ]);
            return false;
        }
    }

    /**
     * Check if Cloudinary is configured and enabled
     *
     * @return bool
     */
    protected function isCloudinaryEnabled(): bool
    {
        return config('filesystems.default') === 'cloudinary'
            && !empty(config('filesystems.disks.cloudinary.url'));
    }

    /**
     * Get storage method (cloudinary or local)
     *
     * @return string
     */
    protected function getStorageDriver(): string
    {
        return $this->isCloudinaryEnabled() ? 'cloudinary' : 'public';
    }
}
