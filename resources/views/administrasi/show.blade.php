@extends('layouts.app')

@section('title', 'Detail Dokumen')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('administrasi.index', $kategori) }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0">Informasi Dokumen</h3>
                <p class="text-muted mb-0">Kategori: {{ $dokumen->namaKategori }}</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('administrasi.edit', [$kategori, $dokumen->id]) }}" class="btn btn-warning text-white rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-pen-to-square me-2"></i> Edit
            </a>
            <a href="{{ route('administrasi.download', [$kategori, $dokumen->id]) }}" class="btn btn-success rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-download me-2"></i> Unduh File
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Document Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        @if($dokumen->tipe_file == 'pdf')
                            <i class="fa-solid fa-file-pdf text-danger" style="font-size: 5rem;"></i>
                        @elseif(in_array($dokumen->tipe_file, ['doc', 'docx']))
                            <i class="fa-solid fa-file-word text-primary" style="font-size: 5rem;"></i>
                        @else
                            <i class="fa-solid fa-file-image text-success" style="font-size: 5rem;"></i>
                        @endif
                        <h5 class="fw-bold mt-3 mb-1">{{ $dokumen->judul }}</h5>
                        <span class="badge {{ $dokumen->status == 'aktif' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-3">
                            Status: {{ ucfirst($dokumen->status) }}
                        </span>
                    </div>

                    <hr>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Tipe File</span>
                            <span class="fw-bold text-uppercase">{{ $dokumen->tipe_file }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Ukuran</span>
                            <span class="fw-bold">{{ $dokumen->ukuranFormat }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Diunggah Oleh</span>
                            <span class="fw-bold">{{ $dokumen->uploader ? $dokumen->uploader->name : 'Sistem' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Tanggal Unggah</span>
                            <span class="fw-bold">{{ $dokumen->created_at->format('d M Y, H:i') }}</span>
                        </li>
                    </ul>

                    <div class="mt-4">
                        <h6 class="text-muted small fw-bold text-uppercase">Deskripsi / Keterangan</h6>
                        <p class="mb-0 bg-light p-3 rounded text-dark">
                            {{ $dokumen->deskripsi ?? 'Tidak ada deskripsi yang ditambahkan untuk dokumen ini.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Preview Area (if PDF or Image) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-eye me-2 text-primary"></i> Preview Dokumen</h6>
                </div>
                <div class="card-body p-0 bg-light d-flex align-items-center justify-content-center" style="min-height: 500px;">
                    
                    @if(in_array($dokumen->tipe_file, ['jpg', 'jpeg', 'png']))
                        <!-- Storage file needs to be accessed via controller route if it's in private storage -->
                        <!-- But for visual placeholder in this UI, we just show a mock message -->
                        <div class="text-center p-5">
                            <i class="fa-solid fa-image text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5>Preview Gambar Tersedia</h5>
                            <p class="text-muted">Klik tombol "Unduh File" untuk melihat resolusi penuh.</p>
                            <!-- In real app, we use route('administrasi.download', [$kategori, $dokumen->id]) in an img tag if public -->
                        </div>
                    @elseif($dokumen->tipe_file == 'pdf')
                        <div class="text-center p-5">
                            <i class="fa-solid fa-file-pdf text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5>Preview PDF Diblokir</h5>
                            <p class="text-muted">File dokumen tersimpan di folder private yang aman. <br>Silakan unduh untuk melihat kontennya secara aman.</p>
                            <a href="{{ route('administrasi.download', [$kategori, $dokumen->id]) }}" class="btn btn-outline-primary mt-3">Unduh PDF</a>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="fa-solid fa-file-word text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5>Tidak Ada Preview</h5>
                            <p class="text-muted">Format file .{{ $dokumen->tipe_file }} tidak mendukung preview langsung di browser.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
