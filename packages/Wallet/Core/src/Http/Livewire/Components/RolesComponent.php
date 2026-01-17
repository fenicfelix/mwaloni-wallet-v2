<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Wallet\Core\Http\Enums\PermissionType;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\Role;
use Wallet\Core\Repositories\RoleRepository;

class RolesComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title;

    public ?bool $add = false;

    public ?int $formId = null;

    public ?array $formData = [];

    public $permissionTypes;

    public ?array $permissionsConfig = [];

    public ?int $count = 1;

    public $listeners = [
        'editFunction'
    ];

    public function mount()
    {
        $this->permissionTypes = collect(PermissionType::cases())
            ->mapWithKeys(fn($case) => [
                $case->value => $case->label(), // or $case->name
            ]);
        $this->permissionsConfig = config('core.acl.permissions');
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "System Roles";
        $this->resetValues();
    }

    public function rules()
    {
        $rules =  [
            'formData.name' => 'required',
            'formData.permission_type' => 'required|in:' . implode(',', PermissionType::values()),
        ];

        return $rules;
    }

    public function store()
    {
        // Update permissiions
        if ($this->formData['permission_type'] !== 'all') {
            $permissions = array_values($this->formData['permissions'] ?? []);
            $this->formData['permissions'] = $permissions;
        }

        try {
            $this->validate();
        } catch (\Throwable $th) {
            //throw $th;
        }

        $roleRepository = app(RoleRepository::class);
        if ($this->formId) {
            $result = $roleRepository->update($this->formId, $this->formData);
        } else {
            $result = $roleRepository->create($this->formData);
        }

        if ($result) {
            $this->notify(($this->formId) ? 'The role has been updated.' : 'The role has been created.', "success");
            $this->resetValues();
        } else {
            $this->initializeValues();
            $this->notify(($this->formId) ? 'The role has been updated.' : 'The role has not been created.', "error");
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

    public function addFunction()
    {
        $this->resetValues();
        $this->formData['permission_type'] = PermissionType::CUSTOM->value;
        $this->formData['permissions'] = [];
        $this->add = !$this->add;
    }

    public function editFunction($id)
    {
        $this->formId = $id;

        $role = Role::where('id', $id)->first();
        $this->formData = $role->toArray();
        $this->add = true;
    }

    public function backAction()
    {
        $this->resetValues();
    }

    public function resetValues()
    {
        $this->formId = null;
        $this->formData = [];
        $this->add = false;
    }

    public function render()
    {
        return view('core::livewire.roles-component')
            ->layout('core::layouts.app');
    }
}
