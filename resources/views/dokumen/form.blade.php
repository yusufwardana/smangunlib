@extends('layouts.app')

@section('title', isset($dokumen) ? 'Update Versi Dokumen' : 'Unggah Dokumen')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ isset($dokumen) ? route('dokumen.show', $dokumen->id) : route('dokumen.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">{{ isset($dokumen) ? 'Update Versi Dokumen' : 'Unggah Dokumen Baru' }}</h3>
        </div>
    </div>

    @if(isset($dokumen))
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <i class="fa-solid fa-circle-info me-2"></i> <strong>Sistem Versioning Aktif!</strong> Jika Anda mengunggah file baru, versi lama akan secara otomatis diarsipkan ke dalam "Riwayat Dokumen" dan file baru ini akan menjadi versi terkini yang aktif.
    </div>
    @endif

    <form action="{{ isset($dokumen) ? route('dokumen.update', $dokumen->id) : route('dokumen.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($dokumen)) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Judul / Nama Dokumen <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" value="{{ old('judul', $dokumen->judul ?? '') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori Spesifik <span class="text-danger">*</span></label>
                                <select name="kategori_dokumen" class="form-select" required>
                                    @php $kats = ['Administrasi', 'Legalitas', 'Koleksi', 'Sarana Prasarana', 'Literasi', 'Evaluasi', 'SOP', 'Program Kerja', 'Laporan']; @endphp
                                    <option value="">-- Pilih --</option>
                                    @foreach($kats as $kat)
                                        <option value="{{ $kat }}" {{ old('kategori_dokumen', $dokumen->kategori_dokumen ?? '') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Versi <span class="text-danger">*</span></label>
                                <input type="text" name="versi" class="form-control" value="{{ old('versi', $dokumen->versi ?? 'v1.0') }}" placeholder="Contoh: v1.0, Revisi 2" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Masa Berlaku Sampai</label>
                                <input type="date" name="masa_berlaku_sampai" class="form-control" value="{{ old('masa_berlaku_sampai', isset($dokumen) && $dokumen->masa_berlaku_sampai ? $dokumen->masa_berlaku_sampai->format('Y-m-d') : '') }}">
                                <small class="text-muted">Kosongkan jika dokumen ini berlaku seumur hidup (contoh: Notulen Rapat).</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Deskripsi / Keterangan Tambahan</label>
                                <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $dokumen->deskripsi ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
                    <div class="card-body p-4 text-center">
                        <i class="fa-solid fa-file-arrow-up text-primary mb-3" style="font-size: 4rem;"></i>
                        <h6 class="fw-bold">File Dokumen (PDF/JPG/PNG)</h6>
                        <input type="file" name="file" class="form-control mt-3" accept=".pdf,image/*" {{ isset($dokumen) ? '' : 'required' }}>
                        <small class="text-muted d-block mt-2">Maksimal ukuran file: 10MB.</small>
                        
                        @if(isset($dokumen))
                            <small class="text-warning d-block mt-1">Kosongkan jika hanya ingin mengupdate judul/teks saja.</small>
                        @endif

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 mt-4 rounded-pill shadow-sm">
                            <i class="fa-solid fa-save me-2"></i> {{ isset($dokumen) ? 'Simpan Update Versi' : 'Unggah ke Server' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
