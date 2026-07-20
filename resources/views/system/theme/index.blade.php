@extends('layouts.app')

@section('title', 'Theme Manager')

@php
    /*
     | Metadata tampilan untuk tiap grup: label tab, ikon, dan definisi field.
     | Tipe field diambil dari ThemeService::defaults(); di sini kita hanya
     | menambahkan label yang ramah pengguna serta opsi untuk field select.
     */
    $groupsMeta = [
        'general' => ['label' => 'General', 'icon' => 'fa-gear', 'fields' => [
            'theme_name'    => ['label' => 'Nama Tema'],
            'mode'          => ['label' => 'Mode', 'type' => 'select', 'options' => ['light' => 'Light', 'dark' => 'Dark', 'auto' => 'Auto (Follow System)']],
            'border_radius' => ['label' => 'Border Radius (mis. 1rem)'],
            'shadow_style'  => ['label' => 'Shadow Style'],
            'animation'     => ['label' => 'Animation'],
            'font_family'   => ['label' => 'Font Family'],
            'font_size'     => ['label' => 'Font Size (mis. 16px)'],
        ]],
        'color' => ['label' => 'Colors', 'icon' => 'fa-palette', 'fields' => [
            'primary_color' => ['label' => 'Primary'], 'secondary_color' => ['label' => 'Secondary'],
            'success_color' => ['label' => 'Success'], 'danger_color' => ['label' => 'Danger'],
            'warning_color' => ['label' => 'Warning'], 'info_color' => ['label' => 'Info'],
            'background_color' => ['label' => 'Background'], 'sidebar_color' => ['label' => 'Sidebar'],
            'navbar_color' => ['label' => 'Navbar'], 'card_color' => ['label' => 'Card'],
            'footer_color' => ['label' => 'Footer'], 'text_color' => ['label' => 'Text'],
            'link_color' => ['label' => 'Link'], 'hover_color' => ['label' => 'Hover'],
            'button_color' => ['label' => 'Button'],
        ]],
        'logo' => ['label' => 'Logo', 'icon' => 'fa-image', 'fields' => [
            'logo_sekolah' => ['label' => 'Logo Sekolah'], 'logo_perpustakaan' => ['label' => 'Logo Perpustakaan'],
            'logo_login' => ['label' => 'Logo Login'], 'logo_sidebar' => ['label' => 'Logo Sidebar'],
            'logo_footer' => ['label' => 'Logo Footer'],
        ]],
        'favicon' => ['label' => 'Favicon', 'icon' => 'fa-star', 'fields' => [
            'favicon' => ['label' => 'Favicon (ICO/PNG)'],
        ]],
        'login' => ['label' => 'Login', 'icon' => 'fa-right-to-bracket', 'fields' => [
            'background_color' => ['label' => 'Background Login'], 'overlay_color' => ['label' => 'Overlay Color'],
            'card_color' => ['label' => 'Login Card Color'], 'button_color' => ['label' => 'Button Color'],
            'background_image' => ['label' => 'Background Image'], 'video_background' => ['label' => 'Video Background (opsional)'],
        ]],
        'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-grid-2', 'fields' => [
            'sidebar_color' => ['label' => 'Sidebar'], 'navbar_color' => ['label' => 'Navbar'],
            'widget_color' => ['label' => 'Widget Color'], 'chart_color' => ['label' => 'Chart Color'],
            'table_style' => ['label' => 'Table Style', 'type' => 'select', 'options' => ['striped' => 'Striped', 'bordered' => 'Bordered', 'hover' => 'Hover', 'flat' => 'Flat']],
            'card_style' => ['label' => 'Card Style', 'type' => 'select', 'options' => ['flat' => 'Flat', 'shadow' => 'Shadow', 'glass' => 'Glassmorphism', 'bordered' => 'Bordered']],
        ]],
        'landing' => ['label' => 'Landing Page', 'icon' => 'fa-house', 'fields' => [
            'hero_background' => ['label' => 'Hero Background'], 'gradient' => ['label' => 'Gradient'],
            'button_style' => ['label' => 'Button Style', 'type' => 'select', 'options' => ['rounded' => 'Rounded', 'square' => 'Square', 'pill' => 'Pill']],
            'section_background' => ['label' => 'Section Background'], 'footer_background' => ['label' => 'Footer Background'],
            'typography' => ['label' => 'Typography'],
        ]],
        'typography' => ['label' => 'Typography', 'icon' => 'fa-font', 'fields' => [
            'google_fonts' => ['label' => 'Google Fonts'], 'system_fonts' => ['label' => 'System Fonts'],
            'heading_font' => ['label' => 'Heading Font'], 'body_font' => ['label' => 'Body Font'],
            'font_weight' => ['label' => 'Font Weight', 'type' => 'select', 'options' => ['300' => 'Light (300)', '400' => 'Regular (400)', '500' => 'Medium (500)', '600' => 'Semibold (600)', '700' => 'Bold (700)']],
        ]],
        'button' => ['label' => 'Button', 'icon' => 'fa-square', 'fields' => [
            'shape' => ['label' => 'Shape', 'type' => 'select', 'options' => ['rounded' => 'Rounded', 'square' => 'Square', 'pill' => 'Pill']],
            'variant' => ['label' => 'Variant', 'type' => 'select', 'options' => ['filled' => 'Filled', 'outline' => 'Outline']],
            'shadow' => ['label' => 'Shadow'],
        ]],
        'card' => ['label' => 'Card', 'icon' => 'fa-clone', 'fields' => [
            'style' => ['label' => 'Style', 'type' => 'select', 'options' => ['flat' => 'Flat', 'shadow' => 'Shadow', 'glass' => 'Glassmorphism', 'bordered' => 'Bordered']],
            'radius' => ['label' => 'Radius'],
        ]],
        'sidebar' => ['label' => 'Sidebar', 'icon' => 'fa-bars', 'fields' => [
            'collapsed_default' => ['label' => 'Collapsed Default'], 'mini' => ['label' => 'Mini Sidebar'],
            'width' => ['label' => 'Sidebar Width (mis. 260px)'],
            'position' => ['label' => 'Position', 'type' => 'select', 'options' => ['left' => 'Left', 'right' => 'Right']],
        ]],
        'navbar' => ['label' => 'Navbar', 'icon' => 'fa-window-maximize', 'fields' => [
            'sticky' => ['label' => 'Sticky'], 'transparent' => ['label' => 'Transparent'],
            'style' => ['label' => 'Style', 'type' => 'select', 'options' => ['solid' => 'Solid', 'transparent' => 'Transparent', 'blur' => 'Blur']],
        ]],
        'layout' => ['label' => 'Layout', 'icon' => 'fa-table-columns', 'fields' => [
            'mode' => ['label' => 'Mode', 'type' => 'select', 'options' => ['full' => 'Full Width', 'boxed' => 'Boxed']],
            'container_width' => ['label' => 'Container Width (mis. 1320px)'],
        ]],
        'loading' => ['label' => 'Loading', 'icon' => 'fa-spinner', 'fields' => [
            'enable' => ['label' => 'Enable Loading'],
            'spinner_style' => ['label' => 'Spinner Style', 'type' => 'select', 'options' => ['border' => 'Border', 'grow' => 'Grow', 'dots' => 'Dots']],
            'logo' => ['label' => 'Loading Logo'],
        ]],
        'custom' => ['label' => 'Custom CSS/JS', 'icon' => 'fa-code', 'fields' => [
            'css' => ['label' => 'Custom CSS'], 'js' => ['label' => 'Custom JavaScript'],
        ]],
    ];
