@extends('installer.layouts.master', ['step' => 5])

@section('content')
<h5 class="fw-bold mb-4">Profil Instansi (Aplikasi)</h5>
<p class="text-muted mb-4">Masukkan data inti aplikasi (Data ini akan disimpan ke dalam Environment Variables).</p>

<form action="{{ route('installer.app.store') }}" method="POST" id="appForm">
    @csrf
    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label fw-bold">Nama Sistem Perpustakaan <span class="text-danger">*</span></label>
            <input type="text" name="app_name" class="form-control" value="SMANGUNLIB" required>
            <small class="text-muted">Gunakan satu kata atau sambung dengan _ (underscore) jika tanpa spasi.</small>
        </div>
        <div class="col-md-12">
            <label class="form-label fw-bold">App URL (Tautan Website) <span class="text-danger">*</span></label>
            <input type="url" name="app_url" class="form-control" value="{{ url('/') }}" required>
            <small class="text-muted">Pastikan format diawali dengan http:// atau https://</small>
        </div>
    </div>
</form>
@endsection

@section('footer')
    <a href="{{ route('installer.database') }}" class="btn btn-light rounded-pill px-4">Kembali</a>
    <button type="button" onclick="document.getElementById('appForm').submit()" class="btn btn-primary rounded-pill px-4 fw-bold">Install & Generate <i class="fa-solid fa-gears ms-2"></i></button>
@endsection
