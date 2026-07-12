@extends('layouts.app')

@section('title', 'Dashboard - SMAN GUN LIB')

@section('content')
<div class="fade-in">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Dashboard Overview</h3>
            <p class="text-muted mb-0">Selamat datang kembali, {{ Auth::user()->name }}. Berikut adalah ringkasan hari ini.</p>
        </div>
        <div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fa-solid fa-plus me-2"></i> Transaksi Baru</button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-primary h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Total Buku</h6>
                        <h2 class="fw-bold mb-0">12,450</h2>
                        <small class="text-white-50"><i class="fa-solid fa-arrow-up me-1"></i> +124 bulan ini</small>
                    </div>
                    <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-success h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Total Anggota</h6>
                        <h2 class="fw-bold mb-0">1,204</h2>
                        <small class="text-white-50"><i class="fa-solid fa-arrow-up me-1"></i> +45 bulan ini</small>
                    </div>
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-warning h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Buku Dipinjam</h6>
                        <h2 class="fw-bold mb-0">342</h2>
                        <small class="text-white-50"><i class="fa-solid fa-rotate me-1"></i> Sedang sirkulasi</small>
                    </div>
                    <div class="stat-icon"><i class="fa-solid fa-hand-holding-hand"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-danger h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Buku Terlambat</h6>
                        <h2 class="fw-bold mb-0">18</h2>
                        <small class="text-white-50"><i class="fa-solid fa-triangle-exclamation me-1"></i> Perlu ditindak</small>
                    </div>
                    <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Statistik Peminjaman (Tahun Ini)</h5>
                    <select class="form-select form-select-sm w-auto rounded-pill border-0 bg-light">
                        <option>2026</option>
                        <option>2025</option>
                    </select>
                </div>
                <div class="card-body p-4">
                    <canvas id="peminjamanChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Pengunjung Hari Ini</h5>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <canvas id="pengunjungChart" height="200"></canvas>
                    <div class="mt-4 text-center">
                        <h3 class="fw-bold text-primary mb-0">142</h3>
                        <span class="text-muted">Total Pengunjung Hari Ini</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables & Notifications Row -->
    <div class="row g-4">
        <!-- Aktivitas Terbaru / DataTables -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Peminjaman Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelPeminjaman" class="table table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No TRX</th>
                                    <th>Peminjam</th>
                                    <th>Judul Buku</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="fw-bold text-primary">TRX-001</span></td>
                                    <td>Andi Saputra <br><small class="text-muted">Siswa - XII IPA 1</small></td>
                                    <td>Bumi Manusia <br><small class="text-muted">Pramoedya Ananta Toer</small></td>
                                    <td>08 Jul 2026</td>
                                    <td><span class="badge bg-primary-subtle text-primary rounded-pill px-3">Dipinjam</span></td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold text-primary">TRX-002</span></td>
                                    <td>Rina Melati <br><small class="text-muted">Siswa - XI IPS 2</small></td>
                                    <td>Laskar Pelangi <br><small class="text-muted">Andrea Hirata</small></td>
                                    <td>05 Jul 2026</td>
                                    <td><span class="badge bg-warning-subtle text-warning rounded-pill px-3">Terlambat</span></td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold text-primary">TRX-003</span></td>
                                    <td>Drs. Haryanto <br><small class="text-muted">Guru Sejarah</small></td>
                                    <td>Sapiens <br><small class="text-muted">Yuval Noah Harari</small></td>
                                    <td>01 Jul 2026</td>
                                    <td><span class="badge bg-success-subtle text-success rounded-pill px-3">Dikembalikan</span></td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold text-primary">TRX-004</span></td>
                                    <td>Budi Doremi <br><small class="text-muted">Siswa - X IPA 3</small></td>
                                    <td>Filosofi Teras <br><small class="text-muted">Henry Manampiring</small></td>
                                    <td>07 Jul 2026</td>
                                    <td><span class="badge bg-primary-subtle text-primary rounded-pill px-3">Dipinjam</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifikasi / Quick Actions -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Aktivitas Sistem</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush border-0">
                        <a href="#" class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-start gap-3">
                            <div class="bg-success-subtle text-success rounded-circle p-2 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-right-to-bracket"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Pengembalian Buku</h6>
                                <p class="mb-0 text-muted small">Andi Saputra mengembalikan buku "Fisika Kelas XII".</p>
                                <small class="text-muted" style="font-size: 0.7rem;">10 menit yang lalu</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-start gap-3">
                            <div class="bg-danger-subtle text-danger rounded-circle p-2 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-money-bill"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Pembayaran Denda</h6>
                                <p class="mb-0 text-muted small">Rina Melati membayar denda keterlambatan Rp 15.000.</p>
                                <small class="text-muted" style="font-size: 0.7rem;">1 jam yang lalu</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-start gap-3">
                            <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Buku Baru Ditambahkan</h6>
                                <p class="mb-0 text-muted small">Admin menambahkan 12 eksemplar buku baru ke rak sejarah.</p>
                                <small class="text-muted" style="font-size: 0.7rem;">Kemarin, 14:30</small>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-3">
                    <button class="btn btn-light rounded-pill px-4 text-primary fw-bold w-100">Lihat Semua Aktivitas</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#tabelPeminjaman').DataTable({
            "language": {
                "search": "Cari Data:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            "pageLength": 4,
            "lengthChange": false,
            "dom": '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
        });

        // Initialize Peminjaman Chart (Line)
        const ctxPeminjaman = document.getElementById('peminjamanChart').getContext('2d');
        
        // Gradient for Line Chart
        let gradientBlue = ctxPeminjaman.createLinearGradient(0, 0, 0, 400);
        gradientBlue.addColorStop(0, 'rgba(67, 97, 238, 0.4)');
        gradientBlue.addColorStop(1, 'rgba(67, 97, 238, 0.0)');

        new Chart(ctxPeminjaman, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Total Peminjaman',
                    data: [120, 190, 150, 220, 180, 250, 210, 190, 230, 280, 240, 200],
                    borderColor: '#4361ee',
                    backgroundColor: gradientBlue,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4361ee',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });

        // Initialize Pengunjung Chart (Doughnut)
        const ctxPengunjung = document.getElementById('pengunjungChart').getContext('2d');
        new Chart(ctxPengunjung, {
            type: 'doughnut',
            data: {
                labels: ['Siswa', 'Guru', 'Umum'],
                datasets: [{
                    data: [110, 25, 7],
                    backgroundColor: [
                        '#4361ee', // Primary
                        '#2a9d8f', // Success
                        '#f77f00'  // Warning
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
