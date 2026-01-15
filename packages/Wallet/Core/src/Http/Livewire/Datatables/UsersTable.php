<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersTable extends DataTableComponent
{
    protected $model = User::class;
    public $counter = 1;

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
            Column::make("First Name", "first_name")
                ->sortable()
                ->searchable(),
            Column::make("Last Name", "last_name")
                ->sortable()
                ->searchable(),
            Column::make("Phone", "phone_number")
                ->sortable()
                ->searchable(),
            Column::make("Email", "email")
                ->sortable()
                ->searchable(),
            Column::make("Status", "active")
                ->sortable()
                ->format(
                    fn($value) => ($value) ? '<span class="disc text-success"></span>' : '<span class="disc text-danger"></span>'
                )->html(),
            Column::make("Member Since", "created_at")
                ->format(
                    fn($value) => date('d M, Y', strtotime($value))
                )
                ->sortable(),
            Column::make('Action')
                ->label(
                    function ($row, Column $column) {
                        $html = '<div class="btn-group">';
                        $html .= '<a href="#" data-toggle="dropdown"><img height="16" src="' . asset('themes/agile/img/icon_more.png') . '" alt=""></a>';
                        $html .= '<div class="dropdown-menu bg-light" role="menu">';
                        $html .= '<a href="#" class="dropdown-item" wire:click="showApiDetails(' . $row->id . ')">API Details</a>';
                        $html .= '<a href="#" class="dropdown-item" wire:click="editFunction(' . $row->id . ')">Edit</a>';
                        $html .= '<a href="#" class="dropdown-item"wire:click="resetPasswordFunction(' . $row->id . ')">Reset Password</a>';
                        $html .= '</div></div>';
                        return $html;
                    }
                )
                ->html(),
        ];
    }

    public function showApiDetails($form_id)
    {
        $this->dispatch('showApiDetails', $form_id);
    }

    public function editFunction($form_id)
    {
        $this->dispatch('editFunction', $form_id);
    }

    public function resetPasswordFunction($form_id)
    {
        $this->dispatch('resetPasswordFunction', $form_id);
    }
}
