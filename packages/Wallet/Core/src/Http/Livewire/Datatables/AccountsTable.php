<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountsTable extends DataTableComponent
{
    protected $model = Account::class;
    protected $counter = 1;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'ASC');
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
            Column::make("Account Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Account Number", "account_number")
                ->sortable()
                ->searchable(),
            Column::make("Country Code", "country_code")
                ->sortable()
                ->searchable(),
            Column::make("Currency", "currency.code")
                ->sortable()
                ->searchable(),
            Column::make("Working Balance", "working_balance")
                ->sortable()
                ->format(
                    fn($value) => number_format($value, 2)
                ),
            Column::make("Utility Balance", "utility_balance")
                ->sortable()
                ->format(
                    fn($value) => number_format($value, 2)
                ),
            Column::make("Total Balance")
                ->sortable()
                ->label(function ($row, Column $column) {
                    return number_format($row->working_balance + $row->utility_balance, 2);
                }),
            Column::make("Revenue", "revenue")
                ->sortable()
                ->format(
                    fn($value) => number_format($value)
                ),
            Column::make("Operational Balance")
                ->sortable()
                ->label(function ($row, Column $column) {
                    return number_format((($row->working_balance + $row->utility_balance) - $row->revenue), 2);
                }),
            Column::make("Status", "active")
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
                        if ($row->active == 1) $html .= '<a href="#" class="dropdown-item" wire:click="editFunction(' . $row->id . ')">Edit</a>';
                        if ($row->active == "1") $html .= '<a href="#" class="dropdown-item" wire:click="deactivateAccount(' . $row->id . ')">Deactivate</a>';
                        if ($row->active != "1") $html .= '<a href="#" class="dropdown-item" wire:click="deactivateAccount(' . $row->id . ')">Activate</a>';
                        if ($row->active == 1) $html .= '<a href="#" class="dropdown-item"wire:click="cashoutFunction(' . $row->id . ')">Cashout</a>';
                        if ($row->active == 1) $html .= '<a href="#" class="dropdown-item"wire:click="fetchBalance(' . $row->id . ')">Fetch Balance</a>';
                        if ($row->active == 1) $html .= '<a href="#" class="dropdown-item"wire:click="updateWithheldAmount(' . $row->id . ')">Update Withheld Amount</a>';
                        $html .= '</div></div>';

                        return $html;
                    }
                )
                ->html(),
        ];
    }

    public function editFunction($form_id)
    {
        $this->emit('editFunction', $form_id);
    }

    public function deactivateAccount($form_id)
    {
        $this->emit('deactivateAccount', $form_id);
    }

    public function cashoutFunction($form_id)
    {
        $this->emit('cashoutFunction', $form_id);
    }

    public function fetchBalance($form_id)
    {
        $this->emit('fetchBalance', $form_id);
    }

    public function updateWithheldAmount($form_id)
    {
        $this->emit('updateWithheldAmount', $form_id);
    }
}
