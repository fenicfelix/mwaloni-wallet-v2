<!-- ========== Horizontal Menu Start ========== -->
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none" href="{{ route('dashboard') }}" role="button">
                            <i class="uil-home-alt"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none" href="{{ route('transactions') }}" role="button">
                            <i class="uil-money-withdrawal"></i>Transactions
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none" href="{{ route('accounts') }}" role="button">
                            <i class="uil-layer-group"></i>Accounts
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none" href="{{ route('services') }}" role="button">
                            <i class="uil-exchange-alt"></i>Services
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none" href="{{ route('messages') }}" role="button">
                            <i class="uil-envelope-alt"></i>Messages
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none" href="{{ route('clients') }}" role="button">
                            <i class="uil-money-withdrawal"></i>Clients
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link arrow-none" href="{{ route('users') }}" role="button">
                            <i class="uil-users-alt"></i>System Users
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-reports" role="button" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="uil-lock"></i>Technical <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-reports">
                            <a href="{{ route('technical.roles') }}" class="dropdown-item {{ request()->is('*roles*') ? 'active' : '' }}">
                                Roles
                            </a>
                            <a href="{{ route('technical.account_types') }}" class="dropdown-item {{ request()->is('*account-types*') ? 'active' : '' }}">
                                Account Types
                            </a>
                            <a href="{{ route('technical.transaction_charges') }}" class="dropdown-item {{ request()->is('*transaction-charges*') ? 'active' : '' }}">
                                Transaction Charges
                            </a>
                            <a href="{{ route('technical.preferences') }}" class="dropdown-item {{ request()->is('*preferences*') ? 'active' : '' }}">
                                Preferences
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- ========== Horizontal Menu End ========== -->