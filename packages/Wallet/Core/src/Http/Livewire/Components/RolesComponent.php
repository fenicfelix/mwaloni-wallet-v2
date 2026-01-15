<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Wallet\Core\Http\Traits\WalletEvents;
use Wallet\Core\Models\Role;

class RolesComponent extends Component
{
    use WalletEvents;

    public ?string $content_title;

    public ?bool $add = false;

    public ?Collection $items;

    public ?Collection $permissions;

    public ?int $edit_id;

    public ?string $name;

    public ?array $selectedPermissions;

    public ?int $count = 1;

    public $listeners = [
        'editFunction'
    ];

    public function mount()
    {
        $this->items = Role::get();
        $this->permissions = null;
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "System Roles";

        $this->edit_id = NULL;
        $this->name = NULL;
        $this->selectedPermissions = [];
    }

    public function addFunction()
    {
        $this->add = !$this->add;
        $this->initializeValues();
    }

    public function editFunction($id)
    {
        $this->edit_id = $id;

        $role = Role::where('id', $id)->first();
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck("id");
        $this->add = true;
    }

    public function rules()
    {
        $rules =  [
            'name' => 'required',
        ];

        return $rules;
    }

    public function store()
    {
        $update = false;
        if ($this->edit_id) {
            $this->validate();
        }

        if ($this->edit_id) $update = true;

        $transaction = DB::transaction(function () {

            if ($this->edit_id) {
                $role = Role::where("id", $this->edit_id)->first();
                $role->name = $this->name;
                $role->save();

                if (!$role->save()) return false;
            } else {
                $role = Role::create(['name' => $this->name]);
            }

            $role->syncPermissions($this->selectedPermissions);

            return true;
        }, 2);

        if ($transaction) {
            $this->initializeValues();
            $this->add = false;
            $this->notify(($update) ? 'The role has been updated.' : 'The role has been created.', "success");
        } else {
            $this->initializeValues();
            $this->add = false;
            $this->notify(($update) ? 'The role has been updated.' : 'The role has not been created.', "error");
        }
    }
    
    public function render()
    {
        return view('core::livewire.roles-component')
            ->layout('core::layouts.app');
    }
}