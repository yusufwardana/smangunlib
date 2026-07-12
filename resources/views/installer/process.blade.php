@extends('installer.layouts.master', ['step' => 6])

@section('content')
<div class="text-center py-4">
    <i class="fa-solid fa-server text-primary mb-4 fa-bounce" style="font-size: 4rem;"></i>
    <h4 class="fw-bold mb-3">Sistem Sedang Bekerja</h4>
    <p class="text-muted">Tunggu beberapa saat, sistem sedang melakukan migrasi database, seeding struktur, dan pembuatan symlink. Jangan tutup browser Anda.</p>
    
    <div class="progress mt-4 mb-3" style="height: 20px; border-radius: 10px;">
        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;">0%</div>
    </div>
    
    <div id="statusLog" class="text-start mt-4 bg-light p-3 rounded font-monospace small" style="height: 150px; overflow-y: auto; border: 1px solid #ddd;">
        <div class="text-muted">> Memulai proses instalasi...</div>
    </div>
</div>
@endsection

@section('footer')
    <div class="w-100 text-center">
        <button id="btnNext" class="btn btn-success rounded-pill px-5 fw-bold d-none" onclick="window.location.href='{{ route('installer.admin') }}'">Lanjut ke Setup Akun <i class="fa-solid fa-arrow-right ms-2"></i></button>
        <button id="btnRetry" class="btn btn-danger rounded-pill px-5 fw-bold d-none" onclick="location.reload()">Coba Ulangi Proses</button>
    </div>
@endsection

@push('scripts')
<script>
    const steps = [
        { name: 'Men-generate .env...', url: '{{ route('installer.process.env') }}', percent: 20 },
        { name: 'Men-generate APP_KEY...', url: '{{ route('installer.process.key') }}', percent: 40 },
        { name: 'Membuat Storage Symlink...', url: '{{ route('installer.process.symlink') }}', percent: 60 },
        { name: 'Menjalankan Migrasi Database...', url: '{{ route('installer.process.migrate') }}', percent: 80 },
        { name: 'Mengisi Data Awal (Seeder)...', url: '{{ route('installer.process.seed') }}', percent: 100 }
    ];

    let currentStep = 0;

    function logStatus(msg, isError = false) {
        const color = isError ? 'text-danger fw-bold' : 'text-dark';
        $('#statusLog').append(`<div class="${color}">> ${msg}</div>`);
        $('#statusLog').scrollTop($('#statusLog')[0].scrollHeight);
    }

    function runNextStep() {
        if (currentStep >= steps.length) {
            logStatus('✅ SELURUH PROSES SELESAI.', false);
            $('#btnNext').removeClass('d-none');
            return;
        }

        const step = steps[currentStep];
        logStatus(step.name);
        
        $.ajax({
            url: step.url,
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                if (res.success) {
                    logStatus('  [OK] ' + res.message);
                    $('#progressBar').css('width', step.percent + '%').text(step.percent + '%');
                    currentStep++;
                    setTimeout(runNextStep, 500); // Jeda visual sedikit
                } else {
                    logStatus('  [ERROR] ' + res.message, true);
                    $('#btnRetry').removeClass('d-none');
                    $('#progressBar').removeClass('bg-primary').addClass('bg-danger');
                }
            },
            error: function(err) {
                logStatus('  [FATAL ERROR] Server mengembalikan 500. Kemungkinan timeout.', true);
                $('#btnRetry').removeClass('d-none');
                $('#progressBar').removeClass('bg-primary').addClass('bg-danger');
            }
        });
    }

    // Start
    $(document).ready(function() {
        setTimeout(runNextStep, 1000);
    });
</script>
@endpush
