@extends('layouts.app')

@section('title', 'Landing Content')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Landing Content: {{ $typeLabel }}</h4>
            <p class="text-muted mb-0">Kelola konten landing page dari dashboard.</p>
        </div>
        <a href="{{ route('system.contents.create', $type) }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Tambah</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card mb-3"><div class="card-body d-flex gap-2 flex-wrap">
        @foreach($types as $key => $label)
            <a class="btn btn-sm {{ $key === $type ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('system.contents.index', $key) }}">{{ $label }}</a>
        @endforeach
    </div></div>
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Judul</th><th>Status</th><th>Urutan</th><th>Tanggal</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td><div class="fw-semibold">{{ $item->title ?? '-' }}</div><small class="text-muted">{{ $item->slug }}</small></td>
                        <td><span class="badge bg-{{ in_array($item->status, ['active','published']) ? 'success' : 'secondary' }}">{{ $item->status }}</span></td>
                        <td>{{ $item->sort_order }}</td>
                        <td>{{ $item->content_date?->format('d M Y') ?? $item->published_at?->format('d M Y') ?? '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('system.contents.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('system.contents.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus konten ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada konten.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $items->links() }}</div>
    </div>
</div>
@endsection
