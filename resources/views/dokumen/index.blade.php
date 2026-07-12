@extends('layouts.app')

@section('title', 'Manajemen Dokumen')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Manajemen Dokumen (E-Archive)</h3>
            <p class="text-muted mb-0">Kelola SOP, Surat Keputusan, Laporan, dan dokumen penting lainnya.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dokumen.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-cloud-arrow-up me-2"></i> Unggah Dokumen
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
                    <label class="form-label fw-bold small text-muted">Filter Kategori</label>
                    <select id="filterKategori" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        <option value="Administrasi">Administrasi</option>
                        <option value="Legalitas">Legalitas</option>
                        <option value="Koleksi">Koleksi</option>
                        <option value="Sarana Prasarana">Sarana Prasarana</option>
                        <option value="Literasi">Literasi</option>
                        <option value="Evaluasi">Evaluasi</option>
                        <option value="SOP">SOP</option>
                        <option value="Program Kerja">Program Kerja</option>
                        <option value="Laporan">Laporan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Status / Validitas</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="aktif">Aktif (Berlaku)</option>
                        <option value="expired">Expired (Kadaluwarsa)</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tabelDokumen" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="35%">Nama Dokumen & Versi</th>
                            <th width="20%">Kategori</th>
                            <th width="15%">Uploader</th>
                            <th width="15%">Status Validitas</th>
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
        var table = $('#tabelDokumen').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dokumen.index') }}",
                data: function (d) {
                    d.kategori = $('#filterKategori').val();
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {data: 'file_info', name: 'judul'},
                {data: 'kategori_dokumen', name: 'kategori_dokumen'},
                {data: 'uploader.name', name: 'uploader.name', defaultContent: '-'},
                {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                search: "Cari Dokumen:",
                lengthMenu: "Tampilkan _MENU_ baris"
            }
        });

        $('#filterKategori, #filterStatus').on('change', function() {
            table.draw();
        });
    });
</script>
@endpush
