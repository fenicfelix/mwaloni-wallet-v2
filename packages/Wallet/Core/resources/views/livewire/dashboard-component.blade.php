<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <small class="text-muted font-italic">Last Updated: {{ $transactionMetrics?->updated_at->diffForHumans()
                        }}</small>
                    <button class="btn btn-dark btn-sm" wire:click="refreshData"><i
                            class="uil-refresh"></i></button>
                </div>
                <div class="page-title-box">
                    <h4 class="mt-4 mb-0">Good day, {{ auth()->user()->first_name }} 👋</h4>
                    <p class="text-muted font-14">Here's what's happening on eWallet today.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- *************************** Top Analytics Data ************************** -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="uil-bill"></i>
                    <h6 class="text-uppercase mt-0">Total Spent</h6>
                    <h2 class="my-2"><span class="sub-text">KES</span> {{ number_format($transactionMetrics->total_spent ??
                        0) }}</h2>
                </div> <!-- end card-body-->
            </div>
        </div>
        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="uil-briefcase-alt"></i>
                    <h6 class="text-uppercase mt-0">Account Balance</h6>
                    <h2 class="my-2"><span class="sub-text">KES</span> {{ number_format($analytics['account_balance'] ??
                        0, 2) }}</h2>
                </div> <!-- end card-body-->
            </div>
        </div>

        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="uil-check"></i>
                    <h6 class="text-uppercase mt-0">Successful Transactions</h6>
                    <h2 class="my-2"><span class="sub-text">KES</span> {{ number_format($transactionMetrics->successful_transactions ??
                        0, 2) }}</h2>
                </div> <!-- end card-body-->
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-warning tilebox-one">
                <div class="card-body">
                    <i class="mdi mdi-spin mdi-loading text-white font-24"></i>
                    <h6 class="text-uppercase mt-0">Incomplete Transactions</h6>
                    <h2 class="my-2"><span class="sub-text">KES</span> {{ number_format($transactionMetrics->pending_transactions ??
                        0, 2) }}</h2>
                </div> <!-- end card-body-->
            </div>
        </div>
        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="uil-envelope-alt"></i>
                    <h6 class="text-uppercase mt-0">Total Messages</h6>
                    <h2 class="my-2"><span class="sub-text">KES</span> {{
                        number_format($analytics['total_messages'] ??
                        0, 2) }}</h2>
                </div> <!-- end card-body-->
            </div>
        </div>

        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="uil-bill"></i>
                    <h6 class="text-uppercase mt-0">Total SMS Cost</h6>
                    <h2 class="my-2"><span class="sub-text">KES</span> {{
                        number_format($transactionMetrics->total_sms_cost ??
                        0, 2) }}</h2>
                </div> <!-- end card-body-->
            </div>
        </div>

        <div class="col-md-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="uil-dollar-sign"></i>
                    <h6 class="text-uppercase mt-0">Total Revenue</h6>
                    <h2 class="my-2"><span class="sub-text">KES</span> {{
                        number_format($transactionMetrics->total_revenue ??
                        0, 2) }}</h2>
                </div> <!-- end card-body-->
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-success tilebox-one">
                <div class="card-body">
                    <i class="uil-money-withdraw"></i>
                    <h6 class="text-uppercase mt-0">Available Revenue</h6>
                    <h2 class="my-2"><span class="sub-text">KES</span> {{
                        number_format($transactionMetrics->available_revenue ??
                        0, 2) }}</h2>
                </div> <!-- end card-body-->
            </div>
        </div>
    </div>
    <!-- *************************** Top Analytics Data ************************** -->
    
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card p-4">
                <div class="pb-4">
                    <div class="d-flex mb-3">
                        <span class="text-md">{{ $graph_title }}</span>
                        <span class="flex"></span>
                    </div>
                </div>
                <div id="processedAmountChart"></div>
            </div>
        </div>
    </div>
</div>

@once
@push('js')
    <!-- chartjs plugin -->

    <script>
        window.dashboardChartData = @json($graph_data);

        console.log(window.dashboardChartData.dates);
        console.log(window.dashboardChartData.values);
    </script>

    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js?v=0.0.3') }}"></script>
@endpush    
@endonce
