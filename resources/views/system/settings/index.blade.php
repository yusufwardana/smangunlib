@extends('layouts.app')

@section('title', 'System Settings & Content Management')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">System Settings & Content Management</h4>
            <p class="text-muted mb-0">Kelola identitas sekolah, aplikasi, landing page, SEO, media, dan integrasi.</p>
        </div>
        <a href="{{ route('landing') }}" target="_blank" class="btn btn-primary">
            <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Lihat Landing
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card"><div class="card-body"><div class="text-muted small">Konten Landing</div><div class="fs-3 fw-bold">{{ $contentCounts->sum() }}</div></div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body"><div class="text-muted small">Menu Landing</div><div class="fs-3 fw-bold">{{ $menuCount }}</div></div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body"><div class="text-muted small">Media Asset</div><div class="fs-3 fw-bold">{{ $mediaCount }}</div></div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills card-header-pills flex-wrap gap-2" id="settingsTabs" role="tablist">
                @foreach ([
                    'school' => 'Identitas Sekolah',
                    'app' => 'Aplikasi',
                    'smtp' => 'SMTP Email',
                    'whatsapp' => 'WhatsApp',
                    'library' => 'Perpustakaan',
                    'landing' => 'Landing Page',
                    'seo' => 'SEO',
                    'contact' => 'Kontak',
                    'footer' => 'Footer',
                ] as $key => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#tab-{{ $key }}" type="button">{{ $label }}</button>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                @include('system.settings.partials', ['group' => 'school', 'active' => true, 'title' => 'Identitas Sekolah', 'fields' => [
                    'nama_sekolah' => 'Nama Sekolah', 'npsn' => 'NPSN', 'nss' => 'NSS', 'alamat' => 'Alamat', 'kecamatan' => 'Kecamatan', 'kabupaten_kota' => 'Kabupaten/Kota', 'provinsi' => 'Provinsi', 'kode_pos' => 'Kode Pos', 'telepon' => 'Telepon', 'email' => 'Email', 'website' => 'Website', 'kepala_sekolah' => 'Kepala Sekolah', 'kepala_perpustakaan' => 'Kepala Perpustakaan', 'tahun_berdiri' => 'Tahun Berdiri', 'akreditasi' => 'Akreditasi Sekolah'
                ], 'textareas' => ['deskripsi' => 'Deskripsi Sekolah'], 'uploads' => ['logo_sekolah' => 'Logo Sekolah', 'logo_perpustakaan' => 'Logo Perpustakaan', 'favicon' => 'Favicon']])

                @include('system.settings.partials', ['group' => 'app', 'title' => 'Pengaturan Aplikasi', 'fields' => [
                    'nama_aplikasi' => 'Nama Aplikasi', 'versi' => 'Versi', 'copyright' => 'Copyright', 'timezone' => 'Timezone', 'bahasa' => 'Bahasa', 'format_tanggal' => 'Tanggal', 'format_nomor' => 'Format Nomor', 'footer_text' => 'Footer Text'
                ], 'checks' => ['dark_mode_default' => 'Dark Mode Default', 'sidebar_collapse' => 'Sidebar Collapse'], 'uploads' => ['logo_login' => 'Logo Login', 'background_login' => 'Background Login']])

                @include('system.settings.partials', ['group' => 'smtp', 'title' => 'SMTP Email', 'fields' => [
                    'host' => 'SMTP Host', 'port' => 'SMTP Port', 'username' => 'Username', 'password' => 'Password', 'encryption' => 'Encryption', 'from_name' => 'From Name', 'from_email' => 'From Email'
                ], 'testRoute' => route('system.settings.test-smtp')])

                @include('system.settings.partials', ['group' => 'whatsapp', 'title' => 'WhatsApp Gateway', 'fields' => [
                    'provider' => 'Provider', 'api_url' => 'API URL', 'api_key' => 'API Key', 'sender_number' => 'Sender Number', 'status' => 'Status'
                ], 'testRoute' => route('system.settings.test-whatsapp')])

                @include('system.settings.partials', ['group' => 'library', 'title' => 'Pengaturan Perpustakaan', 'fields' => [
                    'lama_pinjam' => 'Lama Pinjam', 'maksimal_buku' => 'Maksimal Buku Dipinjam', 'denda_per_hari' => 'Denda Per Hari', 'maksimal_perpanjangan' => 'Maksimal Perpanjangan', 'hari_libur' => 'Hari Libur', 'jam_operasional' => 'Jam Operasional'
                ], 'checks' => ['reservasi_aktif' => 'Reservasi Aktif', 'auto_hitung_denda' => 'Auto Hitung Denda']])

                @include('system.settings.partials', ['group' => 'landing', 'title' => 'Landing Page Content', 'fields' => [
                    'hero_title' => 'Judul Hero', 'hero_subtitle' => 'Subjudul Hero', 'hero_button_1' => 'Button 1', 'hero_link_1' => 'Link Button 1', 'hero_button_2' => 'Button 2', 'hero_link_2' => 'Link Button 2', 'profile_title' => 'Judul Profil'
                ], 'textareas' => ['profile_description' => 'Deskripsi Profil', 'profile_history' => 'Sejarah', 'profile_vision' => 'Visi', 'profile_mission' => 'Misi', 'profile_goal' => 'Tujuan', 'running_text' => 'Pengumuman Berjalan'], 'uploads' => ['hero_background' => 'Background Hero', 'profile_photo' => 'Foto Profil']])

                @include('system.settings.partials', ['group' => 'seo', 'title' => 'SEO', 'fields' => [
                    'meta_title' => 'Meta Title', 'meta_description' => 'Meta Description', 'meta_keyword' => 'Meta Keyword', 'google_analytics' => 'Google Analytics', 'google_search_console' => 'Google Search Console Verification'
                ], 'textareas' => ['robots_txt' => 'Robots.txt', 'sitemap' => 'Sitemap'], 'uploads' => ['og_image' => 'Open Graph Image']])

                @include('system.settings.partials', ['group' => 'contact', 'title' => 'Kontak', 'fields' => [
                    'google_maps' => 'Google Maps Embed URL', 'alamat' => 'Alamat', 'telepon' => 'Telepon', 'email' => 'Email', 'whatsapp' => 'WhatsApp', 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube'
                ]])

                @include('system.settings.partials', ['group' => 'footer', 'title' => 'Footer', 'fields' => [
                    'copyright' => 'Copyright', 'quick_links' => 'Link Cepat', 'social_media' => 'Media Sosial'
                ], 'textareas' => ['description' => 'Deskripsi Footer']])
            </div>
        </div>
    </div>

    <div class="row g-3 mt-4">
        <div class="col-md-4"><a class="card h-100 text-decoration-none" href="{{ route('system.contents.index', 'service') }}"><div class="card-body"><h5 class="fw-bold">Landing Content</h5><p class="text-muted mb-0">CRUD layanan, literasi, banner, slider, pengumuman, berita, galeri, FAQ.</p></div></a></div>
        <div class="col-md-4"><a class="card h-100 text-decoration-none" href="{{ route('system.menus.index') }}"><div class="card-body"><h5 class="fw-bold">Menu Manager</h5><p class="text-muted mb-0">Kelola menu landing page, urutan, icon, parent menu, dan status.</p></div></a></div>
        <div class="col-md-4"><a class="card h-100 text-decoration-none" href="{{ route('system.media.index') }}"><div class="card-body"><h5 class="fw-bold">Media Manager</h5><p class="text-muted mb-0">Upload logo, banner, foto, dokumen, video, dan PDF.</p></div></a></div>
    </div>
</div>
@endsection
