<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'role:admin'])->group(function () {
    \Livewire\Volt\Volt::route('admin/users', 'pages.admin.user-index')
        ->name('admin.users');
});

Route::get('shizuefi/{page?}', function (string $page = 'index') {
    if (! view()->exists("shizuefi.{$page}")) {
        abort(404);
    }
    return view("shizuefi.{$page}");
})->name('shizuefi.page');

require __DIR__.'/auth.php';
