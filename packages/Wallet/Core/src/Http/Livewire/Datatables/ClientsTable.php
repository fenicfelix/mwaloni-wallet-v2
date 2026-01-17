<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientsTable extends DataTableComponent
{
    protected $model = Client::class;
    protected $counter = 1;

    public function builder(): Builder
    {
        $query = Client::join('users', 'clients.account_manager', '=', 'users.id')
            ->select(
                'clients.id',
                'clients.name',
                'clients.active',
                'clients.created_at',
                'users.first_name',
                'users.last_name'
            );

        return $query;
    }

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
            Column::make("Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Account manager", 'manager.id')
                ->sortable()
                ->searchable()
                ->label(fn($row) => $row->first_name . ' ' . $row->last_name),
            Column::make("Active", "active")
                ->sortable()
                ->format(
                    fn($value) =>  '<span class="badge badge-circle xs text-' . ($value ? 'success' : 'danger') . ' mx-1"></span>'
                )->html(),
            Column::make("Registered On", "created_at")
                ->sortable()
                ->format(
                    fn($value) => date('d M, Y', strtotime($value))
                ),
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
