@extends('installer.layouts.master', ['step' => 4])

@section('content')
<h5 class="fw-bold mb-4">Konfigurasi Database MySQL</h5>
<p class="text-muted mb-4">Silakan isi detail database yang telah Anda buat di cPanel MySQL Databases.</p>

<form id="dbForm">
    <div class="row g-3">
        <div class="col-md-9">
            <label class="form-label fw-bold">Database Host</label>
            <input type="text" name="db_host" id="db_host" class="form-control" value="127.0.0.1" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Port</label>
            <input type="text" name="db_port" id="db_port" class="form-control" value="3306" required>
        </div>
        <div class="col-md-12">
            <label class="form-label fw-bold">Nama Database</label>
            <input type="text" name="db_name" id="db_name" class="form-control" placeholder="Contoh: u12345_smangunlib" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Username Database</label>
            <input type="text" name="db_user" id="db_user" class="form-control" placeholder="Contoh: u12345_root" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Password Database</label>
            <input type="password" name="db_password" id="db_password" class="form-control" placeholder="Biarkan kosong jika tidak ada">
        </div>
    </div>
    
    <div id="testResult" class="mt-4"></div>
</form>
@endsection

@section('footer')
    <a href="{{ route('installer.permissions') }}" class="btn btn-light rounded-pill px-4">Kembali</a>
    <div>
        <button type="button" id="btnTest" class="btn btn-outline-primary rounded-pill px-4 me-2">Test Connection</button>
        <button type="button" id="btnNext" class="btn btn-primary rounded-pill px-4 fw-bold" disabled>Selanjutnya <i class="fa-solid fa-arrow-right ms-2"></i></button>
    </div>
@endsection

@push('scripts')
<script>
    $('#btnTest').click(function() {
        let btn = $(this);
        btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Menghubungkan...').prop('disabled', true);
        $('#testResult').html('');
        
        $.ajax({
            url: "{{ route('installer.database.test') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                db_host: $('#db_host').val(),
                db_port: $('#db_port').val(),
                db_name: $('#db_name').val(),
                db_user: $('#db_user').val(),
                db_password: $('#db_password').val()
            },
            success: function(res) {
                btn.html('Test Connection').prop('disabled', false);
                if(res.success) {
                    $('#testResult').html('<div class="alert alert-success border-0 shadow-sm"><i class="fa-solid fa-circle-check me-2"></i> <strong>Connection Success!</strong> Kredensial valid.</div>');
                    $('#btnNext').prop('disabled', false);
                } else {
                    $('#testResult').html('<div class="alert alert-danger border-0 shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Gagal Terhubung:</strong> ' + res.message + '</div>');
                    $('#btnNext').prop('disabled', true);
                }
            },
            error: function() {
                btn.html('Test Connection').prop('disabled', false);
                $('#testResult').html('<div class="alert alert-danger border-0 shadow-sm">Terjadi kesalahan sistem saat mencoba menghubungi server.</div>');
            }
        });
    });

    $('#btnNext').click(function() {
        window.location.href = "{{ route('installer.app') }}";
    });
</script>
@endpush
