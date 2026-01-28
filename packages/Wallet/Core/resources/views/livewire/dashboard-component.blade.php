<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title padding pb-0">
                <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}</h2></span>
                <span class="float-right">
                    <small class="text-muted font-italic">Last Updated: {{ $transactionMetrics?->updated_at->diffForHumans()
                        }}</small>
                        <button class="btn btn-dark btn-sm i-con-h-a" wire:click="refreshData"><i class="i-con i-con-refresh"></i></button>
                </span>
            </div>
        </div>
    </div>
        <!-- *************************** Top Analytics Data ************************** -->
    <div class="px-4 py-0 pt-4">
        <div class="row">
            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="card">
                    <div class="card-head">
                        <div class="col-12">
                            <p class="my-2"><small>Total Spent</small></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <sup class="text-muted">KSH</sup>
                            <span class="h3 font-weight-bold">{{ number_format($transactionMetrics->total_spent ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="card">
                    <div class="card-head">
                        <div class="col-12">
                            <p class="my-2"><small>Account Balance</small></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <sup class="text-muted">KSH</sup>
                            <span class="h3 font-weight-bold">{{ number_format($analytics['account_balance'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="card">
                    <div class="card-head">
                        <div class="col-12">
                            <p class="my-2"><small>Successful Transactions</small></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <span class="h3 font-weight-bold">{{ number_format($transactionMetrics->successful_transactions ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="card">
                    <div class="card-head">
                        <div class="col-12">
                            <p class="my-2"><small>Incomplete Transactions</small></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <span class="h3 font-weight-bold">{{ number_format($transactionMetrics->pending_transactions ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="card">
                    <div class="card-head">
                        <div class="col-12">
                            <p class="my-2"><small>Total Revenue</small></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <sup class="text-muted">KSH</sup>
                            <span class="h3 font-weight-bold">{{ number_format($transactionMetrics->total_revenue ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="card">
                    <div class="card-head">
                        <div class="col-12">
                            <p class="my-2"><small>Available Revenue</small></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <sup class="text-muted">KSH</sup>
                            <span class="h3 font-weight-bold">{{ number_format($transactionMetrics->available_revenue ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="card">
                    <div class="card-head">
                        <div class="col-12">
                            <p class="my-2"><small>Total Messages</small></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <span class="h3 font-weight-bold">{{ number_format($analytics['total_messages'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="card">
                    <div class="card-head">
                        <div class="col-12">
                            <p class="my-2"><small>Message Cost</small></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <sup class="text-muted">KSH</sup>
                            <span class="h3 font-weight-bold">{{ number_format($transactionMetrics->total_sms_cost ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- *************************** Top Analytics Data ************************** -->
    
    <div class="px-4 py-4">
         <div class="row">
            <div class="col-12 col-md-12">
               <div class="card p-4">
                  <div class="pb-4">
                     <div class="d-flex mb-3">
                        <span class="text-md">Daily transactions for the month of {{ date('M Y') }}</span>
                        <span class="flex"></span>
                     </div>
                  </div>
                  <div style="height: 300px">
                     <canvas data-plugin="chartjs" id="chart-line-1"></canvas>
                  </div>
               </div>
            </div>
         </div>
      </div>
</div>

@once
@push('js')
    <!-- chartjs plugin -->
    <script src="{{ asset('themes/agile/libs/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('themes/agile/js/plugins/jquery.chartjs.js') }}"></script>
    <script src="{{ asset('themes/agile/js/plugins/chartjs.js') }}"></script>

    <script>
    
    $(document).ready(function() {
        var graph_data = JSON.parse('<?=json_encode($graph_data);?>');

        var ctx = document.getElementById("chart-line-1");
        var barChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: graph_data["dates"],
                datasets: [{
                    label: 'Value in KSh. ',
                    data: graph_data["values"],
                    backgroundColor: 'rgba(43, 48, 53, 0.5)',
                    borderColor: 'rgba(43, 48, 53, 1)',
                    borderWidth: 1
                }],
            },
            options: {
                tooltips: {
                callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);
                            value = value.join(',');
                            return 'KSh. '+value;
                        }
                } // end callbacks:
                }, //end tooltips
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true,
                            userCallback: function(value, index, values) {
                                // Convert the number to a string and splite the string every 3 charaters from the end
                                value = value.toString();
                                value = value.split(/(?=(?:...)*$)/);
                                value = value.join(',');
                                return value;
                            }
                        }
                    }],
                    xAxes: [{
                        ticks: {
                        }
                    }],
                }
            }
        });
    });
    </script>
@endpush    
@endonce
