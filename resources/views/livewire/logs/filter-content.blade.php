<!-- 1. Periode Waktu -->
<div class="filter-section">
    <div class="filter-section-header">
        <span class="filter-section-title">Periode Waktu</span>
        <span class="filter-section-subtitle">Pilih Cepat</span>
    </div>

    <!-- Presets Chips -->
    <div class="filter-chip-list">
        <button type="button" wire:click="setPreset('today')" class="filter-chip-item {{ $activePreset === 'today' ? 'active' : '' }}">Hari Ini</button>
        <button type="button" wire:click="setPreset('yesterday')" class="filter-chip-item {{ $activePreset === 'yesterday' ? 'active' : '' }}">Kemarin</button>
        <button type="button" wire:click="setPreset('7days')" class="filter-chip-item {{ $activePreset === '7days' ? 'active' : '' }}">7 Hari Terakhir</button>
        <button type="button" wire:click="setPreset('this_month')" class="filter-chip-item {{ $activePreset === 'this_month' ? 'active' : '' }}">Bulan Ini</button>
        <button type="button" wire:click="setPreset('all')" class="filter-chip-item {{ $activePreset === 'all' ? 'active' : '' }}">Semua</button>
    </div>

    <!-- Custom Date Inputs (Clean Side-by-Side) -->
    <div class="filter-date-grid">
        <div class="filter-date-group">
            <label class="filter-date-label">Dari Tanggal</label>
            <input type="date" wire:model.live="startDate" class="filter-date-control">
        </div>
        <div class="filter-date-group">
            <label class="filter-date-label">Sampai Tanggal</label>
            <input type="date" wire:model.live="endDate" class="filter-date-control">
        </div>
    </div>
</div>

<div class="filter-separator"></div>

<!-- 2. Tingkat (Level) -->
<div class="filter-section">
    <div class="filter-section-header">
        <span class="filter-section-title">Tingkat (Level)</span>
    </div>
    <div class="filter-chip-list">
        <button type="button" wire:click="setLevel('')" class="filter-chip-item {{ empty($filterLevel) ? 'active' : '' }}">Semua</button>
        <button type="button" wire:click="setLevel('info')" class="filter-chip-item {{ $filterLevel === 'info' ? 'active-info' : '' }}"><span class="filter-dot bg-info"></span> Info</button>
        <button type="button" wire:click="setLevel('warning')" class="filter-chip-item {{ $filterLevel === 'warning' ? 'active-warning' : '' }}"><span class="filter-dot bg-warning"></span> Warning</button>
        <button type="button" wire:click="setLevel('error')" class="filter-chip-item {{ $filterLevel === 'error' ? 'active-danger' : '' }}"><span class="filter-dot bg-danger"></span> Error</button>
    </div>
</div>

<div class="filter-separator"></div>

<!-- 3. Modul Sistem -->
<div class="filter-section">
    <div class="filter-section-header">
        <span class="filter-section-title">Modul Sistem</span>
    </div>
    <div class="filter-chip-list">
        <button type="button" wire:click="setModule('')" class="filter-chip-item {{ empty($filterModule) ? 'active' : '' }}">Semua Modul</button>
        @foreach ($modules as $module)
            <button type="button" wire:click="setModule('{{ $module }}')" class="filter-chip-item {{ $filterModule === $module ? 'active' : '' }}">{{ ucfirst($module) }}</button>
        @endforeach
    </div>
</div>
