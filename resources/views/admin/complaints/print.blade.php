<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Detail Keluhan - {{ $complaint->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            max-width: 210mm;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #4F46E5;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24px;
            color: #1F2937;
            margin-bottom: 5px;
        }

        .header p {
            color: #6B7280;
            font-size: 14px;
        }

        .complaint-id {
            background: #F3F4F6;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: inline-block;
            font-weight: bold;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #E5E7EB;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 12px;
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #4B5563;
        }

        .info-value {
            color: #1F2937;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-in_progress { background: #DBEAFE; color: #1E40AF; }
        .status-resolved { background: #D1FAE5; color: #065F46; }
        .status-rejected { background: #FEE2E2; color: #991B1B; }

        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .priority-low { background: #F3F4F6; color: #374151; }
        .priority-medium { background: #FEF3C7; color: #92400E; }
        .priority-high { background: #FED7AA; color: #9A3412; }
        .priority-urgent { background: #FEE2E2; color: #991B1B; }

        .description-box {
            background: #F9FAFB;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #4F46E5;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .location-box {
            background: #EFF6FF;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #3B82F6;
        }

        .response-box {
            background: #F0FDF4;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #10B981;
            margin-top: 10px;
        }

        .images-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .image-container {
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .image-container img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .image-label {
            padding: 8px;
            background: #F9FAFB;
            text-align: center;
            font-size: 12px;
            color: #6B7280;
        }

        .resolution-image {
            border: 2px solid #10B981;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 12px;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 80px;
            padding-top: 5px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-before: always;
            }
        }

        .print-button {
            background: #4F46E5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .print-button:hover {
            background: #4338CA;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center;">
        <button class="print-button" onclick="window.print()">🖨️ Print Dokumen</button>
        <button class="print-button" style="background: #6B7280;" onclick="window.close()">✖ Tutup</button>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>DETAIL LAPORAN KELUHAN</h1>
        <p>MyPengaduan</p>
    </div>

    <div class="complaint-id">
        ID Keluhan: #{{ $complaint->id }}
    </div>

    <!-- Informasi Umum -->
    <div class="section">
        <h2 class="section-title">Informasi Keluhan</h2>
        <div class="info-grid">
            <div class="info-label">Judul:</div>
            <div class="info-value"><strong>{{ $complaint->title }}</strong></div>

            <div class="info-label">Kategori:</div>
            <div class="info-value">
                @if($complaint->category)
                    {{ $complaint->category->icon }} {{ $complaint->category->name }}
                @else
                    -
                @endif
            </div>

            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status-badge status-{{ $complaint->status }}">
                    @if($complaint->status === 'pending') Menunggu
                    @elseif($complaint->status === 'in_progress') Sedang Diproses
                    @elseif($complaint->status === 'resolved') Selesai
                    @else Ditolak @endif
                </span>
            </div>

            <div class="info-label">Prioritas:</div>
            <div class="info-value">
                <span class="priority-badge priority-{{ $complaint->priority }}">
                    @if($complaint->priority === 'low') Rendah
                    @elseif($complaint->priority === 'medium') Sedang
                    @elseif($complaint->priority === 'high') Tinggi
                    @else Mendesak @endif
                </span>
            </div>

            <div class="info-label">Tanggal Dibuat:</div>
            <div class="info-value">{{ $complaint->created_at->format('d F Y, H:i') }} WIB</div>

            <div class="info-label">Lama Penanganan:</div>
            <div class="info-value">
                @php
                    $endDate = $complaint->status === 'resolved' ? $complaint->updated_at : now();
                    $diff = $complaint->created_at->diff($endDate);

                    if ($diff->days > 0) {
                        echo $diff->days . ' hari';
                        if ($diff->h > 0) echo ' ' . $diff->h . ' jam';
                    } elseif ($diff->h > 0) {
                        echo $diff->h . ' jam';
                        if ($diff->i > 0) echo ' ' . $diff->i . ' menit';
                    } elseif ($diff->i > 0) {
                        echo $diff->i . ' menit';
                    } else {
                        echo 'Baru saja';
                    }
                @endphp
            </div>
        </div>
    </div>

    <!-- Deskripsi Keluhan -->
    <div class="section">
        <h2 class="section-title">Deskripsi Keluhan</h2>
        <div class="description-box">{{ $complaint->description }}</div>

        @if($complaint->location)
        <div class="location-box">
            <strong>📍 Lokasi:</strong> {{ $complaint->location }}
        </div>
        @endif
    </div>

    <!-- Foto Keluhan -->
    @if($complaint->complaintAttachments && $complaint->complaintAttachments->count() > 0)
    <div class="section">
        <h2 class="section-title">Foto Keluhan</h2>
        <div class="images-grid">
            @foreach($complaint->complaintAttachments as $index => $attachment)
            <div class="image-container">
                <img src="{{ $attachment->file_url }}"
                     alt="Foto keluhan {{ $index + 1 }}"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect width=\'200\' height=\'200\' fill=\'%23ddd\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'14\' fill=\'%23999\'%3EGambar tidak tersedia%3C/text%3E%3C/svg%3E';">
                <div class="image-label">Foto {{ $index + 1 }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Respon Admin -->
    @if($complaint->admin_response)
    <div class="section">
        <h2 class="section-title">Respon Admin</h2>
        <div class="response-box">
            <strong>Tanggapan:</strong><br>
            {{ $complaint->admin_response }}
            <br><br>
            <small style="color: #6B7280;">
                Direspon pada: {{ $complaint->updated_at->format('d F Y, H:i') }} WIB
            </small>
        </div>
    </div>
    @endif

    <!-- Foto Penyelesaian -->
    @if($complaint->status === 'resolved' && $complaint->resolutionAttachments && $complaint->resolutionAttachments->count() > 0)
    <div class="section page-break">
        <h2 class="section-title">✅ Dokumentasi Penyelesaian</h2>
        <div class="images-grid">
            @foreach($complaint->resolutionAttachments as $index => $attachment)
            <div class="image-container resolution-image">
                <img src="{{ $attachment->file_url }}"
                     alt="Foto penyelesaian {{ $index + 1 }}"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect width=\'200\' height=\'200\' fill=\'%23d1fae5\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-size=\'14\' fill=\'%23059669\'%3EGambar tidak tersedia%3C/text%3E%3C/svg%3E';">
                <div class="image-label" style="background: #D1FAE5; color: #065F46;">Penyelesaian {{ $index + 1 }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Informasi Pelapor -->
    <div class="section">
        <h2 class="section-title">Informasi Pelapor</h2>
        <div class="info-grid">
            <div class="info-label">Nama:</div>
            <div class="info-value">{{ $complaint->user->name }}</div>

            <div class="info-label">Email:</div>
            <div class="info-value">{{ $complaint->user->email }}</div>

            @if($complaint->user->phone)
            <div class="info-label">Telepon:</div>
            <div class="info-value">{{ $complaint->user->phone }}</div>
            @endif

            @if($complaint->user->address)
            <div class="info-label">Alamat:</div>
            <div class="info-value">{{ $complaint->user->address }}</div>
            @endif
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Pelapor,</p>
            <div class="signature-line">
                {{ $complaint->user->name }}
            </div>
        </div>
        <div class="signature-box">
            <p>Admin RT,</p>
            <div class="signature-line">
                (.................................)
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak pada {{ now()->format('d F Y, H:i') }} WIB</p>
        <p>MyPengaduan - {{ config('app.name') }}</p>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
