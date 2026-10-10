<!-- ========== Horizontal Menu Start ========== -->
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    @can('dashboard.access')
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none" href="{{ route('dashboard') }}" role="button">
                            <i class="uil-home-alt"></i>Dashboard
                        </a>
                    </li>
                    @endcan
                    @can('transactions.access')
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none {{ request()->is('*transactions*') && !request()->is('*wallet-transactions*') ? 'active' : '' }}"
                            href="{{ route('transactions') }}" role="button">
                            <i class="uil-money-withdrawal"></i>Transactions
                        </a>
                    </li>
                    @endcan
                    @can('orders.access')
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none {{ request()->is('*orders*') ? 'active' : '' }}"
                            href="{{ route('orders') }}" role="button">
                            <i class="mdi mdi-shopping-outline"></i>Orders
                        </a>
                    </li>
                    @endcan
                    @can('invoices.access')
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none {{ request()->is('*invoices*') && !request()->is('*invoices*') ? 'active' : '' }}"
                            href="{{ route('invoices') }}" role="button">
                            <i class="uil-invoice"></i>Invoices
                        </a>
                    </li>
                    @endcan
                    @can('customers.access')
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none {{ request()->is('*customer*') ? 'active' : '' }}"
                            href="{{ route('customers') }}" role="button">
                            <i class="uil-user-check"></i>Customers
                        </a>
                    </li>
                    @endcan
                    @can('accounts.access')
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none" href="{{ route('accounts') }}" role="button">
                            <i class="uil-layers"></i>Accounts
                        </a>
                    </li>
                    @endcan
                    @can('reports.access')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-reports" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="uil-chart-line"></i>Reports <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-reports">
                            @can('wallet-transactions.access')
                            <a href="{{ route('wallet-reports.access') }}"
                                class="dropdown-item {{ request()->is('*wallet-reports*') ? 'active' : '' }}">Wallet
                                Reports</a>
                            @endcan
                            @can('wallet-transactions.access')
                            <a href="{{ route('wallet-transactions.access') }}"
                                class="dropdown-item {{ request()->is('*wallet-transactions*') ? 'active' : '' }}">Wallet
                                Transactions</a>
                            @endcan
                            @can('wallet-transactions.access')
                            <a href="{{ route('wallet-balances.access') }}"
                                class="dropdown-item {{ request()->is('*wallet-balances*') ? 'active' : '' }}">Wallet
                                Balances</a>
                            @endcan
                            @can('wallet-transactions.access')
                            <a href="{{ route('daily-balances.access') }}"
                                class="dropdown-item {{ request()->is('*daily-balances*') ? 'active' : '' }}">Daily
                                Balances</a>
                            @endcan
                            @can('reports.audit-trail')
                            <a href="{{ route('reports.auditTrail') }}"
                                class="dropdown-item {{ request()->is('*audit-trail*') ? 'active' : '' }}">Audit
                                Trail</a>
                            @endcan
                            @can('reports.transactions')
                            <a href="{{ route('reports.transactions') }}"
                                class="dropdown-item {{ request()->is('*transactions*') ? 'active' : '' }}">
                                Transactions
                            </a>
                            @endcan
                        </div>
                    </li>
                    @endcan
                    @can('reports.access')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-reports" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="uil-lock"></i>Admin <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-reports">
                            @can('users.access')
                            <a href="{{ route('system-users') }}"
                                class="dropdown-item {{ request()->is('*system-users*') ? 'active' : '' }}">System
                                Users</a>
                            @endcan
                            {{-- <div class="dropdown-divider"></div> --}}
                            @can('roles.access')
                            <a href="{{ route('settings.roles') }}"
                                class="dropdown-item {{ request()->is('*roles*') ? 'active' : '' }}">Roles</a>
                            @endcan
                            @can('permissions.access')
                            <a href="{{ route('settings.permissions') }}"
                                class="dropdown-item {{ request()->is('*permissions*') ? 'active' : '' }}">Permissions</a>
                            @endcan
                            @can('core_configs.access')
                            <a href="{{ route('settings.core-configs') }}"
                                class="dropdown-item {{ request()->is('*core-configs*') ? 'active' : '' }}">Core
                                Configs</a>
                            @endcan
                            @can('transaction_charges.access')
                            <a href="{{ route('settings.transaction-charges') }}"
                                class="dropdown-item {{ request()->is('*transaction-charges*') ? 'active' : '' }}">Transaction
                                Charges</a>
                            @endcan
                        </div>
                    </li>
                    @endcan
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- ========== Horizontal Menu End ========== -->