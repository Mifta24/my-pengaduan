<?php

use App\Models\Attachment;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attachments:migrate-resolution-to-cloudinary {--dry-run : Preview changes without updating DB} {--limit=0 : Limit number of records to process} {--delete-local : Delete local file after successful upload}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $limit = (int) $this->option('limit');
    $deleteLocal = (bool) $this->option('delete-local');

    $query = Attachment::query()
        ->where('attachment_type', 'resolution')
        ->where('attachable_type', \App\Models\Complaint::class)
        ->whereNotNull('file_path');

    if ($limit > 0) {
        $query->limit($limit);
    }

    $attachments = $query->get()->filter(function ($attachment) {
        return !filter_var($attachment->file_path, FILTER_VALIDATE_URL);
    })->values();

    if ($attachments->isEmpty()) {
        $this->info('No local resolution attachments found for migration.');
        return;
    }

    $this->info('Found ' . $attachments->count() . ' local resolution attachment(s).');

    $migrated = 0;
    $skipped = 0;
    $failed = 0;

    foreach ($attachments as $attachment) {
        $localPath = $attachment->file_path;

        if (!Storage::disk('public')->exists($localPath)) {
            $this->warn("[SKIP] #{$attachment->id} missing file: {$localPath}");
            $skipped++;
            continue;
        }

        if ($dryRun) {
            $this->line("[DRY-RUN] #{$attachment->id} {$localPath}");
            $skipped++;
            continue;
        }

        try {
            $absolutePath = Storage::disk('public')->path($localPath);

            $uploaded = Cloudinary::uploadApi()->upload($absolutePath, [
                'folder' => 'complaints/resolutions',
                'resource_type' => 'image',
                'transformation' => [
                    'quality' => 'auto:good',
                    'fetch_format' => 'auto',
                ],
            ]);

            $newUrl = $uploaded['secure_url'] ?? null;

            if (!$newUrl) {
                throw new \RuntimeException('Cloudinary secure_url is empty');
            }

            $attachment->update([
                'file_path' => $newUrl,
                'file_size' => $uploaded['bytes'] ?? $attachment->file_size,
            ]);

            if ($deleteLocal) {
                Storage::disk('public')->delete($localPath);
            }

            $this->info("[OK] #{$attachment->id} migrated");
            $migrated++;
        } catch (\Throwable $e) {
            $this->error("[FAIL] #{$attachment->id} {$e->getMessage()}");
            $failed++;
        }
    }

    $this->newLine();
    $this->info("Done. migrated={$migrated}, skipped={$skipped}, failed={$failed}");
})->purpose('Migrate old local resolution attachments to Cloudinary URLs');
