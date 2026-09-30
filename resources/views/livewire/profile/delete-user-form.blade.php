<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';
    public bool $confirmingUserDeletion = false;

    /**
     * Start the deletion confirmation flow.
     */
    public function confirmDeletion(): void
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->confirmingUserDeletion = true;
    }

    /**
     * Cancel the deletion confirmation flow.
     */
    public function cancelDeletion(): void
    {
        $this->confirmingUserDeletion = false;
        $this->password = '';
    }

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    @if (! $confirmingUserDeletion)
        <div>
            <p class="text-muted text-[13px] leading-relaxed mb-3">
                Setelah akun Anda dihapus, semua data profil, riwayat aktivitas, dan data terkait akan dihapus secara <strong>permanen</strong> dan tidak dapat dipulihkan.
            </p>
            <button wire:click="confirmDeletion" type="button" wire:loading.attr="disabled" wire:target="confirmDeletion" class="btn btn-outline-danger btn-block font-weight-600 shadow-sm">
                <i wire:loading wire:target="confirmDeletion" class="fas fa-spinner fa-spin mr-1.5"></i>
                <i wire:loading.remove wire:target="confirmDeletion" class="fas fa-trash-alt mr-1.5"></i> Hapus Akun Saya
            </button>
        </div>
    @else
        <div class="profile-danger-box">
            <div class="profile-danger-box-title">
                <i class="fas fa-exclamation-triangle mr-2"></i> Konfirmasi Hapus Akun
            </div>
            <p class="profile-danger-box-desc">
                Silakan masukkan kata sandi Anda saat ini untuk mengonfirmasi bahwa Anda benar-benar ingin menghapus akun ini secara permanen.
            </p>

            <form wire:submit="deleteUser">
                <div class="form-group mb-3">
                    <label for="delete_password" class="text-dark font-weight-600 text-[12px] mb-1">
                        Kata Sandi Saat Ini <span class="text-danger">*</span>
                    </label>
                    <input wire:model="password" type="password" id="delete_password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi Anda..." required autofocus>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-between gap-2 pt-1">
                    <button wire:click="cancelDeletion" type="button" class="btn btn-secondary btn-sm px-3">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm px-3 shadow-sm" wire:loading.attr="disabled" wire:target="deleteUser">
                        <span wire:loading.remove wire:target="deleteUser">
                            <i class="fas fa-trash mr-1"></i> Ya, Hapus Akun
                        </span>
                        <span wire:loading wire:target="deleteUser">
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                            Menghapus...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
