<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $settings['seo.meta_description'] ?? 'Halaman depan Perpustakaan SMA untuk informasi koleksi buku, layanan, program literasi, berita, jam layanan, dan kontak perpustakaan sekolah.' }}">
    <meta name="keywords" content="{{ $settings['seo.meta_keyword'] ?? 'perpustakaan SMA, koleksi buku, literasi sekolah, layanan perpustakaan, SMANGUNLIB' }}">
    <meta name="author" content="Perpustakaan SMA">
    <meta name="theme-color" content="#0f766e">
    <title>{{ $settings['seo.meta_title'] ?? 'Perpustakaan SMA - SMANGUNLIB' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
@php
    $setting = fn ($key, $default = null) => $settings[$key] ?? $default;
    $bookHighlights = ($contents['book_highlight'] ?? collect());
    $books = $latestBooks->isNotEmpty() ? $latestBooks : $bookHighlights;
    $stats = ($contents['stat'] ?? collect());
    $bookCategories = ($contents['book_category'] ?? collect());
    $services = ($contents['service'] ?? collect())->take(6);
    $literacyPrograms = ($contents['literacy_program'] ?? collect())->take(5);
    $newsItems = ($contents['news'] ?? collect())->take(6);
    $faqs = ($contents['faq'] ?? collect())->take(10);
    $calendarEvents = ($contents['calendar_event'] ?? collect())->take(3);
    $downloads = ($contents['download'] ?? collect())->take(2);
    $profilePhoto = $setting('landing.profile_photo')
        ? asset('storage/'.$setting('landing.profile_photo'))
        : 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=900&q=80';
    $heroImage = $setting('landing.hero_background')
        ? asset('storage/'.$setting('landing.hero_background'))
        : 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1100&q=80';
@endphp

<header class="library-header">
    <nav class="navbar navbar-expand-lg fixed-top library-navbar" id="libraryNavbar" aria-label="Navigasi utama">
        <div class="container">
            <a class="navbar-brand" href="#beranda" aria-label="Beranda Perpustakaan SMA">
                <span class="logo-mark"><i class="bi bi-book-half"></i></span>
                <span>
                    <strong>Perpustakaan</strong>
                    <small>{{ $setting('school.nama_sekolah', 'SMA Negeri') }}</small>
                </span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Buka menu">
                <i class="bi bi-list fs-2"></i>
            </button>
            <div class="collapse navbar-collapse" id="mainMenu">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    @forelse($menus as $menu)
                        <li class="nav-item"><a class="nav-link" href="{{ $menu->url }}"><i class="bi {{ $menu->icon }} d-lg-none me-1"></i>{{ $menu->name }}</a></li>
                    @empty
                        <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="#koleksi">Koleksi</a></li>
                        <li class="nav-item"><a class="nav-link" href="#berita">Berita</a></li>
                        <li class="nav-item"><a class="nav-link" href="#berita">Pengumuman</a></li>
                        <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                    @endforelse
                </ul>

                @guest
                    {{-- Guest: hanya menu Login yang tampil, menu Dashboard disembunyikan --}}
                    <a class="btn btn-primary btn-sm px-4" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
                @endguest

                @auth
                    @php
                        $authUser = auth()->user();
                        $authName = $authUser->name ?? 'Pengguna';
                        $authRole = $authUser->roles->first()->name ?? 'pengguna';
                        $authRoleLabel = ucwords(str_replace('_', ' ', $authRole));
                        $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($authName).'&background=0f766e&color=fff';
                    @endphp
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        {{-- Tombol Dashboard menggantikan tombol Login --}}
                        <a class="btn btn-primary btn-sm px-3" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>

                        {{-- Notifikasi --}}
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('dashboard') }}" aria-label="Notifikasi"><i class="bi bi-bell"></i></a>

                        {{-- Dropdown Profil --}}
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ $avatarUrl }}" alt="Avatar {{ $authName }}" class="rounded-circle" width="36" height="36">
                                <span class="ms-2 d-none d-lg-inline text-start">
                                    <strong class="d-block" style="font-size:.85rem;line-height:1">{{ $authName }}</strong>
                                    <small class="text-muted" style="font-size:.7rem">{{ $authRoleLabel }}</small>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li class="px-3 py-2 border-bottom">
                                    <strong class="d-block">{{ $authName }}</strong>
                                    <small class="text-muted">{{ $authRoleLabel }}</small>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>

                                {{-- Shortcut sesuai role --}}
                                @if($authUser->hasRole('siswa'))
                                    <li><a class="dropdown-item" href="{{ route('koleksi.buku.index') }}"><i class="bi bi-journal-bookmark me-2"></i>Peminjaman Saya</a></li>
                                @endif
                                @if($authUser->hasAnyRole(['pustakawan', 'kepala_perpustakaan', 'super_admin']))
                                    <li><a class="dropdown-item" href="{{ route('koleksi.buku.index') }}"><i class="bi bi-book me-2"></i>Manajemen Buku</a></li>
                                @endif
                                @if($authUser->hasAnyRole(['super_admin', 'kepala_sekolah', 'kepala_perpustakaan']))
                                    <li><a class="dropdown-item" href="{{ route('laporan.index') }}"><i class="bi bi-graph-up me-2"></i>Laporan</a></li>
                                @endif

                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-landing').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </a>
                                    <form id="logout-form-landing" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="hero-library" id="beranda">
        <div class="hero-orb hero-orb-one"></div>
        <div class="hero-orb hero-orb-two"></div>
        <div class="container">
            <nav aria-label="breadcrumb" class="landing-breadcrumb">
                <ol class="breadcrumb small mb-3">
                    <li class="breadcrumb-item"><a href="{{ route('landing') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Landing Page</li>
                </ol>
            </nav>
            <script type="application/ld+json">
            {
                "@@context": "https://schema.org",
                "@@type": "BreadcrumbList",
                "itemListElement": [
                    { "@@type": "ListItem", "position": 1, "name": "Beranda", "item": "{{ route('landing') }}" }
                ]
            }
            </script>
            <div class="running-text" role="status" aria-label="Pengumuman berjalan">
                <i class="bi bi-megaphone"></i>
                <div><span>{{ $setting('landing.running_text', 'Pengumuman: Pengembalian buku semester ini paling lambat Jumat pukul 14.00 WIB. Ikuti juga kegiatan 15 Menit Membaca setiap Selasa pagi.') }}</span></div>
            </div>
            <div class="row align-items-center g-5">
                <div class="col-lg-6 reveal">
                    <span class="section-kicker"><i class="bi bi-stars"></i> Pusat Sumber Belajar Sekolah</span>
                    <h1>{{ $setting('landing.hero_title', 'Selamat Datang di Perpustakaan SMA') }}</h1>
                    <p>{{ $setting('landing.hero_subtitle', 'Menyediakan layanan informasi, koleksi buku, dan sumber belajar untuk mendukung kegiatan belajar mengajar.') }}</p>
                    <form class="search-box" action="{{ url('/koleksi/buku') }}" method="GET" role="search" aria-label="Pencarian buku">
                        <i class="bi bi-search"></i>
                        <input type="search" name="q" placeholder="Cari judul buku, penulis, atau kategori..." aria-label="Kata kunci pencarian buku">
                        <button type="submit">Cari</button>
                    </form>
                    <div class="d-flex gap-3 flex-wrap mt-4">
                        <a class="btn btn-primary btn-lg" href="{{ $setting('landing.hero_link_1', '#koleksi') }}">{{ $setting('landing.hero_button_1', 'Telusuri Koleksi') }}</a>
                        <a class="btn btn-outline-primary btn-lg" href="{{ $setting('landing.hero_link_2', route('login')) }}">{{ $setting('landing.hero_button_2', 'Login') }}</a>
                    </div>
                    <div class="hero-trust-strip" aria-label="Keunggulan layanan perpustakaan">
                        <span><i class="bi bi-check2-circle"></i> Katalog online</span>
                        <span><i class="bi bi-check2-circle"></i> Buku digital</span>
                        <span><i class="bi bi-check2-circle"></i> Layanan siswa & guru</span>
                    </div>
                </div>
                <div class="col-lg-6 reveal slide-left">
                    <figure class="library-visual">
                        <img src="{{ $heroImage }}" alt="Ilustrasi perpustakaan sekolah modern" loading="eager">
                        <figcaption>
                            <span class="live-dot"></span>
                            <strong>Pengunjung hari ini</strong>
                            <span>128 siswa dan guru aktif membaca</span>
                        </figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section" aria-label="Statistik perpustakaan">
        <div class="container">
            <div class="row g-3">
                @foreach ($stats as $stat)
                    <div class="col-6 col-lg reveal">
                        <article class="info-stat">
                            <i class="bi {{ $stat->icon ?: 'bi-info-circle' }}"></i>
                            <strong>{{ $stat->description }}</strong>
                            <span>{{ $stat->title }}</span>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-space" id="profil">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 reveal">
                    <img class="profile-photo" src="{{ $profilePhoto }}" alt="Ruang baca perpustakaan sekolah" loading="lazy">
                </div>
                <div class="col-lg-7 reveal slide-left">
                    <span class="section-kicker">Profil Perpustakaan</span>
                    <h2>{{ $setting('landing.profile_title', 'Ruang belajar yang nyaman, tertib, dan mendukung budaya literasi.') }}</h2>
                    <p class="text-muted">{{ $setting('landing.profile_description', 'Perpustakaan SMA hadir sebagai pusat informasi dan sumber belajar bagi siswa, guru, dan tenaga kependidikan.') }}</p>
                    <div class="profile-grid">
                        <article><h3>Sejarah Singkat</h3><p>{{ $setting('landing.profile_history', 'Berkembang dari ruang baca sekolah menjadi layanan perpustakaan digital dan fisik yang terintegrasi.') }}</p></article>
                        <article><h3>Visi</h3><p>{{ $setting('landing.profile_vision', 'Menjadi pusat literasi sekolah yang inklusif, informatif, dan adaptif terhadap perkembangan teknologi.') }}</p></article>
                        <article><h3>Misi</h3><p>{{ $setting('landing.profile_mission', 'Menyediakan koleksi bermutu, layanan ramah, dan program literasi yang berkelanjutan.') }}</p></article>
                        <article><h3>Tujuan</h3><p>{{ $setting('landing.profile_goal', 'Mendukung kegiatan belajar mengajar serta membangun kebiasaan membaca di lingkungan sekolah.') }}</p></article>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space soft-bg" id="layanan">
        <div class="container">
            <div class="section-title reveal">
                <span class="section-kicker">Layanan</span>
                <h2>Layanan perpustakaan untuk siswa dan guru.</h2>
            </div>
            <div class="row g-4">
                @foreach ($services as $service)
                    <div class="col-md-6 col-lg-4 reveal">
                        <article class="service-card">
                            <i class="bi {{ $service->icon ?: 'bi-journal-check' }}"></i>
                            <h3>{{ $service->title }}</h3>
                            <p>{{ $service->description }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-space" id="koleksi">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4">
                <div class="section-title mb-0 reveal">
                    <span class="section-kicker">Koleksi Terbaru</span>
                    <h2>Buku baru yang tersedia di perpustakaan.</h2>
                </div>
                <a class="btn btn-outline-primary" href="{{ url('/koleksi/buku') }}">Lihat Semua</a>
            </div>
            <div class="row g-4">
                @foreach ($books->take(8) as $book)
                    @php
                        $isModel = is_object($book) && method_exists($book, 'getAttribute') && isset($book->judul);
                        $isContent = is_object($book) && method_exists($book, 'getAttribute') && isset($book->title);
                        if ($isContent) {
                            $title = $book->title;
                            [$author, $category] = array_pad(explode('|', (string) $book->description, 2), 2, 'Umum');
                            $cover = $book->image ? asset('storage/'.$book->image) : 'https://placehold.co/320x440/dbeafe/1e40af?text='.urlencode($title);
                        } else {
                            $title = $isModel ? $book->judul : $book['judul'];
                            $author = $isModel ? $book->pengarang : $book['pengarang'];
                            $category = $isModel ? ($book->kategori->first()->nama_kategori ?? 'Umum') : $book['kategori'];
                            $cover = $isModel ? $book->cover_url : $book['cover'];
                        }
                    @endphp
                    <div class="col-6 col-lg-3 reveal">
                        <article class="book-card">
                            <img src="{{ $cover }}" alt="Cover buku {{ $title }}" loading="lazy">
                            <div>
                                <span>{{ $category }}</span>
                                <h3>{{ $title }}</h3>
                                <p>{{ $author }}</p>
                                <small><i class="bi bi-check-circle-fill"></i> Tersedia</small>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <div class="section-title reveal">
                <span class="section-kicker">Kategori Buku</span>
                <h2>Temukan koleksi sesuai kebutuhan belajar.</h2>
            </div>
            <div class="category-grid">
                @foreach ($bookCategories as $category)
                    <a class="category-card reveal" href="{{ url('/koleksi/buku') }}">
                        <i class="bi {{ $category->icon ?: 'bi-book' }}"></i>
                        <span>{{ $category->title }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-space soft-bg" id="literasi">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 reveal">
                    <span class="section-kicker">Program Literasi</span>
                    <h2>Kegiatan yang menumbuhkan budaya baca.</h2>
                    <p class="text-muted">Program literasi dilaksanakan berkala bersama guru, pustakawan, dan siswa untuk membuat perpustakaan hidup sebagai ruang belajar.</p>
                    <div class="literacy-list">
                        @foreach ($literacyPrograms as $program)
                            <span><i class="bi {{ $program->icon ?: 'bi-check2-circle' }}"></i>{{ $program->title }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-7 reveal slide-left">
                    <div class="literacy-banner">
                        <img src="https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?auto=format&fit=crop&w=1100&q=80" alt="Kegiatan membaca dan literasi siswa" loading="lazy">
                        <div>
                            <strong>Agenda Literasi Bulan Ini</strong>
                            <span>Bedah buku dan pameran karya literasi siswa.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space" id="berita">
        <div class="container">
            <div class="section-title reveal">
                <span class="section-kicker">Berita & Pengumuman</span>
                <h2>Informasi terbaru dari perpustakaan.</h2>
            </div>
            <div class="row g-4">
                @forelse ($newsItems as $news)
                    <div class="col-md-6 col-lg-4 reveal">
                        <article class="news-card">
                            <img src="{{ $news->image ? asset('storage/'.$news->image) : 'https://placehold.co/640x420/e0f2fe/1d4ed8?text=Berita+Perpustakaan' }}" alt="Gambar berita {{ $news->title }}" loading="lazy">
                            <div>
                                <time datetime="{{ optional($news->published_at ?? $news->content_date)->format('Y-m-d') }}">{{ optional($news->published_at ?? $news->content_date)->format('d M Y') ?? $news->created_at->format('d M Y') }}</time>
                                <h3>{{ $news->title }}</h3>
                                <p>{{ $news->description }}</p>
                                <a href="{{ route('berita.show', $news->slug) }}" class="btn btn-sm btn-outline-primary mt-2">Baca selengkapnya</a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12"><p class="text-muted">Belum ada berita.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section-space soft-bg">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 reveal">
                    <article class="schedule-card">
                        <i class="bi bi-clock-history"></i>
                        <h2>Jam Layanan</h2>
                        <p>{{ $setting('library.hari_libur', 'Senin - Jumat') }}</p>
                        <strong>{{ $setting('library.jam_operasional', '07.00 - 15.30 WIB') }}</strong>
                        <small>Sabtu, Minggu, dan hari libur nasional tutup.</small>
                    </article>
                </div>
                <div class="col-lg-4 reveal">
                    <article class="calendar-card">
                        <h2>Kalender Kegiatan</h2>
                        <ul>
                            @foreach($calendarEvents as $event)
                                <li><span>{{ $event->description }}</span> {{ $event->title }}</li>
                            @endforeach
                        </ul>
                    </article>
                </div>
                <div class="col-lg-4 reveal">
                    <article class="download-card">
                        <h2>Unduhan</h2>
                        @foreach($downloads as $download)
                            <a href="{{ $download->attachment ? asset('storage/'.$download->attachment) : $download->description }}" download><i class="bi {{ $download->icon ?: 'bi-download' }}"></i> {{ $download->title }}</a>
                        @endforeach
                        <h3>Buku Sering Dipinjam</h3>
                        <p>Matematika Lanjut SMA, Cerita dari Ruang Kelas, Ensiklopedia Sains Remaja.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space" id="kontak">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5 reveal">
                    <span class="section-kicker">Kontak</span>
                    <h2>Hubungi perpustakaan sekolah.</h2>
                    <div class="contact-list">
                        <p><i class="bi bi-geo-alt"></i> {{ $setting('contact.alamat', 'Jl. Pendidikan No. 1, Kota Sekolah') }}</p>
                        <p><i class="bi bi-telephone"></i> {{ $setting('contact.telepon', '(022) 1234 5678') }}</p>
                        <p><i class="bi bi-envelope"></i> {{ $setting('contact.email', 'perpustakaan@sma.sch.id') }}</p>
                        <p><i class="bi bi-whatsapp"></i> {{ $setting('contact.whatsapp', '+62 812-3456-7890') }}</p>
                    </div>
                </div>
                <div class="col-lg-7 reveal slide-left">
                    <div class="map-box" role="img" aria-label="Lokasi Google Maps perpustakaan sekolah">
                        <iframe title="Lokasi Perpustakaan SMA" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="{{ $setting('contact.google_maps', 'https://www.google.com/maps?q=Jakarta%20Indonesia&output=embed') }}"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($faqs->isNotEmpty())
    <section class="section-space soft-bg">
        <div class="container">
            <div class="section-title reveal">
                <span class="section-kicker">FAQ</span>
                <h2>Pertanyaan seputar layanan perpustakaan.</h2>
            </div>
            <div class="accordion" id="landingFaq">
                @foreach($faqs as $faq)
                    <div class="accordion-item reveal">
                        <h3 class="accordion-header">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $faq->id }}">
                                {{ $faq->title }}
                            </button>
                        </h3>
                        <div id="faq-{{ $faq->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#landingFaq">
                            <div class="accordion-body">{!! nl2br(e($faq->body)) !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</main>

<footer class="school-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-logo"><i class="bi bi-book-half"></i><span>Perpustakaan {{ $setting('school.nama_sekolah', 'SMA Negeri') }}</span></div>
                <p>{{ $setting('footer.description', 'Halaman informasi layanan, koleksi, literasi, dan pengumuman perpustakaan sekolah.') }}</p>
            </div>
            <div class="col-6 col-lg-2">
                <h2>Tautan Cepat</h2>
                <a href="#profil">Profil</a>
                <a href="#koleksi">Koleksi</a>
                <a href="#layanan">Layanan</a>
                <a href="#berita">Berita</a>
            </div>
            <div class="col-6 col-lg-3">
                <h2>Kontak</h2>
                <p>{{ $setting('contact.alamat', 'Jl. Pendidikan No. 1') }}</p>
                <p>{{ $setting('contact.email', 'perpustakaan@sma.sch.id') }}</p>
                <p>{{ $setting('contact.telepon', '(022) 1234 5678') }}</p>
            </div>
            <div class="col-lg-3">
                <h2>Media Sosial</h2>
                <div class="socials">
                    <a href="{{ $setting('contact.instagram', '#') }}" aria-label="Instagram sekolah"><i class="bi bi-instagram"></i></a>
                    <a href="{{ $setting('contact.facebook', '#') }}" aria-label="Facebook sekolah"><i class="bi bi-facebook"></i></a>
                    <a href="{{ $setting('contact.youtube', '#') }}" aria-label="YouTube sekolah"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">{{ $setting('footer.copyright', 'Hak Cipta '.date('Y').' Perpustakaan SMA Negeri. Didukung oleh SMANGUNLIB.') }}</div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="{{ asset('js/landing.js') }}" defer></script>
</body>
</html>
