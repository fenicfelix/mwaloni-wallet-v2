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
use Wallet\Core\Http\Traits\WalletEvents;
use Wallet\Core\Models\Account;
use Wallet\Core\Models\Client;
use Wallet\Core\Models\PaymentChannel;
use Wallet\Core\Models\Service;
use Illuminate\Support\Str;
use Wallet\Core\Jobs\ProcessPayment;
use Wallet\Core\Models\Transaction;

class ServicesComponent extends Component
{
    use WalletEvents, MwaloniWallet;

    public ?string $content_title = "Services Manager";

    public ?bool $add = false;

    public ?int $editId;

    public array $form = [];

    public ?bool $withdraw = false;

    public ?User $user;

    public ?Collection $clients = null;

    public ?Collection $accounts = null;

    public ?Collection $payment_channels = null;

    public array $withdraw_from = [];

    public string $country_code = '';

    public string $bank_code = '';

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
        if (!$this->editId) $this->form['username'] = Str::slug($this->form['name']);
    }

    private function initializeValues()
    {
        $this->resetView();
        $this->content_title = "Services Manager";

        $this->clients = Client::get();
        $this->accounts = Account::get();
        $this->user = Auth::user();
        $this->payment_channels = PaymentChannel::get();
    }

    public function resetView()
    {
        $this->add = false;
        $this->withdraw = false;
        $this->editId = NULL;

        $this->reset('max_amount', 'withdraw_from', 'form');

        $this->service = null;

        $this->form = [
            'active' => 1,
        ];
    }

    public function addFunction()
    {
        $this->resetView();
        $this->add = true;
        $this->form['password'] = $this->generateRandomString('password');
    }

    public function editFunction($formId)
    {
        $this->resetView();
        $this->add = true;
        $this->editId = $formId;
        $this->form = Service::where("id", $this->editId)->first()->toArray();
        unset($this->form['password']);
        unset($this->form['created_at']);
        unset($this->form['updated_at']);
    }

    public function backToList()
    {
        $this->resetView();
    }

    public function withdrawCharges($editId)
    {
        $this->editId = $editId;
        $this->resetView();
        $this->withdraw = true;
        $this->service = Service::with(["account"])->where("id", $editId)->first();
        $this->max_amount = (($this->service->account->utility_balance + $this->service->account->working_balance) - $this->service->revenue);
    }

    public function generateNewPassword()
    {
        if ($this->form) {
            $this->form['password'] = $this->generateRandomString('password');
        } else {
            $this->notify("Missing service details.", "warning");
        }
    }

    public function rules()
    {
        $rules = [];

        if ($this->add) {
            $rules =  [
                'form.name' => 'required',
                'form.description' => 'required',
                'form.client_id' => 'required:exists,clients,status_id',
                'form.account_id' => 'required:exists,accounts,status_id',
                'form.system_charges' => 'required',
                'form.max_trx_amount' => 'required|min:2',
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
        $this->validate();

        $trx = DB::transaction(function () {
            if (isset($this->form['password']) && $this->form['password'] != "") {
                $this->form['password'] = Hash::make($this->form['password']);
            }
            if ($this->editId) {
                $this->form['updated_by'] = $this->user->id;
                $this->form['updated_at'] = date('Y-m-d H:i:s');
                $update = Service::where("id", $this->editId)->update($this->form);
                if (!$update) return false;
                return true;
            } else {
                $this->form['identifier'] = generate_identifier();
                $this->form['added_by'] = $this->user->id;
                $this->form['updated_by'] = $this->user->id;
                $this->form['active'] = 1;
                $service = Service::query()->create($this->form);

                if (!$service) return false;

                // Generate Service ID
                $service_id = str_pad($service->id, 5, "0", STR_PAD_LEFT);
                $service->service_id = "SRV-" . $service_id;
                if (!$service->save()) return false;
            }

            return true;
        }, 2);

        if ($trx) {
            if ($this->editId) $this->notify("The service has been updated.", "success");
            else $this->notify("The service has been added.", "success");
            $this->resetView();
        } else {
            if ($this->editId) $this->notify("The service was not updated. Please try again.", "error");
            else $this->notify("The service could not be added. Please try again.", "error");
        }
    }

    public function doWithdrawCash()
    {
        $key_block = sha1($this->withdraw_from['amount'] . $this->withdraw_from['account_number'] . $this->editId . $this->withdraw_from['channel_id'] . date('Ymd'));
        $paymentChannel = PaymentChannel::where("slug", $this->withdraw_from['channel_id'])->first();
        $transaction_charges = get_transaction_charges($this->withdraw_from['amount'], $paymentChannel->id);

        $request = [
            "type" => "withdraw",
            "id" => $this->service->id,
            "account_number" => $this->withdraw_from['account_number'],
            "account_name" => $this->withdraw_from['account_name'],
            "channel_id" => $this->withdraw_from['channel_id'],
            "account_reference" => $this->withdraw_from['account_reference'] ?? null,
            "amount" => $this->withdraw_from['amount'],
        ];

        $transaction = DB::transaction(
            function () use ($request, $key_block, $paymentChannel, $transaction_charges) {
                $payload = $this->generate_payload($paymentChannel, $request, $this->amount);
                if (!$payload) return false;

                $transaction = Transaction::query()->create([
                    "identifier" => generate_identifier(),
                    "account_name" => $this->withdraw_from['account_name'],
                    "account_number" => $this->withdraw_from['account_number'],
                    'account_reference' => $this->withdraw_from['account_reference'] ?? null,
                    "requested_amount" => $this->withdraw_from['amount'],
                    "disbursed_amount" => $this->withdraw_from['amount'],
                    "key_block" => $key_block,
                    "reference" => $payload["reference"],
                    "description" => "Service Charge",
                    "account_id" => $this->service->account_id,
                    "channel_id" => $paymentChannel->id,
                    "service_id" => $this->service->id,
                    "type_id" => 5,
                    "order_number" => generate_order_number(5),
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

                return $transaction;
            },
            2
        );

        if ($transaction) {
            ProcessPayment::dispatch($transaction->id, $paymentChannel->slug)->onQueue('process-payments');
            $this->notify("Cashout is being processed.", "success");
            $this->resetView();
        } else {
            $this->notify("Cashout failed. Please try again.", "error");
        }
    }

    public function render()
    {
        return view('core::livewire.services-component')
            ->layout('core::layouts.app');
    }
}