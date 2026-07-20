@php
    // Menu dinamis berbasis permission (RBAC). Bila tabel menu belum di-seed,
    // $dynamicMenu akan kosong dan sidebar memakai fallback statis di bawah
    // sehingga fitur navigasi lama tetap berfungsi.
    $dynamicMenu = auth()->check() ? sidebar_menu() : [];
@endphp

<nav id="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-book-open-reader"></i>
        <span>SMAN<span style="color: #2b2d42;">GUN</span>LIB</span>
    </div>

    <ul class="sidebar-nav">
        @if(!empty($dynamicMenu))
            {{-- =========================================================
                 SIDEBAR DINAMIS: otomatis membaca permission dari database.
                 Menu tanpa hak akses tidak akan ditampilkan.
            ========================================================== --}}
            <li class="mt-2 mb-2 text-muted small fw-bold px-4 text-uppercase">Menu</li>

            @foreach ($dynamicMenu as $item)
                @if(empty($item['children']))
                    <li>
                        <a href="{{ $item['url'] }}"
                           class="{{ $item['route'] && request()->routeIs($item['route']) ? 'active' : '' }}"
                           @if($item['key'] === 'landing') target="_blank" @endif>
                            <i class="{{ $item['icon'] ?? 'fa-solid fa-circle' }}"></i>
                            <span>{{ $item['title'] }}</span>
                        </a>
                    </li>
                @else
                    @php $collapseId = 'menu-'.\Illuminate\Support\Str::slug($item['key']); @endphp
                    <li>
                        <a href="#{{ $collapseId }}" data-bs-toggle="collapse" role="button"
                           class="d-flex justify-content-between align-items-center">
                            <span>
                                <i class="{{ $item['icon'] ?? 'fa-solid fa-folder' }}"></i>
                                <span>{{ $item['title'] }}</span>
                            </span>
                            <i class="fa-solid fa-angle-down small"></i>
                        </a>
                        <ul class="collapse list-unstyled ps-3" id="{{ $collapseId }}">
                            @foreach ($item['children'] as $child)
                                <li>
                                    <a href="{{ $child['url'] }}"
                                       class="{{ $child['route'] && request()->routeIs($child['route']) ? 'active' : '' }}">
                                        <i class="{{ $child['icon'] ?? 'fa-solid fa-angle-right' }}"></i>
                                        <span>{{ $child['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
        @else
            {{-- =========================================================
                 FALLBACK STATIS (dipakai bila menu belum di-seed).
                 Mempertahankan navigasi lama agar tidak ada fitur yang hilang.
            ========================================================== --}}
            <li class="mt-2 mb-2 text-muted small fw-bold px-4 text-uppercase">Menu Utama</li>

            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-grid-2"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('landing') }}" target="_blank">
                    <i class="fa-solid fa-house"></i> <span>Lihat Landing Page</span>
                </a>
            </li>
            <li>
                <a href="{{ route('anggota.index') }}" class="{{ request()->routeIs('anggota.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> <span>Data Anggota</span>
                </a>
            </li>

            <li class="mt-4 mb-2 text-muted small fw-bold px-4 text-uppercase">Koleksi & Sirkulasi</li>

            <li>
                <a href="{{ route('koleksi.buku.index') }}" class="{{ request()->routeIs('koleksi.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-book"></i> <span>Katalog Buku</span>
                </a>
            </li>
            <li>
                <a href="{{ route('sirkulasi.index') }}" class="{{ request()->routeIs('sirkulasi.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-right-left"></i> <span>Sirkulasi / Peminjaman</span>
                </a>
            </li>
            <li>
                <a href="#" class="">
                    <i class="fa-solid fa-money-bill-wave"></i> <span>Denda & Keterlambatan</span>
                </a>
            </li>

            <li class="mt-4 mb-2 text-muted small fw-bold px-4 text-uppercase">Laporan & Pengaturan</li>

            <li>
                <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> <span>Laporan Akreditasi</span>
                </a>
            </li>
            <li>
                <a href="{{ route('system.info') }}" class="{{ request()->routeIs('system.info') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i> <span>Pengaturan Sistem</span>
                </a>
            </li>
            <li>
                <a href="{{ route('system.settings.index') }}" class="{{ request()->routeIs('system.settings.*') || request()->routeIs('system.contents.*') || request()->routeIs('system.menus.*') || request()->routeIs('system.media.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i> <span>Konten & Identitas</span>
                </a>
            </li>
            @can('view', \App\Models\ThemeSetting::class)
            <li>
                <a href="{{ route('system.theme.index') }}" class="{{ request()->routeIs('system.theme.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-palette"></i> <span>Theme Manager</span>
                </a>
            </li>
            @endcan
            @can('view', \App\Models\Menu::class)
            <li>
                <a href="{{ route('system.permissions.index') }}" class="{{ request()->routeIs('system.permissions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-lock"></i> <span>Menu Permission</span>
                </a>
            </li>
            @endcan
        @endif

        <li class="mt-5">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();" class="text-danger">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> <span>Logout</span>
            </a>
            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>
