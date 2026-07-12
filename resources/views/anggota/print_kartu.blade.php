<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Anggota Perpustakaan</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background-color: #f8f9fa; }
        .page-container {
            /* Kertas A4 */
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            background: white;
            padding: 10mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            gap: 15px;
        }
        .card-pvc {
            /* Standar CR80 (ID Card) size: 85.6mm x 53.98mm */
            width: 86mm;
            height: 54mm;
            border: 1px dashed #ccc;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            page-break-inside: avoid;
            box-sizing: border-box;
        }
        .card-header {
            text-align: center;
            padding: 5px;
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            text-transform: uppercase;
        }
        .card-body {
            padding: 10px;
            display: flex;
            gap: 10px;
        }
        .card-photo {
            width: 25mm;
            height: 30mm;
            border-radius: 4px;
            background-color: white;
            object-fit: cover;
            border: 2px solid white;
        }
        .card-info {
            flex-grow: 1;
        }
        .card-name { font-size: 11px; font-weight: bold; margin-bottom: 2px; }
        .card-text { font-size: 8px; color: rgba(255,255,255,0.8); margin-bottom: 1px; }
        .qr-code {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: white;
            padding: 3px;
            border-radius: 4px;
        }
        @media print {
            body { background: none; padding: 0; }
            .page-container { box-shadow: none; margin: 0; padding: 0; }
            .no-print { display: none; }
            .card-pvc { border: 0.5px solid #000; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4361ee; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Cetak Kartu (A4)</button>
        <p style="color: #666; font-size: 12px; margin-top: 5px;">Rekomendasi cetak menggunakan kertas Art Carton 260gsm</p>
    </div>

    <div class="page-container">
        @foreach($anggotas as $anggota)
        <div class="card-pvc">
            <div class="card-header">
                Perpustakaan SMAN GUN
            </div>
            <div class="card-body">
                <img src="{{ $anggota->foto_url }}" class="card-photo" alt="Foto">
                <div class="card-info">
                    <div class="card-name">{{ strtoupper(Str::limit($anggota->user->name, 20)) }}</div>
                    <div class="card-text text-uppercase">{{ $anggota->tipe_anggota }}</div>
                    <div class="card-text">NIM/NIP: {{ $anggota->no_identitas }}</div>
                    <div class="card-text">Berlaku s/d: {{ $anggota->masa_berlaku_sampai->format('m/Y') }}</div>
                    
                    <div style="margin-top: 5px; font-size: 10px; font-weight: bold;">{{ $anggota->nomor_anggota }}</div>
                </div>
            </div>
            <div class="qr-code">
                <!-- Fallback to QR API for preview. Real app will use SimpleQRCode package -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=45x45&data={{ $anggota->nomor_anggota }}" width="45" height="45">
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
