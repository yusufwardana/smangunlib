<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode Eksemplar</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .label-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .label-box {
            border: 1px dashed #ccc;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            page-break-inside: avoid;
        }
        .school-name { font-weight: bold; font-size: 12px; margin-bottom: 5px; }
        .barcode { margin: 10px 0; }
        .book-title { font-size: 10px; color: #555; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        @media print {
            .no-print { display: none; }
            .label-box { border: 1px solid #000; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: #4361ee; color: white; border: none; border-radius: 5px;">Cetak Sekarang</button>
    </div>

    <div class="label-container">
        @foreach($bukus as $buku)
            @foreach($buku->eksemplar as $eks)
            <div class="label-box">
                <div class="school-name">PERPUSTAKAAN SMAN GUN</div>
                <div class="book-title">{{ Str::limit($buku->judul, 30) }}</div>
                <div class="barcode">
                    {{-- Note: In real app, this renders actual SVG/IMG using Milon/Barcode, e.g. DNS1D::getBarcodeSVG($eks->nomor_barcode, 'C128', 2, 40) --}}
                    <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $eks->nomor_barcode }}&code=Code128&translate-esc=on" alt="Barcode" style="max-height: 40px;">
                </div>
                <div style="font-size: 11px; font-weight: bold;">{{ $eks->nomor_barcode }}</div>
            </div>
            @endforeach
        @endforeach
    </div>
</body>
</html>
