@extends('installer.layouts.master', ['step' => 3])

@section('content')
<h5 class="fw-bold mb-4">Permission Folder (Hak Akses)</h5>
<p class="text-muted mb-4">Direktori berikut membutuhkan hak akses Write (755 atau 777) agar aplikasi dapat berfungsi.</p>

<div class="list-group list-group-flush border rounded mb-4">
    @foreach($permissions as $folder => $passed)
        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
            <span class="fw-bold font-monospace">{{ $folder }}</span>
            @if($passed)
                <span class="badge bg-success rounded-pill px-3"><i class="fa-solid fa-check me-1"></i> Writable</span>
            @else
                <span class="badge bg-danger rounded-pill px-3"><i class="fa-solid fa-times me-1"></i> Not Writable</span>
            @endif
        </div>
    @endforeach
</div>

@if(!$allPassed)
<div class="alert alert-warning border-0 shadow-sm">
    <strong>Solusi:</strong> Buka File Manager cPanel Anda, klik kanan pada folder di atas, pilih <strong>Change Permissions</strong>, dan set ke <code>755</code> (centang kotak Write pada tabel).
</div>
@endif
@endsection

@section('footer')
    <a href="{{ route('installer.requirements') }}" class="btn btn-light rounded-pill px-4">Kembali</a>
    @if($allPassed)
        <a href="{{ route('installer.database') }}" class="btn btn-primary rounded-pill px-4 fw-bold">Selanjutnya <i class="fa-solid fa-arrow-right ms-2"></i></a>
    @else
        <button class="btn btn-secondary rounded-pill px-4" disabled>Selanjutnya <i class="fa-solid fa-arrow-right ms-2"></i></button>
    @endif
@endsection
