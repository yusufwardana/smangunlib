@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Katalog Buku</h3>
            <p class="text-muted mb-0">Kelola data buku fisik dan e-book.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('koleksi.buku.export') }}" class="btn btn-success rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-file-excel me-2"></i> Export Excel
            </a>
            <a href="{{ route('koleksi.buku.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-plus me-2"></i> Tambah Buku
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Filter Rak</label>
                    <select id="filterRak" class="form-select form-select-sm">
                        <option value="">Semua Rak</option>
                        @foreach($raks as $rak)
                            <option value="{{ $rak->id }}">{{ $rak->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Filter Kategori</label>
                    <select id="filterKategori" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-end">
                    <button class="btn btn-dark btn-sm rounded-pill px-4" id="btnCetakMassal" disabled>
                        <i class="fa-solid fa-print me-1"></i> Cetak Barcode Terpilih
                    </button>
                </div>
            </div>

            <form id="formCetakMassal" action="{{ route('koleksi.buku.print_barcode') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="ids" id="cetakIds">
            </form>

            <div class="table-responsive">
                <table id="tabelBuku" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="3%"><input type="checkbox" id="checkAll"></th>
                            <th width="8%">Cover</th>
                            <th width="30%">Judul & Pengarang</th>
                            <th width="15%">Penerbit</th>
                            <th width="15%">ISBN</th>
                            <th width="15%">Stok Fisik</th>
                            <th width="14%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables content -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#tabelBuku').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('koleksi.buku.index') }}",
                data: function (d) {
                    d.rak_lokasi_id = $('#filterRak').val();
                    d.kategori_id = $('#filterKategori').val();
                }
            },
            columns: [
                {
                    data: 'id', 
                    name: 'checkbox',
                    orderable: false, 
                    searchable: false,
                    render: function(data) {
                        return '<input type="checkbox" class="check-buku" value="'+data+'">';
                    }
                },
                {data: 'cover', name: 'cover', orderable: false, searchable: false},
                {data: 'judul_buku', name: 'judul'},
                {data: 'penerbit', name: 'penerbit'},
                {data: 'isbn', name: 'isbn'},
                {data: 'stok', name: 'stok', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                search: "Cari Buku:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
            }
        });

        $('#filterRak, #filterKategori').on('change', function() {
            table.draw();
        });

        // Handle Checkbox for Mass Print
        $('#checkAll').on('click', function() {
            $('.check-buku').prop('checked', this.checked);
            togglePrintBtn();
        });
        
        $('body').on('click', '.check-buku', function() {
            togglePrintBtn();
        });

        function togglePrintBtn() {
            var checked = $('.check-buku:checked').length;
            $('#btnCetakMassal').prop('disabled', checked === 0);
        }

        $('#btnCetakMassal').on('click', function() {
            var ids = [];
            $('.check-buku:checked').each(function() {
                ids.push($(this).val());
            });
            $('#cetakIds').val(ids.join(','));
            $('#formCetakMassal').submit();
        });

        // SweetAlert Delete
        $('body').on('click', '.btn-delete', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Hapus Buku?',
                text: "Buku akan masuk ke Trash (Soft Delete).",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63946',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('koleksi/buku') }}/" + id,
                        data: { "_token": "{{ csrf_token() }}" },
                        success: function (data) {
                            table.draw();
                            Swal.fire('Terhapus!', data.message, 'success');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
