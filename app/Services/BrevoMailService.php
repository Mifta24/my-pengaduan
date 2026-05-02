<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    public function __construct()
    {
        $this->apiKey = config('services.brevo.api_key');
    }

    /**
     * Kirim email OTP reset password via Brevo API.
     */
    public function sendOtpEmail(string $toEmail, string $toName, string $otp): void
    {
        $html = view('emails.otp_email', [
            'otp'      => $otp,
            'userName' => $toName,
        ])->render();

        $response = Http::withHeaders([
            'api-key'      => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'sender'      => [
                'name'  => config('mail.from.name'),
                'email' => config('mail.from.address'),
            ],
            'to'          => [
                ['email' => $toEmail, 'name' => $toName],
            ],
            'subject'     => '[MyPengaduan] Kode OTP Reset Password Anda',
            'htmlContent' => $html,
        ]);

        if ($response->failed()) {
            Log::error('Brevo API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Gagal mengirim email via Brevo API: ' . $response->body());
        }
    }
}
