<x-app-layout>
    <x-slot name="header">
        <h1>Dashboard</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Utama</div>
        </div>
    </x-slot>

    <!-- Section Title & Lead -->
    <h2 class="section-title">Ringkasan Sistem</h2>
    <p class="section-lead mb-3">
        Selamat datang di panel kontrol utama sistem Larashiz.
    </p>

    <!-- Stat Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="far fa-user"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Pengguna</h4>
                    </div>
                    <div class="card-body">
                        {{ \App\Models\User::count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Pengguna Aktif</h4>
                    </div>
                    <div class="card-body">
                        {{ \App\Models\User::where('is_active', true)->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Menunggu Aktivasi</h4>
                    </div>
                    <div class="card-body">
                        {{ \App\Models\User::where('is_active', false)->count() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Roles</h4>
                    </div>
                    <div class="card-body">
                        {{ \App\Models\Role::count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Hero & Info -->
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Informasi Akun</h4>
                </div>
                <div class="card-body">
                    <div class="hero bg-primary text-white" style="border-radius: 8px; padding: 25px;">
                        <div class="hero-inner">
                            <h2>Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                            <p class="lead">
                                Anda login dengan email <strong>{{ auth()->user()->email }}</strong>.
                            </p>
                            <div class="mt-3">
                                <span class="badge badge-light text-primary font-weight-bold p-2 mr-2">
                                    <i class="fas fa-user-tag"></i> Role: {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'User' }}
                                </span>
                                <span class="badge badge-success p-2">
                                    <i class="fas fa-check-circle"></i> Status Akun: Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Aksi Cepat</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @if (auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-users-cog text-primary mr-2"></i> Kelola Pengguna
                                </div>
                                <span class="badge badge-primary badge-pill">{{ \App\Models\User::where('is_active', false)->count() }} baru</span>
                            </a>
                        @endif
                        <a href="/shizuefi/index" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-line text-success mr-2"></i> Ecommerce Dashboard
                        </a>
                        <a href="/shizuefi/index-0" class="list-group-item list-group-item-action">
                            <i class="fas fa-fire text-danger mr-2"></i> General Dashboard
                        </a>
                        <a href="{{ route('profile') }}" class="list-group-item list-group-item-action">
                            <i class="far fa-user text-info mr-2"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
