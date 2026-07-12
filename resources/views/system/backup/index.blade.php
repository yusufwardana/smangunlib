@extends('layouts.app')

@section('title', 'Backup & Restore')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Pencadangan Sistem (Backup)</h3>
            <p class="text-muted mb-0">Cadangkan database atau file sistem secara utuh untuk menghindari kehilangan data.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}</div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Backup Actions -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white hover-elevate">
                <div class="card-body p-4 text-center">
                    <i class="fa-solid fa-database fs-1 mb-3"></i>
                    <h5 class="fw-bold">Backup Database</h5>
                    <p class="small text-white-50">Dump tabel MySQL (.sql) lalu kompres menjadi ZIP.</p>
                    <form action="{{ route('system.backup.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tipe" value="database">
                        <button type="submit" class="btn btn-light rounded-pill px-4 fw-bold mt-2" onclick="this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> Processing...'">Buat Backup DB</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-info text-white hover-elevate">
                <div class="card-body p-4 text-center">
                    <i class="fa-solid fa-folder-open fs-1 mb-3"></i>
                    <h5 class="fw-bold">Backup Storage</h5>
                    <p class="small text-white-50">Kompres direktori unggahan (Cover, Dokumen PDF, Foto).</p>
                    <form action="{{ route('system.backup.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tipe" value="storage">
                        <button type="submit" class="btn btn-light rounded-pill px-4 fw-bold mt-2" onclick="this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> Processing...'">Buat Backup Storage</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-dark text-white hover-elevate">
                <div class="card-body p-4 text-center">
                    <i class="fa-solid fa-box-archive fs-1 mb-3 text-warning"></i>
                    <h5 class="fw-bold">Full Backup</h5>
                    <p class="small text-white-50">Lakukan backup Database & Storage sekaligus.</p>
                    <form action="{{ route('system.backup.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tipe" value="full">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold mt-2" onclick="this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> Processing...'">Jalankan Full Backup</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h6 class="fw-bold">Riwayat Pencadangan</h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu Backup</th>
                            <th>Nama File</th>
                            <th>Tipe</th>
                            <th>Ukuran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                        <tr>
                            <td>{{ $backup->created_at->format('d/m/Y H:i') }}</td>
                            <td class="font-monospace small">{{ $backup->nama_file }}</td>
                            <td>
                                @if($backup->tipe == 'database') <span class="badge bg-primary-subtle text-primary border border-primary">Database</span>
                                @elseif($backup->tipe == 'storage') <span class="badge bg-info-subtle text-info border border-info">Storage</span>
                                @else <span class="badge bg-dark">Full</span> @endif
                            </td>
                            <td>{{ $backup->ukuran_mb }} MB</td>
                            <td>
                                <a href="{{ route('system.backup.download', $backup->id) }}" class="btn btn-sm btn-success rounded-pill px-3"><i class="fa-solid fa-download"></i></a>
                                <form action="{{ route('system.backup.destroy', $backup->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus file backup ini dari server?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat pencadangan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-elevate { transition: all 0.3s ease; }
    .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important; }
</style>
@endsection
