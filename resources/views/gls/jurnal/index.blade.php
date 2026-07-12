@extends('layouts.app')

@section('title', 'Verifikasi Jurnal Membaca')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Verifikasi Jurnal & Log Bacaan</h3>
            <p class="text-muted mb-0">Review ringkasan/bacaan siswa dan berikan poin literasi.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Filter Status Jurnal</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="pending">Menunggu Verifikasi (Pending)</option>
                        <option value="disetujui">Disetujui (Approved)</option>
                        <option value="ditolak">Ditolak (Rejected)</option>
                        <option value="">Tampilkan Semua</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tabelJurnal" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">Peserta / Program</th>
                            <th width="20%">Judul Buku & Waktu</th>
                            <th width="45%">Refleksi / Ringkasan Bacaan</th>
                            <th width="20%">Aksi / Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Poin -->
<div class="modal fade" id="modalVerify" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h6 class="modal-title fw-bold"><i class="fa-solid fa-check-circle me-2"></i> Setujui Jurnal</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="mb-3 text-muted small">Berapa poin literasi yang ingin Anda berikan atas kualitas review bacaan ini?</p>
                <input type="number" id="inputPoin" class="form-control form-control-lg text-center fw-bold mb-3" value="10" min="0">
                <input type="hidden" id="verifyLogId">
                <button type="button" class="btn btn-success w-100 fw-bold" id="btnProsesVerify">Simpan & Setujui</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#tabelJurnal').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('gls.jurnal.index') }}",
                data: function (d) {
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {data: 'peserta', name: 'pesertaLiterasi.anggota.user.name'},
                {data: 'bacaan', name: 'judul_buku_luar'},
                {data: 'refleksi_text', name: 'refleksi', orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                search: "Cari Siswa/Buku:",
                lengthMenu: "Tampilkan _MENU_ baris",
                emptyTable: "Yeay! Semua jurnal sudah diverifikasi."
            }
        });

        $('#filterStatus').on('change', function() {
            table.draw();
        });

        // Action Logic
        $('body').on('click', '.btn-verify', function() {
            let id = $(this).data('id');
            let action = $(this).data('action');

            if(action === 'disetujui') {
                $('#verifyLogId').val(id);
                $('#inputPoin').val(10); // default
                var myModal = new bootstrap.Modal(document.getElementById('modalVerify'));
                myModal.show();
            } else {
                Swal.fire({
                    title: 'Tolak Jurnal?',
                    text: "Jurnal dianggap tidak valid/plagiat. Poin = 0.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e63946',
                    confirmButtonText: 'Ya, Tolak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        prosesAJAX(id, 'ditolak', 0);
                    }
                });
            }
        });

        $('#btnProsesVerify').click(function() {
            let id = $('#verifyLogId').val();
            let poin = $('#inputPoin').val();
            
            let modalEl = document.getElementById('modalVerify');
            let modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            prosesAJAX(id, 'disetujui', poin);
        });

        function prosesAJAX(id, status, poin) {
            $.ajax({
                type: "POST",
                url: "{{ route('gls.jurnal.verify') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "log_id": id,
                    "status_verifikasi": status,
                    "poin_diberikan": poin
                },
                success: function (data) {
                    if(data.success) {
                        table.draw();
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({ icon: 'success', title: data.message });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                }
            });
        }
    });
</script>
@endpush
