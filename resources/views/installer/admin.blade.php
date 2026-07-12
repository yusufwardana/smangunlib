@extends('installer.layouts.master', ['step' => 7])

@section('content')
<h5 class="fw-bold mb-4">Setup Akun Super Admin</h5>
<p class="text-muted mb-4">Aplikasi dan database telah siap. Buat satu akun master untuk mengontrol seluruh sistem (RBAC).</p>

@if ($errors->any())
    <div class="alert alert-danger shadow-sm border-0">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('installer.admin.store') }}" method="POST" id="adminForm">
    @csrf
    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label fw-bold">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="col-md-12">
            <label class="form-label fw-bold">Alamat Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', 'admin@sekolah.sch.id') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Password</label>
            <input type="password" name="password" class="form-control" minlength="8" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Ulangi Password</label>
            <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
        </div>
    </div>
</form>
@endsection

@section('footer')
    <div></div>
    <button type="button" onclick="document.getElementById('adminForm').submit()" class="btn btn-primary rounded-pill px-4 fw-bold">Buat Akun Admin <i class="fa-solid fa-user-shield ms-2"></i></button>
@endsection
