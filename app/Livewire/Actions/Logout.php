<?php

namespace App\Livewire\Actions;

use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
        $userId = Auth::id();

        if ($userId) {
            app(ActivityLogService::class)->info(
                action: 'LOGOUT',
                module: 'auth',
                message: 'User logged out',
                context: [
                    'user_id' => $userId,
                ],
                userId: $userId
            );
        }

        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}
