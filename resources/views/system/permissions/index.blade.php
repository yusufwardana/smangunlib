@extends('layouts.app')

@section('title', 'Pengaturan Hak Akses Menu')

@push('styles')
<style>
    .rbac-tree .menu-row { border-bottom: 1px solid rgba(0,0,0,0.04); }
    .rbac-tree .menu-title { font-weight: 600; }
    .rbac-tree .submenu { padding-left: 1.5rem; }
    .rbac-tree .menu-actions { display: flex; flex-wrap: wrap; gap: .75rem; }
    .rbac-tree .form-check { margin: 0; }
    .rbac-tree .level-0 > .menu-row { background: rgba(67,97,238,0.04); }
    .rbac-sticky-actions { position: sticky; top: 70px; z-index: 10; }
    .action-legend .badge { font-weight: 500; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1"><i class="fa-solid fa-user-lock me-2"></i>Pengaturan Hak Akses Menu</h4>
        <p class="text-muted mb-0">Kelola hak akses (RBAC) tiap role. Semua menu dikontrol melalui database.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('system.permissions.rebuild') }}" method="POST" onsubmit="return confirm('Bangun ulang seluruh permission dari definisi menu?')">
            @csrf
            <button class="btn btn-outline-primary"><i class="fa-solid fa-arrows-rotate me-1"></i> Rebuild Permission</button>
        </form>
        <form action="{{ route('system.permissions.clear-cache') }}" method="POST">
            @csrf
            <button class="btn btn-outline-secondary"><i class="fa-solid fa-broom me-1"></i> Clear Permission Cache</button>
        </form>
        <a href="{{ route('system.permissions.audit') }}" class="btn btn-outline-dark"><i class="fa-solid fa-shield-halved me-1"></i> Audit Log</a>
    </div>
</div>

@foreach (['success' => 'success', 'info' => 'info', 'error' => 'danger'] as $flash => $variant)
    @if(session($flash))
        <div class="alert alert-{{ $variant }} alert-dismissible fade show">
            {{ session($flash) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endforeach

<div class="row g-3">
    {{-- Panel kiri: pemilihan role & aksi lanjutan --}}
    <div class="col-lg-3">
        <div class="card rbac-sticky-actions">
            <div class="card-header fw-semibold"><i class="fa-solid fa-user-shield me-1"></i> Role</div>
            <div class="list-group list-group-flush">
                @foreach ($roles as $role)
                    <a href="{{ route('system.permissions.index', ['role' => $role->name]) }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selected->id === $role->id ? 'active' : '' }}">
                        {{ $roleNames[$role->name] ?? $role->name }}
                        @if($role->name === 'super_admin')
                            <span class="badge bg-warning text-dark">Full</span>
                        @else
                            <span class="badge bg-light text-muted">{{ $role->permissions->count() }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        @unless($isSuperAdmin)
        <div class="card mt-3">
            <div class="card-header fw-semibold"><i class="fa-solid fa-copy me-1"></i> Copy dari Role Lain</div>
            <div class="card-body">
                <form action="{{ route('system.permissions.copy', $selected) }}" method="POST">
                    @csrf
                    <select name="source_role_id" class="form-select mb-2" required>
                        <option value="">-- Pilih role sumber --</option>
                        @foreach ($roles as $role)
                            @if($role->id !== $selected->id)
                                <option value="{{ $role->id }}">{{ $roleNames[$role->name] ?? $role->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-primary w-100" onclick="return confirm('Salin permission ke role {{ $selected->name }}?')">
                        <i class="fa-solid fa-copy me-1"></i> Copy Permission
                    </button>
                </form>
                <form action="{{ route('system.permissions.reset', $selected) }}" method="POST" class="mt-2"
                      onsubmit="return confirm('Kosongkan seluruh hak akses role {{ $selected->name }}?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger w-100"><i class="fa-solid fa-eraser me-1"></i> Reset Permission</button>
                </form>
            </div>
        </div>
        @endunless
    </div>

    {{-- Panel kanan: matriks pohon menu x aksi --}}
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="fw-semibold">
                    <i class="fa-solid fa-sitemap me-1"></i> Hak Akses: {{ $roleNames[$selected->name] ?? $selected->name }}
                </span>
                @unless($isSuperAdmin)
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-success" onclick="rbacSelectAll(true)"><i class="fa-solid fa-check-double me-1"></i> Select All</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="rbacSelectAll(false)"><i class="fa-solid fa-xmark me-1"></i> Unselect All</button>
                </div>
                @endunless
            </div>

            <div class="card-body">
                @if($isSuperAdmin)
                    <div class="alert alert-warning mb-0">
                        <i class="fa-solid fa-crown me-1"></i>
                        <strong>Super Admin</strong> secara otomatis memiliki seluruh hak akses (via Gate) dan tidak dapat dibatasi.
                    </div>
                @else
                    <form action="{{ route('system.permissions.update', $selected) }}" method="POST" id="rbac-form">
                        @csrf
                        @method('PUT')

                        <div class="rbac-tree border rounded">
                            @foreach ($tree as $menu)
                                @include('system.permissions.partials.menu-node', ['menu' => $menu, 'level' => 0, 'granted' => $granted, 'actions' => $actions])
                            @endforeach
                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Hak Akses</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function rbacSelectAll(state) {
        document.querySelectorAll('#rbac-form input[type="checkbox"]').forEach(function (cb) {
            cb.checked = state;
        });
    }

    // Centang "view" otomatis saat aksi lain dicentang (view adalah prasyarat).
    document.addEventListener('change', function (e) {
        if (e.target.matches('#rbac-form .action-checkbox')) {
            if (e.target.checked && e.target.dataset.action !== 'view') {
                const row = e.target.closest('.menu-actions');
                const viewBox = row && row.querySelector('.action-checkbox[data-action="view"]');
                if (viewBox) viewBox.checked = true;
            }
        }
    });
</script>
@endpush
