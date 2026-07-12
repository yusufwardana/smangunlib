@extends('installer.layouts.master', ['step' => 1])

@section('content')
<div class="text-center py-4">
    <i class="fa-solid fa-rocket text-primary mb-4" style="font-size: 5rem;"></i>
    <h3 class="fw-bold">Selamat Datang di Instalasi SMANGUNLIB</h3>
    <p class="text-muted mt-3 mb-0">
        Wizard ini akan memandu Anda melakukan instalasi Sistem Informasi Perpustakaan Sekolah. 
        Pastikan Anda telah menyiapkan detail koneksi Database (Host, Username, Password, Nama DB) sebelum melanjutkan.
    </p>
</div>
@endsection

@section('footer')
    <div></div>
    <a href="{{ route('installer.requirements') }}" class="btn btn-primary rounded-pill px-4 fw-bold">Mulai Instalasi <i class="fa-solid fa-arrow-right ms-2"></i></a>
@endsection
