<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\Client;

class ClientsComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title;

    public bool $add = false;

    public ?int $editId = null;

    public ?Client $client;

    public ?string $name, $account_manager;

    public Collection $managers;

    public $listeners = [
        'editFunction'
    ];

    public function mount()
    {
        $this->initializeVariables();
    }

    private function initializeVariables()
    {
        $this->content_title = "Clients Manager";
        $this->add = false;

        $this->managers = User::orderBy("first_name", "ASC")->get();
    }

    private function clearVariables()
    {
        $this->add = false;
        $this->client = NULL;
    }

    public function addFunction()
    {
        $this->add = !$this->add;
    }
    public function editFunction($id)
    {
        $this->add = !$this->add;
        $this->editId = $id;
        $this->client = Client::find($id);

        $this->name = $this->client->name;
        $this->account_manager = $this->client->account_manager;
    }

    public function rules()
    {
        $rules =  [
            'name' => 'required|unique:clients',
            'account_manager' => 'required',
        ];

        if ($this->editId) {
            $rules["name"] = 'required|unique:clients,name,' . $this->editId;
        }

        return $rules;
    }

    public function store()
    {
        $this->validate();

        $user_id = Auth::id();

        if ($this->editId) {
            $update = $this->client->update(
                [
                    "name" => $this->name,
                    "account_manager" => $this->account_manager,
                ]
            );
            if ($update) {
                $this->clearVariables();
                $this->notify("The client has been updated.", "success");
            } else {
                $this->notify("The client has not been updated.", "error");
            }
        } else {
            $this->client = Client::create([
                "identifier" => generate_identifier(),
                "name" => $this->name,
                "account_manager" => $this->account_manager,
                "balance" => 0,
                "active" => 1,
                "added_by" => $user_id,
                "updated_by" => $user_id,
            ]);

            if ($this->client) {
                $client_id = str_pad($this->client->id, 5, "0", STR_PAD_LEFT);
                $this->client->client_id = "CLT-" . $client_id;
                $this->client->save();
                $this->notify("The client has been added successfully.", "success");

                $this->clearVariables();
            } else {
                $this->notify("Client could not be added.", "error");
            }
        }
    }

    public function render()
    {
        return view('core::livewire.clients-component')
            ->layout('core::layouts.app');
    }
}
