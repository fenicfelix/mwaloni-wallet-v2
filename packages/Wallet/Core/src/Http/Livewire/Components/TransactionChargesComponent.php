<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\PaymentChannel;
use Wallet\Core\Repositories\TransactionChargeRepository;

class TransactionChargesComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title = "";

    public ?bool $add = false;

    public ?int $formId = null;

    public ?array $formData = [];

    public ?Collection $payment_channels = null;

    public function mount()
    {
        $this->initializeValues();
    }

    private function initializeValues()
    {
        $this->content_title = "Transaction Charges Manager";
        $this->payment_channels = PaymentChannel::get();
        $this->resetValues();
    }

    public function rules()
    {
        $rules =  [
            'formData.minimum' => 'required',
            'formData.maximum' => 'required',
            'formData.payment_channel_id' => 'required|exists:payment_channels,id',
            'formData.charge' => 'required|numeric|min:0'
        ];

        return $rules;
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
            $preference = app(TransactionChargeRepository::class)->create($this->formData);
        } else {
            $preference = app(TransactionChargeRepository::class)->update($this->formId, $this->formData);
        }

        if (!$preference) {
            $this->notify('Operation not successful. Please try again.', 'error');
            return;
        }

        $this->notify('Operation successful. Please try again.', 'success');
        $this->initializeValues();
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
        $this->formData = app(TransactionChargeRepository::class)->find($id)->toArray();
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
        return view('core::livewire.transaction-charges-component')
            ->layout('core::layouts.app');
    }
}
