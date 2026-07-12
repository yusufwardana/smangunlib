<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Peminjaman #{{ $peminjaman->nomor_transaksi }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; margin: 0; padding: 20px; background: #eee; }
        .receipt-container {
            width: 80mm;
            background: white;
            padding: 5mm;
            margin: auto;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
            color: #000;
        }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-3 { margin-bottom: 15px; }
        .border-dashed { border-bottom: 1px dashed #000; margin: 10px 0; }
        .item-list { font-size: 11px; margin-bottom: 5px; }
        .small { font-size: 10px; }
        @media print {
            body { background: none; padding: 0; }
            .receipt-container { box-shadow: none; margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-3">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">Cetak Struk Thermal</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Tutup Halaman</button>
    </div>

    <div class="receipt-container">
        <div class="text-center">
            <h3 class="mb-1 fw-bold" style="margin-top: 0;">SMAN GUN LIB</h3>
            <div class="small">Sistem Informasi Perpustakaan</div>
            <div class="small">Jl. Pendidikan No. 1, Jakarta</div>
        </div>

        <div class="border-dashed"></div>
        
        <div class="small mb-1">TRX   : {{ $peminjaman->nomor_transaksi }}</div>
        <div class="small mb-1">TGL   : {{ $peminjaman->tanggal_pinjam->format('d/m/Y H:i') }}</div>
        <div class="small mb-1">NAMA  : {{ strtoupper(Str::limit($peminjaman->anggota->user->name, 20)) }}</div>
        <div class="small mb-1">PETUGAS: {{ strtoupper(Str::limit($peminjaman->pustakawan->name ?? 'SYSTEM', 15)) }}</div>

        <div class="border-dashed"></div>
        
        <div class="fw-bold small mb-1">BUKU DIPINJAM:</div>
        
        @foreach($peminjaman->detailPeminjaman as $idx => $detail)
        <div class="item-list">
            {{ $idx+1 }}. {{ Str::limit($detail->eksemplar->buku->judul, 25) }}
            <br>
            <span style="padding-left: 10px;">ID: {{ $detail->eksemplar->nomor_barcode }}</span>
        </div>
        @endforeach

        <div class="border-dashed"></div>

        <div class="small text-center fw-bold">
            JATUH TEMPO PENGEMBALIAN:<br>
            <span style="font-size: 14px;">{{ $peminjaman->due_date->format('d M Y') }}</span>
        </div>

        <div class="border-dashed"></div>
        
        <div class="small text-center" style="font-size: 9px; line-height: 1.2;">
            Denda keterlambatan Rp1.000/hari/buku.<br>
            Harap simpan struk ini sebagai bukti peminjaman yang sah.<br>
            <br>
            -- Terima Kasih --
        </div>
    </div>
</body>
</html>
