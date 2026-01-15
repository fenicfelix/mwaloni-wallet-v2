<div class="col-xsm-12 col-sm-6 col-md-3">
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
        <a class="nav-link {{ request()->is('*roles*') ? 'active' : ''}}" href="{{ route('roles') }}">Roles</a>
        <a class="nav-link {{ request()->is('*permissions*') ? 'active' : ''}}" href="{{ route('technical.permissions') }}">Permissions</a>
        <a class="nav-link {{ request()->is('*account-types*') ? 'active' : ''}}" href="{{ route('technical.account_types') }}">Account Types</a>
        <a class="nav-link {{ request()->is('*statuses*') ? 'active' : ''}}" href="{{ route('technical.statuses') }}">Status</a>
        <a class="nav-link {{ request()->is('*transaction-types*') ? 'active' : ''}}" href="{{ route('technical.transaction_types') }}">Transaction Types</a>
        <a class="nav-link {{ request()->is('*transaction-charges*') ? 'active' : ''}}" href="{{ route('technical.transaction_charges') }}">Transaction Charges</a>
        <a class="nav-link {{ request()->is('*preferences*') ? 'active' : ''}}" href="{{ route('technical.preferences') }}">Preferences</a>
    </div>
</div>