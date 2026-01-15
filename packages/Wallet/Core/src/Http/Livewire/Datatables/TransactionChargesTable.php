<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\TransactionCharge;

class TransactionChargesTable extends DataTableComponent
{
    protected $model = TransactionCharge::class;
    public $counter = 1;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('minimum', 'ASC');
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
            Column::make("Payment channel id", "channel.name")
                ->sortable()
                ->searchable(),
            Column::make("Minimum", "minimum")
                ->sortable()
                ->searchable()
                ->format(
                    fn($value) => number_format($value)
                ),
            Column::make("Maximum", "maximum")
                ->sortable()
                ->searchable()
                ->format(
                    fn($value) => number_format($value)
                ),
            Column::make("Charge", "charge")
                ->sortable()
                ->searchable(),
            Column::make('Action')
                ->label(
                    function ($row, Column $column) {
                        $html = '<div class="btn-group">';
                        $html .= '<a href="#" data-toggle="dropdown"><img height="16" src="' . asset('themes/agile/img/icon_more.png') . '" alt=""></a>';
                        $html .= '<div class="dropdown-menu bg-light" role="menu">';
                        $html .= '<a href="#" class="dropdown-item" wire:click="editFunction(' . $row->id . ')">Edit</a>';
                        $html .= '</div></div>';
                        return $html;
                    }
                )
                ->html(),
        ];
    }

    public function editFunction($form_id)
    {
        $this->dispatch('editFunction', $form_id);
    }
}
