<ul style="list-style:none;padding:0;margin:0">
    @if(Auth::user()->canFillPlan())
    <li class="n-nav-item">
        <a href="{{ route('karyawan.kalender') }}" class="{{ request()->routeIs('karyawan.kalender*') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> Kalender Saya
        </a>
    </li>
    @endif

    <li class="n-nav-item">
        <a href="{{ route('announcements.index') }}" class="{{ request()->routeIs('announcements.*') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i> Pengumuman
        </a>
    </li>

    @if(Auth::user()->canMonitor())
    <div class="n-nav-divider"></div>
    <li class="n-nav-item">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="n-nav-item">
        <a href="{{ route('monitoring.tim') }}" class="{{ request()->routeIs('monitoring*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Tim
        </a>
    </li>
    <li class="n-nav-item">
        <a href="{{ route('kinerja.index') }}" class="{{ request()->routeIs('kinerja*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Rekap Kinerja
        </a>
    </li>
    <li class="n-nav-item">
        <a href="{{ route('leaderboard.index') }}" class="{{ request()->routeIs('leaderboard*') ? 'active' : '' }}">
            <i class="bi bi-trophy"></i> Leaderboard
        </a>
    </li>
    <li class="n-nav-item">
        <a href="{{ route('report.weekly') }}" class="{{ request()->routeIs('report.weekly*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Rekap Mingguan
        </a>
    </li>
    <li class="n-nav-item">
        <a href="{{ route('targets.index') }}" class="{{ request()->routeIs('targets*') ? 'active' : '' }}">
            <i class="bi bi-bullseye"></i> Target Bulanan
        </a>
    </li>
    @if(Auth::user()->isManager() || Auth::user()->isAdmin())
    <li class="n-nav-item">
        @php $pendingCount = \App\Models\User::where('status','pending')->when(Auth::user()->isManager(), fn($q) => $q->where('division_id', Auth::user()->division_id))->count(); @endphp
        <a href="{{ route('approval.index') }}" class="{{ request()->routeIs('approval*') ? 'active' : '' }}">
            <i class="bi bi-person-check"></i> Persetujuan
            @if($pendingCount > 0)
                <span class="n-nav-badge n-nav-badge-warn">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
    @endif
    @endif

    @if(Auth::user()->isAdmin())
    <div class="n-nav-divider" style="margin-top:10px"></div>
    <div class="n-nav-section">Admin</div>
    <li class="n-nav-item">
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> Kelola User
        </a>
    </li>
    <li class="n-nav-item">
        <a href="{{ route('admin.divisions.index') }}" class="{{ request()->routeIs('admin.divisions*') ? 'active' : '' }}">
            <i class="bi bi-diagram-3"></i> Divisi
        </a>
    </li>
    <li class="n-nav-item">
        <a href="{{ route('admin.holidays.index') }}" class="{{ request()->routeIs('admin.holidays*') ? 'active' : '' }}">
            <i class="bi bi-calendar-x"></i> Hari Libur
        </a>
    </li>
    <li class="n-nav-item">
        <a href="{{ route('admin.announcements.index') }}" class="{{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
            <i class="bi bi-megaphone-fill"></i> Kelola Pengumuman
        </a>
    </li>
    @endif
</ul>
