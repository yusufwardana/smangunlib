@extends('layouts.app')

@section('title', isset($dokumen) ? 'Edit Dokumen' : 'Unggah Dokumen Baru')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('administrasi.index', $kategori) }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">{{ isset($dokumen) ? 'Edit Dokumen' : 'Unggah Dokumen Baru' }}</h3>
            <p class="text-muted mb-0">Kategori: {{ ucwords(str_replace('_', ' ', $kategori)) }}</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <form action="{{ isset($dokumen) ? route('administrasi.update', [$kategori, $dokumen->id]) : route('administrasi.store', $kategori) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($dokumen)) @method('PUT') @endif

                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $dokumen->judul ?? '') }}" required placeholder="Contoh: SK Pengangkatan Pustakawan 2026">
                            @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi Tambahan</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Keterangan opsional tentang dokumen ini...">{{ old('deskripsi', $dokumen->deskripsi ?? '') }}</textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="aktif" {{ old('status', $dokumen->status ?? '') == 'aktif' ? 'selected' : '' }}>Berlaku / Aktif</option>
                                    <option value="arsip" {{ old('status', $dokumen->status ?? '') == 'arsip' ? 'selected' : '' }}>Arsip / Non-aktif</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light border-0 rounded-4 h-100">
                            <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                <i class="fa-solid fa-cloud-arrow-up text-primary mb-3" style="font-size: 3rem;"></i>
                                <h6 class="fw-bold">Unggah File</h6>
                                <p class="text-muted small">Format didukung: PDF, DOCX, JPG, PNG. Maksimal 10 MB.</p>
                                
                                <input type="file" name="file_dokumen" class="form-control mt-2 @error('file_dokumen') is-invalid @enderror" {{ isset($dokumen) ? '' : 'required' }} accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                @error('file_dokumen') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                @if(isset($dokumen))
                                <div class="mt-3 text-start bg-white p-3 rounded shadow-sm">
                                    <p class="mb-1 fw-bold small">File saat ini:</p>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-file-pdf text-danger fs-4"></i>
                                        <span class="text-truncate small">{{ basename($dokumen->file_path) }}</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah file.</small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('administrasi.index', $kategori) }}" class="btn btn-light px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fa-solid fa-save me-2"></i> {{ isset($dokumen) ? 'Simpan Perubahan' : 'Unggah Dokumen' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
