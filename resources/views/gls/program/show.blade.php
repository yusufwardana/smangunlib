@extends('layouts.app')

@section('title', 'Detail Program GLS')

@section('content')
<div class="fade-in">
    <div class="mb-4 d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('gls.program.index') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0">Detail Program GLS</h3>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('gls.export', ['program_id' => $program->id]) }}" class="btn btn-success text-white rounded-pill shadow-sm px-4">
                <i class="fa-solid fa-file-excel me-2"></i> Export Excel
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white" style="background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ $program->nama_program }}</h5>
                    <p class="mb-4 text-white-50 small">{{ $program->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                    
                    <ul class="list-group list-group-flush small bg-transparent">
                        <li class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-white-50">
                            <span>Status</span>
                            <span class="fw-bold">{{ strtoupper($program->status) }}</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-white-50">
                            <span>Target Membaca</span>
                            <span class="fw-bold">{{ $program->target_baca }} Buku</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white px-0 d-flex justify-content-between border-white-50">
                            <span>Periode</span>
                            <span class="fw-bold">{{ $program->periode_mulai->format('d/m/Y') }} - {{ $program->periode_selesai->format('d/m/Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-0">
                    <ul class="nav nav-tabs nav-fill border-0" id="glsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold py-3 text-dark border-0 rounded-0" data-bs-toggle="tab" data-bs-target="#peserta" type="button">Klasemen Peserta</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-3 text-dark border-0 rounded-0" data-bs-toggle="tab" data-bs-target="#dokumentasi" type="button">Dokumentasi & Laporan</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="glsTabsContent">
                        
                        <!-- TAB PESERTA / LEADERBOARD -->
                        <div class="tab-pane fade show active" id="peserta">
                            @if($program->peserta->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Rank</th>
                                            <th>Nama Peserta</th>
                                            <th>Tipe</th>
                                            <th>Status</th>
                                            <th class="text-end">Total Poin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sortedPeserta = $program->peserta->sortByDesc('total_poin')->values();
                                        @endphp
                                        @foreach($sortedPeserta as $idx => $peserta)
                                        <tr>
                                            <td><span class="fw-bold fs-5 {{ $idx == 0 ? 'text-warning' : '' }}">#{{ $idx + 1 }}</span></td>
                                            <td>
                                                <strong>{{ $peserta->anggota->user->name ?? '-' }}</strong><br>
                                                <small class="text-muted">{{ $peserta->anggota->nomor_anggota }}</small>
                                            </td>
                                            <td>{{ strtoupper($peserta->anggota->tipe_anggota) }}</td>
                                            <td>
                                                @if($peserta->status == 'lulus') <span class="badge bg-success">Lulus</span>
                                                @elseif($peserta->status == 'gagal') <span class="badge bg-danger">Gagal</span>
                                                @else <span class="badge bg-primary">Aktif</span> @endif
                                            </td>
                                            <td class="text-end fw-bold text-primary fs-5">{{ $peserta->total_poin }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                                <div class="text-center text-muted p-5 bg-light rounded">
                                    <i class="fa-solid fa-users-slash fs-1 mb-3"></i>
                                    <p>Belum ada peserta yang mendaftar pada program ini.</p>
                                </div>
                            @endif
                        </div>

                        <!-- TAB DOKUMENTASI -->
                        <div class="tab-pane fade" id="dokumentasi">
                            <!-- Form Upload -->
                            <form action="{{ route('gls.program.dokumentasi', $program->id) }}" method="POST" enctype="multipart/form-data" class="mb-4 bg-light p-3 rounded border border-dashed">
                                @csrf
                                <h6 class="fw-bold mb-3">Unggah Dokumentasi Baru</h6>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Jenis File</label>
                                        <select name="tipe_file" class="form-select form-select-sm" required>
                                            <option value="foto">Foto Kegiatan</option>
                                            <option value="pdf">Laporan (PDF)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">File (Max 5MB)</label>
                                        <input type="file" name="file" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold"><i class="fa-solid fa-upload"></i> Unggah</button>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <input type="text" name="keterangan" class="form-control form-control-sm" placeholder="Keterangan singkat / caption...">
                                    </div>
                                </div>
                            </form>

                            <!-- Gallery -->
                            <div class="row g-3">
                                @forelse($program->dokumentasi as $doc)
                                    <div class="col-md-4">
                                        <div class="card h-100 shadow-sm border-0">
                                            @if($doc->tipe_file == 'foto')
                                                <img src="{{ $doc->file_url }}" class="card-img-top" style="height: 120px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary d-flex align-items-center justify-content-center text-white" style="height: 120px;">
                                                    <i class="fa-solid fa-file-pdf fs-1"></i>
                                                </div>
                                            @endif
                                            <div class="card-body p-2 text-center">
                                                <small class="d-block mb-2">{{ Str::limit($doc->keterangan ?? 'Tanpa Keterangan', 30) }}</small>
                                                <a href="{{ $doc->file_url }}" target="_blank" class="btn btn-sm btn-outline-dark w-100"><i class="fa-solid fa-download"></i> Buka File</a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted p-4">
                                        <p class="mb-0">Belum ada file dokumentasi yang diunggah.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
