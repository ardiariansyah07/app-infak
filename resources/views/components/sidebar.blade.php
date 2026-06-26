@php
    $role = Auth::user()->role ?? null;
    $dashboardRoute = match ($role) {
        'admin' => 'admin.dashboard',
        'petugas_infak' => 'petugas.dashboard',
        'pembimbing_rayon' => 'rayon.dashboard',
        'orang_tua' => 'ortu.dashboard',
        default => 'dashboard',
    };
@endphp

<div id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="bi bi-wallet2"></i>
            <span class="menu-text">Sistem Infak</span>
        </div>
        <div class="sidebar-subtitle">
            {{ str_replace('_', ' ', strtoupper($role ?? 'USER')) }}
        </div>
    </div>

    <div class="sidebar-menu mt-3">
        <a href="{{ route($dashboardRoute) }}" class="{{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span class="menu-text">Dashboard</span>
        </a>
    </div>

    @if($role === 'admin')
        <div class="menu-group">MASTER DATA</div>
        <div class="sidebar-menu">
            <a href="#" onclick="toggleMasterData(event)">
                <i class="bi bi-folder2-open"></i>
                <span class="menu-text">Master Data</span>
                <span class="ms-auto menu-text"><i id="master-arrow" class="bi bi-chevron-down"></i></span>
            </a>
            <div id="master-menu">
                <a href="{{ route('admin.tahun-ajaran.index') }}" class="submenu {{ request()->is('admin/tahun-ajaran*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-range"></i><span class="menu-text">Tahun Ajaran</span>
                </a>
                <a href="{{ route('admin.guru.index') }}" class="submenu {{ request()->is('admin/guru*') ? 'active' : '' }}">
                    <i class="bi bi-person-workspace"></i><span class="menu-text">Guru</span>
                </a>
                <a href="{{ route('admin.rayon.index') }}" class="submenu {{ request()->is('admin/rayon*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i><span class="menu-text">Rayon</span>
                </a>
                <a href="{{ route('admin.rombel.index') }}" class="submenu {{ request()->is('admin/rombel*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i><span class="menu-text">Rombel</span>
                </a>
                <a href="{{ route('admin.siswa.index') }}" class="submenu {{ request()->is('admin/siswa*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i><span class="menu-text">Siswa</span>
                </a>
            </div>
        </div>

        <div class="menu-group">AKSES</div>
        <div class="sidebar-menu">
            <a href="{{ route('admin.user.index') }}" class="{{ request()->is('admin/user*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i><span class="menu-text">User & Role</span>
            </a>
        </div>

        <div class="menu-group">TRANSAKSI</div>
        <div class="sidebar-menu">
            <a href="#" onclick="toggleTransaksi(event)">
                <i class="bi bi-cash-stack"></i>
                <span class="menu-text">Transaksi</span>
                <span class="ms-auto menu-text"><i id="transaksi-arrow" class="bi bi-chevron-down"></i></span>
            </a>
            <div id="transaksi-menu">
                <a href="{{ route('admin.komitmen-infak.index') }}" class="submenu {{ request()->is('admin/komitmen-infak*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i><span class="menu-text">Komitmen Infak</span>
                </a>
                <a href="{{ route('admin.tagihan.index') }}" class="submenu {{ request()->is('admin/tagihan*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i><span class="menu-text">Tagihan</span>
                </a>
                <a href="{{ route('admin.pembayaran.index') }}" class="submenu {{ request()->is('admin/pembayaran*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i><span class="menu-text">Pembayaran</span>
                </a>
            </div>
        </div>

        <div class="menu-group">LAPORAN</div>
        <div class="sidebar-menu">
            <a href="{{ route('admin.laporan.index') }}" class="{{ request()->is('admin/laporan*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i><span class="menu-text">Laporan</span>
            </a>
        </div>
    @endif

    @if($role === 'petugas_infak')
        <div class="menu-group">TRANSAKSI</div>
        <div class="sidebar-menu">
            <a href="{{ route('petugas.pembayaran.index') }}" class="{{ request()->is('petugas/pembayaran*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i><span class="menu-text">Pembayaran</span>
            </a>
        </div>
    @endif

    @if($role === 'orang_tua')
        <div class="menu-group">ORANG TUA</div>
        <div class="sidebar-menu">
            <a href="{{ route('ortu.komitmen.index') }}" class="{{ request()->is('ortu/komitmen-infak*') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i><span class="menu-text">Nominal Infak</span>
            </a>
            <a href="{{ route('ortu.pembayaran.create') }}" class="{{ request()->is('ortu/pembayaran/create') ? 'active' : '' }}">
                <i class="bi bi-upload"></i><span class="menu-text">Lapor Bayar</span>
            </a>
            <a href="{{ route('ortu.pembayaran.index') }}" class="{{ request()->is('ortu/pembayaran') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i><span class="menu-text">Riwayat</span>
            </a>
        </div>
    @endif

    @if($role === 'pembimbing_rayon')
        <div class="menu-group">RAYON</div>
        <div class="sidebar-menu">
            <a href="{{ route('rayon.siswa.index') }}" class="{{ request()->is('rayon/siswa*') ? 'active' : '' }}">
                <i class="bi bi-people"></i><span class="menu-text">Siswa Rayon</span>
            </a>
        </div>
    @endif

    <div class="menu-group">AKUN</div>
    <div class="sidebar-menu">
        <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i>
            <span class="menu-text">Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">
            @csrf
        </form>
    </div>
</div>
