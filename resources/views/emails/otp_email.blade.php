<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }
        .wrapper {
            max-width: 560px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #1a7a4a 0%, #25a265 100%);
            padding: 40px 32px;
            text-align: center;
        }
        .header .logo-icon {
            width: 56px;
            height: 56px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .header p {
            color: rgba(255,255,255,0.85);
            font-size: 14px;
            margin-top: 4px;
        }
        .body {
            padding: 36px 32px;
        }
        .greeting {
            font-size: 16px;
            color: #374151;
            margin-bottom: 16px;
        }
        .greeting strong { color: #1a7a4a; }
        .info-text {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .otp-box {
            background: linear-gradient(135deg, #f0faf4 0%, #e8f5ee 100%);
            border: 2px solid #a7f3c2;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin-bottom: 28px;
        }
        .otp-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #1a7a4a;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .otp-code {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: 12px;
            color: #1a7a4a;
            font-family: 'Courier New', monospace;
            line-height: 1;
        }
        .otp-validity {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
        }
        .otp-validity span {
            color: #dc2626;
            font-weight: 600;
        }
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 24px 0;
        }
        .warning-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            padding: 14px 16px;
            margin-bottom: 20px;
        }
        .warning-box p {
            font-size: 13px;
            color: #92400e;
            line-height: 1.5;
        }
        .warning-box strong { color: #78350f; }
        .footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 24px 32px;
            text-align: center;
        }
        .footer p {
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.6;
        }
        .footer .app-name {
            color: #1a7a4a;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Header -->
        <div class="header">
            <div class="logo-icon">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>
            <h1>Reset Password</h1>
            <p>MyPengaduan — Sistem Pengaduan Warga</p>
        </div>

        <!-- Body -->
        <div class="body">
            <p class="greeting">Halo, <strong>{{ $userName }}</strong> 👋</p>
            <p class="info-text">
                Kami menerima permintaan untuk mereset password akun MyPengaduan Anda.
                Gunakan kode OTP berikut untuk melanjutkan proses reset password.
            </p>

            <!-- OTP Box -->
            <div class="otp-box">
                <p class="otp-label">Kode Verifikasi OTP</p>
                <p class="otp-code">{{ $otp }}</p>
                <p class="otp-validity">Berlaku selama <span>10 menit</span> sejak email ini dikirim</p>
            </div>

            <div class="warning-box">
                <p>
                    ⚠️ <strong>Jangan bagikan kode ini kepada siapapun.</strong>
                    Tim MyPengaduan tidak akan pernah meminta kode OTP Anda.
                    Jika Anda tidak merasa meminta reset password, abaikan email ini.
                </p>
            </div>

            <div class="divider"></div>

            <p class="info-text">
                Kode ini hanya dapat digunakan satu kali. Setelah berhasil diverifikasi,
                kode akan otomatis tidak berlaku.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Email ini dikirim otomatis oleh sistem <span class="app-name">MyPengaduan</span>.<br>
                Harap tidak membalas email ini.
            </p>
            <p style="margin-top: 8px;">© {{ date('Y') }} MyPengaduan — Warga Gang Annur 2 RT 05</p>
        </div>
    </div>
</body>
</html>
