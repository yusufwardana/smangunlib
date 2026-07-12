@extends('layouts.app')

@section('title', 'Dashboard - SMANGUNLIB')

@section('content')
<div class="row fade-in mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 1rem; overflow: hidden;">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-book-open-reader fa-2x"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-3 text-dark">Selamat Datang di SMANGUNLIB</h2>
                <p class="text-muted lead mb-5">Sistem Manajemen Perpustakaan Terintegrasi. Pilih menu di bawah atau di sidebar untuk mulai mengelola.</p>
                
                <div class="row justify-content-center g-4">
                    <div class="col-md-3">
                        <a href="{{ url('koleksi/buku') }}" class="text-decoration-none">
                            <div class="card bg-gradient-primary border-0 h-100 shadow">
                                <div class="card-body py-4">
                                    <i class="fa-solid fa-book stat-icon mb-3"></i>
                                    <h5 class="fw-bold mb-0">Koleksi Buku</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('keanggotaan/anggota') }}" class="text-decoration-none">
                            <div class="card bg-gradient-success border-0 h-100 shadow">
                                <div class="card-body py-4">
                                    <i class="fa-solid fa-users stat-icon mb-3"></i>
                                    <h5 class="fw-bold mb-0">Anggota</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('sirkulasi/peminjaman') }}" class="text-decoration-none">
                            <div class="card bg-gradient-warning border-0 h-100 shadow">
                                <div class="card-body py-4">
                                    <i class="fa-solid fa-hand-holding-hand stat-icon mb-3"></i>
                                    <h5 class="fw-bold mb-0">Sirkulasi</h5>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
