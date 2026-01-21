<?php

namespace Wallet\Core\Http\Livewire\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;

class UsersTable extends DataTableComponent
{
    protected $model = User::class;
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
            Column::make("Role", "role.name")
                ->sortable()
                ->searchable(),
            Column::make("Status", "active")
                ->sortable()
                ->format(
                    fn($value) =>  '<span class="badge badge-circle xs text-' . ($value ? 'success' : 'danger') . ' mx-1"></span>'
                )->html(),
            Column::make("Member Since", "created_at")
                ->format(
                    fn($value) => date('d M, Y', strtotime($value))
                )
                ->sortable(),

        ];

        $columns[] = Column::make('Action')
            ->label(
                function ($row, Column $column) {
                    if ($row->active == true) {
                        $html = '<div class="btn-group">';
                        $html .= '<a href="#" data-toggle="dropdown"><img height="16" src="' . asset('themes/agile/img/icon_more.png') . '" alt=""></a>';
                        $html .= '<div class="dropdown-menu bg-light" role="menu">';
                        $html .= '<a href="#" class="dropdown-item" wire:click="showApiDetails(' . $row->id . ')">Get API Details</a>';
                        $html .= '<a href="#" class="dropdown-item" wire:click="editFunction(' . $row->id . ')">Edit</a>';
                        $html .= '<a href="#" class="dropdown-item"wire:click="resetPasswordFunction(' . $row->id . ')">Reset Password</a>';
                        $html .= '<a href="#" class="dropdown-item"wire:click="deleteAccountFunction(' . $row->id . ')">Delete Account</a>';
                        $html .= '</div></div>';
                        return $html;
                    }
                }
            )
            ->html();

        return $columns;
    }

    public function showApiDetails($form_id)
    {
        $this->dispatch('generateApiDetails', $form_id);
    }

    public function editFunction($form_id)
    {
        $this->dispatch('editFunction', $form_id);
    }

    public function resetPasswordFunction($form_id)
    {
        $this->dispatch('resetPasswordFunction', $form_id);
    }

    public function deleteAccountFunction($form_id)
    {
        $this->dispatch('deleteAccountFunction', $form_id);
    }
}
