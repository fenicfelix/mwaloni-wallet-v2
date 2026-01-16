<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ClientsTable extends DataTableComponent
{
    protected $model = Client::class;
    protected $counter = 1;

    public function builder(): Builder
    {
        return Client::query()
            ->with('manager');
    }

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
            Column::make("Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Client ID", "client_id")
                ->sortable(),
            Column::make("Account manager", 'manager.phone_number')
                ->sortable()
                ->searchable(),
            Column::make("Balance", "balance")
                ->sortable(),
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
