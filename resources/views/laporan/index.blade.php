@extends('layouts.app')

@section('title', 'Pusat Laporan')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Pusat Laporan & Analitik</h3>
            <p class="text-muted mb-0">Hasilkan laporan komprehensif untuk pengawasan dan evaluasi.</p>
        </div>
    </div>

    <div class="row g-4">
        @php
            $menus = [
                ['tipe' => 'koleksi', 'judul' => 'Koleksi Buku', 'icon' => 'fa-book', 'color' => 'primary', 'desc' => 'Laporan pertumbuhan koleksi dan eksemplar.'],
                ['tipe' => 'anggota', 'judul' => 'Keanggotaan', 'icon' => 'fa-users', 'color' => 'success', 'desc' => 'Laporan daftar anggota dan statistik pendaftaran.'],
                ['tipe' => 'peminjaman', 'judul' => 'Sirkulasi (Pinjam)', 'icon' => 'fa-hand-holding-hand', 'color' => 'info', 'desc' => 'Laporan pergerakan sirkulasi buku harian.'],
                ['tipe' => 'denda', 'judul' => 'Keuangan (Denda)', 'icon' => 'fa-money-bill-wave', 'color' => 'warning', 'desc' => 'Laporan penerimaan denda dan status piutang.'],
                ['tipe' => 'pengunjung', 'judul' => 'Buku Tamu', 'icon' => 'fa-shoe-prints', 'color' => 'secondary', 'desc' => 'Laporan statistik kunjungan fisik perpustakaan.'],
                ['tipe' => 'inventaris', 'judul' => 'Sarana Prasarana', 'icon' => 'fa-boxes-stacked', 'color' => 'dark', 'desc' => 'Laporan inventaris rak, meja, dan fasilitas.'],
                ['tipe' => 'literasi', 'judul' => 'Program GLS', 'icon' => 'fa-award', 'color' => 'primary', 'desc' => 'Laporan partisipasi dan keberhasilan GLS.'],
                ['tipe' => 'evaluasi', 'judul' => 'Dokumen Evaluasi', 'icon' => 'fa-file-signature', 'color' => 'danger', 'desc' => 'Rekapitulasi berkas hasil evaluasi perpustakaan.']
            ];
        @endphp

        @foreach($menus as $menu)
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-elevate cursor-pointer btn-report" data-tipe="{{ $menu['tipe'] }}" data-judul="{{ $menu['judul'] }}">
                <div class="card-body p-4 text-center">
                    <div class="bg-{{ $menu['color'] }}-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fa-solid {{ $menu['icon'] }} fs-2 text-{{ $menu['color'] }}"></i>
                    </div>
                    <h6 class="fw-bold mb-2">{{ $menu['judul'] }}</h6>
                    <p class="text-muted small mb-0">{{ $menu['desc'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Filter Generator -->
<div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('laporan.generate') }}" method="GET" target="_blank">
                <div class="modal-header border-0 bg-light rounded-top-4 p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Konfigurasi Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="tipe_laporan" id="inputTipeLaporan">
                    
                    <!-- Date Range -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted"><i class="fa-regular fa-calendar me-1"></i> Rentang Tanggal</label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control" required>
                            <span class="input-group-text bg-white border-start-0 border-end-0">s/d</span>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <small class="text-muted mt-1 d-block">Kosongkan jika ingin mencetak seluruh data tanpa batasan waktu (hapus atribut required via DOM jika diizinkan, di sini kita set required agar data tidak terlalu besar).</small>
                    </div>

                    <!-- Kategori Khusus (Dinamis via JS nantinya) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted"><i class="fa-solid fa-filter me-1"></i> Filter Kategori (Opsional)</label>
                        <select name="kategori" class="form-select">
                            <option value="">-- Semua Kategori --</option>
                            <!-- Tambahkan dinamis jika perlu -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-bolt me-2"></i> Generate Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .hover-elevate { transition: all 0.3s ease; }
    .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; border-color: var(--bs-primary) !important; }
</style>
@endsection

@push('scripts')
<script>
    $('.btn-report').click(function() {
        let tipe = $(this).data('tipe');
        let judul = $(this).data('judul');
        
        $('#inputTipeLaporan').val(tipe);
        $('#modalTitle').text('Parameter Laporan: ' + judul);
        
        var modal = new bootstrap.Modal(document.getElementById('modalFilter'));
        modal.show();
    });
</script>
@endpush
