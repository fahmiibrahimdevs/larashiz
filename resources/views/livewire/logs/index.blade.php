<div>
    <x-slot name="header">
        <h1>Monitoring Log Aktivitas</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Monitoring Log</div>
        </div>
    </x-slot>

    <!-- Section Title & Lead -->
    <h2 class="section-title">Log Aktivitas</h2>
    <p class="section-lead mb-3">
        Monitoring dan lacak seluruh rekaman aktivitas sistem dan interaksi pengguna secara real-time.
    </p>

    <!-- Top Summary Statistic Cards (Bawaan Template Stisla) -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Aktivitas</h4>
                    </div>
                    <div class="card-body">
                        {{ number_format($statistics['total']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Log Info</h4>
                    </div>
                    <div class="card-body">
                        {{ number_format($statistics['info']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Log Warning</h4>
                    </div>
                    <div class="card-body">
                        {{ number_format($statistics['warning']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Log Error</h4>
                    </div>
                    <div class="card-body">
                        {{ number_format($statistics['error']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row align-items-start">
        <!-- Left Sidebar: Filter Panel (Desktop Only: d-none d-md-block) -->
        <div class="col-lg-4 col-md-5 d-none d-md-block">
            <div class="card">
                <div class="card-header">
                    <h4>Filter Log</h4>
                    @if ($search || $activePreset !== 'today' || $filterLevel || $filterModule)
                        <div class="card-header-action">
                            <button type="button" wire:click="resetFilters" class="btn btn-sm btn-link text-danger font-weight-bold text-decoration-none p-0">
                                <i class="fas fa-undo-alt mr-1"></i> Reset Filter
                            </button>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    @include('livewire.logs.filter-content')
                </div>
            </div>
        </div>

        <!-- Right Side: Log Data Table (Full width on Mobile, 8 cols on Desktop) -->
        <div class="col-lg-8 col-md-7 col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Riwayat Aktivitas</h4>
                </div>

                <div class="card-body p-0">
                    <!-- Controls: Show Entries & Search Bar (DataTables Style) -->
                    <div class="dt-control-wrapper">
                        <div class="dt-show-entries">
                            Show
                            <select wire:model.live="perPage" class="dt-select-entries">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            Entries
                        </div>

                        <div class="dt-search-wrapper">
                            <span>Search:</span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="dt-search-input" placeholder="Cari pesan, user, IP, aksi...">
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="table-responsive">
                        <table class="table table-clean">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-center text-nowrap" style="width: 40px;">NO</th>
                                    <th class="text-nowrap" style="width: 105px;">WAKTU</th>
                                    <th class="text-center text-nowrap" style="width: 80px;">LEVEL</th>
                                    <th class="text-nowrap" style="width: 125px;">MODUL & AKSI</th>
                                    <th class="text-nowrap">PESAN LOG</th>
                                    <th class="text-nowrap" style="width: 115px;">USER / IP</th>
                                    <th class="text-center text-nowrap" style="width: 50px;">DETAIL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $index => $log)
                                    <tr wire:key="log-{{ $log->id }}">
                                        <td class="text-center font-weight-600 text-muted align-middle">
                                            {{ $logs->firstItem() + $index }}
                                        </td>
                                        <td class="align-middle text-nowrap">
                                            <div class="log-date-text">
                                                {{ $log->created_at->format('d M Y') }}
                                            </div>
                                            <div class="log-time-text">
                                                <i class="far fa-clock mr-1"></i>{{ $log->created_at->format('H:i:s') }}
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-nowrap">
                                            <span class="{{ $log->level_badge_class }}">
                                                {{ strtoupper($log->level) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-nowrap">
                                            <span class="badge badge-light border mr-1">
                                                {{ strtoupper($log->module) }}
                                            </span>
                                            <span class="badge badge-secondary">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="log-message-text" title="{{ $log->message }}">
                                                {{ $log->message }}
                                            </div>
                                        </td>
                                        <td class="align-middle text-nowrap">
                                            @if ($log->user)
                                                <div class="log-user-title">
                                                    {{ $log->user->name }}
                                                </div>
                                            @else
                                                <div class="text-muted font-italic text-[12px]">
                                                    Sistem / Tamu
                                                </div>
                                            @endif
                                            <div class="log-ip-text">
                                                <i class="fas fa-network-wired mr-1"></i>{{ $log->ip_address ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-nowrap">
                                            <button type="button" wire:click="showDetail({{ $log->id }})" wire:loading.attr="disabled" wire:target="showDetail({{ $log->id }})" class="btn btn-primary btn-action" data-toggle="tooltip" title="Lihat Detail Context">
                                                <i wire:loading wire:target="showDetail({{ $log->id }})" class="fas fa-spinner fa-spin"></i>
                                                <i wire:loading.remove wire:target="showDetail({{ $log->id }})" class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-history text-muted mb-2" style="font-size: 36px; opacity: 0.4;"></i>
                                                <div class="font-weight-600 mt-2 text-dark">Tidak ada data log aktivitas</div>
                                                <small class="text-muted d-block mt-1">
                                                    @if ($search || $startDate || $endDate || $filterLevel || $filterModule)
                                                        Tidak ditemukan log aktivitas yang sesuai dengan kriteria filter.
                                                    @else
                                                        Belum ada aktivitas yang tercatat untuk hari ini.
                                                    @endif
                                                </small>
                                                @if ($search || $activePreset !== 'today' || $filterLevel || $filterModule)
                                                    <button type="button" wire:click="resetFilters" wire:loading.attr="disabled" wire:target="resetFilters" class="btn btn-primary btn-sm mt-3 px-3">
                                                        <i wire:loading wire:target="resetFilters" class="fas fa-spinner fa-spin mr-1"></i>
                                                        <i wire:loading.remove wire:target="resetFilters" class="fas fa-undo-alt mr-1"></i> Reset ke Hari Ini
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer: Info & Pagination -->
                    <div class="dt-footer-wrapper">
                        <div>
                            Showing {{ $logs->total() > 0 ? $logs->firstItem() : 0 }} to {{ $logs->total() > 0 ? $logs->lastItem() : 0 }} of {{ $logs->total() }} entries
                        </div>
                        <div>
                            {{ $logs->links('livewire.custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button (Filter Modal for Mobile) -->
    <button type="button" class="btn-floating-filter d-md-none" data-toggle="modal" data-target="#filterMobileModal" title="Buka Filter Log" aria-label="Filter Log">
        <i class="fas fa-filter"></i>
        @if ($search || $activePreset !== 'today' || $filterLevel || $filterModule)
            <span class="filter-badge-indicator"></span>
        @endif
    </button>

    <!-- Mobile Filter Modal (Terapkan Filter) -->
    @include('livewire.logs.filter-modal')

    <!-- Detail Context Modal -->
    @include('livewire.logs.detail-modal')
</div>
