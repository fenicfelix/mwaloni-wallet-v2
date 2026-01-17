<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\SystemPreference;

class PreferencesComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title;

    public ?bool $add = false;

    public ?array $items;

    public ?int $edit_id;

    public ?string $title;

    public ?string $slug;

    public ?string $value;

    public $listeners = [
        'editFunction'
    ];

    public function mount()
    {
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "System Preferences Manager";

        $this->edit_id = NULL;
        $this->title = NULL;
        $this->slug = NULL;
        $this->value = NULL;
    }

    public function addFunction()
    {
        $this->add = !$this->add;
        $this->initializeValues();
    }

    public function editFunction($id)
    {
        $this->edit_id = $id;

        $item = SystemPreference::where('id', $id)->first();
        $this->title = $item->title;
        $this->slug = $item->slug;
        $this->value = $item->value;
        $this->add = true;
    }

    public function rules()
    {
        $rules =  [
            'title' => 'required',
            'value' => 'required',
        ];

        return $rules;
    }

    public function store()
    {
        if ($this->edit_id) {
            $this->validate();
        }

        $status = SystemPreference::updateOrCreate(["id" => $this->edit_id], [
            "identifier" => generate_identifier(),
            "title" => $this->title,
            "slug" => Str::slug($this->title, '-'),
            "value" => $this->value,
        ]);

        if (!$status) {
            $message = ($this->edit_id) ? "The value has not been updated." : "The value has not been added";
            $this->notify($message, "success");
        } else {
            $message = ($this->edit_id) ? "The value has been updated." : "The value has been added";
            $this->notify($message, "success");

            $this->initializeValues();
            $this->add = false;
        }
    }

    public function render()
    {
        return view('core::livewire.preferences-component')
            ->layout('core::layouts.app');
    }
}
