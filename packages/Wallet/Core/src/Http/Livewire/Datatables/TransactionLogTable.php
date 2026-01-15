<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\TransactionLog;

class TransactionLogTable extends DataTableComponent
{
    protected $model = TransactionLog::class;
    protected $counter = 1;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setTableAttributes([
            'class' => 'table table-theme table-row v-middle',
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()
                ->format(fn() => $this->counter++),
            Column::make("Date", "initiated_on")
                ->sortable()
                ->format(
                    fn($value) => date('d M, Y', strtotime($value))
                ),
            Column::make("Time", "initiated_on")
                ->sortable()
                ->format(
                    fn($value) => date('h:i A', strtotime($value))
                ),
            Column::make("TRX. Type", "type.name")
                ->sortable()
                ->searchable(),
            Column::make("Client", "client.name")
                ->sortable()
                ->searchable(),
            Column::make("Service", "service.name")
                ->sortable()
                ->searchable(),
            Column::make("Account Name", "account.name")
                ->sortable()
                ->searchable(),
            Column::make("Amount", "amount")
                ->sortable()
                ->searchable(),
            Column::make("Status", "status")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    $status = '<span class="badge bg-danger">' . strtoupper($value) . '</span>';
                    if (strtolower($value) == 'success') $status = '<span class="badge bg-success">' . strtoupper($value) . '</span>';
                    return $status;
                })->html(),
            Column::make("Reference", "reference")
                ->sortable()
                ->searchable(),
        ];
    }
}
