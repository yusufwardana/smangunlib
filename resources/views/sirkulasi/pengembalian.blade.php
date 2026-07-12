@extends('layouts.app')

@section('title', 'Proses Pengembalian')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('sirkulasi.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">Proses Pengembalian Buku</h3>
            <p class="text-muted mb-0">Cari transaksi berdasarkan No. Transaksi (TRX).</p>
        </div>
    </div>

    <!-- Pengecekan Transaksi -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
        <div class="card-body p-4">
            <form action="" method="GET" class="d-flex gap-3 align-items-end">
                <div class="flex-grow-1">
                    <label class="form-label fw-bold">Nomor Transaksi (Scan Barcode Struk / Ketik Manual)</label>
                    <input type="text" name="trx" class="form-control form-control-lg" placeholder="TRX-2026..." value="{{ $nomor_transaksi }}" autofocus>
                </div>
                <button type="button" class="btn btn-primary btn-lg px-4" onclick="window.location.href='{{ url('sirkulasi/pengembalian') }}/' + $('input[name=trx]').val()">
                    <i class="fa-solid fa-search"></i> Cari Data
                </button>
            </form>
        </div>
    </div>

    @if($peminjaman)
    <form action="{{ route('sirkulasi.pengembalian.proses', $peminjaman->id) }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Info Transaksi -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Informasi Transaksi</h6>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">No. Transaksi</span> 
                                <span class="fw-bold text-primary">{{ $peminjaman->nomor_transaksi }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Peminjam</span> 
                                <span class="fw-bold text-end">{{ $peminjaman->anggota->user->name }}<br>{{ $peminjaman->anggota->nomor_anggota }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Tgl. Pinjam</span> 
                                <span class="fw-bold">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between">
                                <span class="text-muted">Jatuh Tempo</span> 
                                <span class="fw-bold">{{ $peminjaman->due_date->format('d M Y') }}</span>
                            </li>
                        </ul>

                        @php
                            $terlambat = \Carbon\Carbon::now()->startOfDay()->diffInDays($peminjaman->due_date, false);
                            $isTerlambat = $terlambat < 0;
                            $hariTerlambat = $isTerlambat ? abs($terlambat) : 0;
                        @endphp

                        <div class="mt-4 p-3 rounded {{ $isTerlambat ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                            <h6 class="fw-bold mb-1"><i class="fa-solid fa-clock me-1"></i> Status Waktu</h6>
                            @if($isTerlambat)
                                <p class="mb-0 small">Transaksi ini terlambat <strong>{{ $hariTerlambat }} hari</strong> dari tenggat waktu. Denda akan otomatis dikenakan Rp1.000/hari per buku.</p>
                            @else
                                <p class="mb-0 small">Pengembalian tepat waktu. Sisa waktu {{ $terlambat }} hari.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Buku -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Eksekusi Pengembalian (Checklist Buku)</h6>
                        
                        <div class="table-responsive">
                            <table class="table align-middle border">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center"><i class="fa-solid fa-check-double"></i></th>
                                        <th width="20%">Barcode</th>
                                        <th width="45%">Judul Buku</th>
                                        <th width="30%">Kondisi Pengembalian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($peminjaman->detailPeminjaman as $detail)
                                    @if($detail->status == 'dipinjam')
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input check-kembali" type="checkbox" name="detail[{{ $detail->id }}][kembalikan]" value="1" checked style="transform: scale(1.5);">
                                            </div>
                                        </td>
                                        <td><span class="fw-bold text-primary">{{ $detail->eksemplar->nomor_barcode }}</span></td>
                                        <td>{{ $detail->eksemplar->buku->judul }}</td>
                                        <td>
                                            <select name="detail[{{ $detail->id }}][kondisi_kembali]" class="form-select form-select-sm select-kondisi">
                                                <option value="baik">Kondisi Baik</option>
                                                <option value="rusak">Buku Rusak (Denda)</option>
                                                <option value="hilang">Buku Hilang (Ganti Rugi)</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 shadow fw-bold">
                                <i class="fa-solid fa-check me-2"></i> Proses Pengembalian
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @elseif($nomor_transaksi)
        <!-- Jika diketik tapi gak ketemu -->
        <div class="text-center mt-5">
            <i class="fa-solid fa-file-circle-xmark text-muted mb-3" style="font-size: 4rem;"></i>
            <h5 class="fw-bold">Transaksi Tidak Ditemukan</h5>
            <p class="text-muted">Nomor transaksi salah, atau seluruh buku pada transaksi ini sudah dikembalikan sebelumnya.</p>
        </div>
    @endif
</div>
@endsection
