<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\PaymentChannel;
use Wallet\Core\Models\TransactionCharge;

class TransactionChargesComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title = "";

    public ?bool $add = false;

    public ?Collection $items = null;

    public ?Collection $payment_channels = null;

    public ?int $formId = null;

    public ?string $payment_channel = null;

    public ?float $minimum = null;

    public ?float $maximum = null;

    public ?float $charge = null;

    public $listeners = [
        'editFunction'
    ];

    public function mount()
    {
        $this->items = TransactionCharge::get();
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "Transaction Charges Manager";
        $this->payment_channels = PaymentChannel::get();

        $this->formId = NULL;
        $this->payment_channel = NULL;
        $this->minimum = NULL;
        $this->maximum = NULL;
        $this->charge = NULL;
    }

    public function addFunction()
    {
        $this->add = !$this->add;
        $this->initializeValues();
    }

    public function editFunction($id)
    {
        $this->formId = $id;

        $item = TransactionCharge::where('id', $id)->first();
        $this->payment_channel = $item->payment_channel;
        $this->minimum = $item->minimum;
        $this->maximum = $item->maximum;
        $this->charge = $item->charge;
        $this->add = true;
    }

    public function rules()
    {
        $rules =  [
            'name' => 'required',
            'minimum' => 'required',
            'maximum' => 'required'
        ];

        return $rules;
    }

    public function store()
    {
        if ($this->formId) {
            $this->validate();
        }

        $status = TransactionCharge::updateOrCreate(["id" => $this->formId], [
            "name" => $this->name,
            "minimum" => $this->minimum,
            "maximum" => $this->maximum
        ]);

        if (!$status) {
            $message = ($this->formId) ? "The charge has not been updated." : "The charge has not been added";
            $this->notify($message, "success");
        } else {
            $message = ($this->formId) ? "The charge has been updated." : "The charge has been added";
            $this->notify($message, "success");

            $this->initializeValues();
            $this->add = false;
        }
    }

    public function render()
    {
        return view('core::livewire.transaction-charges-component')
            ->layout('core::layouts.app');
    }
}
