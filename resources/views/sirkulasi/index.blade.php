@extends('layouts.app')

@section('title', 'Sirkulasi Perpustakaan')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Sirkulasi & Peminjaman</h3>
            <p class="text-muted mb-0">Pantau transaksi harian perpustakaan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sirkulasi.pengembalian.form') }}" class="btn btn-success rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-right-left me-2"></i> Pengembalian
            </a>
            <a href="{{ route('sirkulasi.peminjaman.form') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-hand-holding-hand me-2"></i> Peminjaman Baru
            </a>
        </div>
    </div>

    <!-- Dashboard Stats Mini -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Sedang Dipinjam</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['sedang_dipinjam'] }} <span class="fs-6 fw-normal">Trx</span></h3>
                    </div>
                    <i class="fa-solid fa-book-open-reader fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Terlambat</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['terlambat'] }} <span class="fs-6 fw-normal">Trx</span></h3>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2">Total Transaksi</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_transaksi'] }} <span class="fs-6 fw-normal">Trx</span></h3>
                    </div>
                    <i class="fa-solid fa-boxes-stacked fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-black-50 text-uppercase fw-bold mb-2">Piutang Denda</h6>
                        <h3 class="fw-bold mb-0">Rp {{ number_format($stats['denda_belum_dibayar'], 0, ',', '.') }}</h3>
                    </div>
                    <i class="fa-solid fa-money-bill-wave fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Filter Status</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="dipinjam">Sedang Dipinjam</option>
                        <option value="dikembalikan">Selesai (Dikembalikan)</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold small text-muted">Rentang Waktu Peminjaman</label>
                    <div class="input-group input-group-sm">
                        <input type="date" id="tglDari" class="form-control">
                        <span class="input-group-text">s/d</span>
                        <input type="date" id="tglSampai" class="form-control">
                        <button class="btn btn-outline-secondary" id="btnFilterTgl">Filter</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tabelSirkulasi" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">No Transaksi</th>
                            <th width="20%">Peminjam</th>
                            <th width="20%">Waktu Pinjam & Tempo</th>
                            <th width="10%">Perpanjangan</th>
                            <th width="15%">Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#tabelSirkulasi').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('sirkulasi.index') }}",
                data: function (d) {
                    d.status = $('#filterStatus').val();
                    d.tanggal_dari = $('#tglDari').val();
                    d.tanggal_sampai = $('#tglSampai').val();
                }
            },
            columns: [
                {data: 'nomor_transaksi', name: 'nomor_transaksi'},
                {data: 'peminjam', name: 'anggota.user.name'},
                {data: 'tanggal', name: 'tanggal_pinjam'},
                {data: 'perpanjangan_count', name: 'perpanjangan_count', render: function(data) { return data + ' Kali'; }},
                {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                search: "Cari No. TRX / Peminjam:",
                lengthMenu: "Tampilkan _MENU_ baris"
            },
            order: [[2, 'desc']] // Sort by date desc
        });

        $('#filterStatus').on('change', function() { table.draw(); });
        $('#btnFilterTgl').on('click', function() { table.draw(); });

        // Perpanjangan AJAX
        $('body').on('click', '.btn-perpanjang', function() {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Perpanjang Peminjaman?',
                text: "Masa pinjam akan ditambah 3 hari dari tanggal jatuh tempo saat ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4361ee',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Perpanjang!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('sirkulasi/perpanjang') }}/" + id,
                        data: { "_token": "{{ csrf_token() }}" },
                        success: function (data) {
                            if(data.success) {
                                table.draw();
                                Swal.fire('Berhasil!', data.message, 'success');
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
