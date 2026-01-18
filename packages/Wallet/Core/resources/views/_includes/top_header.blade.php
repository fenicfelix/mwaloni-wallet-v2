<header id="header" class="page-header bg-white box-shadow animate fadeInDown">
  <div class="navbar navbar-expand-lg" >
    <!-- brand -->
    <a href="#" class="navbar-brand">
    	<!-- brand -->
        <a href="#" class="navbar-brand">
            <img src="{{ asset('themes/agile/img/mwaloni_logo.png') }}" alt="{{ config('app_name') }} Logo">
        </a>
        <!-- / brand -->
    </a>
    <!-- / brand -->
  
    <!-- Navbar collapse -->
    <div class="collapse navbar-collapse order-2 order-lg-1 justify-content-center" id="navbarToggler">
  	  <ul class="navbar-nav" data-nav>
        <li class="nav-item {{ request()->is('/') ? 'active' : ''}}">
            <a href="{{ route('dashboard') }}" class="nav-link"><span class="nav-text">Home</span></a>
        </li>
        <li class="nav-item {{ request()->is('*transactions*') ? 'active' : ''}}">
            <a href="{{ route('transactions') }}" class="nav-link"><span class="nav-text">Transactions</span></a>
        </li>
        <li class="nav-item {{ request()->is('*accounts*') ? 'active' : ''}}">
            <a href="{{ route('accounts') }}" class="nav-link"><span class="nav-text">Accounts</span></a>
        </li>
        <li class="nav-item {{ request()->is('*services*') ? 'active' : ''}}">
            <a href="{{ route('services') }}" class="nav-link"><span class="nav-text">Services</span></a>
        </li>
        <li class="nav-item {{ request()->is('*messages*') ? 'active' : ''}}">
            <a href="{{ route('messages') }}" class="nav-link"><span class="nav-text">Messages</span></a>
        </li>
        <li class="nav-item {{ request()->is('*clients*') ? 'active' : ''}}">
            <a href="{{ route('clients') }}" class="nav-link"><span class="nav-text">Clients</span></a>
        </li>
        <li class="nav-item {{ request()->is('*users*') ? 'active' : ''}}">
            <a href="{{ route('users') }}" class="nav-link"><span class="nav-text">System Users</span></a>
        </li>
        <li class="nav-item {{ request()->is('*technical*') ? 'active' : ''}}">
            <a href="{{ route('technical.roles') }}" class="nav-link"><span class="nav-text">Technical</span></a>
        </li>
  	  </ul>
    </div>
  
    <ul class="nav navbar-menu order-1 order-lg-2">
        <!-- User dropdown menu -->
        <li class="nav-item dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link d-flex align-items-center py-0 px-lg-0 px-2 text-color">
                <span class=" mx-2 d-custom-none l-h-1x d-lg-block text-right">
                    <small class='text-fade d-block mb-1'>Hello, Welcome</small>
                    <span>{{ auth()->user()->first_name." ".auth()->user()->last_name }}</span>
                </span>
                <span class="avatar w-36">
                    <i class="mr-2 i-con i-con-user"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right w pt-0 mt-3 animate fadeIn">
                <a class="dropdown-item" href="{{ route('my-profile') }}"><span> <i class="mr-2 i-con i-con-user"></i> Profile</span></a>
                <a id="action-logout" class="dropdown-item" href="#"> <i class="mr-2 i-con i-con-close"></i> Sign out</a>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                </form>
            </div>
        </li>
        <!-- Navarbar toggle btn -->
        <li class="nav-item d-lg-none">
            <a href="#" class="nav-link i-con-h-a px-1" data-toggle="collapse" data-toggle-class data-target="#navbarToggler">
            <i class="i-con i-con-nav text-muted"><i></i></i>
            </a>
        </li>
    </ul>
  
  </div>
</header>