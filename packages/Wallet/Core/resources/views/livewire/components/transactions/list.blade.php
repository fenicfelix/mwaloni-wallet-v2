<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
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
            <div class="card-body text-center">
                <i class="uil-money-withdrawal font-24"></i>
                <h3><span>{{ number_format($analytics->total_transactions ?? 0) }}</span></h3>
                <p class="text-muted font-15 mb-0">Total Transactions</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-bg-warning">
            <div class="card-body text-center">
                <i class="mdi mdi-spin mdi-loading text-white font-24"></i>
                <h3><span>{{ number_format($analytics->pending_transactions ?? 0) }}</span></h3>
                <p class="text-white font-15 mb-0">Pending Transactions</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-bg-danger">
            <div class="card-body text-center">
                <i class="uil-times text-white font-24"></i>
                <h3><span>{{ number_format($analytics->failed_transactions ?? 0) }}</span>
                </h3>
                <p class="text-white font-15 mb-0">Failed Transactions</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card text-bg-success">
            <div class="card-body text-center">
                <i class="uil-check text-white font-24"></i>
                <h3><span class="sub-text">KES</span> <span>{{ number_format($analytics->available_revenue ?? 0) }}</span></h3>
                <p class="text-white font-15 mb-0">Available Revenue</p>
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
                    <h4 class="header-title">List of Transactions</h4>
                    <p class="text-muted font-14 d-none d-md-block">
                        Here you can manage all transactions. You can add, edit, delete and view transactions.
                        <br>
                    </p>
                    <div class="clearfix"></div>
                    <div class="table-responsive">
                        @livewire('core-datatables.transactions-table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>