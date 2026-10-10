<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\TransactionCharge;

class TransactionChargesTable extends DataTableComponent
{
    protected $model = TransactionCharge::class;
    protected $counter = 1;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('minimum', 'asc');
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
        $columns = [
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
        ];

        $columns[] = Column::make('Action')
            ->label(
                function ($row, Column $column) {
                $html = '<div class="dropdown">
                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    $html .= '<a href="#" class="dropdown-item" wire:click="editFunction(' . $row->id . ')">Edit</a>';
                    $html .= '</div></div>';
                    return $html;
                }
            )
            ->html();

        return $columns;
    }

    public function editFunction(int $form_id)
    {
        $this->dispatch('editFunction', $form_id);
    }
}
