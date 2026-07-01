<ul class="nav flex-column px-2">
    @if(Auth::user()->isKaryawan())
        <li class="nav-item">
            <a href="{{ route('karyawan.kalender') }}" class="nav-link {{ request()->routeIs('karyawan.kalender*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Kalender Saya
            </a>
        </li>
    @endif

    @if(Auth::user()->isManager() || Auth::user()->isAdmin())
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('monitoring.tim') }}" class="nav-link {{ request()->routeIs('monitoring*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Tim
            </a>
        </li>
        <li class="nav-item">
            @php $pendingCount = \App\Models\User::where('status','pending')->when(Auth::user()->isManager(), fn($q) => $q->where('division_id', Auth::user()->division_id))->count(); @endphp
            <a href="{{ route('approval.index') }}" class="nav-link {{ request()->routeIs('approval*') ? 'active' : '' }}">
                <i class="bi bi-person-check"></i> Persetujuan
                @if($pendingCount > 0)
                    <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
    @endif

    @if(Auth::user()->isAdmin())
        <li class="mt-2 px-2 text-muted small text-uppercase fw-semibold" style="font-size:.7rem">Admin</li>
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Kelola User
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.divisions.index') }}" class="nav-link {{ request()->routeIs('admin.divisions*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i> Divisi
            </a>
        </li>
    @endif
</ul>
