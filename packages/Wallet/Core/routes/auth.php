<?php

use Illuminate\Support\Facades\Route;
use Wallet\Core\Auth\Controllers\ForgotPasswordController;
use Wallet\Core\Auth\Controllers\LoginController;

Route::middleware(['guest', 'web'])->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');

    Route::post('/login', [LoginController::class, 'store']);

    // forgot-password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('forgot-password');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('forgot-password.email');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

