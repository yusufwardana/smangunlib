@extends('layouts.app')

@section('title', 'Data Anggota')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Manajemen Anggota</h3>
            <p class="text-muted mb-0">Kelola data keanggotaan Siswa, Guru, dan Staf.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('anggota.export') }}" class="btn btn-success rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-file-excel me-2"></i> Export Excel
            </a>
            <a href="{{ route('anggota.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-user-plus me-2"></i> Pendaftaran Baru
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
                    <label class="form-label fw-bold small text-muted">Tipe Anggota</label>
                    <select id="filterTipe" class="form-select form-select-sm">
                        <option value="">Semua Tipe</option>
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru</option>
                        <option value="tendik">Staf / Tendik</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Status</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="non-aktif">Non-aktif</option>
                        <option value="blacklist">Blacklist</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-end">
                    <button class="btn btn-dark btn-sm rounded-pill px-4" id="btnCetakMassal" disabled>
                        <i class="fa-solid fa-print me-1"></i> Cetak Kartu Terpilih
                    </button>
                </div>
            </div>

            <form id="formCetakMassal" action="{{ route('anggota.print_kartu') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="ids" id="cetakIds">
            </form>

            <div class="table-responsive">
                <table id="tabelAnggota" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="3%"><input type="checkbox" id="checkAll"></th>
                            <th width="7%">Foto</th>
                            <th width="25%">Nama & Nomor</th>
                            <th width="15%">Identitas (NIS/NIP)</th>
                            <th width="15%">Kontak</th>
                            <th width="15%">Tipe Anggota</th>
                            <th width="10%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Diisi via DataTables -->
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
        var table = $('#tabelAnggota').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('anggota.index') }}",
                data: function (d) {
                    d.tipe_anggota = $('#filterTipe').val();
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {
                    data: 'id', 
                    name: 'checkbox',
                    orderable: false, 
                    searchable: false,
                    render: function(data) {
                        return '<input type="checkbox" class="check-anggota" value="'+data+'">';
                    }
                },
                {data: 'foto_profil', name: 'foto', orderable: false, searchable: false},
                {data: 'nama_lengkap', name: 'user.name'},
                {data: 'no_identitas', name: 'no_identitas'},
                {data: 'no_telepon', name: 'no_telepon'},
                {
                    data: 'tipe_anggota', 
                    name: 'tipe_anggota',
                    render: function(data) { return data.toUpperCase(); }
                },
                {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                search: "Cari Anggota:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
            }
        });

        $('#filterTipe, #filterStatus').on('change', function() {
            table.draw();
        });

        // Mass Print Checkbox Logic
        $('#checkAll').on('click', function() {
            $('.check-anggota').prop('checked', this.checked);
            togglePrintBtn();
        });
        
        $('body').on('click', '.check-anggota', function() {
            togglePrintBtn();
        });

        function togglePrintBtn() {
            var checked = $('.check-anggota:checked').length;
            $('#btnCetakMassal').prop('disabled', checked === 0);
        }

        $('#btnCetakMassal').on('click', function() {
            var ids = [];
            $('.check-anggota:checked').each(function() {
                ids.push($(this).val());
            });
            $('#cetakIds').val(ids.join(','));
            $('#formCetakMassal').submit();
        });

        // Delete (Non-aktifkan) Button
        $('body').on('click', '.btn-delete', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Non-aktifkan Anggota?',
                text: "Data anggota tidak dihapus dari sistem (Soft Delete).",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63946',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Nonaktifkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('keanggotaan/anggota') }}/" + id,
                        data: { "_token": "{{ csrf_token() }}" },
                        success: function (data) {
                            table.draw();
                            Swal.fire('Berhasil!', data.message, 'success');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
