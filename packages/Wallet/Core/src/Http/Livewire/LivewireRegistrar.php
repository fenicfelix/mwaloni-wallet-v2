<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire;

use Livewire\Livewire;
use Wallet\Core\Http\Livewire\Components\AccountsComponent;
use Wallet\Core\Http\Livewire\Components\AccountTypesComponent;
use Wallet\Core\Http\Livewire\Components\ClientsComponent;
use Wallet\Core\Http\Livewire\Components\DashboardComponent;
use Wallet\Core\Http\Livewire\Components\MessagesComponent;
use Wallet\Core\Http\Livewire\Components\PreferencesComponent;
use Wallet\Core\Http\Livewire\Components\ProfileComponent;
use Wallet\Core\Http\Livewire\Components\RolesComponent;
use Wallet\Core\Http\Livewire\Components\ServicesComponent;
use Wallet\Core\Http\Livewire\Components\StatusesComponent;
use Wallet\Core\Http\Livewire\Components\TransactionChargesComponent;
use Wallet\Core\Http\Livewire\Components\TransactionsComponent;
use Wallet\Core\Http\Livewire\Components\TransactionTypesComponent;
use Wallet\Core\Http\Livewire\Components\UsersComponent;

final class LivewireRegistrar
{
    public static function register(): void
    {
        Livewire::component('core.dashboard-component', DashboardComponent::class);
        Livewire::component('core.profile-component', ProfileComponent::class);
        Livewire::component('core.transactions-component', TransactionsComponent::class);
        Livewire::component('core.accounts-component', AccountsComponent::class);
        Livewire::component('core.services-component', ServicesComponent::class);
        Livewire::component('core.clients-component', ClientsComponent::class);
        Livewire::component('core.messages-component', MessagesComponent::class);
        Livewire::component('core.preferences-component', PreferencesComponent::class);
        Livewire::component('core.roles-component', RolesComponent::class);
        Livewire::component('core.transaction-charges-component', TransactionChargesComponent::class);
        Livewire::component('core.transaction-types-component', TransactionTypesComponent::class);
        Livewire::component('core.users-component', UsersComponent::class);
        Livewire::component('core.statuses-component', StatusesComponent::class);
        Livewire::component('core.account-types-component', AccountTypesComponent::class);

    }
}
