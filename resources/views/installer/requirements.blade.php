@extends('installer.layouts.master', ['step' => 2])

@section('content')
<h5 class="fw-bold mb-4">Pemeriksaan Server (Requirements)</h5>
<p class="text-muted mb-4">Pastikan server Anda memenuhi persyaratan minimum untuk menjalankan Laravel 12.</p>

<div class="list-group list-group-flush border rounded mb-4">
    @foreach($requirements as $ext => $passed)
        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
            <span class="fw-bold">{{ $ext }}</span>
            @if($passed)
                <span class="badge bg-success rounded-pill px-3"><i class="fa-solid fa-check me-1"></i> Tersedia</span>
            @else
                <span class="badge bg-danger rounded-pill px-3"><i class="fa-solid fa-times me-1"></i> Tidak Tersedia</span>
            @endif
        </div>
    @endforeach
</div>

@if(!$allPassed)
<div class="alert alert-danger border-0 shadow-sm">
    <i class="fa-solid fa-triangle-exclamation me-2"></i> Mohon lengkapi ekstensi PHP yang kurang (ditandai merah) di panel kontrol hosting Anda sebelum melanjutkan.
</div>
@endif
@endsection

@section('footer')
    <a href="{{ route('installer.welcome') }}" class="btn btn-light rounded-pill px-4">Kembali</a>
    @if($allPassed)
        <a href="{{ route('installer.permissions') }}" class="btn btn-primary rounded-pill px-4 fw-bold">Selanjutnya <i class="fa-solid fa-arrow-right ms-2"></i></a>
    @else
        <button class="btn btn-secondary rounded-pill px-4" disabled>Selanjutnya <i class="fa-solid fa-arrow-right ms-2"></i></button>
    @endif
@endsection
