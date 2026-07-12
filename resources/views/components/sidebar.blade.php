<nav id="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-book-open-reader"></i>
        <span>SMAN<span style="color: #2b2d42;">GUN</span>LIB</span>
    </div>
    
    <ul class="sidebar-nav">
        <li class="mt-2 mb-2 text-muted small fw-bold px-4 text-uppercase">Menu Utama</li>
        
        <li>
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                <i class="fa-solid fa-grid-2"></i> <span>Dashboard</span>
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
            <a href="{{ route('system.info') }}" class="{{ request()->routeIs('system.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> <span>Pengaturan Sistem</span>
            </a>
        </li>
        <li>
            <a href="{{ route('system.settings.index') }}" class="{{ request()->routeIs('system.settings.*') || request()->routeIs('system.contents.*') || request()->routeIs('system.menus.*') || request()->routeIs('system.media.*') ? 'active' : '' }}">
                <i class="fa-solid fa-sliders"></i> <span>Konten & Identitas</span>
            </a>
        </li>
        
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
