<div class="row">
    <div class="col-12">
        <div class="page-title padding pb-0 ">
            <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}</h2></span>
        </div>
    </div>
</div>

<!-- *************************** Top Analytics Data ************************** -->
<div class="px-4 py-0 pt-4">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="d-flex align-items-center i-con-h-a my-1">
                <div>
                    <span class="avatar w-40 b-a b-2x"><i class="i-con i-con-circle b-2x text-dark"><i></i></i></span>
                </div>
                <div class="mx-3">
                    <span class="analytics-value">{{ number_format($analytics->total_transactions ?? 0) }}</span> <br>
                    <small class="text-muted">Total Transactions</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="d-flex align-items-center i-con-h-a my-1">
                <div>
                    <span class="avatar w-40 b-a b-2x"><i class="i-con i-con-circle b-2x text-dark"><i></i></i></span>
                </div>
                <div class="mx-3">
                    <span class="analytics-value">{{ number_format($analytics->pending_transactions ?? 0) }}</span> <br>
                    <small class="text-muted">Pending Transactions</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="d-flex align-items-center i-con-h-a my-1">
                <div>
                    <span class="avatar w-40 b-a b-2x"><i class="i-con i-con-circle b-2x text-dark"><i></i></i></span>
                </div>
                <div class="mx-3">
                    <span class="analytics-value">{{ number_format($analytics->failed_transactions ?? 0) }}</span> <br>
                    <small class="text-muted">Failed Transactions</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="d-flex align-items-center i-con-h-a my-1">
                <div>
                    <span class="avatar w-40 b-a b-2x"><i class="i-con i-con-circle b-2x text-dark"><i></i></i></span>
                </div>
                <div class="mx-3">
                    <sup class="text-muted">KSH</sup>
                    <span class="analytics-value">{{ number_format($analytics->available_revenue ?? 0, 2) }}</span> <br>
                    <small class="text-muted">Available Revenue</small>
                </div>
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
                    <div class="">
                        <div class="">
                            <div class="table-responsive">
                                @livewire('core-datatables.transactions-table')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>