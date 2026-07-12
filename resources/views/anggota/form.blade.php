@extends('layouts.app')

@section('title', isset($anggota) ? 'Edit Anggota' : 'Pendaftaran Anggota')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('anggota.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">{{ isset($anggota) ? 'Edit Data Anggota' : 'Pendaftaran Anggota Baru' }}</h3>
            <p class="text-muted mb-0">Lengkapi formulir untuk mencetak kartu dan mengaktifkan akun login.</p>
        </div>
    </div>

    <form action="{{ isset($anggota) ? route('anggota.update', $anggota->id) : route('anggota.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($anggota)) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Data Pribadi -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Informasi Demografi</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor Identitas (NIS/NIP) <span class="text-danger">*</span></label>
                                <input type="text" name="no_identitas" class="form-control" value="{{ old('no_identitas', $anggota->no_identitas ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tipe Keanggotaan <span class="text-danger">*</span></label>
                                <select name="tipe_anggota" class="form-select" required>
                                    <option value="siswa" {{ old('tipe_anggota', $anggota->tipe_anggota ?? '') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                    <option value="guru" {{ old('tipe_anggota', $anggota->tipe_anggota ?? '') == 'guru' ? 'selected' : '' }}>Guru</option>
                                    <option value="tendik" {{ old('tipe_anggota', $anggota->tipe_anggota ?? '') == 'tendik' ? 'selected' : '' }}>Staf / Tendik</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $anggota->tempat_lahir ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', isset($anggota) ? $anggota->tanggal_lahir->format('Y-m-d') : '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L" {{ old('jenis_kelamin', $anggota->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $anggota->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor Telepon / WhatsApp</label>
                                <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $anggota->no_telepon ?? '') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', $anggota->alamat ?? '') }}</textarea>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Status Keanggotaan <span class="text-danger">*</span></label>
                                <select name="status" class="form-select">
                                    <option value="aktif" {{ old('status', $anggota->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="non-aktif" {{ old('status', $anggota->status ?? '') == 'non-aktif' ? 'selected' : '' }}>Non-aktif (Cuti/Lulus)</option>
                                    <option value="blacklist" {{ old('status', $anggota->status ?? '') == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Akun Login -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary-subtle text-primary">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 border-bottom border-primary pb-2"><i class="fa-solid fa-lock me-2"></i> Kredensial Akun (Login Aplikasi)</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap (Sesuai KTP/KK) <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $anggota->user->name ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Aktif <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $anggota->user->email ?? '') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Password Login</label>
                                <input type="password" name="password" class="form-control" placeholder="{{ isset($anggota) ? 'Kosongkan jika tidak ingin mengubah password' : 'Bila dikosongkan, default password adalah smangunlib123' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Foto Profil -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Foto Profil Keanggotaan</h6>
                        @if(isset($anggota) && $anggota->foto)
                            <img src="{{ $anggota->foto_url }}" alt="Foto" class="img-thumbnail rounded-circle mb-3 shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                                <i class="fa-solid fa-camera text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <input type="file" name="foto" class="form-control mt-2" accept="image/*">
                        <small class="text-muted d-block mt-2">Digunakan untuk Kartu Anggota (Max 2MB)</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-3 shadow-sm rounded-pill">
                    <i class="fa-solid fa-save me-2"></i> {{ isset($anggota) ? 'Update Data Anggota' : 'Daftarkan Anggota' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
