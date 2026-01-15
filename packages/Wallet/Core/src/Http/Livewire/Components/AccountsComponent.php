<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use App\Jobs\Jenga\QueryJengaBalance;
use App\Jobs\Ncba\QueryNcbaBalance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Http\Traits\WalletEvents;
use Wallet\Core\Jobs\FetchAccountBalance;
use Wallet\Core\Jobs\ProcessPayment;
use Wallet\Core\Models\Account;
use Wallet\Core\Models\AccountType;
use Wallet\Core\Models\Currency;
use Wallet\Core\Models\PaymentChannel;
use Wallet\Core\Models\Transaction;

class AccountsComponent extends Component
{
    use WalletEvents, MwaloniWallet;

    public ?int $editId = null;

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
        $this->editId = null;
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

        $this->account_managers = User::get();

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
        $this->editId = $id;
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
        $this->editId = $id;
        $this->add = false;
        $this->editWithheldAmount = true;
        $this->account = Account::where("id", $id)->first();
        $this->form = $this->account->toArray();
    }

    public function saveWithheldAmount()
    {
        $this->validate();

        $account = Account::where("id", $this->editId)->first();
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
        $this->editId = $id;
        $this->account = Account::with(['accountType'])->where("id", $this->editId)->first();
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

        $this->validate();

        $paymentChannel = PaymentChannel::find($this->cashout_form['channel_id'])->first();
        $transaction_charges = get_transaction_charges($this->cashout_form['amount'], $paymentChannel->id);
        $key_block = sha1($this->cashout_form['amount'] . $this->cashout_form['account_number'] . $this->editId . $this->cashout_form['channel_id'] . date('Ymd'));

        $transaction = DB::transaction(
            function () use ($key_block, $paymentChannel, $transaction_charges) {

                $request = [
                    "type" => "revenue",
                    "id" => $this->editId,
                    "account_number" => $this->cashout_form['account_number'],
                    "account_name" => $this->cashout_form['account_name'],
                    "channel_id" => $this->cashout_form['channel_id'],
                    "account_reference" => $this->cashout_form['account_reference'] ?? NULL,
                    "amount" => $this->cashout_form['amount'],
                ];

                $orderNumber = generate_order_number(9);
                $payload = $this->generate_payload($paymentChannel, $request, $this->cashout_form['amount']);
                $transaction = Transaction::query()->create([
                    "identifier" => generate_identifier(),
                    "account_name" => $this->cashout_form['account_name'],
                    "account_number" => $this->cashout_form['account_number'],
                    'account_reference' => $this->cashout_form['account_reference'] ?? null,
                    "requested_amount" => $this->cashout_form['amount'],
                    "disbursed_amount" => $this->cashout_form['amount'],
                    "key_block" => $key_block,
                    "reference" => $payload["reference"] ?? date('ymdhis'),
                    "description" => "Revenue Cashout",
                    "account_id" => $this->editId,
                    "channel_id" => $paymentChannel->id,
                    "service_id" => NULL, //Check this out
                    "type_id" => 8,
                    "order_number" => $orderNumber,
                    "status_id" => "6",
                    "system_charges" => 0,
                    "sms_charges" => 0,
                    "revenue" => 0,
                    "transaction_charges" => $transaction_charges,
                    "requested_by" => Auth::id(),
                    "requested_on" => date('Y-m-d H:i:s'),
                    "transaction_date" => date('Y-m-d H:i:s'),
                    "payment_channel_id" => $paymentChannel->id,
                ]);

                $transaction->payload()->create([
                    "raw_request" => json_encode($request),
                    "trx_payload" => json_encode($payload)
                ]);

                if (!$transaction) return false;

                //Add to revenue
                $this->account->revenue -= $this->cashout_form['amount'];
                if (!$this->account->save()) return false;

                return $transaction;
            },
            2
        );

        if ($transaction) {
            ProcessPayment::dispatch($transaction->id, $paymentChannel->slug)->onQueue('process-payments');
            $this->notify("Cashout request has been placed successfully", "success");
            $this->resetVariables();
        } else {
            $this->notify("Cashout request has not been placed.", "error");
        }
    }

    public function store()
    {
        $this->validate();

        $userId = Auth::id();

        $transaction = DB::transaction(function () use ($userId) {

            $this->form["updated_by"] = $userId;
            $this->form['updated_at'] = date('Y-m-d H:i:s');
            if ($this->editId) {
                $account = Account::where("id", $this->editId)->update($this->form);
            } else {
                $this->form['identifier'] = generate_identifier();
                $this->form["added_by"] = $userId;
                $this->form['created_at'] = date('Y-m-d H:i:s');
                $account = Account::create($this->form);
            }

            if (!$account) return false;

            return true;
        }, 2);

        $task = ($this->editId) ? "updated" : "created";

        if ($transaction) {
            $this->notify("Account has been " . $task . " successfully", "success");
            $this->resetVariables();
        } else {
            $this->notify("Account could not be " . $task . ". Please try again later.", "error");
        }
    }
    
    public function render()
    {
        return view('core::livewire.accounts-component')
            ->layout('core::layouts.app');
    }
}