@extends('layouts.app')

@section('title', $judulLaporan)

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">{{ $judulLaporan }}</h3>
            <p class="text-muted mb-0">Periode: {{ $start ? $start->format('d/m/Y') : 'Awal' }} s/d {{ $end ? $end->format('d/m/Y') : 'Sekarang' }}</p>
        </div>
        <div class="d-flex gap-2">
            <!-- Gunakan request full url dan tambahkan params export -->
            <a href="{{ request()->fullUrlWithQuery(['export' => 'print']) }}" target="_blank" class="btn btn-outline-dark rounded-pill shadow-sm px-4 fw-bold">
                <i class="fa-solid fa-print me-2"></i> Cetak / PDF
            </a>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success rounded-pill shadow-sm px-4 fw-bold text-white">
                <i class="fa-solid fa-file-excel me-2"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Area Grafik Statistik (Tampil jika data > 0) -->
    @if($data->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold mb-4">Grafik Distribusi Data</h6>
                    <!-- ChartJS Container -->
                    <div style="height: 250px; width: 100%; display: flex; justify-content: center;">
                        <canvas id="laporanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Area Tabel Data -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <!-- Kita akan merender view Print di sini agar seragam, namun dibungkus UI web -->
                @include('laporan.print', ['isExcel' => false, 'isHtmlPreview' => true])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Simple Chart Logic Generator based on Type
    @if($data->count() > 0)
        const tipe = "{{ $tipe }}";
        const rawData = {!! json_encode($data) !!};
        
        let labels = [];
        let dataPoints = [];
        let chartType = 'bar'; // Default

        // Aggregate Data for Chart
        if(tipe === 'peminjaman' || tipe === 'pengunjung') {
            chartType = 'line';
            let tglCount = {};
            rawData.forEach(item => {
                let tgl = item.tanggal_pinjam ? item.tanggal_pinjam.split('T')[0] : (item.tanggal_kunjungan ? item.tanggal_kunjungan.split('T')[0] : item.created_at.split('T')[0]);
                tglCount[tgl] = (tglCount[tgl] || 0) + 1;
            });
            labels = Object.keys(tglCount).sort();
            dataPoints = labels.map(l => tglCount[l]);
        } else if(tipe === 'inventaris') {
            chartType = 'pie';
            let kondisiCount = {};
            rawData.forEach(item => {
                kondisiCount[item.kondisi] = (kondisiCount[item.kondisi] || 0) + 1;
            });
            labels = Object.keys(kondisiCount);
            dataPoints = Object.values(kondisiCount);
        } else {
            chartType = 'bar';
            let catCount = {};
            rawData.forEach(item => {
                let cat = item.kategori_dokumen || item.tipe_anggota || item.status || 'Data';
                catCount[cat] = (catCount[cat] || 0) + 1;
            });
            labels = Object.keys(catCount);
            dataPoints = Object.values(catCount);
        }

        var ctx = document.getElementById('laporanChart').getContext('2d');
        new Chart(ctx, {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah',
                    data: dataPoints,
                    backgroundColor: ['#4361ee', '#3f37c9', '#4895ef', '#4cc9f0', '#f72585'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    @endif
</script>
@endpush
