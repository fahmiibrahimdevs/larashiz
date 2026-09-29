<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
        </ul>
        <div class="search-element">
            <input class="form-control" type="search" placeholder="Search" aria-label="Search" data-width="250">
            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            <div class="search-backdrop"></div>
            <div class="search-result">
                <div class="search-header">Quick Search</div>
                <div class="search-item"><a href="{{ route('dashboard') }}">Dashboard</a></div>
                <div class="search-item"><a href="/shizuefi/index">Ecommerce Dashboard</a></div>
                <div class="search-item"><a href="/shizuefi/bootstrap-card">Bootstrap Card</a></div>
            </div>
        </div>
    </form>

    <ul class="navbar-nav navbar-right">
        @auth
            <!-- User Dropdown -->
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img alt="image" src="/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
                    <div class="d-sm-none d-lg-inline-block">Hi, {{ auth()->user()->name }}</div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">
                        Role: {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}
                    </div>
                    <a href="{{ route('profile') }}" class="dropdown-item has-icon">
                        <i class="far fa-user"></i> Profil Saya
                    </a>
                    @if (auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.users') }}" class="dropdown-item has-icon">
                            <i class="fas fa-users-cog"></i> Kelola Pengguna
                        </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="dropdown-item has-icon text-danger border-0 bg-transparent w-100 text-left" style="cursor: pointer; display: flex; align-items: center; outline: none;">
                            <i class="fas fa-sign-out-alt mr-2"></i> Keluar (Logout)
                        </button>
                    </form>
                </div>
            </li>
        @else
            <li class="nav-item">
                <a href="{{ route('login') }}" class="nav-link nav-link-lg">Login</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('register') }}" class="nav-link nav-link-lg">Register</a>
            </li>
        @endauth
    </ul>
</nav>
