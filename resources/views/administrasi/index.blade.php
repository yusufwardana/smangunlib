@extends('layouts.app')

@section('title', 'Dokumen: ' . ucwords(str_replace('_', ' ', $kategori)))

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Manajemen {{ ucwords(str_replace('_', ' ', $kategori)) }}</h3>
            <p class="text-muted mb-0">Kelola dan arsipkan dokumen legalitas perpustakaan.</p>
        </div>
        <div>
            <a href="{{ route('administrasi.create', $kategori) }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-cloud-arrow-up me-2"></i> Unggah Dokumen Baru
            </a>
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
            <div class="table-responsive">
                <table id="tabelAdministrasi" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Format</th>
                            <th width="35%">Judul Dokumen</th>
                            <th width="15%">Uploader</th>
                            <th width="10%">Status</th>
                            <th width="10%">Ukuran</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Diisi via AJAX oleh DataTables -->
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
        var table = $('#tabelAdministrasi').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('administrasi.index', $kategori) }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'file', name: 'file', orderable: false, searchable: false},
                {data: 'judul', name: 'judul'},
                {data: 'uploader_name', name: 'uploader.name'},
                {
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        return data === 'aktif' 
                            ? '<span class="badge bg-success-subtle text-success rounded-pill px-3">Aktif</span>' 
                            : '<span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Arsip</span>';
                    }
                },
                {data: 'ukuran_file', name: 'ukuran_file', render: function(data) { return data + ' KB'; }},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                search: "Cari Dokumen:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ dokumen"
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
        });

        // SweetAlert untuk Konfirmasi Hapus
        $('body').on('click', '.btn-delete', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Hapus Dokumen?',
                text: "Dokumen yang dihapus tidak bisa dikembalikan secara langsung (Soft Delete).",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63946',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('administrasi/'.$kategori) }}/" + id,
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function (data) {
                            table.draw();
                            Swal.fire('Terhapus!', data.message, 'success');
                        },
                        error: function (data) {
                            Swal.fire('Error!', 'Gagal menghapus dokumen.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
