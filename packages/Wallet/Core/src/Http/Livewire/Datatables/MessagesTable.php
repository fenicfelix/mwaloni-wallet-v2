<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\Outbox;

class MessagesTable extends DataTableComponent
{
    protected $model = Outbox::class;
    public $counter = 1;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setTableAttributes([
            'class' => 'table table-theme table-row v-middle',
        ]);
        $this->setFilterPillsItemAttributes([
            'class' => 'badge badge-pill badge-dark d-inline-flex align-items-center', // Add these classes to the filter pills item
            'default-colors' => false, // Do not output the default colors
            'default-styling' => true // Output the default styling
        ]);
        $this->setSortingPillsItemAttributes([
            'class' => 'badge badge-pill badge-dark d-inline-flex align-items-center', // Add these classes to the sorting pills item
            'default-colors' => false, // Do not output the default colors
            'default-styling' => true // Output the default styling
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()
                ->format(fn() => $this->counter++),
            Column::make("To", "to")
                ->searchable()
                ->sortable(),
            Column::make("Message", "message")
                ->searchable()
                ->sortable(),
            Column::make("Status", "sent")
                ->sortable()
                ->format(
                    fn($value) =>  '<span class="badge badge-circle xs text-' . ($value ? 'success' : 'danger') . ' mx-1"></span>'
                )->html(),
            Column::make("Sent On", "created_at")
                ->sortable()
                ->format(
                    fn($value, $row, Column $column) => ($row->sent_at) ? date('d M, Y', strtotime($row->sent_at)) : "-"
                ),
            Column::make("Sent At", "sent_at")
                ->sortable()
                ->format(
                    fn($value, $row, Column $column) => ($row->sent_at) ? date('h:i A', strtotime($row->sent_at)) : "-"
                ),
        ];
    }
}
