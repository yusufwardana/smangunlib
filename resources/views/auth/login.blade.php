<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SMANGUNLIB</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-card { max-width: 400px; margin: 100px auto; border-radius: 1rem; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">SMANGUNLIB</h3>
                    <p class="text-muted">Masuk ke Dashboard</p>
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
            </div>
        </div>
    </div>
</body>
</html>
