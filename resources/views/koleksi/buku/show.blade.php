@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('koleksi.buku.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0">Detail Katalog Buku</h3>
            </div>
        </div>
        <div>
            <a href="{{ route('koleksi.buku.edit', $buku->id) }}" class="btn btn-warning text-white rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-pen-to-square me-2"></i> Edit Katalog
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Buku Detail -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    <img src="{{ $buku->cover_url }}" alt="Cover" class="img-fluid rounded shadow-sm mb-4" style="max-height: 250px;">
                    <h5 class="fw-bold mb-1">{{ $buku->judul }}</h5>
                    <p class="text-muted mb-3">{{ $buku->pengarang }}</p>

                    @if($buku->is_digital)
                        <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill"><i class="fa-solid fa-laptop me-1"></i> E-Book Tersedia</span>
                    @endif

                    <hr class="my-4">

                    <ul class="list-group list-group-flush text-start small">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Penerbit</span> <span class="fw-bold">{{ $buku->penerbit }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Tahun</span> <span class="fw-bold">{{ $buku->tahun_terbit }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">ISBN</span> <span class="fw-bold">{{ $buku->isbn ?? '-' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Rak</span> <span class="fw-bold">{{ $buku->rakLokasi->nama_lokasi ?? 'Tidak Ada Rak' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Eksemplar Fisik & Ebook -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-4 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Eksemplar Fisik & Barcode</h5>
                    <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="fa-solid fa-plus me-1"></i> Tambah Eksemplar</button>
                </div>
                <div class="card-body p-4">
                    
                    @if($buku->eksemplar->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Barcode</th>
                                    <th>Kondisi</th>
                                    <th>Status</th>
                                    <th>Tgl Pengadaan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($buku->eksemplar as $eks)
                                <tr>
                                    <td><span class="fw-bold text-primary">{{ $eks->nomor_barcode }}</span></td>
                                    <td>{{ ucfirst($eks->kondisi) }}</td>
                                    <td>
                                        @if($eks->status_sirkulasi == 'tersedia')
                                            <span class="badge bg-success-subtle text-success">Tersedia</span>
                                        @elseif($eks->status_sirkulasi == 'dipinjam')
                                            <span class="badge bg-warning-subtle text-warning">Dipinjam</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">{{ ucfirst($eks->status_sirkulasi) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $eks->tanggal_pengadaan ? $eks->tanggal_pengadaan->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-light border" title="Cetak Barcode"><i class="fa-solid fa-print"></i></button>
                                        <button class="btn btn-sm btn-light border text-danger" title="Hapus Fisik"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted p-5 bg-light rounded">
                        <i class="fa-solid fa-box-open fs-1 mb-3"></i>
                        <p>Belum ada buku fisik (eksemplar) yang diinput untuk katalog ini.</p>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
