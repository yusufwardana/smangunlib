@php
    // Cek apakah dipanggil dari fungsi Export Excel atau Print PDF/Web
    $isExcel = isset($isExcel) ? $isExcel : false;
    $isHtmlPreview = isset($isHtmlPreview) ? $isHtmlPreview : false;
@endphp

@if(!$isExcel && !$isHtmlPreview)
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judulLaporan }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        @media print {
            @page { margin: 1.5cm; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
@endif

@if(!$isHtmlPreview)
    <div class="header">
        <h2>{{ config('app.name', 'PERPUSTAKAAN SMAN GUN') }}</h2>
        <h3>{{ $judulLaporan }}</h3>
        <p>Periode: {{ $start ? $start->format('d M Y') : '-' }} s/d {{ $end ? $end->format('d M Y') : '-' }}</p>
    </div>
@endif

<table class="{{ $isHtmlPreview ? 'table table-bordered table-striped' : '' }}">
    <thead>
        <!-- Render Kolom Secara Dinamis Berdasarkan Tipe -->
        <tr>
            <th>No</th>
            
            @if($tipe == 'koleksi')
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun</th>
                <th>Total Eksemplar</th>
            
            @elseif($tipe == 'anggota')
                <th>Nomor Anggota</th>
                <th>Nama Lengkap</th>
                <th>Tipe</th>
                <th>Email</th>
            
            @elseif($tipe == 'peminjaman')
                <th>Tanggal Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Anggota</th>
                <th>Status Transaksi</th>
            
            @elseif($tipe == 'denda')
                <th>Tanggal Peminjaman</th>
                <th>Anggota</th>
                <th>Jumlah Denda</th>
                <th>Status Pembayaran</th>
            
            @elseif($tipe == 'pengunjung')
                <th>Tanggal Kunjungan</th>
                <th>Nama</th>
                <th>Tipe / No. Identitas</th>
                <th>Tujuan Kunjungan</th>
                
            @elseif($tipe == 'inventaris' || $tipe == 'sarana_prasarana')
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Kondisi</th>
                <th>Jumlah</th>
            
            @elseif($tipe == 'literasi')
                <th>Nama Program</th>
                <th>Periode</th>
                <th>Target Buku</th>
                <th>Jumlah Peserta</th>
                <th>Status</th>
            
            @elseif($tipe == 'evaluasi')
                <th>Tanggal Upload</th>
                <th>Judul Dokumen</th>
                <th>Versi</th>
                <th>Status</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse($data as $idx => $row)
        <tr>
            <td>{{ $idx + 1 }}</td>

            @if($tipe == 'koleksi')
                <td>{{ $row->judul }}</td>
                <td>{{ $row->pengarang }}</td>
                <td>{{ $row->penerbit }}</td>
                <td>{{ $row->tahun_terbit }}</td>
                <td style="text-align: center;">{{ $row->eksemplar_count }}</td>

            @elseif($tipe == 'anggota')
                <td>{{ $row->nomor_anggota }}</td>
                <td>{{ $row->user->name ?? '-' }}</td>
                <td>{{ ucfirst($row->tipe_anggota) }}</td>
                <td>{{ $row->user->email ?? '-' }}</td>

            @elseif($tipe == 'peminjaman')
                <td>{{ \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') }}</td>
                <td>{{ $row->anggota->user->name ?? '-' }}</td>
                <td>{{ strtoupper($row->status) }}</td>

            @elseif($tipe == 'denda')
                <td>{{ \Carbon\Carbon::parse($row->peminjaman->tanggal_pinjam ?? $row->created_at)->format('d/m/Y') }}</td>
                <td>{{ $row->peminjaman->anggota->user->name ?? '-' }}</td>
                <td>Rp {{ number_format($row->jumlah_denda, 0, ',', '.') }}</td>
                <td>{{ strtoupper(str_replace('_', ' ', $row->status_pembayaran)) }}</td>

            @elseif($tipe == 'pengunjung')
                <td>{{ \Carbon\Carbon::parse($row->tanggal_kunjungan)->format('d/m/Y H:i') }}</td>
                <td>{{ $row->nama }}</td>
                <td>{{ ucfirst($row->tipe_pengunjung) }} ({{ $row->no_identitas ?: '-' }})</td>
                <td>{{ $row->tujuan_kunjungan }}</td>

            @elseif($tipe == 'inventaris' || $tipe == 'sarana_prasarana')
                <td>{{ $row->nomor_inventaris }}</td>
                <td>{{ $row->nama_barang }}</td>
                <td>{{ $row->kategori_barang }}</td>
                <td>{{ strtoupper(str_replace('_', ' ', $row->kondisi)) }}</td>
                <td style="text-align: center;">{{ $row->jumlah }}</td>

            @elseif($tipe == 'literasi')
                <td>{{ $row->nama_program }}</td>
                <td>{{ \Carbon\Carbon::parse($row->periode_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($row->periode_selesai)->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $row->target_baca }}</td>
                <td style="text-align: center;">{{ $row->peserta_count }}</td>
                <td>{{ strtoupper($row->status) }}</td>

            @elseif($tipe == 'evaluasi')
                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>
                <td>{{ $row->judul }}</td>
                <td>{{ $row->versi }}</td>
                <td>{{ strtoupper($row->status) }}</td>
            @endif
        </tr>
        @empty
        <tr>
            <td colspan="10" style="text-align: center; padding: 20px;">Tidak ada data pada periode dan filter ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@if(!$isExcel && !$isHtmlPreview)
    <div style="margin-top: 50px; text-align: right; width: 100%;">
        <div style="float: right; width: 250px; text-align: center;">
            <p>Kota, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="margin-bottom: 70px;">Kepala Perpustakaan</p>
            <p style="font-weight: bold; text-decoration: underline;">_________________________</p>
            <p>NIP. -</p>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
@endif