@endphp

@section('content')
<div class="fade-in" id="theme-manager">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-palette me-2"></i>Theme Manager</h4>
            <p class="text-muted mb-0">Kelola tampilan aplikasi & landing page tanpa mengubah kode. Perubahan tampil langsung (live preview).</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('system.theme.export') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-file-export me-1"></i>Export
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fa-solid fa-file-import me-1"></i>Import
            </button>
            <button type="button" class="btn btn-outline-danger" id="btn-reset-theme">
                <i class="fa-solid fa-rotate-left me-1"></i>Reset
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3">
        {{-- Panel pengaturan --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header p-0">
                    <ul class="nav nav-pills flex-wrap gap-1 p-3" id="themeTabs" role="tablist">
                        @foreach($groupsMeta as $group => $meta)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        data-bs-toggle="pill"
                                        data-bs-target="#tab-{{ $group }}"
                                        type="button">
                                    <i class="fa-solid {{ $meta['icon'] }} me-1"></i>{{ $meta['label'] }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        @foreach($groupsMeta as $group => $meta)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $group }}">
                                <form class="theme-form" method="POST" action="{{ route('system.theme.update') }}" enctype="multipart/form-data" data-group="{{ $group }}">
                                    @csrf
                                    <input type="hidden" name="group" value="{{ $group }}">

                                    <div class="row">
                                        @foreach($meta['fields'] as $key => $fieldMeta)
                                            @include('system.theme.partials.field', [
                                                'group'   => $group,
                                                'key'     => $key,
                                                'type'    => $fieldMeta['type'] ?? ($defaults[$group][$key]['type'] ?? 'string'),
                                                'label'   => $fieldMeta['label'],
                                                'value'   => $values[$group.'.'.$key] ?? ($defaults[$group][$key]['value'] ?? ''),
                                                'options' => $fieldMeta['options'] ?? [],
                                            ])
                                        @endforeach
                                    </div>

                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-floppy-disk me-1"></i>Simpan {{ $meta['label'] }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel live preview --}}
        <div class="col-lg-4">
            <div class="card position-sticky" style="top: 90px;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="fa-solid fa-eye me-1"></i>Live Preview</span>
                    <span class="badge bg-success" id="preview-status">Realtime</span>
                </div>
                <div class="card-body" id="theme-preview-pane">
                    <div class="mb-3 p-3 rounded" style="background: var(--primary-color); color:#fff;">
                        <div class="fw-bold">Primary Surface</div>
                        <small>Contoh area dengan warna utama.</small>
                    </div>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-primary btn-sm">Primary</button>
                        <button type="button" class="btn btn-success btn-sm">Success</button>
                        <button type="button" class="btn btn-danger btn-sm">Danger</button>
                        <button type="button" class="btn btn-warning btn-sm">Warning</button>
                        <button type="button" class="btn btn-info btn-sm">Info</button>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="mb-1">Contoh Kartu</h6>
                            <p class="text-muted small mb-2">Teks isi kartu untuk melihat warna teks & kartu.</p>
                            <a href="#" onclick="return false;">Contoh tautan</a>
                        </div>
                    </div>
                    <div class="alert alert-primary mb-0 small">Preview mengikuti perubahan warna & tipografi secara langsung.</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Import --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('system.theme.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Tema (JSON)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Pilih file JSON hasil export</label>
                    <input type="file" name="file" class="form-control" accept="application/json,.json" required>
                    <small class="text-muted">Maksimal 5 MB.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Form tersembunyi untuk reset --}}
<form id="reset-theme-form" method="POST" action="{{ route('system.theme.reset') }}" class="d-none">
    @csrf
</form>
@endsection

@push('styles')
<style>
    #theme-manager .nav-pills .nav-link { font-size: .85rem; padding: .4rem .75rem; }
    #theme-manager .form-control-color { width: 3rem; padding: .2rem; }
    #theme-manager .theme-code { font-size: .85rem; }
    #theme-manager .theme-preview-img { object-fit: contain; background: #f1f3f5; }
</style>
@endpush

@push('scripts')
<script>
    window.ThemeManagerRoutes = {
        preview: "{{ route('system.theme.preview') }}",
        reset:   "{{ route('system.theme.reset') }}",
    };
</script>
<script src="{{ asset('js/theme-manager.js') }}"></script>
@endpush
