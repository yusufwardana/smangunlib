<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    @if(theme_asset('favicon'))
        <link rel="icon" href="{{ theme_asset('favicon') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            font-family: 'Inter', sans-serif;
            color: #fff;
        }
        .forbidden-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 480px;
        }
        .forbidden-code { font-size: 5rem; font-weight: 800; line-height: 1; }
        .forbidden-icon { font-size: 3rem; opacity: .85; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="forbidden-card">
        <div class="forbidden-icon"><i class="fa-solid fa-user-lock"></i></div>
        <div class="forbidden-code">403</div>
        <h4 class="mt-2 mb-3">Akses Ditolak</h4>
        <p class="mb-4">
            {{ $exception?->getMessage() ?: 'Anda tidak memiliki hak akses.' }}
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ url()->previous() }}" class="btn btn-light">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ url('/dashboard') }}" class="btn btn-outline-light">
                <i class="fa-solid fa-house me-1"></i> Dashboard
            </a>
        </div>
    </div>
</body>
</html>
