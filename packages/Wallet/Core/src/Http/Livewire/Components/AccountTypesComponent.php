<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\AccountType;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Wallet\Core\Repositories\AccountTypeRepository;

class AccountTypesComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title = null;

    public ?bool $add = false;

    public ?int $formId = null;

    public ?array $formData = [];

    public function mount()
    {
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "Account Types";
    }

    public function rules()
    {
        return  [
            'formData.account_type' =>  'required|min:3',
            'formData.slug' => 'required|min:3|unique:account_types,slug,' . $this->formId,
        ];
    }

    public function store()
    {
        if ($this->formId === null) $this->formData['slug'] = Str::slug($this->formData['account_type']);

        try {
            $this->validate();
        } catch (\Throwable $th) {
            $this->notify(
                'There were validation errors. Please check the form and try again.',
                'error'
            );
            return;
        }

        if ($this->formId === null) {
            $preference = app(AccountTypeRepository::class)->create($this->formData);
        } else {
            $preference = app(AccountTypeRepository::class)->update($this->formId, $this->formData);
        }

        if (!$preference) {
            $this->notify('Operation not successful. Please try again.', 'error');
            return;
        }

        $this->notify('Operation successful. Please try again.', 'success');
        $this->resetValues();
    }

    public function addFunction()
    {
        $this->resetValues();
        $this->add = !$this->add;
    }

    #[On('editFunction')]
    public function editFunction($id)
    {
        $this->resetValues();
        $this->formId = $id;
        $this->formData = app(AccountTypeRepository::class)->find($id)->toArray();
        $this->add = true;
    }

    public function backAction()
    {
        $this->resetValues();
    }

    public function resetValues()
    {
        $this->reset(['formId', 'formData', 'add']);
    }

    public function render()
    {
        return view('core::livewire.account-types-component')
            ->layout('core::layouts.app');
    }
}
