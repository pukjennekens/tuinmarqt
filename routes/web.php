<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/image/{id}', [ExportController::class, 'troubleFreeImage'])
    ->name('image');

require __DIR__.'/auth.php';
