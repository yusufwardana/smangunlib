@extends('layouts.app')

@section('title', 'System Update')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">System Update (OTA)</h3>
            <p class="text-muted mb-0">Perbarui sistem dengan mengunggah file `update.zip` resmi dari pengembang.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold"><i class="fa-solid fa-cloud-arrow-up me-2 text-primary"></i> Upload Paket Pembaruan</h6>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-warning border-0 small">
                        <strong>Peringatan!</strong> Jangan me-refresh halaman saat proses instalasi berjalan. Server akan mem-backup *core system* Anda secara otomatis sebelum menimpa file baru.
                    </div>
                    
                    <form action="{{ route('system.update.upload') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoading()">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih File `update.zip`</label>
                            <input type="file" name="update_file" class="form-control form-control-lg" accept=".zip" required>
                        </div>
                        <button type="submit" id="btnUpdate" class="btn btn-primary rounded-pill px-4 fw-bold w-100"><i class="fa-solid fa-rocket me-2"></i> Mulai Instalasi Update</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> Riwayat Pembaruan</h6>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Versi Lama</th>
                                    <th>Versi Baru</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($updates as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                    <td><span class="badge bg-secondary rounded-pill">{{ $log->versi_lama }}</span></td>
                                    <td><span class="badge bg-primary rounded-pill">{{ $log->versi_baru }}</span></td>
                                    <td>
                                        @if($log->status == 'success')
                                            <span class="text-success fw-bold"><i class="fa-solid fa-check"></i> Sukses</span>
                                        @elseif($log->status == 'extracting')
                                            <span class="text-warning fw-bold"><i class="fa-solid fa-spinner fa-spin"></i> Proses</span>
                                        @else
                                            <span class="text-danger fw-bold"><i class="fa-solid fa-times"></i> Gagal</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada riwayat pembaruan sistem.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showLoading() {
        let btn = document.getElementById('btnUpdate');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Sedang memproses... Jangan tutup halaman!';
        btn.disabled = true;
        btn.classList.replace('btn-primary', 'btn-secondary');
    }
</script>
@endpush
