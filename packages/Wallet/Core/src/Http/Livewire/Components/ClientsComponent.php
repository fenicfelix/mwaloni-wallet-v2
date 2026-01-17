<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Repositories\ClientRepository;

class ClientsComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title;

    public bool $add = false;

    public ?int $formId = null;

    public ?array $formData = [];

    public ?Collection $managers;

    public function mount()
    {
        $this->managers = User::orderBy("first_name", "ASC")->get();
        // concatenate first_name and last_name
        $this->managers = $this->managers->mapWithKeys(function ($manager) {
            return [
                $manager->id => trim("{$manager->first_name} {$manager->last_name}"),
            ];
        });

        $this->initializeVariables();
    }

    private function initializeVariables()
    {
        $this->resetValues();
        $this->content_title = "Clients Manager";
        $this->add = false;
    }

    public function rules()
    {
        return  [
            'formData.name' => 'required|unique:clients,name,' . $this->formId,
            'formData.account_manager' => 'required|exists:users,id',
        ];
    }

    public function store()
    {
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
            $client = app(ClientRepository::class)->create($this->formData);
        } else {
            $client = app(ClientRepository::class)->update($this->formId, $this->formData);
        }

        if (!$client) {
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
        $this->formData = app(ClientRepository::class)->find($id)->toArray();
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
        return view('core::livewire.clients-component')
            ->layout('core::layouts.app');
    }
}
