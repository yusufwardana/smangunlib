@extends('layouts.app')

@section('title', isset($program) ? 'Edit Program GLS' : 'Buat Program GLS')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('gls.program.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">{{ isset($program) ? 'Edit Program Literasi' : 'Buat Program Literasi Baru' }}</h3>
        </div>
    </div>

    <form action="{{ isset($program) ? route('gls.program.update', $program->id) : route('gls.program.store') }}" method="POST">
        @csrf
        @if(isset($program)) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nama Program / Tantangan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_program" class="form-control" value="{{ old('nama_program', $program->nama_program ?? '') }}" placeholder="Contoh: Tantangan Membaca 30 Buku Kelas 10" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Periode Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="periode_mulai" class="form-control" value="{{ old('periode_mulai', isset($program) ? $program->periode_mulai->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Periode Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="periode_selesai" class="form-control" value="{{ old('periode_selesai', isset($program) ? $program->periode_selesai->format('Y-m-d') : '') }}" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Deskripsi & Syarat Ketentuan</label>
                                <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $program->deskripsi ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Baca (Jumlah Buku) <span class="text-danger">*</span></label>
                            <input type="number" name="target_baca" class="form-control form-control-lg text-center" value="{{ old('target_baca', $program->target_baca ?? 1) }}" min="1" required>
                            <small class="text-muted mt-1 d-block text-center">Berapa buku minimal yang harus diselesaikan peserta?</small>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Status Program <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                <option value="draft" {{ old('status', $program->status ?? '') == 'draft' ? 'selected' : '' }}>Draft (Belum Mulai)</option>
                                <option value="aktif" {{ old('status', $program->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif (Sedang Berjalan)</option>
                                <option value="selesai" {{ old('status', $program->status ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 rounded-pill shadow-sm">
                            <i class="fa-solid fa-save me-2"></i> {{ isset($program) ? 'Update Program' : 'Simpan Program' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
