@extends('layouts.app')

@section('title', isset($buku) ? 'Edit Buku' : 'Tambah Buku Baru')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('koleksi.buku.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">{{ isset($buku) ? 'Edit Buku' : 'Tambah Buku Baru' }}</h3>
            <p class="text-muted mb-0">Formulir pendaftaran katalog baru.</p>
        </div>
    </div>

    <form action="{{ isset($buku) ? route('koleksi.buku.update', $buku->id) : route('koleksi.buku.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($buku)) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Informasi Dasar</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Judul Buku <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" value="{{ old('judul', $buku->judul ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pengarang <span class="text-danger">*</span></label>
                                <input type="text" name="pengarang" class="form-control" value="{{ old('pengarang', $buku->pengarang ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Penerbit <span class="text-danger">*</span></label>
                                <input type="text" name="penerbit" class="form-control" value="{{ old('penerbit', $buku->penerbit ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tahun Terbit <span class="text-danger">*</span></label>
                                <input type="number" name="tahun_terbit" class="form-control" value="{{ old('tahun_terbit', $buku->tahun_terbit ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">ISBN</label>
                                <input type="text" name="isbn" class="form-control" value="{{ old('isbn', $buku->isbn ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bahasa <span class="text-danger">*</span></label>
                                <input type="text" name="bahasa" class="form-control" value="{{ old('bahasa', $buku->bahasa ?? 'Indonesia') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Kategori</label>
                                <select name="kategori_ids[]" class="form-select" multiple>
                                    @foreach($kategoris as $kat)
                                        <option value="{{ $kat->id }}" {{ (isset($buku) && $buku->kategori->contains($kat->id)) ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Sinopsis / Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $buku->deskripsi ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Cover Buku</h6>
                        @if(isset($buku) && $buku->cover_image)
                            <img src="{{ $buku->cover_url }}" alt="Cover" class="img-fluid rounded mb-3" style="max-height: 200px;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                                <i class="fa-solid fa-image text-muted" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-2">Max 2MB (JPG/PNG)</small>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-laptop text-primary me-2"></i> E-Book / Buku Digital</h6>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_digital" name="is_digital" value="1" {{ old('is_digital', $buku->is_digital ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_digital">Tersedia dalam bentuk Digital</label>
                        </div>
                        
                        <div id="fileDigitalContainer" style="{{ old('is_digital', $buku->is_digital ?? false) ? '' : 'display: none;' }}">
                            <label class="form-label small fw-bold">Unggah File PDF (Max 15MB)</label>
                            <input type="file" name="file_digital" class="form-control" accept=".pdf">
                            @if(isset($buku) && $buku->file_digital)
                                <small class="text-success d-block mt-1"><i class="fa-solid fa-check-circle"></i> File E-Book sudah tersimpan.</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Rak Fisik</h6>
                        <label class="form-label small fw-bold">Lokasi Rak Penempatan</label>
                        <select name="rak_lokasi_id" class="form-select">
                            <option value="">Pilih Rak (Bisa dikosongkan jika E-book saja)</option>
                            @foreach($raks as $rak)
                                <option value="{{ $rak->id }}" {{ old('rak_lokasi_id', $buku->rak_lokasi_id ?? '') == $rak->id ? 'selected' : '' }}>{{ $rak->nama_lokasi }} ({{ $rak->kode_rak }})</option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="btn btn-primary w-100 fw-bold mt-4 py-2">
                            <i class="fa-solid fa-save me-2"></i> {{ isset($buku) ? 'Update Buku' : 'Simpan Buku' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#is_digital').change(function() {
            if(this.checked) {
                $('#fileDigitalContainer').slideDown();
            } else {
                $('#fileDigitalContainer').slideUp();
            }
        });
    });
</script>
@endpush
