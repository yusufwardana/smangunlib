@extends('layouts.app')

@section('title', 'Dashboard Literasi')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Gerakan Literasi Sekolah (GLS)</h3>
            <p class="text-muted mb-0">Pantau dan kelola budaya baca di lingkungan sekolah.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('gls.jurnal.index') }}" class="btn btn-warning text-dark rounded-pill shadow-sm px-4 fw-bold">
                <i class="fa-solid fa-list-check me-2"></i> Verifikasi Jurnal @if($stats['jurnal_pending'] > 0) <span class="badge bg-danger ms-1">{{ $stats['jurnal_pending'] }}</span> @endif
            </a>
            <a href="{{ route('gls.program.index') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-flag me-2"></i> Kelola Program GLS
            </a>
        </div>
    </div>

    <!-- Statistik Mini -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-white border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Total Program</h6>
                        <h3 class="fw-bold mb-0 text-primary">{{ $stats['total_program'] }}</h3>
                    </div>
                    <i class="fa-solid fa-layer-group fs-1 text-primary opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Total Peserta</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['total_peserta'] }}</h3>
                    </div>
                    <i class="fa-solid fa-users fs-1 text-success opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Buku Dibaca (Sah)</h6>
                        <h3 class="fw-bold mb-0 text-info">{{ $stats['buku_dibaca'] }}</h3>
                    </div>
                    <i class="fa-solid fa-book-open fs-1 text-info opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Menunggu Verifikasi</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['jurnal_pending'] }} <span class="fs-6 fw-normal">Jurnal</span></h3>
                    </div>
                    <i class="fa-solid fa-hourglass-half fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Grafik Tren Membaca -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4">Tren Membaca (6 Bulan Terakhir)</h6>
                    <canvas id="literacyChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4"><i class="fa-solid fa-ranking-star text-warning me-2"></i> Top 5 Readers</h6>
                    @if($leaderboard->count() > 0)
                        <div class="list-group list-group-flush rounded bg-transparent">
                            @foreach($leaderboard as $idx => $peserta)
                            <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-3 {{ !$loop->last ? 'border-bottom' : 'border-0' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-4 fw-bold {{ $idx == 0 ? 'text-warning' : ($idx == 1 ? 'text-secondary' : ($idx == 2 ? 'text-danger' : 'text-muted')) }}">
                                        #{{ $idx + 1 }}
                                    </div>
                                    <img src="{{ $peserta->anggota->foto_url }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ Str::limit($peserta->anggota->user->name, 20) }}</h6>
                                        <small class="text-muted">{{ $peserta->programLiterasi->nama_program }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill fs-6">{{ $peserta->total_poin }} Poin</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted mt-5">
                            <p>Belum ada data peserta berprestasi.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('literacyChart').getContext('2d');
    var literacyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Buku Selesai Dibaca',
                data: {!! json_encode($data) !!},
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                borderWidth: 3,
                tension: 0.4, // smooth curves
                fill: true,
                pointBackgroundColor: '#4361ee',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
@endpush
