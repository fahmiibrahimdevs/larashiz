<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->email)->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            app(ActivityLogService::class)->warning(
                action: 'LOGIN_FAILED',
                module: 'auth',
                message: 'User login failed',
                context: [
                    'email' => $this->email,
                    'reason' => 'invalid_credentials',
                ]
            );

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        if (! $user->is_active) {
            RateLimiter::hit($this->throttleKey());

            app(ActivityLogService::class)->warning(
                action: 'LOGIN_BLOCKED',
                module: 'auth',
                message: 'User login blocked',
                context: [
                    'user_id' => $user->id,
                    'email' => $this->email,
                    'reason' => 'account_inactive',
                ],
                userId: $user->id
            );

            throw ValidationException::withMessages([
                'form.email' => 'Akun Anda berstatus nonaktif. Silakan hubungi admin untuk mengaktifkan akun Anda.',
            ]);
        }

        Auth::login($user, $this->remember);

        app(ActivityLogService::class)->info(
            action: 'LOGIN',
            module: 'auth',
            message: 'User logged in',
            context: [
                'user_id' => $user->id,
                'email' => $user->email,
            ],
            userId: $user->id
        );

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        app(ActivityLogService::class)->warning(
            action: 'RATE_LIMIT_EXCEEDED',
            module: 'auth',
            message: 'User login rate limited',
            context: [
                'email' => $this->email,
                'available_in_seconds' => $seconds,
            ]
        );

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
