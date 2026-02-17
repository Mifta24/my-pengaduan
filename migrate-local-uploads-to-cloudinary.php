<?php

use App\Models\Complaint;
use App\Models\Attachment;
use App\Models\User;
use App\Models\Announcement;
use App\Traits\HandlesCloudinaryUpload;
use Illuminate\Support\Str;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var HandlesCloudinaryUpload $uploader */
$uploader = new class {
    use HandlesCloudinaryUpload;
};

function migrateComplaintPhotos($uploader)
{
    $complaints = Complaint::whereNotNull('photo')
        ->where('photo', 'NOT LIKE', 'http%')
        ->get();

    foreach ($complaints as $complaint) {
        echo "Migrating complaint photo ID {$complaint->id}..." . PHP_EOL;

        $localPath = storage_path('app/public/' . $complaint->photo);
        if (!file_exists($localPath)) {
            echo "  Skipped, file not found: {$localPath}" . PHP_EOL;
            continue;
        }

        $file = new Illuminate\Http\UploadedFile(
            $localPath,
            basename($localPath),
            mime_content_type($localPath),
            null,
            true
        );

        $upload = $uploader->uploadToCloudinary($file, 'complaints/photos', 1920, 85);
        $complaint->update(['photo' => $upload['url']]);

        echo "  -> Migrated to {$upload['url']}" . PHP_EOL;
    }
}

function migrateAttachments($uploader)
{
    $attachments = Attachment::whereNotNull('file_path')
        ->where('file_path', 'NOT LIKE', 'http%')
        ->get();

    foreach ($attachments as $attachment) {
        echo "Migrating attachment ID {$attachment->id}..." . PHP_EOL;

        $localPath = storage_path('app/public/' . $attachment->file_path);
        if (!file_exists($localPath)) {
            echo "  Skipped, file not found: {$localPath}" . PHP_EOL;
            continue;
        }

        $folder = str_starts_with($attachment->attachment_type, 'resolution')
            ? 'responses/attachments'
            : 'complaints/attachments';

        $file = new Illuminate\Http\UploadedFile(
            $localPath,
            basename($localPath),
            mime_content_type($localPath),
            null,
            true
        );

        $upload = $uploader->uploadToCloudinary($file, $folder, 1920, 85);
        $attachment->update(['file_path' => $upload['url']]);

        echo "  -> Migrated to {$upload['url']}" . PHP_EOL;
    }
}

function migrateUserKtp($uploader)
{
    $users = User::whereNotNull('ktp_path')
        ->where('ktp_path', 'NOT LIKE', 'http%')
        ->get();

    foreach ($users as $user) {
        echo "Migrating user KTP user_id {$user->id}..." . PHP_EOL;

        $localPath = storage_path('app/public/' . $user->ktp_path);
        if (!file_exists($localPath)) {
            echo "  Skipped, file not found: {$localPath}" . PHP_EOL;
            continue;
        }

        $file = new Illuminate\Http\UploadedFile(
            $localPath,
            basename($localPath),
            mime_content_type($localPath),
            null,
            true
        );

        $upload = $uploader->uploadToCloudinary($file, 'ktp/photos', 1920, 85);
        $user->update(['ktp_path' => $upload['url']]);

        echo "  -> Migrated to {$upload['url']}" . PHP_EOL;
    }
}

function migrateAnnouncementAttachments($uploader)
{
    $announcements = Announcement::whereNotNull('attachments')->get();

    foreach ($announcements as $announcement) {
        $attachments = $announcement->attachments ?? [];
        $changed = false;

        foreach ($attachments as &$attachment) {
            if (!is_array($attachment) || empty($attachment['path'])) {
                continue;
            }

            $path = $attachment['path'];

            // Skip if already a full URL (Cloudinary)
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                continue;
            }

            $localPath = storage_path('app/public/' . $path);
            if (!file_exists($localPath)) {
                echo "Skipped announcement {$announcement->id} attachment, file not found: {$localPath}" . PHP_EOL;
                continue;
            }

            echo "Migrating announcement {$announcement->id} attachment {$path}..." . PHP_EOL;

            $file = new Illuminate\Http\UploadedFile(
                $localPath,
                basename($localPath),
                mime_content_type($localPath),
                null,
                true
            );

            $upload = $uploader->uploadToCloudinary($file, 'announcements/attachments', 1920, 85);
            $attachment['path'] = $upload['url'];
            $changed = true;

            echo "  -> Migrated to {$upload['url']}" . PHP_EOL;
        }

        unset($attachment);

        if ($changed) {
            $announcement->attachments = $attachments;
            $announcement->save();
        }
    }
}

echo "Starting migration of local uploads to Cloudinary..." . PHP_EOL;

migrateAnnouncementImages($uploader);
migrateComplaintPhotos($uploader);
migrateAttachments($uploader);
migrateUserKtp($uploader);
migrateAnnouncementAttachments($uploader);

echo "Migration completed." . PHP_EOL;
