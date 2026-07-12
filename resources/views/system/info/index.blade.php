@extends('layouts.app')

@section('title', 'System Information')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Informasi Sistem</h3>
            <p class="text-muted mb-0">Monitor spesifikasi server dan kesehatan database.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- App Info -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold"><i class="fa-solid fa-layer-group me-2 text-primary"></i> Application Info</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr><td class="text-muted w-50">Nama Aplikasi</td><td class="fw-bold">{{ $app['app_name'] }}</td></tr>
                        <tr><td class="text-muted">Versi Saat Ini</td><td class="fw-bold"><span class="badge bg-primary rounded-pill">{{ $app['app_version'] }}</span></td></tr>
                        <tr><td class="text-muted">Build Number</td><td class="fw-bold">{{ $app['build_number'] }}</td></tr>
                        <tr><td class="text-muted">Release Date</td><td class="fw-bold">{{ $app['release_date'] }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Server Info -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold"><i class="fa-solid fa-server me-2 text-info"></i> Server Environment</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr><td class="text-muted w-50">PHP Version</td><td class="fw-bold">{{ $server['php_version'] }}</td></tr>
                        <tr><td class="text-muted">Laravel Version</td><td class="fw-bold">{{ $server['laravel_version'] }}</td></tr>
                        <tr><td class="text-muted">Software</td><td class="fw-bold">{{ $server['server_software'] }}</td></tr>
                        <tr><td class="text-muted">Memory Limit</td><td class="fw-bold">{{ $server['memory_limit'] }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold"><i class="fa-solid fa-heart-pulse me-2 text-danger"></i> System Health Check</h6>
                </div>
                <div class="card-body row text-center">
                    @foreach($health as $key => $status)
                    <div class="col-md-3 mb-3">
                        <div class="p-3 rounded-4 bg-light border">
                            <h6 class="text-uppercase fw-bold text-muted small mb-3">{{ $key }}</h6>
                            @if($status == 'ok')
                                <i class="fa-solid fa-check-circle text-success fs-1"></i>
                                <div class="mt-2 fw-bold text-success">Sehat</div>
                            @elseif($status == 'warning')
                                <i class="fa-solid fa-triangle-exclamation text-warning fs-1"></i>
                                <div class="mt-2 fw-bold text-warning">Peringatan</div>
                            @else
                                <i class="fa-solid fa-circle-xmark text-danger fs-1"></i>
                                <div class="mt-2 fw-bold text-danger">Error</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- PHP Extensions -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold"><i class="fa-solid fa-puzzle-piece me-2 text-warning"></i> PHP Extensions</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($extensions as $ext => $loaded)
                            @if($loaded)
                                <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2"><i class="fa-solid fa-check me-1"></i> {{ strtoupper($ext) }}</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3 py-2"><i class="fa-solid fa-times me-1"></i> {{ strtoupper($ext) }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
