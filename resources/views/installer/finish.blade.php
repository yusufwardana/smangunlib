@extends('installer.layouts.master')

@section('content')
<div class="text-center py-5">
    <div class="rounded-circle bg-success-subtle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
        <i class="fa-solid fa-check text-success" style="font-size: 3.5rem;"></i>
    </div>
    <h3 class="fw-bold text-success mb-3">Instalasi Berhasil!</h3>
    <p class="text-muted mb-4">Aplikasi SMANGUNLIB Anda kini telah terkunci ke mode operasional secara permanen. Modul Web Installer telah dinonaktifkan demi keamanan.</p>

    <div class="card border border-warning bg-warning-subtle mx-auto mb-4" style="max-width: 400px;">
        <div class="card-body p-3 text-start">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-lock me-2"></i> Informasi Login Super Admin</h6>
            <div class="mb-2">
                <small class="text-muted d-block">Alamat Email:</small>
                <strong class="font-monospace fs-6">{{ $email }}</strong>
            </div>
            <div>
                <small class="text-muted d-block">Password:</small>
                <strong class="font-monospace fs-6">{{ $password }}</strong>
            </div>
        </div>
    </div>
    
    <p class="small text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Harap simpan kredensial di atas. Form ini tidak akan bisa dibuka kembali!</p>
</div>
@endsection

@section('footer')
    <div class="w-100 text-center">
        <a href="{{ url('/') }}" class="btn btn-dark rounded-pill px-5 fw-bold py-3 shadow-lg hover-elevate">Masuk ke Dashboard Sekarang <i class="fa-solid fa-right-to-bracket ms-2"></i></a>
    </div>
@endsection
