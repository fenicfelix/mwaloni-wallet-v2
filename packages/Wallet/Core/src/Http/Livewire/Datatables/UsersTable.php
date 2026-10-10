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
                ->format(function ($value) {
                    if (! $value) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }

                    return '<span class="badge bg-success">Active</span>';
                })->html(),
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
                    $html = '<div class="dropdown">
                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                        $html .= '<a href="#" class="dropdown-item" wire:click="showApiDetails(' . $row->id . ')">Get API Details</a>';
                        $html .= '<a href="#" class="dropdown-item" wire:click="viewFunction(' . $row->id . ')">View</a>';
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

    public function viewFunction(int $form_id)
    {
        $this->dispatch('viewFunction', $form_id);
    }

    public function showApiDetails(int $form_id)
    {
        $this->dispatch('generateApiDetails', $form_id);
    }

    public function editFunction(int $form_id)
    {
        $this->dispatch('editFunction', $form_id);
    }

    public function resetPasswordFunction(int $form_id)
    {
        $this->dispatch('resetPasswordFunction', $form_id);
    }

    public function deleteAccountFunction(int $form_id)
    {
        $this->dispatch('deleteAccountFunction', $form_id);
    }
}
