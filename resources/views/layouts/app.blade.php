<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dinas Pemberdayaan Masyarakat Desa Kabupaten Pasuruan')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body>
    @auth
        <div class="admin-shell">
            <aside class="sidebar" id="adminSidebar" aria-label="Navigasi admin">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/Lambang_Kabupaten_Pasuruan.png" alt="Lambang Kabupaten Pasuruan" class="sidebar-logo">
                    <span class="sidebar-brand-text">
                        <span class="sidebar-brand-title">DINAS PEMBERDAYAAN</span>
                        <span class="sidebar-brand-sub">MASYARAKAT DESA</span>
                        <span class="sidebar-brand-reg">KABUPATEN PASURUAN</span>
                    </span>
                </a>

                <div class="sidebar-profile">
                    @if (auth()->user()->foto_profil)
                        <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" alt="Foto {{ auth()->user()->name }}" class="avatar">
                    @else
                        <span class="avatar avatar-placeholder">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                    <div>
                        <div class="profile-name">{{ auth()->user()->name }}</div>
                        <div class="profile-role">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                    </div>
                </div>

                <nav class="side-nav">
                    <div class="sidebar-section">Menu Utama</div>
                    <div class="nav-group">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 3h8v8H3V3Zm10 0h8v8h-8V3ZM3 13h8v8H3v-8Zm10 0h8v8h-8v-8Z"/></svg>
                            </span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                        <a href="{{ route('desa.index') }}" class="nav-link {{ request()->routeIs('desa.*') ? 'active' : '' }}" data-tooltip="Data Wilayah">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2 3 7v15h18V7l-9-5Zm-4 18v-7h8v7H8Z"/></svg>
                            </span>
                            <span class="nav-text">Data Wilayah</span>
                        </a>
                        <a href="{{ route('perangkat.index') }}" class="nav-link {{ request()->routeIs('perangkat.*') ? 'active' : '' }}" data-tooltip="Perangkat">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm-12 9c.7-3.2 3.6-5 8-5s7.3 1.8 8 5H4Z"/></svg>
                            </span>
                            <span class="nav-text">Perangkat</span>
                        </a>
                    </div>

                    <div class="sidebar-section">Administrasi</div>
                    <div class="nav-group">
                        <a href="{{ route('settings.profile') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" data-tooltip="Profil">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm8 10H4c.7-4.1 3.6-7 8-7s7.3 2.9 8 7Z"/></svg>
                            </span>
                            <span class="nav-text">Profil</span>
                        </a>
                        @if (auth()->user()->hasRole('super_admin'))
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" data-tooltip="Manajemen User">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8.5 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7ZM2 21c.6-4.1 3-7 7-7s6.4 2.9 7 7H2Zm14.5-7c2.9.2 4.8 2.1 5.5 5h-4.1a9 9 0 0 0-1.4-5Z"/></svg>
                                </span>
                                <span class="nav-text">Manajemen User</span>
                            </a>
                            <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" data-tooltip="Audit Log">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h14a1 1 0 0 1 1 1v17l-3-2-3 2-3-2-3 2-3-2-3 2V4a1 1 0 0 1 1-1Zm4 5h8V6H9v2Zm0 4h8v-2H9v2Zm0 4h5v-2H9v2Z"/></svg>
                                </span>
                                <span class="nav-text">Audit Log</span>
                            </a>
                            <a href="{{ route('backups.index') }}" class="nav-link {{ request()->routeIs('backups.*') ? 'active' : '' }}" data-tooltip="Backup">
                                <span class="nav-icon">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3C7.6 3 4 4.3 4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6c0-1.7-3.6-3-8-3Zm0 2c3.6 0 6 .8 6 1s-2.4 1-6 1-6-.8-6-1 2.4-1 6-1Zm0 14c-3.6 0-6-.8-6-1v-2.2c1.4.8 3.6 1.2 6 1.2s4.6-.4 6-1.2V18c0 .2-2.4 1-6 1Zm0-4c-3.6 0-6-.8-6-1v-2.2c1.4.8 3.6 1.2 6 1.2s4.6-.4 6-1.2V14c0 .2-2.4 1-6 1Z"/></svg>
                                </span>
                                <span class="nav-text">Backup</span>
                            </a>
                        @endif
                    </div>
                </nav>

                <div class="sidebar-footer">
                    <button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" aria-label="Ciutkan sidebar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-button" aria-label="Log Out">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" aria-hidden="true"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 3v18"/></svg>
                            <span class="logout-text">Log Out</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="sidebar-backdrop" data-sidebar-close></div>

            <div class="main-area">
                <header class="topbar">
                    <div class="topbar-inner">
                        <div class="topbar-left">
                            <button type="button" class="hamburger-btn" id="hamburgerBtn" aria-label="Buka navigasi">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                            </button>
                        </div>
                        <div class="top-actions">
                            <div class="notification-menu" data-user-menu>
                                <button type="button" class="top-action blue" data-user-menu-toggle aria-expanded="false" aria-label="Notifikasi masa jabatan">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm8-5-2-2.2V10a6 6 0 0 0-4.5-5.8V3a1.5 1.5 0 0 0-3 0v1.2A6 6 0 0 0 6 10v4.8L4 17v1h16v-1Z"/></svg>
                                    @if (($navNotificationCount ?? 0) > 0)
                                        <span class="notification-dot">{{ min($navNotificationCount, 99) }}</span>
                                    @endif
                                </button>
                                <div class="user-dropdown notification-dropdown" data-user-dropdown>
                                    <div class="notification-header">
                                        <div>
                                            <div class="user-dropdown-name">Notifikasi</div>
                                            <div class="user-dropdown-role">Masa jabatan perangkat</div>
                                        </div>
                                        @if (($navNotificationCount ?? 0) > 0)
                                            <span class="result-pill">{{ $navNotificationCount }}</span>
                                        @endif
                                    </div>

                                    @forelse (($navNotifications ?? collect()) as $notification)
                                        @php
                                            $isExpired = $notification->akhir_menjabat->lt($navNotificationToday);
                                            $notificationUrl = auth()->user()->canManageData()
                                                ? route('perangkat.edit', $notification)
                                                : route('perangkat.index', ['search' => $notification->nama]);
                                        @endphp
                                        <a href="{{ $notificationUrl }}" class="notification-item">
                                            <span class="notification-status {{ $isExpired ? 'danger' : 'warning' }}"></span>
                                            <span class="notification-body">
                                                <strong>{{ $notification->nama }}</strong>
                                                <span>{{ $notification->jabatanPerangkat->nama }} - {{ $notification->wilayah->nama }}</span>
                                                <span class="{{ $isExpired ? 'text-danger' : 'text-warning' }}">{{ $notification->countdown_masa_jabatan }}</span>
                                            </span>
                                        </a>
                                    @empty
                                        <div class="notification-empty">Tidak ada masa jabatan yang perlu ditindaklanjuti.</div>
                                    @endforelse

                                    <a href="{{ route('perangkat.index', ['masa_jabatan' => 'hampir_berakhir']) }}" class="dropdown-link">Lihat perangkat hampir berakhir</a>
                                    <a href="{{ route('perangkat.index', ['masa_jabatan' => 'berakhir']) }}" class="dropdown-link">Lihat perangkat sudah berakhir</a>
                                </div>
                            </div>
                            <div class="user-menu" data-user-menu>
                                <button type="button" class="top-action yellow user-menu-toggle" data-user-menu-toggle aria-expanded="false" aria-label="Settings & Menu">
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m19.4 13.5.1-1.5-.1-1.5 2-1.5-2-3.4-2.4 1a7.8 7.8 0 0 0-2.6-1.5L14 2h-4l-.4 3.1A7.8 7.8 0 0 0 7 6.6l-2.4-1-2 3.4 2 1.5L4.5 12l.1 1.5-2 1.5 2 3.4 2.4-1a7.8 7.8 0 0 0 2.6 1.5L10 22h4l.4-3.1a7.8 7.8 0 0 0 2.6-1.5l2.4 1 2-3.4-2-1.5ZM12 15.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7Z"/></svg>
                                </button>
                                <div class="user-dropdown" data-user-dropdown>
                                    <div class="user-dropdown-header">
                                        @if (auth()->user()->foto_profil)
                                            <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" alt="Foto {{ auth()->user()->name }}" class="avatar user-avatar">
                                        @else
                                            <span class="avatar avatar-placeholder user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                        @endif
                                        <div>
                                            <div class="user-dropdown-name">{{ auth()->user()->name }}</div>
                                            <div class="user-dropdown-role">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                                        </div>
                                    </div>
                                    <a href="{{ route('settings.profile') }}" class="dropdown-link">Settings / Profil Saya</a>
                                    @if (auth()->user()->hasRole('super_admin'))
                                        <a href="{{ route('users.index') }}" class="dropdown-link">Manajemen User</a>
                                        <a href="{{ route('activity-logs.index') }}" class="dropdown-link">Audit Log</a>
                                        <a href="{{ route('backups.index') }}" class="dropdown-link">Backup</a>
                                    @endif
                                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                                        @csrf
                                        <button type="submit" class="dropdown-link dropdown-link-danger">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="container">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error">Periksa kembali input yang belum sesuai.</div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <main class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">Periksa kembali input yang belum sesuai.</div>
            @endif

            @yield('content')
        </main>
    @endauth

    @stack('scripts')
    <script>
        // ── User Menu ──
        document.querySelectorAll('[data-user-menu]').forEach((menu) => {
            const toggle = menu.querySelector('[data-user-menu-toggle]');

            toggle.addEventListener('click', (event) => {
                event.stopPropagation();
                const isOpen = menu.classList.toggle('open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', (event) => {
                if (! menu.contains(event.target)) {
                    menu.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    menu.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // ── Mobile drawer: hamburger & backdrop ──
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const backdrop = document.querySelector('.sidebar-backdrop');

        function setSidebarOpen(isOpen) {
            document.body.classList.toggle('sidebar-open', isOpen);
            if (hamburgerBtn) {
                hamburgerBtn.setAttribute('aria-label', isOpen ? 'Tutup navigasi' : 'Buka navigasi');
            }
        }

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', () => {
                const isNowOpen = ! document.body.classList.contains('sidebar-open');
                setSidebarOpen(isNowOpen);
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', () => setSidebarOpen(false));
        }

        // Close mobile drawer on nav link click
        document.querySelectorAll('.sidebar .nav-link').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.matchMedia('(max-width: 900px)').matches) {
                    setSidebarOpen(false);
                }
            });
        });

        // ── Desktop sidebar collapse/expand with localStorage ──
        const collapseBtn = document.getElementById('sidebarCollapseBtn');

        function setSidebarCollapsed(collapsed) {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            if (collapseBtn) {
                collapseBtn.setAttribute('aria-label', collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar');
            }
            try {
                localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
            } catch (e) { /* localStorage unavailable */ }
        }

        if (collapseBtn) {
            // Restore saved state (desktop only)
            if (! window.matchMedia('(max-width: 900px)').matches) {
                try {
                    const saved = localStorage.getItem('sidebarCollapsed');
                    if (saved === '1') {
                        setSidebarCollapsed(true);
                    }
                } catch (e) { /* ignore */ }
            }

            collapseBtn.addEventListener('click', () => {
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                setSidebarCollapsed(!isCollapsed);
            });
        }

        // ── Auto-dismiss flash alerts with smooth collapse ──
        document.querySelectorAll('.alert-success').forEach((alert) => {
            setTimeout(() => {
                alert.classList.add('alert-fade-out');
                setTimeout(() => {
                    alert.style.maxHeight = '0';
                    alert.style.marginBottom = '0';
                    alert.style.paddingTop = '0';
                    alert.style.paddingBottom = '0';
                    alert.style.overflow = 'hidden';
                }, 50);
                setTimeout(() => alert.remove(), 350);
            }, 4000);
        });

        // ── Form submit: loading state ──
        // IMPORTANT: SEMUA perubahan DOM di-defer ke requestAnimationFrame
        // agar browser punya waktu penuh mengumpulkan data form dan
        // memulai navigasi SEBELUM tombol diubah.
        document.querySelectorAll('form').forEach((form) => {
            let submitting = false;

            form.addEventListener('submit', function (e) {
                const btn = this.querySelector('button[type="submit"]');
                if (!btn || submitting) {
                    e.preventDefault();
                    return;
                }
                submitting = true;

                // Defer semua perubahan DOM — jangan ubah apapun
                // secara sinkronus di dalam event submit handler!
                requestAnimationFrame(() => {
                    btn.disabled = true;
                    btn.classList.add('is-loading');
                    if (!btn.dataset.originalText) {
                        btn.dataset.originalText = btn.innerHTML;
                    }
                    btn.innerHTML = '<span class="spinner"></span> Memproses...';
                });
            });
        });

        // ── Escape key closes mobile drawer ──
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setSidebarOpen(false);
            }
        });

        // ── Refresh collapse state on resize ──
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const isMobile = window.matchMedia('(max-width: 900px)').matches;
                if (isMobile) {
                    document.body.classList.remove('sidebar-collapsed');
                } else {
                    try {
                        const saved = localStorage.getItem('sidebarCollapsed');
                        if (saved === '1') {
                            document.body.classList.add('sidebar-collapsed');
                        } else {
                            document.body.classList.remove('sidebar-collapsed');
                        }
                    } catch (e) { /* ignore */ }
                }
            }, 150);
        });
    </script>
</body>
</html>
