@extends('layouts.app')

@section('title', 'Detail Dokumen')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('dokumen.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0">Detail Dokumen <span class="badge bg-primary ms-2">{{ $dokumen->versi }}</span></h3>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dokumen.download', $dokumen->id) }}" class="btn btn-dark rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-download me-2"></i> Download File
            </a>
            <a href="{{ route('dokumen.edit', $dokumen->id) }}" class="btn btn-warning text-white rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-pen me-2"></i> Update Versi Baru
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        <!-- Document Viewer -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-0 d-flex flex-column" style="min-height: 600px;">
                    <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0"><i class="fa-solid fa-eye me-2"></i> Pratinjau Dokumen</h6>
                        <span class="badge bg-secondary">{{ strtoupper($dokumen->tipe_file) }}</span>
                    </div>
                    
                    <div class="flex-grow-1 p-3 bg-secondary-subtle d-flex align-items-center justify-content-center">
                        @if($dokumen->tipe_file == 'pdf')
                            <iframe src="{{ route('dokumen.preview', $dokumen->id) }}" width="100%" height="600px" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);"></iframe>
                        @elseif(in_array($dokumen->tipe_file, ['jpg','jpeg','png']))
                            <img src="{{ route('dokumen.preview', $dokumen->id) }}" class="img-fluid rounded shadow" style="max-height: 600px;">
                        @else
                            <div class="text-center text-muted">
                                <i class="fa-solid fa-file-circle-xmark fs-1 mb-3"></i>
                                <p>Pratinjau tidak tersedia untuk format file ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Data & History -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white" style="background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ $dokumen->judul }}</h5>
                    <p class="mb-4 text-white-50 small">{{ $dokumen->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                    
                    <ul class="list-group list-group-flush small bg-transparent">
                        <li class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-white-50">
                            <span>Kategori</span>
                            <span class="fw-bold">{{ strtoupper($dokumen->kategori_dokumen) }}</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-white-50">
                            <span>Ukuran File</span>
                            <span class="fw-bold">{{ $dokumen->ukuran_format }}</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-white-50">
                            <span>Diunggah Oleh</span>
                            <span class="fw-bold">{{ $dokumen->uploader->name ?? 'Sistem' }}</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-white-50">
                            <span>Masa Berlaku</span>
                            @if($dokumen->masa_berlaku_sampai)
                                <span class="fw-bold {{ \Carbon\Carbon::today()->gt($dokumen->masa_berlaku_sampai) ? 'text-danger bg-white px-2 rounded' : '' }}">{{ $dokumen->masa_berlaku_sampai->format('d M Y') }}</span>
                            @else
                                <span class="fw-bold">- (Seumur Hidup)</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Version History Tree -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left me-2"></i> Riwayat Versi (Versioning)</h6>
                </div>
                <div class="card-body p-3">
                    <div class="timeline position-relative ps-3" style="border-left: 2px solid #e9ecef;">
                        <!-- Current Version -->
                        <div class="position-relative mb-4">
                            <span class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -23px; top: 5px;"></span>
                            <h6 class="fw-bold mb-1 text-primary">{{ $dokumen->versi }} (Saat Ini)</h6>
                            <small class="text-muted d-block mb-2">{{ $dokumen->updated_at->format('d M Y, H:i') }}</small>
                            <span class="badge bg-success-subtle text-success">Aktif Diberlakukan</span>
                        </div>

                        <!-- Archived Child Versions -->
                        @forelse($dokumen->history as $hist)
                        <div class="position-relative mb-4">
                            <span class="position-absolute bg-secondary rounded-circle" style="width: 10px; height: 10px; left: -22px; top: 5px;"></span>
                            <h6 class="fw-bold mb-1 text-muted">{{ $hist->versi }}</h6>
                            <small class="text-muted d-block mb-2">{{ $hist->created_at->format('d M Y, H:i') }}</small>
                            <a href="{{ route('dokumen.download', $hist->id) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;"><i class="fa-solid fa-download"></i> Unduh Arsip</a>
                        </div>
                        @empty
                        <p class="small text-muted mb-0">Belum ada riwayat pembaruan dokumen.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
