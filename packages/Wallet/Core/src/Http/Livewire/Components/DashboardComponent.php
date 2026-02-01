<?php

namespace Wallet\Core\Http\Livewire\Components;

use Wallet\Core\Models\Outbox;
use Wallet\Core\Models\TransactionMetric;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Jobs\PopulateTransactionMetricTable;

class DashboardComponent extends Component
{
    use NotifyBrowser;

    public string $content_title = "";

    public string $graph_title = "";

    public ?TransactionMetric $transactionMetrics;

    public ?array $analytics = [];

    public ?array $graph_data = [];

    public function mount()
    {
        $this->content_title = "Home";

        $this->transactionMetrics = TransactionMetric::first();

        $this->analytics = [
            'account_balance' => DB::table('accounts')->sum(DB::raw('working_balance + utility_balance - withheld_amount')),
            'total_messages' => Outbox::count('id')
        ];

        $this->graph_data = $this->fetchGraphData();
    }

    public function hydrate()
    {
        $this->transactionMetrics = TransactionMetric::first();
    }

    private function fetchGraphData()
    {
        // $start_date = date("Y-m-01");
        $end_date = date("Y-m-d");
        $start_date = date("Y-m-d", strtotime("-30 days", strtotime($end_date)));
        $days_diff = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
        $this->graph_title = "Transactions in the last " . $days_diff . " days";
        $sql = "select (SUM(a.disbursed_amount)+SUM(a.system_charges)+SUM(a.sms_charges)) AS total, DATE(a.transaction_date) as date
        FROM transactions a
        where date(a.transaction_date) between '" . $start_date . "' and '" . $end_date . "'
        GROUP BY DATE(a.transaction_date)";
        info($sql);
        $data = DB::select($sql);

        $dates = [];
        $values = [];
        for ($i = 1; $i <= $days_diff; $i++) {
            $date = date("Y-m-d", strtotime("+ $i days", strtotime($start_date)));
            $total = 0;
            if ($data) {
                foreach ($data as $d) {
                    if ($date == $d->date) {
                        $total = $d->total;
                    }
                }
            }
            array_push($values, $total);
            array_push($dates, $date);
        }

        $result = [
            "dates" => $dates,
            "values" => $values
        ];

        return $result;
    }

    public function refreshData()
    {
        PopulateTransactionMetricTable::dispatch()->onQueue('default');
        $this->notify("Data refresh has been initiated.", "success");
    }

    public function render()
    {
        return view('core::livewire.dashboard-component')
            ->layout('core::layouts.app');
    }
}
