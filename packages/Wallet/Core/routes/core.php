<?php

use Illuminate\Support\Facades\Route;
use Wallet\Core\Http\Livewire\Components\AccountsComponent;
use Wallet\Core\Http\Livewire\Components\AccountTypesComponent;
use Wallet\Core\Http\Livewire\Components\ClientsComponent;
use Wallet\Core\Http\Livewire\Components\DashboardComponent;
use Wallet\Core\Http\Livewire\Components\MessagesComponent;
use Wallet\Core\Http\Livewire\Components\PreferencesComponent;
use Wallet\Core\Http\Livewire\Components\ProfileComponent;
use Wallet\Core\Http\Livewire\Components\RolesComponent;
use Wallet\Core\Http\Livewire\Components\ServicesComponent;
use Wallet\Core\Http\Livewire\Components\TransactionChargesComponent;
use Wallet\Core\Http\Livewire\Components\TransactionsComponent;
use Wallet\Core\Http\Livewire\Components\UsersComponent;

Route::middleware(['auth', 'web'])->group(function () {
    // Route::get('/dashboard', DashboardComponent::class)->name('dashboard');
    Route::get('/', DashboardComponent::class)->name("dashboard");
    Route::redirect('/dashboard', '/');

    Route::get('users', UsersComponent::class)->name('users');
    Route::get('clients', ClientsComponent::class)->name('clients');
    Route::get('messages', MessagesComponent::class)->name('messages');
    Route::get('services', ServicesComponent::class)->name("services");
    Route::get('accounts', AccountsComponent::class)->name("accounts");
    Route::get('transactions', TransactionsComponent::class)->name("transactions");

    // Route::middleware(['role_or_permission:user-create'])->get('user/reset-password/{id}', [UsersController::class, 'reset_password'])->name("reset_password");

    Route::prefix('technical')->as('technical.')->group(function () {
        Route::get('roles', RolesComponent::class)->name('roles');
        Route::get('account-types', AccountTypesComponent::class)->name('account_types');
        Route::get('transaction-charges', TransactionChargesComponent::class)->name("transaction_charges");
        Route::get('preferences', PreferencesComponent::class)->name('preferences');
    });

    // my-profile
    Route::get('/profile', ProfileComponent::class)->name('my-profile');
});

require __DIR__ . '/auth.php';

require __DIR__ . '/callbacks.php';
