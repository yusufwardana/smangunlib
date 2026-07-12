@extends('installer.layouts.master', ['step' => 8])

@section('content')
<h5 class="fw-bold mb-4">Pengaturan Awal Perpustakaan</h5>
<p class="text-muted mb-4">Tentukan regulasi dasar perpustakaan yang akan diterapkan secara global ke dalam sistem (Anda bisa mengubahnya nanti di Dashboard).</p>

<form action="{{ route('installer.config.store') }}" method="POST" id="configForm">
    @csrf
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <label class="form-label fw-bold">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="{{ date('Y') }}/{{ date('Y')+1 }}" required>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <label class="form-label fw-bold">Semester</label>
                    <select name="semester" class="form-select">
                        <option value="ganjil">Ganjil</option>
                        <option value="genap">Genap</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="col-md-12"><hr class="text-muted"></div>
        
        <div class="col-md-4">
            <label class="form-label fw-bold">Denda Keterlambatan</label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="denda_per_hari" class="form-control" value="1000" min="0" required>
                <span class="input-group-text">/ Hari</span>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">Lama Pinjam Default</label>
            <div class="input-group">
                <input type="number" name="lama_pinjam_default" class="form-control" value="7" min="1" required>
                <span class="input-group-text">Hari</span>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">Batas Max Pinjam Buku</label>
            <div class="input-group">
                <input type="number" name="maksimal_pinjam" class="form-control" value="3" min="1" required>
                <span class="input-group-text">Buku</span>
            </div>
        </div>

        <!-- System -->
        <input type="hidden" name="timezone" value="Asia/Jakarta">
        <input type="hidden" name="locale" value="id">
    </div>
</form>
@endsection

@section('footer')
    <div></div>
    <button type="button" onclick="document.getElementById('configForm').submit()" class="btn btn-primary rounded-pill px-4 fw-bold">Selesaikan Instalasi <i class="fa-solid fa-flag-checkered ms-2"></i></button>
@endsection
