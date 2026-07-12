<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SMANGUNLIB - Setup Wizard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .wizard-container { max-width: 800px; margin: 40px auto; }
        .wizard-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .wizard-header { background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); color: white; padding: 30px; text-align: center; }
        .wizard-body { padding: 40px; background: white; }
        .wizard-footer { padding: 20px 40px; background: #f8f9fa; border-top: 1px solid #eee; display: flex; justify-content: space-between; }
        .step-indicator { display: flex; justify-content: center; margin-top: 20px; gap: 10px; }
        .step-dot { width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.3); }
        .step-dot.active { background: white; box-shadow: 0 0 10px rgba(255,255,255,0.8); }
    </style>
</head>
<body>

<div class="container wizard-container">
    <div class="card wizard-card">
        <div class="wizard-header">
            <h2 class="fw-bold mb-0"><i class="fa-solid fa-book-open-reader me-2"></i> SMANGUNLIB</h2>
            <p class="text-white-50 mb-0">Web Installer v1.0</p>
            
            <div class="step-indicator">
                @for($i=1; $i<=8; $i++)
                    <div class="step-dot {{ (isset($step) && $step == $i) ? 'active' : '' }}"></div>
                @endfor
            </div>
        </div>

        <div class="wizard-body">
            @yield('content')
        </div>

        @hasSection('footer')
        <div class="wizard-footer">
            @yield('footer')
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@stack('scripts')
</body>
</html>
