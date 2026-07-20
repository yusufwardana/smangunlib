@extends('layouts.app')

@section('title', 'Audit Log Hak Akses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1"><i class="fa-solid fa-shield-halved me-2"></i>Audit Log Hak Akses</h4>
        <p class="text-muted mb-0">Riwayat perubahan permission: siapa, kapan, role, serta permission lama & baru.</p>
    </div>
    <a href="{{ route('system.permissions.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Target</th>
                        <th>Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-nowrap">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td><span class="badge bg-primary">{{ $log->action }}</span></td>
                            <td><code>{{ $log->model_type }}</code></td>
                            <td>
                                @php
                                    $before = collect($log->before_data['permissions'] ?? []);
                                    $after = collect($log->after_data['permissions'] ?? []);
                                    $added = $after->diff($before);
                                    $removed = $before->diff($after);
                                @endphp
                                @if($added->isEmpty() && $removed->isEmpty())
                                    <span class="text-muted small">
                                        @if(isset($log->after_data['synced']))
                                            {{ $log->after_data['synced'] }} aksi disinkronkan
                                        @else
                                            —
                                        @endif
                                    </span>
                                @else
                                    @foreach ($added as $perm)
                                        <span class="badge bg-success-subtle text-success border border-success me-1">+ {{ $perm }}</span>
                                    @endforeach
                                    @foreach ($removed as $perm)
                                        <span class="badge bg-danger-subtle text-danger border border-danger me-1">− {{ $perm }}</span>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat perubahan hak akses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $logs->links() }}
    </div>
</div>
@endsection
