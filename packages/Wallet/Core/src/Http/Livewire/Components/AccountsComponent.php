<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use App\Jobs\Jenga\QueryJengaBalance;
use App\Jobs\Ncba\QueryNcbaBalance;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Jobs\FetchAccountBalance;
use Wallet\Core\Jobs\ProcessPayment;
use Wallet\Core\Models\Account;
use Wallet\Core\Models\AccountType;
use Wallet\Core\Models\Currency;
use Wallet\Core\Models\PaymentChannel;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Services\PayloadGeneratorService;
use Illuminate\Support\Str;
use Wallet\Core\Repositories\AccountRepository;
use Wallet\Core\Services\CashoutService;

class AccountsComponent extends Component
{
    use NotifyBrowser, MwaloniWallet;

    public ?int $formId = null;

    public array $form = [];

    public ?string $content_title;

    public ?Account $account;

    public bool $add = false;

    public bool $cashout = false;

    public bool $editWithheldAmount = false;

    public ?array $cashout_form;

    public ?Collection $payment_channels, $account_types, $currencies;

    public ?Collection $account_managers;

    public $listeners = [
        "editFunction",
        "deactivateAccount",
        "fetchBalance",
        "cashoutFunction",
        "updateWithheldAmount"
    ];

    public function mount()
    {
        $this->initializeVariables();
    }

    private function resetVariables()
    {
        $this->content_title = "Accounts Manager";
        $this->editWithheldAmount = false;
        $this->add = false;
        $this->cashout = false;
        $this->formId = null;
        $this->account = new Account();

        $this->reset("form");

        $this->form = [
            "account_type_id" => "",
            "country_name" => "Kenya",
            "country_code" => "KE"
        ];
    }

    public function initializeVariables()
    {
        $this->content_title = "Accounts Manager";

        $this->account_managers = User::orderBy("first_name", "ASC")->get();
        // concatenate first_name and last_name
        $this->account_managers = $this->account_managers->mapWithKeys(function ($manager) {
            return [
                $manager->id => trim("{$manager->first_name} {$manager->last_name}"),
            ];
        });

        $this->form = [
            "account_type_id" => "",
            "country_name" => "Kenya",
            "country_code" => "KE"
        ];

        $this->add = false;

        $this->payment_channels = PaymentChannel::get();
        $this->account_types = AccountType::get();
        $this->currencies = Currency::get();
    }

    public function addFunction()
    {
        $this->resetVariables();
        $this->content_title = "Add Account";
        $this->add = true;
        // clear variables
    }

    public function editFunction($id)
    {
        $this->content_title = "Update Account";
        $this->formId = $id;
        $this->add = true;
        $this->editWithheldAmount = false;
        $this->account = Account::where("id", $id)->first();

        $this->form = $this->account->toArray();
        $this->form["bank_code"] = $this->account->bank_code;
        $this->form["branch_code"] = $this->account->branch_code;
    }

    public function backToList()
    {
        $this->content_title = "Accounts Manager";
        $this->add = false;
        $this->editWithheldAmount = false;
        $this->cashout = false;
    }

    public function deactivateAccount($id)
    {
        $account = Account::where("id", $id)->first();
        if ($account->active == "1") $account->active = "0";
        else $account->active = "1";

        $account->updated_by = Auth::id();
        if ($account->save()) $this->notify("The account status has been updated.", "success");
        else $this->notify("The account status has not been updated.", "error");
    }

    public function fetchBalance($id)
    {
        $account = Account::where("id", $id)->first();
        if ($account->account_type_id == 1) { //Daraja
            info('Fetching balance for daraja account');
            FetchAccountBalance::dispatch($account)->onQueue("fetch-balance");
        } else if ($account->account_type_id == 2) { //Jenga
            info('Fetching balance for jenga account');
            QueryJengaBalance::dispatch($account->id);
        } else {
            info('Fetching balance for ncba account');
            QueryNcbaBalance::dispatch($account->id);
        }

        $this->notify("Balance enquiry made successfully.", "success");

        return redirect()->route('accounts');
    }

    public function updateWithheldAmount($id)
    {
        $this->content_title = "Update Withheld Amount";
        $this->formId = $id;
        $this->add = false;
        $this->editWithheldAmount = true;
        $this->account = Account::where("id", $id)->first();
        $this->form = $this->account->toArray();
    }

    public function saveWithheldAmount()
    {
        $this->customValidate();

        $account = Account::where("id", $this->formId)->first();
        $account->withheld_amount = $this->form['withheld_amount'];
        $account->updated_by = Auth::id();

        if ($account->save()) {
            $this->resetVariables();
            $this->notify("Withheld amount updated successfully.", "success");
        } else {
            $this->notify("Withheld amount could not be updated.", "error");
        }
    }

    public function cashoutFunction($id)
    {
        $this->content_title = "Cashout";
        $this->add = false;
        $this->cashout = true;
        $this->formId = $id;
        $this->account = Account::with(['accountType'])->where("id", $this->formId)->first();
        $this->cashout_form = [
            'channel_id' => $this->account->channel_id
        ];
    }

    public function rules()
    {
        $rules = [];

        if ($this->editWithheldAmount) {
            $rules = [
                'form.withheld_amount' => 'required|numeric'
            ];
        } else {
            if ($this->cashout) {
                $rules =  [
                    'cashout_form.account_number' => 'required',
                    'cashout_form.channel_id' => 'required|exists:payment_channels,id',
                    'cashout_form.amount' => 'required',
                    'cashout_form.account_reference' => 'required_if:channel_id,==,daraja-paybill',
                ];
            } else if ($this->add) {
                $rules = [
                    'form.currency_id' => 'required',
                    'form.name' =>  'required',
                ];

                if ($this->form["account_type_id"] != "1") {
                    $rules['form.bank_code'] = 'required';
                    $rules['form.branch_code'] = 'required';
                    $rules['form.country_name'] = 'required';
                }
            }
        }

        return $rules;
    }

    public function doCashout()
    {
        $this->customValidate();

        $cashoutTransaction = app(CashoutService::class)->processCashout($this->cashout_form, $this->formId);

        if ($cashoutTransaction) {
            ProcessPayment::dispatch($cashoutTransaction->id, $cashoutTransaction->payment_channel->slug)->onQueue('process-payments');
            $this->notify("Cashout request has been placed successfully", "success");
            $this->resetVariables();
        } else {
            $this->notify("Cashout request has not been placed.", "error");
        }
    }

    public function store()
    {
        $this->customValidate();

        if($this->formId === null) {
            $account = app(AccountRepository::class)->create($this->form);
        } else {
            $account = app(AccountRepository::class)->update($this->formId, $this->form);
        }

        if (!$account) {
            $this->notify('Operation not successful. Please try again.', 'error');
            return;
        }

        $this->notify('Operation successful. Please try again.', 'success');
        $this->resetValues();
    }

    public function customValidate() {
        try {
            $this->validate();
        } catch (\Throwable $th) {
            $this->notify(
                'There were validation errors. Please check the form and try again.',
                'error'
            );
            return;
        }
    }

    public function render()
    {
        return view('core::livewire.accounts-component')
            ->layout('core::layouts.app');
    }
}
