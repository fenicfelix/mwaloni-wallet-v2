<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Wallet\Core\Models\Service;
use Wallet\Core\Models\Status;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;

class TransactionsTable extends DataTableComponent
{
    protected $model = Transaction::class;
    protected $user;
    protected $counter = 1;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'DESC');
        $this->setTableAttributes([
            'class' => 'table-theme table-row v-middle',
        ]);
        $this->setFilterLayout('slide-down');

        $this->setBulkActions([
            'bulkRetry' => 'Retry',
            'queryMultipleStatus' => 'Check Status',
            'export' => 'Export to CSV'
        ]);
    }



    public function builder(): Builder
    {
        // return Transaction::query()->with(['status']);
        // use joins
        return Transaction::query()
            ->join('statuses', 'transactions.status_id', '=', 'statuses.id')
            ->select(
                'transactions.*',
                'statuses.name as status_name'
            );
    }

    public function columns(): array
    {
        $columns = [
            Column::make('Id', 'id')
                ->sortable()
                ->format(fn() => $this->counter++),

            Column::make("Date", "transaction_date")
                ->sortable()
                ->excludeFromColumnSelect()
                ->format(
                    fn($value) => date('d M, Y', strtotime($value))
                ),

            Column::make("Time", "transaction_date")
                ->excludeFromColumnSelect()
                ->sortable()
                ->format(
                    fn($value) => date('h:i A', strtotime($value))
                ),

            Column::make("TRX. Type", 'type.name')
                ->excludeFromColumnSelect()
                ->searchable()
                ->sortable(),

            Column::make("Service", 'service.name')
                ->excludeFromColumnSelect()
                ->searchable()
                ->sortable(),

            Column::make("Channel", 'paymentChannel.name')
                ->excludeFromColumnSelect()
                ->searchable()
                ->sortable(),

            Column::make("TRX ID", "order_number")
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect(),

            Column::make("Receipt No.", "receipt_number")
                ->sortable()
                ->searchable(),

            Column::make("Account No.", "account_number")
                ->sortable()
                ->searchable(),

            Column::make("Account Name", "account_name")
                ->sortable()
                ->searchable()
                ->format(
                    fn($value) => ucwords(strtolower($value))
                ),

            Column::make("Amount", "disbursed_amount")
                ->sortable()
                ->format(
                    fn($value) => number_format($value)
                ),

            Column::make("Charges", "system_charges")
                ->sortable()
                ->label(function ($row, Column $column) {
                    return ($row->system_charges + $row->sms_charges);
                }),

            Column::make("Total Cost", "system_charges")
                ->sortable()
                ->label(function ($row, Column $column) {
                    return number_format($row->disbursed_amount + $row->system_charges + $row->sms_charges);
                })
                ->deselected(),

            Column::make("Revenue", "revenue")
                ->sortable()
                ->deselected(),

            Column::make("Status")
                ->sortable()
                ->label(function ($row, Column $column) {
                    $status = '<span class="badge bg-warning">' . $row->status_name . '</span>';
                    if ($row->status_id == 2) $status = '<span class="badge bg-success">' . $row->status_name . '</span>';
                    else if (in_array($row->status_id, [3, 4])) $status = '<span class="badge bg-danger">' . $row->status_name . '</span>';
                    return $status;
                })->html(),

            Column::make('Action')
                ->label(
                    function ($row, Column $column) {
                        $userPermission = Auth::user();
                        $html = '<div class="btn-group">';
                        $html .= '<a href="#" data-toggle="dropdown"><img height="16" src="' . asset('themes/agile/img/icon_more.png') . '" alt=""></a>';
                        $html .= '<div class="dropdown-menu bg-light" role="menu">';
                        $html .= '<a href="#" class="dropdown-item" wire:click="viewFunction(' . $row->id . ')">View Details</a>';
                        if ($row->status_id != 2) {
                            if ($row->status_id == 3) $html .= '<a href="#" class="dropdown-item" wire:click="editFunction(' . $row->id . ')">Edit Details</a>';
                            if ($row->status_id == 3) $html .= '<a href="#" class="dropdown-item" wire:click="retryPayment(' . $row->id . ')">Retry Payment</a>';
                            $html .= '<a href="#" class="dropdown-item" wire:click="paidOffline(' . $row->id . ')">Paid Offline</a>';
                            if (in_array($row->status_id, [1, 3])) $html .= '<a href="#" class="dropdown-item" wire:click="queryStatus(' . $row->id . ')">Query Status</a>';
                        }

                        if ($row->status_id == 2) $html .= '<a href="#" class="dropdown-item" wire:click="reverse(' . $row->id . ')">Reverse</a>';
                        $html .= '</div></div>';
                        return $html;
                    }
                )
                ->html(),

            Column::make("System charges", "system_charges")->deselected(),

            Column::make("SMS Charges", "sms_charges")->deselected(),

            Column::make("Status Id", "status_id")->deselected(),
        ];

        return $columns;
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Service', 'service')
                ->options(['' => 'All Services'] + Service::all()->pluck('name', 'id',)->toArray())
                ->filter(function (Builder $builder, string $value) {
                    $builder
                        ->where('transactions.service_id', $value);
                }),
            SelectFilter::make('Status', 'status')
                ->options(['' => 'All Status'] + Status::all()->pluck('name', 'id',)->toArray())
                ->filter(function (Builder $builder, string $value) {
                    $builder
                        ->where('transactions.status_id', $value);
                }),
            DateFilter::make('Start Date', 'start_date')
                ->filter(function (Builder $builder, string $value) {
                    $builder
                        ->where('transactions.transaction_date', ">", $value);
                }),
            DateFilter::make('End Date', 'end_date')
                ->filter(function (Builder $builder, string $value) {
                    $builder
                        ->where('transactions.transaction_date', "<", $value);
                }),
        ];
    }

    public function viewFunction($form_id)
    {
        $this->dispatch('viewFunction', $form_id);
    }

    public function editFunction($form_id)
    {
        $this->dispatch('editFunction', $form_id);
    }

    public function bulkRetry()
    {
        foreach ($this->getSelected() as $form_id) {
            $this->dispatch('retryPayment', $form_id);
        }
        $this->clearSelected();
    }

    public function retryPayment($form_id)
    {
        $this->dispatch('retryPayment', $form_id);
    }

    public function paidOffline($form_id)
    {
        $this->dispatch('paidOffline', $form_id);
    }

    public function queryStatus($form_id)
    {
        $this->dispatch('queryStatus', $form_id);
    }

    public function queryMultipleStatus()
    {
        foreach ($this->getSelected() as $form_id) {
            $this->dispatch('queryStatus', $form_id);
        }
        $this->clearSelected();
    }

    public function export()
    {
        // $transactions = $this->getSelected();
        // $this->clearSelected();
        // return Excel::download(new TransactionsExport($transactions), 'transactions.xlsx');
    }

    public function reverse($form_id)
    {
        $this->dispatch('reverse', $form_id);
    }
}
