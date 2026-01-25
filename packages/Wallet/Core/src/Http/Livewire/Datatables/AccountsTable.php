<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Wallet\Core\Models\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class AccountsTable extends DataTableComponent
{
    protected $model = Account::class;
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

    public function builder(): Builder
    {
        return Account::query()
            ->select('accounts.*')
            ->selectSub(
                function ($query) {
                    $query->from('balance_reservations')
                        ->selectRaw('COALESCE(SUM(amount), 0)')
                        ->whereColumn('balance_reservations.account_id', 'accounts.id');
                },
                'transaction_reservations'
            );
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
            // Column::make("Total Balance")
            //     ->sortable()
            //     ->label(function ($row, Column $column) {
            //         return number_format($row->acc_float, 2);
            //     }),
            Column::make("Revenue", "revenue")
                ->sortable()
                ->format(
                    fn($value) => number_format($value)
                ),
            Column::make("Withheld Amount", "withheld_amount")
                ->sortable()
                ->format(
                    fn($value) => number_format($value, 2)
                ),

            Column::make("TRX. Reservations")
                ->sortable()
                ->label(function ($row, Column $column) {
                    return number_format($row->transaction_reservations, 2);
                }),
            Column::make("Operational Balance")
                ->sortable()
                ->label(function ($row, Column $column) {
                    $operational_balance = $row->working_balance + $row->utility_balance - ($row->revenue + $row->withheld_amount + $row->transaction_reservations);
                    return number_format($operational_balance, 2);
                }),
            Column::make("Status", "active")
                ->sortable()
                ->format(
                    fn($value) =>  '<span class="badge badge-circle xs text-'.($value ? 'success' : 'danger').' mx-1"></span>'
                )->html(),
            Column::make('Action')
                ->label(
                    function ($row, Column $column) {
                        $userPermission = Auth::user();
                        $html = '<div class="btn-group">';
                        $html .= '<a href="#" data-toggle="dropdown"><img height="16" src="' . asset('themes/agile/img/icon_more.png') . '" alt=""></a>';
                        $html .= '<div class="dropdown-menu bg-light" role="menu">';
                        if ($row->active == 1) $html .= '<a href="#" class="dropdown-item" wire:click="editFunction(' . $row->id . ')">Edit</a>';
                        if ($row->active == "1") $html .= '<a href="#" class="dropdown-item" wire:click="deactivateAccount(' . $row->id . ', `deactivate`)">Deactivate</a>';
                        if ($row->active != "1") $html .= '<a href="#" class="dropdown-item" wire:click="deactivateAccount(' . $row->id . ', `activate`)">Activate</a>';
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
        $this->dispatch('editFunction', $form_id);
    }

    public function deactivateAccount($form_id, $task)
    {
        $this->dispatch('deactivateAccount', $form_id, $task);
    }

    public function cashoutFunction($form_id)
    {
        $this->dispatch('cashoutFunction', $form_id);
    }

    public function fetchBalance($form_id)
    {
        $this->dispatch('fetchBalance', $form_id);
    }

    public function updateWithheldAmount($form_id)
    {
        $this->dispatch('updateWithheldAmount', $form_id);
    }
}
