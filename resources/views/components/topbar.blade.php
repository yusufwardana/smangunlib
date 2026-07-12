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
        @php
            $overdueLoans = \App\Models\Peminjaman::query()
                ->whereIn('status', ['aktif', 'terlambat'])
                ->whereDate('due_date', '<', \Illuminate\Support\Carbon::today())
                ->count();

            $newStudentRegistrations = \App\Models\Anggota::query()
                ->where('tipe_anggota', 'siswa')
                ->where('created_at', '>=', \Illuminate\Support\Carbon::now()->subDays(7))
                ->count();

            $reservationsReadyForPickup = \App\Models\Reservasi::query()
                ->where('status', 'tersedia')
                ->where('tanggal_kadaluarsa', '>=', \Illuminate\Support\Carbon::now())
                ->count();

            $topbarNotifications = [
                [
                    'count' => $overdueLoans,
                    'icon' => 'fa-solid fa-circle-exclamation',
                    'color' => 'text-warning',
                    'label' => 'Buku melewati due date',
                    'url' => route('sirkulasi.index'),
                ],
                [
                    'count' => $newStudentRegistrations,
                    'icon' => 'fa-solid fa-user-plus',
                    'color' => 'text-success',
                    'label' => 'Registrasi siswa baru 7 hari terakhir',
                    'url' => route('anggota.index'),
                ],
                [
                    'count' => $reservationsReadyForPickup,
                    'icon' => 'fa-solid fa-bookmark',
                    'color' => 'text-primary',
                    'label' => 'Reservasi siap diambil',
                    'url' => '#',
                ],
            ];
            $topbarNotificationTotal = $overdueLoans + $newStudentRegistrations + $reservationsReadyForPickup;
        @endphp
        <div class="dropdown">
            <a href="#" class="text-dark position-relative text-decoration-none" data-bs-toggle="dropdown">
                <i class="fa-regular fa-bell fs-5"></i>
                @if($topbarNotificationTotal > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        {{ $topbarNotificationTotal }}
                    </span>
                @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-3" style="width: 300px; border-radius: 1rem;">
                <li class="px-3 py-2 fw-bold border-bottom">Notifikasi Baru</li>
                @foreach($topbarNotifications as $notification)
                    @if($notification['count'] > 0)
                        <li>
                            <a class="dropdown-item py-2" href="{{ $notification['url'] }}">
                                <i class="{{ $notification['icon'] }} {{ $notification['color'] }} me-2"></i>
                                {{ $notification['count'] }} {{ $notification['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
                @if($topbarNotificationTotal === 0)
                    <li><span class="dropdown-item py-2 text-muted">Tidak ada notifikasi baru</span></li>
                @endif
            </ul>
        </div>

        @php
            $user = Auth::user();
            $userName = $user?->name ?? 'Pengguna';
            $roleName = $user?->roles?->first()?->name ?? 'Pengguna';
        @endphp

        <!-- User Profile -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=4361ee&color=fff" alt="User" class="rounded-circle shadow-sm" width="40" height="40">
                <div class="ms-2 d-none d-lg-block">
                    <span class="d-block fw-bold" style="font-size: 0.9rem;">{{ $userName }}</span>
                    <span class="d-block text-muted text-capitalize" style="font-size: 0.75rem;">{{ str_replace('_', ' ', $roleName) }}</span>
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
