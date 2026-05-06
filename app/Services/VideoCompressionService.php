<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VideoCompressionService
{
    private int $targetSizeMB;
    private ?string $ffmpegBin = null;
    private ?string $ffprobeBin = null;

    public function __construct(int $targetSizeMB = 40)
    {
        $this->targetSizeMB = $targetSizeMB;
    }

    /**
     * Compress video from $inputPath and save result to $outputPath.
     * $inputPath and $outputPath are absolute filesystem paths.
     * Returns true on success, false if FFmpeg unavailable or compression fails.
     */
    public function compress(string $inputPath, string $outputPath): bool
    {
        if (!$this->detectBinaries()) {
            Log::warning('VideoCompressionService: FFmpeg not found, skipping compression.');
            return false;
        }

        $duration = $this->getDuration($inputPath);
        if ($duration <= 0) {
            Log::warning('VideoCompressionService: Could not determine video duration.', ['path' => $inputPath]);
            return false;
        }

        $videoBitrateKbps = $this->calculateBitrate($duration);
        $tmpPath = $outputPath . '.tmp.mp4';

        $cmd = sprintf(
            '%s -i %s -c:v libx264 -b:v %dk -c:a aac -b:a 128k -movflags +faststart -y %s 2>&1',
            escapeshellarg($this->ffmpegBin),
            escapeshellarg($inputPath),
            $videoBitrateKbps,
            escapeshellarg($tmpPath)
        );

        @set_time_limit(0);
        $output = shell_exec($cmd);

        if (!file_exists($tmpPath) || filesize($tmpPath) === 0) {
            Log::error('VideoCompressionService: Compression failed.', ['output' => $output]);
            @unlink($tmpPath);
            return false;
        }

        // Replace output path with compressed result
        if (file_exists($outputPath) && $outputPath !== $inputPath) {
            @unlink($outputPath);
        }
        rename($tmpPath, $outputPath);

        Log::info('VideoCompressionService: Compressed successfully.', [
            'input_size' => filesize($inputPath),
            'output_size' => filesize($outputPath),
        ]);

        return true;
    }

    public function needsCompression(string $absolutePath): bool
    {
        return file_exists($absolutePath)
            && filesize($absolutePath) > $this->targetSizeMB * 1024 * 1024;
    }

    public function isAvailable(): bool
    {
        return $this->detectBinaries();
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function calculateBitrate(float $duration): int
    {
        $targetBits = $this->targetSizeMB * 8 * 1024 * 1024;
        $audioBits  = 128 * 1000 * $duration; // 128 kbps audio track
        $videoBits  = $targetBits - $audioBits;
        return max(100, (int)($videoBits / $duration / 1000));
    }

    private function getDuration(string $filePath): float
    {
        if (!$this->ffprobeBin) {
            return 0;
        }

        $cmd    = sprintf(
            '%s -v quiet -show_entries format=duration -of csv=p=0 %s 2>&1',
            escapeshellarg($this->ffprobeBin),
            escapeshellarg($filePath)
        );
        $output = shell_exec($cmd);
        return (float) trim((string) $output);
    }

    private function detectBinaries(): bool
    {
        if ($this->ffmpegBin) {
            return true;
        }

        $candidates = array_filter([
            config('ffmpeg.ffmpeg_binaries'),
            'ffmpeg',
            '/usr/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
            'C:\\laragon\\bin\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\ProgramData\\chocolatey\\bin\\ffmpeg.exe',
        ]);

        foreach ($candidates as $bin) {
            $check = @shell_exec(sprintf('%s -version 2>&1', escapeshellarg($bin)));
            if ($check && str_contains((string) $check, 'ffmpeg version')) {
                $this->ffmpegBin = $bin;
                $probeBin = str_replace('ffmpeg', 'ffprobe', $bin);
                $probeCheck = @shell_exec(sprintf('%s -version 2>&1', escapeshellarg($probeBin)));
                if ($probeCheck && str_contains((string) $probeCheck, 'ffprobe version')) {
                    $this->ffprobeBin = $probeBin;
                }
                return true;
            }
        }

        return false;
    }
}