@extends('layouts.app')

@section('title', 'Transaksi Peminjaman')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center gap-3">
        <a href="{{ route('sirkulasi.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">Transaksi Peminjaman Baru</h3>
            <p class="text-muted mb-0">Scan kartu anggota dan barcode buku fisik.</p>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('sirkulasi.peminjaman.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Data Peminjam -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary-subtle border-primary">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-user-check me-2"></i> 1. Identitas Peminjam</h6>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Scan / Pilih Anggota</label>
                            <select name="anggota_id" class="form-select @error('anggota_id') is-invalid @enderror" id="selectAnggota" required>
                                <option value="">Cari NIS/NIP atau Nama...</option>
                                @foreach($anggotas as $anggota)
                                    <option value="{{ $anggota->id }}" data-tipe="{{ $anggota->tipe_anggota }}">
                                        {{ $anggota->nomor_anggota }} - {{ $anggota->user->name ?? '' }} ({{ strtoupper($anggota->tipe_anggota) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('anggota_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 mt-4">
                            <label class="form-label fw-bold">Keterangan / Catatan Tambahan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional..."></textarea>
                        </div>
                        
                        <div class="alert alert-info border-0 mt-4 small">
                            <i class="fa-solid fa-circle-info me-1"></i> Masa pinjam standar adalah 7 hari kalender.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keranjang Buku -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4"><i class="fa-solid fa-barcode me-2"></i> 2. Scan Eksemplar Buku</h6>
                        
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" id="scanBarcode" class="form-control border-start-0 ps-0" placeholder="Arahkan kursor ke sini, lalu tembak barcode fisik buku..." autofocus>
                                <button class="btn btn-primary px-4" type="button" id="btnTambahBuku">Tambahkan</button>
                            </div>
                            <!-- Hidden Select mapping barcode to ID for JS logic -->
                            <select id="masterBuku" class="d-none">
                                @foreach($eksemplars as $eks)
                                    <option value="{{ $eks->id }}" data-barcode="{{ $eks->nomor_barcode }}" data-judul="{{ $eks->buku->judul }}">
                                        {{ $eks->nomor_barcode }} - {{ $eks->buku->judul }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle border" id="tabelKeranjang">
                                <thead class="table-light">
                                    <tr>
                                        <th width="20%">No. Barcode</th>
                                        <th width="65%">Judul Buku</th>
                                        <th width="15%" class="text-center">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyRow">
                                        <td colspan="3" class="text-center text-muted py-4">Belum ada buku yang di-scan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold" id="btnProses" disabled>
                                Proses Peminjaman <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let items = [];

        $('#btnTambahBuku').click(function() {
            let barcode = $('#scanBarcode').val().trim();
            if(barcode === '') return;

            let option = $('#masterBuku option[data-barcode="'+barcode+'"]');
            
            if(option.length > 0) {
                let id = option.val();
                let judul = option.data('judul');

                // Cek duplikasi di keranjang
                if(!items.includes(id)) {
                    if(items.length >= 3) {
                        Swal.fire('Batas Maksimal', 'Anggota hanya boleh meminjam maksimal 3 buku.', 'warning');
                        $('#scanBarcode').val('');
                        return;
                    }

                    items.push(id);
                    $('#emptyRow').hide();
                    
                    let html = `<tr id="row-${id}">
                        <td><span class="fw-bold text-primary">${barcode}</span>
                            <input type="hidden" name="eksemplar_ids[]" value="${id}">
                        </td>
                        <td>${judul}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove" data-id="${id}"><i class="fa-solid fa-times"></i></button>
                        </td>
                    </tr>`;
                    $('#tabelKeranjang tbody').append(html);
                    
                    $('#btnProses').prop('disabled', false);
                    $('#scanBarcode').val('').focus();
                } else {
                    Swal.fire('Oops', 'Buku ini sudah ada di keranjang pinjam.', 'error');
                }
            } else {
                Swal.fire('Tidak Ditemukan', 'Barcode tidak ditemukan atau buku sedang tidak tersedia di rak.', 'error');
            }
        });

        // Trigger enter on barcode input
        $('#scanBarcode').keypress(function(e) {
            if(e.which == 13) {
                e.preventDefault();
                $('#btnTambahBuku').click();
            }
        });

        $('body').on('click', '.btn-remove', function() {
            let id = String($(this).data('id'));
            $(`#row-${id}`).remove();
            items = items.filter(item => item !== id);
            
            if(items.length === 0) {
                $('#emptyRow').show();
                $('#btnProses').prop('disabled', true);
            }
        });
    });
</script>
@endpush
