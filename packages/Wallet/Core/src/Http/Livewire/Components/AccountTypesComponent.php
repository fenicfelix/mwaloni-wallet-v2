<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\AccountType;
use Illuminate\Support\Str;

class AccountTypesComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title = null;

    public ?bool $add = false;

    public ?Collection $items = null;

    public ?int $edit_id = null;

    public ?string $account_type = null;

    public ?string $slug = null;

    public $listeners = [
        'editFunction'
    ];

    public function mount()
    {
        $this->items = AccountType::get();
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "Account Types";

        $this->edit_id = NULL;
        $this->account_type = NULL;
        $this->slug = NULL;
    }

    public function addFunction()
    {
        $this->add = !$this->add;
        $this->initializeValues();
    }

    public function editFunction($id)
    {
        $this->edit_id = $id;

        $item = AccountType::where('id', $id)->first();
        $this->account_type = $item->account_type;
        $this->slug = $item->slug;
        $this->add = true;
    }

    public function rules()
    {
        if ($this->edit_id) {
            $rules =  [
                'account_type' =>  'required|min:3|unique:account_types,account_type,' . $this->edit_id,
            ];
        } else {
            $rules =  [
                'account_type' => 'required|min:3|unique:account_types',
            ];
        }

        return $rules;
    }

    public function store()
    {
        $this->validate();

        $item = AccountType::updateOrCreate(["id" => $this->edit_id], ["identifier" => Str::uuid(), "account_type" => $this->account_type, "slug" => Str::slug($this->account_type)]);

        if (!$item) {
            $message = ($this->edit_id) ? "The account type has not been updated." : "The account type has not been added";
            $this->notify($message, "success");
        } else {
            $message = ($this->edit_id) ? "The account type has been updated." : "The account type has been added";
            $this->notify($message, "success");

            $this->initializeValues();
            $this->add = false;
        }
    }

    public function render()
    {
        return view('core::livewire.account-types-component')
            ->layout('core::layouts.app');
    }
}
