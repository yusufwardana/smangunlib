@extends('layouts.app')

@section('title', 'Profil Anggota')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('anggota.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0">Profil Anggota</h3>
            </div>
        </div>
        <div class="d-flex gap-2">
            <!-- Form hidden untuk cetak 1 kartu -->
            <form action="{{ route('anggota.print_kartu') }}" method="POST" target="_blank" class="d-inline">
                @csrf
                <input type="hidden" name="ids" value="{{ $anggota->id }}">
                <button type="submit" class="btn btn-dark rounded-pill shadow-sm px-4">
                    <i class="fa-solid fa-id-card me-2"></i> Cetak Kartu
                </button>
            </form>
            <a href="{{ route('anggota.edit', $anggota->id) }}" class="btn btn-warning text-white rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-pen-to-square me-2"></i> Edit Data
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- ID Card Mini Preview -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white" style="background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);">
                <div class="card-body p-4 text-center">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold small text-white-50">PERPUSTAKAAN SMAN GUN</span>
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <img src="{{ $anggota->foto_url }}" alt="Foto" class="img-thumbnail rounded-circle mb-3 shadow" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid white;">
                    <h5 class="fw-bold mb-0">{{ $anggota->user->name }}</h5>
                    <p class="mb-2 text-white-50 text-uppercase" style="font-size: 0.8rem;">{{ $anggota->tipe_anggota }} | {{ $anggota->nomor_anggota }}</p>
                    
                    <div class="bg-white p-2 rounded mx-auto mt-3" style="width: 80px; height: 80px;">
                        <!-- QR Code mock preview using external API, real app uses simple-qrcode in print layout -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ $anggota->nomor_anggota }}" width="100%">
                    </div>
                </div>
            </div>

            <!-- Demographics -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Informasi Kontak</h6>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item px-0"><i class="fa-solid fa-id-badge text-muted me-2"></i> <strong>NIS/NIP:</strong> <br> {{ $anggota->no_identitas }}</li>
                        <li class="list-group-item px-0"><i class="fa-solid fa-envelope text-muted me-2"></i> <strong>Email:</strong> <br> {{ $anggota->user->email }}</li>
                        <li class="list-group-item px-0"><i class="fa-solid fa-phone text-muted me-2"></i> <strong>Telepon:</strong> <br> {{ $anggota->no_telepon ?? '-' }}</li>
                        <li class="list-group-item px-0"><i class="fa-solid fa-map-location-dot text-muted me-2"></i> <strong>Alamat:</strong> <br> {{ $anggota->alamat }}</li>
                        <li class="list-group-item px-0"><i class="fa-solid fa-calendar-check text-muted me-2"></i> <strong>Berlaku Sampai:</strong> <br> {{ $anggota->masa_berlaku_sampai->format('d M Y') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Riwayat Peminjaman -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-4 pb-2">
                    <h5 class="fw-bold mb-0">Riwayat Peminjaman Buku</h5>
                </div>
                <div class="card-body p-4">
                    @if($anggota->peminjaman->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. TRX</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Buku (Eksemplar)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anggota->peminjaman as $trx)
                                    <tr>
                                        <td><span class="fw-bold text-primary">{{ $trx->nomor_transaksi }}</span></td>
                                        <td>{{ $trx->tanggal_pinjam->format('d/m/Y') }}</td>
                                        <td>{{ $trx->due_date->format('d/m/Y') }}</td>
                                        <td>
                                            @if($trx->status == 'dipinjam')
                                                <span class="badge bg-primary-subtle text-primary">Sedang Dipinjam</span>
                                            @elseif($trx->status == 'dikembalikan')
                                                <span class="badge bg-success-subtle text-success">Selesai</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">Terlambat</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="mb-0 ps-3 small">
                                                @foreach($trx->detailPeminjaman as $detail)
                                                    <li>{{ Str::limit($detail->eksemplar->buku->judul, 25) }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted p-5 bg-light rounded">
                            <i class="fa-solid fa-clock-rotate-left fs-1 mb-3"></i>
                            <p>Belum ada riwayat peminjaman yang dilakukan oleh anggota ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
