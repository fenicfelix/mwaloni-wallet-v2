<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServicesTable extends DataTableComponent
{
    protected $model = Service::class;
    protected $counter = 0;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
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
            Column::make("Service Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Service ID", "service_id")
                ->sortable()
                ->searchable(),
            Column::make("Client id", "client.name")
                ->sortable()
                ->searchable(),
            Column::make("Manager", "client.manager.first_name")
                ->sortable()
                ->searchable(),
            Column::make("Account Name", "account.name")
                ->sortable()
                ->searchable(),
            Column::make("Max TRX Amount", "max_trx_amount")
                ->sortable()
                ->format(
                    fn($value) => number_format($value)
                ),
            Column::make('Charges', 'system_charges')
                ->searchable()
                ->format(
                    fn($value, $row, Column $column) => $row->system_charges
                ),
            Column::make('SMS Charges', 'sms_charges')
                ->searchable()
                ->format(
                    fn($value, $row, Column $column) => $row->sms_charges
                ),
            Column::make("Active", "active")
                ->sortable()
                ->format(
                    fn($value) => ($value) ? '<span class="disc text-success"></span>' : '<span class="disc text-danger"></span>'
                )->html(),
            Column::make('Action')
                ->label(
                    function ($row, Column $column) {
                        $userPermission = Auth::user();
                        $html = '<div class="btn-group">';
                        $html .= '<a href="#" data-toggle="dropdown"><img height="16" src="' . asset('themes/agile/img/icon_more.png') . '" alt=""></a>';
                        $html .= '<div class="dropdown-menu bg-light" role="menu">';
                        $html .= '<a href="#" class="dropdown-item" data-toggle="modal" wire:click="editFunction(' . $row->id . ')">Edit</a>';
                        $html .= '<a href="#" wire:click="withdrawCharges(' . $row->id . ')"class="dropdown-item reset-2fa">Withdraw Charges</a>';
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

    public function withdrawCharges($form_id)
    {
        $this->dispatch('withdrawCharges', $form_id);
    }
}
