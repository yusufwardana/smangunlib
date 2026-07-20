<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SMANGUNLIB</title>
    @if(theme_asset('favicon'))
        <link rel="icon" href="{{ theme_asset('favicon') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --teal: #0f766e; --emerald: #10b981; --ink: #0f172a; --muted: #64748b; }
        body { min-height: 100vh; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: radial-gradient(circle at 10% 20%, rgba(16,185,129,.18), transparent 28%), linear-gradient(135deg, #ecfeff 0%, #f8fafc 52%, #eef2ff 100%); color: var(--ink); }
        .login-shell { min-height: 100vh; display: grid; place-items: center; padding: 32px 16px; }
        .login-wrap { width: min(1040px, 100%); display: grid; grid-template-columns: 1.15fr .85fr; background: rgba(255,255,255,.76); border: 1px solid rgba(148,163,184,.24); border-radius: 32px; overflow: hidden; box-shadow: 0 28px 80px rgba(15,23,42,.13); backdrop-filter: blur(18px); }
        .login-hero { position: relative; min-height: 560px; padding: 44px; color: #fff; background: linear-gradient(145deg, rgba(15,118,110,.92), rgba(15,23,42,.82)), url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1200&q=80') center/cover; display: flex; flex-direction: column; justify-content: space-between; }
        .login-brand { display: inline-flex; align-items: center; gap: 12px; font-weight: 800; font-size: 1.15rem; }
        .login-brand i { width: 46px; height: 46px; display: inline-flex; align-items: center; justify-content: center; border-radius: 16px; background: rgba(255,255,255,.18); }
        .login-hero h1 { font-size: clamp(2rem, 4vw, 3.3rem); font-weight: 800; line-height: 1.08; margin: 0 0 18px; }
        .login-hero p { color: rgba(255,255,255,.78); max-width: 520px; line-height: 1.8; }
        .login-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .login-stats span { padding: 14px; border-radius: 18px; background: rgba(255,255,255,.13); font-weight: 700; }
        .login-card { border: 0; background: #fff; min-height: 560px; border-radius: 0; }
        .login-card .card-body { padding: 54px 44px; display: flex; flex-direction: column; justify-content: center; height: 100%; }
        .form-control { border-radius: 16px; border-color: #dbe4ef; padding: 13px 15px; }
        .form-control:focus { border-color: var(--teal); box-shadow: 0 0 0 .22rem rgba(15,118,110,.12); }
        .btn-primary { background: linear-gradient(135deg, var(--teal), var(--emerald)); border: 0; border-radius: 16px; box-shadow: 0 16px 30px rgba(15,118,110,.23); }
        .login-help { border-radius: 18px; background: #f0fdfa; color: #115e59; padding: 14px 16px; font-size: .88rem; }
        @media (max-width: 900px) { .login-wrap { grid-template-columns: 1fr; } .login-hero { min-height: 360px; } }
        @media (max-width: 575.98px) { .login-hero, .login-card .card-body { padding: 30px 24px; } .login-stats { grid-template-columns: 1fr; } }
    </style>

    {{-- ===== Login Theme override dari Theme Manager ===== --}}
    <style>
        @if(theme('login.background_image'))
            body { background: {{ theme('login.overlay_color', 'rgba(15,23,42,.55)') }}, url('{{ theme_asset('login.background_image') }}') center/cover fixed !important; }
        @elseif(theme('login.background_color'))
            body { background: {{ theme('login.background_color') }} !important; }
        @endif
        @if(theme('login.card_color'))
            .login-card { background: {{ theme('login.card_color') }} !important; }
        @endif
        @if(theme('login.button_color'))
            .btn-primary { background: {{ theme('login.button_color') }} !important; box-shadow: none !important; }
        @endif
    </style>
</head>

<body>
    <main class="login-shell">
        <div class="login-wrap">
            <section class="login-hero" aria-label="Ilustrasi perpustakaan sekolah">
                <div class="login-brand"><i class="bi bi-book-half"></i> SMANGUNLIB</div>
                <div>
                    <h1>Portal Perpustakaan Sekolah Modern</h1>
                    <p>Kelola katalog, anggota, sirkulasi, literasi, dokumen, dan laporan perpustakaan dalam satu dashboard.</p>
                </div>
                <div class="login-stats">
                    <span>OPAC</span>
                    <span>E-Book</span>
                    <span>GLS</span>
                </div>
            </section>
            <div class="card login-card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">Masuk Dashboard</h3>
                    <p class="text-muted">Gunakan akun sekolah/perpustakaan Anda.</p>
                </div>
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat Saya</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Masuk</button>
                </form>
                <div class="login-help mt-4">
                    <strong>Akun dummy:</strong> superadmin@smangunlib.test / password123
                </div>
            </div>
            </div>
        </div>
    </main>
</body>
</html>
