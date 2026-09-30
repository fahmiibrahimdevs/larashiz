<?php

use App\Livewire\Logs\Index as LogIndex;
use App\Livewire\Posts\Index;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('posts', Index::class)
    ->middleware(['auth'])
    ->name('posts.index');

Route::get('logs', LogIndex::class)
    ->middleware(['auth'])
    ->name('logs.index');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Volt::route('admin/users', 'pages.admin.user-index')
        ->name('admin.users');
});

Route::get('shizuefi/{page?}', function (string $page = 'index') {
    if (! view()->exists("shizuefi.{$page}")) {
        abort(404);
    }

    return view("shizuefi.{$page}");
})->name('shizuefi.page');

require __DIR__.'/auth.php';
