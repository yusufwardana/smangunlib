@extends('layouts.app')

@section('title', 'Menu Manager')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Menu Manager</h4>
            <p class="text-muted mb-0">Kelola menu landing page, parent menu, icon, urutan, dan status.</p>
        </div>
        <a href="{{ route('system.settings.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="row g-4">
        <div class="col-lg-4">
            <form action="{{ route('system.menus.store') }}" method="POST" class="card">
                @csrf
                <div class="card-header">Tambah Menu</div>
                <div class="card-body">
                    <label class="form-label">Nama Menu</label>
                    <input name="name" class="form-control mb-3" required>
                    <label class="form-label">URL</label>
                    <input name="url" class="form-control mb-3" placeholder="#koleksi" required>
                    <label class="form-label">Icon</label>
                    <input name="icon" class="form-control mb-3" placeholder="bi-book">
                    <label class="form-label">Parent Menu</label>
                    <select name="parent_id" class="form-select mb-3">
                        <option value="">Tidak ada</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    <label class="form-label">Urutan</label>
                    <input type="number" name="sort_order" class="form-control mb-3" value="0">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Aktif</label>
                    </div>
                </div>
                <div class="card-footer bg-white"><button class="btn btn-primary w-100">Simpan</button></div>
            </form>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Menu</th><th>URL</th><th>Parent</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                        <tbody>
                        @foreach($menus as $menu)
                            <tr>
                                <td><i class="bi {{ $menu->icon ?: 'bi-link' }} me-2"></i>{{ $menu->name }} <small class="text-muted">#{{ $menu->sort_order }}</small></td>
                                <td>{{ $menu->url }}</td>
                                <td>{{ $menu->parent?->name ?? '-' }}</td>
                                <td><span class="badge bg-{{ $menu->is_active ? 'success' : 'secondary' }}">{{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#edit-menu-{{ $menu->id }}">Edit</button>
                                    <form action="{{ route('system.menus.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                                </td>
                            </tr>
                            <tr class="collapse" id="edit-menu-{{ $menu->id }}">
                                <td colspan="5">
                                    <form action="{{ route('system.menus.update', $menu) }}" method="POST" class="row g-2">
                                        @csrf @method('PUT')
                                        <div class="col-md-3"><input name="name" class="form-control" value="{{ $menu->name }}"></div>
                                        <div class="col-md-3"><input name="url" class="form-control" value="{{ $menu->url }}"></div>
                                        <div class="col-md-2"><input name="icon" class="form-control" value="{{ $menu->icon }}"></div>
                                        <div class="col-md-2"><input type="number" name="sort_order" class="form-control" value="{{ $menu->sort_order }}"></div>
                                        <div class="col-md-2 d-flex gap-2"><input type="hidden" name="is_active" value="0"><input class="form-check-input mt-2" type="checkbox" name="is_active" value="1" @checked($menu->is_active)><button class="btn btn-primary btn-sm">Update</button></div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body">{{ $menus->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
