<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Wallet\Core\Http\Enums\PermissionType;
use Wallet\Core\Http\Traits\WalletEvents;
use Wallet\Core\Models\Role;

class RolesComponent extends Component
{
    use WalletEvents;

    public ?string $content_title;

    public ?bool $add = false;

    public ?int $edit_id;

    public ?array $formData = [];

    public ?array $permissionTypes = [];

    public ?array $permissionsConfig = [];

    public ?int $count = 1;

    public $listeners = [
        'editFunction'
    ];

    public function mount()
    {
        $this->permissionTypes = PermissionType::cases();
        $this->permissionsConfig = config('core.acl.permissions');
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "System Roles";

        $this->edit_id = NULL;
        $this->name = NULL;
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
        $this->formData = $role->toArray();
        $this->add = true;
    }

    public function rules()
    {
        $rules =  [
            'formData.name' => 'required',
        ];

        return $rules;
    }

    public function store()
    {
        dd($this->formData);

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

    public function toggleGroup(string $group): void
    {
        $groupPermissions = array_keys($this->permissionsConfig[$group] ?? []);
        $current = $this->formData['permissions'] ?? [];

        $allSelected = empty(array_diff($groupPermissions, $current));

        if ($allSelected) {
            // Unselect all in group
            $this->formData['permissions'] = array_values(
                array_diff($current, $groupPermissions)
            );
        } else {
            // Select all in group
            $this->formData['permissions'] = array_values(
                array_unique(array_merge($current, $groupPermissions))
            );
        }
    }

    public function render()
    {
        return view('core::livewire.roles-component')
            ->layout('core::layouts.app');
    }
}
