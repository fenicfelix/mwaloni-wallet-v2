<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Wallet\Core\Models\Role;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RolesTable extends DataTableComponent
{
    protected $model = Role::class;
    protected $counter = 1;

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
        $columns = [
            Column::make('Id', 'id')
                ->sortable()
                ->format(fn() => $this->counter++),

            Column::make("Name", "name")
                ->searchable()
                ->sortable(),

            Column::make("Permission Type", "permission_type")
                ->searchable()
                ->sortable()
                ->format(function ($value) {
                    return $value->label();
                }),
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
