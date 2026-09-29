<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
    <div class="login-brand">
        <img src="{{ asset('assets/img/larashiz-logo.jpg') }}" alt="Larashiz" width="100" height="100" class="shadow-light rounded-circle">
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h4>Verifikasi Email</h4>
        </div>

        <div class="card-body">
            <p class="text-muted">
                Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                        Link verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <button wire:click="sendVerification" class="btn btn-primary btn-lg btn-block mb-3"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Kirim Ulang Email Verifikasi</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Mengirim...</span>
                </button>

                <button wire:click="logout" type="button" class="btn btn-outline-secondary btn-block">
                    Logout
                </button>
            </div>
        </div>
    </div>

    <div class="simple-footer">
        Copyright &copy; {{ config('app.name', 'Larashiz') }} {{ date('Y') }}
    </div>
</div>
