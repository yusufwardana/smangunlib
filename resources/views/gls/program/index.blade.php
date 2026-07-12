@extends('layouts.app')

@section('title', 'Program Literasi')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Manajemen Program GLS</h3>
            <p class="text-muted mb-0">Buat tantangan membaca, lomba, atau program monitoring.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('gls.dashboard') }}" class="btn btn-light rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali
            </a>
            <a href="{{ route('gls.program.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-plus me-2"></i> Buat Program Baru
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
                <table id="tabelProgram" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Nama Program</th>
                            <th width="25%">Periode Pelaksanaan</th>
                            <th width="15%">Target (Buku)</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
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
        $('#tabelProgram').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('gls.program.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'nama_program', name: 'nama_program'},
                {data: 'periode', name: 'periode', searchable: false},
                {data: 'target_baca', name: 'target_baca', render: function(data){ return data + ' Buku'; }},
                {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                search: "Cari Program:",
                lengthMenu: "Tampilkan _MENU_ baris"
            }
        });
    });
</script>
@endpush
