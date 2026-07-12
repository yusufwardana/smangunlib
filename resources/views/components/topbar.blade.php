<header class="topbar">
    <div class="d-flex align-items-center">
        <button class="btn btn-link text-dark text-decoration-none fs-5 d-md-none" id="menu-toggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="input-group d-none d-md-flex" style="width: 300px;">
            <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="text" class="form-control bg-light border-0" placeholder="Cari buku, anggota, NIS...">
        </div>
    </div>
    
    <div class="d-flex align-items-center gap-4">
        <!-- Notifikasi -->
        <div class="dropdown">
            <a href="#" class="text-dark position-relative text-decoration-none" data-bs-toggle="dropdown">
                <i class="fa-regular fa-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                    3
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-3" style="width: 300px; border-radius: 1rem;">
                <li class="px-3 py-2 fw-bold border-bottom">Notifikasi Baru</li>
                <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-circle-exclamation text-warning me-2"></i> 5 Buku melewati due date</a></li>
                <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-user-plus text-success me-2"></i> 12 Registrasi siswa baru</a></li>
                <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-bookmark text-primary me-2"></i> 2 Reservasi menunggu diambil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center text-primary" href="#">Lihat Semua Notifikasi</a></li>
            </ul>
        </div>

        <!-- User Profile -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4361ee&color=fff" alt="User" class="rounded-circle shadow-sm" width="40" height="40">
                <div class="ms-2 d-none d-lg-block">
                    <span class="d-block fw-bold" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                    <span class="d-block text-muted text-capitalize" style="font-size: 0.75rem;">{{ str_replace('_', ' ', Auth::user()->roles->first()->name ?? 'Pengguna') }}</span>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="border-radius: 1rem;">
                <li><a class="dropdown-item" href="#"><i class="fa-regular fa-user me-2"></i> Profil Saya</a></li>
                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-key me-2"></i> Ganti Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-topbar').submit();">
                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout
                    </a>
                    <form id="logout-form-topbar" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
