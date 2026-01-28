<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Models\Account;
use Wallet\Core\Models\AccountType;
use Wallet\Core\Models\Currency;
use Wallet\Core\Models\PaymentChannel;
use Livewire\Attributes\On;
use Wallet\Core\Repositories\AccountRepository;
use Wallet\Core\Services\AccountBalanceService;
use Wallet\Core\Services\CashoutService;

class AccountsComponent extends Component
{
    use NotifyBrowser, MwaloniWallet;

    public ?int $formId = null;

    public array $formData = [];

    public array $cashoutFormData = [];

    public ?string $content_title = "Accounts Manager";

    public bool $add = false;

    public bool $cashout = false;

    public bool $editWithheldAmount = false;

    public ?Collection $payment_channels, $account_types, $currencies;

    public ?Collection $account_managers;

    public ?Account $account = null;

    public ?float $max_cashout_amount = null;

    public function mount()
    {
        $this->initializeVariables();
    }

    public function initializeVariables()
    {
        $this->account_managers = User::orderBy("first_name", "ASC")->get();
        // concatenate first_name and last_name
        $this->account_managers = $this->account_managers->mapWithKeys(function ($manager) {
            return [
                $manager->id => trim("{$manager->first_name} {$manager->last_name}"),
            ];
        });

        $this->formData = [
            "account_type_id" => "",
            "country_name" => "Kenya",
            "country_code" => "KE"
        ];

        $this->add = false;

        // $this->payment_channels = PaymentChannel::get();
        $this->account_types = AccountType::get();
        $this->currencies = Currency::get();
    }

    public function rules()
    {
        $rules = [];

        if ($this->editWithheldAmount) {
            $rules = [
                'formData.withheld_amount' => 'required|numeric'
            ];
        } else if ($this->cashout) {
            $rules =  [
                'cashoutFormData.account_number' => 'required',
                'cashoutFormData.channel_id' => 'required|exists:payment_channels,slug',
                'cashoutFormData.amount' => 'required',
                'cashoutFormData.account_reference' => 'required_if:channel_id,==,daraja-paybill',
            ];
        } else if ($this->add) {
            $rules = [
                'formData.currency_id' => 'required',
                'formData.name' =>  'required',
            ];

            if ($this->formData["account_type_id"] != "1") {
                $rules['formData.bank_code'] = 'required';
                $rules['formData.branch_code'] = 'required';
                $rules['formData.country_name'] = 'required';
            }
        }

        return $rules;
    }

    public function store()
    {
        if (!$this->customValidate()) return;

        if ($this->formId === null) {
            $account = app(AccountRepository::class)->create($this->formData);
        } else {
            $account = app(AccountRepository::class)->update($this->formId, $this->formData);
        }

        if (!$account) {
            $this->notify('Operation not successful. Please try again.', 'error');
            return;
        }

        $this->notify('Operation successful. Please try again.', 'success');
        $this->resetValues();
    }

    public function submitCashout()
    {
        if (!$this->customValidate()) return;
        $cashoutTransaction = app(CashoutService::class)->processCashout($this->cashoutFormData, $this->formId);
        if ($cashoutTransaction) {
            $this->notify("Cashout request has been placed successfully", "success");
            $this->resetValues();
        } else {
            $this->notify("Cashout request has not been placed.", "error");
        }
    }

    public function saveWithheldAmount()
    {
        if (!$this->customValidate()) return;

        $account = app(AccountRepository::class)->updateWithheldAmount($this->formId, (float) $this->formData['withheld_amount']);
        if (!$account) {
            $this->notify("Withheld amount could not be updated.", "error");
            return;
        }

        $this->notify("Withheld amount updated successfully.", "success");
        $this->resetValues();
    }

    public function addFunction()
    {
        $this->resetValues();
        $this->content_title = "Add Account";
        $this->add = true;
    }

    #[On("editFunction")]
    public function editFunction($id)
    {
        $this->resetValues();
        $this->formId = $id;
        $this->add = true;
        $this->formData = app(AccountRepository::class)->find($id)->toArray();

        $this->content_title = "Update Account";
        unset($this->formData['float']);
    }

    #[On("cashoutFunction")]
    public function cashoutFunction($id)
    {
        $this->resetValues();

        $this->formId = $id;
        $this->account = Account::with(['accountType'])->where("id", $this->formId)->first();
        if (!$this->account) {
            $this->notify("Account not found.", "error");
            return;
        }

        $this->payment_channels = PaymentChannel::where('account_type_id', $this->account->account_type_id)->get();
        $this->max_cashout_amount = (float) $this->account?->revenue;

        if ($this->max_cashout_amount <= 0) {
            $this->notify("No revenue available for cashout.", "error");
            return;
        }

        $this->content_title = "Cashout";
        $this->cashout = true;
    }

    #[On("deactivateAccount")]
    public function deactivateAccount($id, $task)
    {
        $this->formId = $id;
        $this->confirm(
            'Confirm Action',
            'Are you sure you want to ' . $task . ' this account?',
            'warning',
            'Yes, ' . ucfirst($task),
            'confirmedDeactivateAccount'
        );
    }

    #[On("confirmedDeactivateAccount")]
    public function confirmedDeactivateAccount()
    {
        $update = app(AccountRepository::class)->activateDeactivate($this->formId);
        if ($update) {
            $this->notify("The account has been deactivated.", "success");
            $this->dispatch('refreshDatatable');
        } else {
            $this->notify("The account could not be deactivated.", "error");
        }
    }

    #[On("fetchBalance")]
    public function fetchBalance($id)
    {
        $fetchBalance = app(AccountBalanceService::class)->fetchBalance($id);
        if ($fetchBalance) {
            $this->notify("Balance enquiry made successfully.", "success");
        } else {
            $this->notify("Balance enquiry failed.", "error");
        }
    }

    #[On("updateWithheldAmount")]
    public function updateWithheldAmount($id)
    {
        $this->resetValues();
        $this->content_title = "Update Withheld Amount";
        $this->formId = $id;
        $this->editWithheldAmount = true;
        $this->formData = app(AccountRepository::class)->find($this->formId)->toArray();
    }

    public function customValidate()
    {
        try {
            $this->validate();
        } catch (\Throwable $th) {
            $this->notify(
                'There were validation errors. Please check your data and try again.',
                'error'
            );
            return false;
        }
        return true;
    }

    public function backAction()
    {
        $this->resetValues();
    }

    private function resetValues()
    {
        $this->reset("content_title", "formId", "formData", "add", "editWithheldAmount", "cashout", "cashoutFormData");
        $this->formData = [
            "account_type_id" => "",
            "country_name" => "Kenya",
            "country_code" => "KE"
        ];
        $this->content_title = "Accounts";
    }

    public function render()
    {
        return view('core::livewire.accounts-component')
            ->layout('core::layouts.app');
    }
}
