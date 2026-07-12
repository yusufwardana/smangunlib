@extends('layouts.app')

@section('title', 'Media Manager')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Media Manager</h4>
            <p class="text-muted mb-0">Upload dan kelola logo, banner, foto, dokumen, video, dan PDF.</p>
        </div>
        <a href="{{ route('system.settings.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="card mb-4">
        <form action="{{ route('system.media.store') }}" method="POST" enctype="multipart/form-data" class="card-body row g-3">
            @csrf
            <div class="col-md-3"><label class="form-label">Kategori</label><input name="category" class="form-control" placeholder="banner, dokumen, foto"></div>
            <div class="col-md-3"><label class="form-label">Folder</label><input name="folder" class="form-control" value="media"></div>
            <div class="col-md-4"><label class="form-label">File</label><input type="file" name="files[]" class="form-control" multiple required></div>
            <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Upload</button></div>
        </form>
    </div>
    <form class="row g-2 mb-3">
        <div class="col-md-4"><input name="q" class="form-control" placeholder="Cari media..." value="{{ request('q') }}"></div>
        <div class="col-md-3"><input name="category" class="form-control" placeholder="Kategori" value="{{ request('category') }}"></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
    <div class="row g-3">
        @forelse($media as $item)
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100">
                    @if(str_starts_with((string) $item->mime_type, 'image/'))
                        <img src="{{ $item->url }}" class="card-img-top" alt="{{ $item->name }}" style="height:160px;object-fit:cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light" style="height:160px;"><i class="fa-solid fa-file fa-3x text-muted"></i></div>
                    @endif
                    <div class="card-body">
                        <div class="fw-semibold text-truncate">{{ $item->name }}</div>
                        <small class="text-muted">{{ $item->category ?? 'Tanpa kategori' }}</small>
                        <input class="form-control form-control-sm mt-2" value="{{ $item->url }}" readonly>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="{{ $item->url }}" target="_blank" class="btn btn-sm btn-outline-primary">Preview</a>
                        <form action="{{ route('system.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus media ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="text-center text-muted py-5">Belum ada media.</div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $media->links() }}</div>
</div>
@endsection
