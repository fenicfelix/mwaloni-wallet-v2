<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\Account;
use Wallet\Core\Models\Client;
use Wallet\Core\Models\PaymentChannel;
use Wallet\Core\Models\Service;
use Illuminate\Support\Str;
use Wallet\Core\Jobs\ProcessPayment;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Repositories\ServiceRepository;
use Wallet\Core\Services\WithdrawService;

class ServicesComponent extends Component
{
    use NotifyBrowser, MwaloniWallet;

    public ?string $content_title = "Services Manager";

    public ?bool $add = false;

    public ?int $formId = null;

    public array $formData = [];

    public ?bool $withdraw = false;

    public ?Collection $clients = null;

    public ?Collection $accounts = null;

    public ?Collection $payment_channels = null;

    public ?array $withdraw_from = [];

    public ?Service $service = null;

    public ?float $max_amount = null;

    public $listeners = [
        'addFunction',
        'editFunction',
        'backToList',
        'withdrawCharges'
    ];

    public function mount()
    {
        $this->initializeValues();
    }

    public function updateUsername()
    {
        if (!$this->formId) $this->formData['username'] = Str::slug($this->formData['name']);
    }

    private function initializeValues()
    {
        $this->resetValues();
        $this->content_title = "Services Manager";

        $this->clients = Client::get();
        $this->accounts = Account::get();
        $this->payment_channels = PaymentChannel::get();
    }

    public function withdrawCharges($formId)
    {
        $this->formId = $formId;
        $this->resetValues();
        $this->withdraw = true;
        $this->service = Service::with(["account"])->where("id", $formId)->first();
        $this->max_amount = (($this->service->account->utility_balance + $this->service->account->working_balance) - $this->service->revenue);
    }

    public function rules()
    {
        $rules = [];

        if ($this->add) {
            $rules =  [
                'formData.name' => 'required',
                'formData.description' => 'required',
                'formData.client_id' => 'required:exists,clients,status_id',
                'formData.account_id' => 'required:exists,accounts,status_id',
                'formData.system_charges' => 'required',
                'formData.max_trx_amount' => 'required|min:2',
            ];
        } else if ($this->withdraw) {
            $rules = [
                'withdraw_from.account_number' => 'required',
                'withdraw_from.channel_id' => 'required|exists:payment_channels,slug',
                'withdraw_from.amount' => 'required',
                'withdraw_from.account_name' => 'required',
                'withdraw_from.account_reference' => 'required_if:channel_id,==,daraja-paybill',
            ];
        }

        return $rules;
    }

    public function updated()
    {
        $this->validate();
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

        if (isset($this->formData['password']) && $this->formData['password'] != "") {
            $this->formData['password'] = Hash::make($this->formData['password']);
        }

        if ($this->formId === null) {
            $service = app(ServiceRepository::class)->create($this->formData);
        } else {
            $service = app(ServiceRepository::class)->update($this->formId, $this->formData);
        }

        if (!$service) {
            $this->notify("Operation failed. Please try again.", "error");
            return;
        }

        $this->notify('Operation successful. Please try again.', 'success');
        $this->resetValues();
    }

    public function doWithdrawCash()
    {
        $transaction = app(WithdrawService::class)->processWithdrawal($this->service->id, $this->withdraw_from);
        if ($transaction) {
            ProcessPayment::dispatch($transaction->id, $transaction->payment_channel->slug)->onQueue('process-payments');
            $this->notify("Cashout is being processed.", "success");
            $this->resetValues();
        } else {
            $this->notify("Cashout failed. Please try again.", "error");
        }
    }

    public function addFunction()
    {
        $this->resetValues();
        $this->add = !$this->add;
        $this->formData['password'] = $this->generateRandomString('password');
    }

    public function editFunction($formId)
    {
        $this->resetValues();
        $this->add = true;
        $this->formId = $formId;
        $this->formData = Service::where("id", $this->formId)->first()->toArray();
        unset($this->formData['password']);
        unset($this->formData['created_at']);
        unset($this->formData['updated_at']);
    }

    public function backToList()
    {
        $this->resetValues();
    }

    public function backAction()
    {
        $this->resetValues();
    }

    public function resetValues()
    {
        $this->reset('add', 'formId', 'withdraw', 'service', 'max_amount', 'withdraw_from', 'formData');
    }

    public function render()
    {
        return view('core::livewire.services-component')
            ->layout('core::layouts.app');
    }
}
