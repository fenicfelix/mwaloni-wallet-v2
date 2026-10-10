<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">{{ $content_title }}</li>
                </ol>
            </div>
            <h4 class="page-title">{{ $content_title }}</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<!-- *************************** Top Analytics Data ************************** -->
<div class="row">

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body text-bg-success text-center">
                <i class="uil-usd-circle font-24"></i>
                <h3><span>{{ number_format($summaries['working_balance'] ?? 0) }}</span></h3>
                <p class="text-white font-15 mb-0">Working Balance</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-bg-success">
            <div class="card-body text-center">
                <i class="uil-usd-circle text-white font-24"></i>
                <h3><span>{{ number_format($summaries['utility_balance'] ?? 0) }}</span></h3>
                <p class="text-white font-15 mb-0">Utility Balance</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="uil-briefcase-alt text-white font-24"></i>
                <h3><span>{{ number_format($summaries['withheld_amount'] ?? 0) }}</span>
                </h3>
                <p class="text-white font-15 mb-0">Withheld Amount</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="uil-gold text-white font-24"></i>
                <h3><span class="sub-text">KES</span> <span>{{ number_format($summaries['reserved_amount'] ?? 0) }}</span></h3>
                <p class="text-white font-15 mb-0">Reserved Amount</p>
            </div>
        </div>
    </div>
</div>
<!-- *************************** Top Analytics Data ************************** -->


<div class="mt-4 padding">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-12">
                    <h4 class="header-title">List of Accounts</h4>
                    <div class="float-end ms-2">
                        <a href="#" wire:click.prevent="addFunction" class="btn btn-dark btn-sm">
                            <i class="uil-plus"></i> Add New Account
                        </a>
                    </div>
                    <p class="text-muted font-14 d-none d-md-block">
                        Here you can manage all accounts. You can add, edit, delete and view accounts.
                        <br>
                    </p>
                    <div class="clearfix"></div>
                    <div class="table-responsive">
                        @livewire('core-datatables.accounts-table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>