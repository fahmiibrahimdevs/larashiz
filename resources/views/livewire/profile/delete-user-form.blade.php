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
        <button wire:click="confirmDeletion" type="button" class="btn btn-danger btn-block">
            <i class="fas fa-trash-alt mr-1"></i> Hapus Akun Saya
        </button>
    @else
        <div class="alert alert-danger mb-3 p-3">
            <h6 class="text-danger font-weight-bold"><i class="fas fa-exclamation-circle mr-1"></i> Konfirmasi Penghapusan</h6>
            <p class="text-small mb-3">
                Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda benar-benar ingin menghapus akun ini secara permanen.
            </p>

            <form wire:submit="deleteUser">
                <div class="form-group mb-2">
                    <input wire:model="password" type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Masukkan Kata Sandi Anda" required autofocus>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button wire:click="cancelDeletion" type="button" class="btn btn-secondary btn-sm">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="fas fa-trash mr-1"></i> Ya, Hapus Akun</span>
                        <span wire:loading><i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
