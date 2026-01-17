<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\Status;
use Illuminate\Support\Str;

class StatusesComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title = null;

    public ?bool $add = false;

    public ?Collection $items = null;

    public ?int $edit_id = null;

    public ?string $name = null;

    public $listeners = [
        'editFunction'
    ];

    public function mount()
    {
        $this->items = Status::get();
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "Statuses Manager";

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

        $item = Status::where('id', $id)->first();
        $this->name = $item->name;
        $this->add = true;
    }

    public function rules()
    {
        if ($this->edit_id) {
            $rules =  [
                'name' =>  'required|min:3|unique:statuses,name,' . $this->edit_id,
            ];
        } else {
            $rules =  [
                'name' => 'required|min:3|unique:statuses',
            ];
        }
        return $rules;
    }

    public function store()
    {
        $this->validate();

        $status = Status::updateOrCreate(["id" => $this->edit_id], ["identifier" => Str::uuid(), "name" => $this->name]);

        if (!$status) {
            $message = ($this->edit_id) ? "The status has not been updated." : "The status has not been added";
            $this->notify($message, "success");
        } else {
            $message = ($this->edit_id) ? "The status has been updated." : "The status has been added";
            $this->notify($message, "success");

            $this->initializeValues();
            $this->add = false;
        }
    }

    public function render()
    {
        return view('core::livewire.statuses-component')
            ->layout('core::layouts.app');
    }
}
