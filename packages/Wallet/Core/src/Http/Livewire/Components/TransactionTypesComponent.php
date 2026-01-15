<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Wallet\Core\Http\Traits\WalletEvents;
use Wallet\Core\Models\TransactionType;

class TransactionTypesComponent extends Component
{
    use WalletEvents;

    public ?string $content_title = "";

    public ?bool $add = false;

    public ?Collection $items = null;

    public ?int $edit_id = null;

    public ?string $name = null;

    public ?string $description = null;

    public $listeners = [
        'editFunction'
    ];

    public function mount()
    {
        $this->items = TransactionType::get();
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "Transaction Types";

        $this->edit_id = NULL;
        $this->name = NULL;
        $this->description = NULL;
    }

    public function addFunction()
    {
        $this->add = !$this->add;
        $this->initializeValues();
    }

    public function editFunction($id)
    {
        $this->edit_id = $id;

        $item = TransactionType::where('id', $id)->first();
        $this->name = $item->name;
        $this->description = $item->description;
        $this->add = true;
    }

    public function rules()
    {
        if ($this->edit_id) {
            $rules =  [
                'name' =>  'required|min:3|unique:transaction_types,name,' . $this->edit_id,
            ];
        } else {
            $rules =  [
                'name' => 'required|min:3|unique:transaction_types',
            ];
        }

        return $rules;
    }

    public function store()
    {
        $this->validate();

        $item = TransactionType::updateOrCreate(["id" => $this->edit_id], ["identifier" => Str::uuid(), "name" => $this->name, "description" => $this->description]);

        if (!$item) {
            $message = ($this->edit_id) ? "The transaction type has not been updated." : "The transaction type has not been added";
            $this->notify($message, "success");
        } else {
            $message = ($this->edit_id) ? "The transaction type has been updated." : "The transaction type has been added";
            $this->notify($message, "success");

            $this->initializeValues();
            $this->add = false;
        }
    }
    
    public function render()
    {
        return view('core::livewire.transaction-types-component')
            ->layout('core::layouts.app');
    }
}