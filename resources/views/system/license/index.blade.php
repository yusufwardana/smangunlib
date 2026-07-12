@extends('layouts.app')

@section('title', 'License Manager')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">License Manager</h3>
            <p class="text-muted mb-0">Kelola lisensi instalasi SMANGUNLIB Anda.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <!-- Current License -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    @if($currentLicense && $currentLicense->status == 'active')
                        <div class="bg-success-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-shield-check text-success fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-success mb-1">Lisensi Aktif</h5>
                        <p class="text-muted small">Masa Berlaku hingga: {{ $currentLicense->expired_date->format('d M Y') }}</p>
                        
                        <div class="bg-light p-3 rounded text-start mt-4">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td class="text-muted w-50">License Key</td><td class="fw-bold font-monospace">{{ $currentLicense->license_key }}</td></tr>
                                <tr><td class="text-muted">Terdaftar Untuk</td><td class="fw-bold">{{ $currentLicense->nama_sekolah }}</td></tr>
                                <tr><td class="text-muted">Domain Target</td><td class="fw-bold">{{ $currentLicense->domain }}</td></tr>
                            </table>
                        </div>
                    @else
                        <div class="bg-danger-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-shield-xmark text-danger fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-danger mb-1">Lisensi Tidak Valid / Kedaluwarsa</h5>
                        <p class="text-muted small">Silakan masukkan License Key baru untuk mendapatkan pembaruan sistem.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activation Form -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold"><i class="fa-solid fa-key me-2 text-warning"></i> Aktivasi Lisensi Baru</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('system.license.activate') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">License Key</label>
                            <input type="text" name="license_key" class="form-control font-monospace" placeholder="LIBSYS-XXXX-XXXX-XXXX-XXXX" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Sekolah</label>
                            <input type="text" name="nama_sekolah" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Email Admin</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold w-100"><i class="fa-solid fa-bolt me-2"></i> Aktivasi Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
