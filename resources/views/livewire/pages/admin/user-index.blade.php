<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $userId): void
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'Akses ditolak.');
        }

        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
            return;
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Akun {$user->name} ({$user->email}) berhasil {$statusText}.");
    }

    public function with(): array
    {
        $users = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return [
            'users' => $users,
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <h1>Kelola Pengguna</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Kelola Pengguna</div>
        </div>
    </x-slot>

    <!-- Flash Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible show fade">
            <div class="alert-body">
                <button class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible show fade">
            <div class="alert-body">
                <button class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4>Daftar Semua Pengguna</h4>
            <div class="card-header-form">
                <div class="input-group">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama atau email...">
                    <div class="input-group-btn">
                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-md">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status Akun</th>
                            <th>Tanggal Daftar</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                            <tr>
                                <td>{{ $users->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if ($user->id === auth()->id())
                                        <span class="badge badge-info ml-1">Akun Anda</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @forelse ($user->roles as $role)
                                        <span class="badge {{ $role->name === 'admin' ? 'badge-primary' : 'badge-secondary' }}">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}</td>
                                <td class="text-right">
                                    @if ($user->id === auth()->id())
                                        <span class="text-muted font-italic small">Tidak dapat diubah</span>
                                    @else
                                        <button
                                            wire:click="toggleStatus({{ $user->id }})"
                                            wire:loading.attr="disabled"
                                            class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}"
                                        >
                                            @if ($user->is_active)
                                                <i class="fas fa-user-times mr-1"></i> Nonaktifkan
                                            @else
                                                <i class="fas fa-user-check mr-1"></i> Aktifkan Akun
                                            @endif
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Tidak ada data pengguna yang sesuai dengan pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-right">
            <nav class="d-inline-block">
                {{ $users->links() }}
            </nav>
        </div>
    </div>
</div>
