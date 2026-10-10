<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\Service;
use Illuminate\Support\Facades\Auth;

class ServicesTable extends DataTableComponent
{
    protected $model = Service::class;
    protected $counter = 1;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'asc');
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
            Column::make("Account Name", "account.display_name")
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
            Column::make("Status", "active")
                ->sortable()
                ->format(function ($value) {
                    if (! $value) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }

                    return '<span class="badge bg-success">Active</span>';
                })->html(),
        ];

        $columns[] = Column::make('Action')
            ->label(
                function ($row, Column $column) {
                    $userPermission = Auth::user();
                $html = '<div class="dropdown">
                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    $html .= '<a href="#" class="dropdown-item" data-toggle="modal" wire:click="editFunction(' . $row->id . ')">Edit</a>';
                    $html .= '<a href="#" wire:click="withdrawFees(' . $row->id . ')"class="dropdown-item reset-2fa">Withdraw Fees</a>';
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

    public function withdrawFees(int $form_id)
    {
        $this->dispatch('withdrawFees', $form_id);
    }
}
