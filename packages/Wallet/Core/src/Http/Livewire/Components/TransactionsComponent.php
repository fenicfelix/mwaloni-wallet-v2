<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Wallet\Core\Http\Traits\WalletEvents;
use Wallet\Core\Models\Status;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Models\TransactionMetric;

class TransactionsComponent extends Component
{
    use WalletEvents;

    public ?string $content_title;

    public bool $list = true;

    public bool $edit = false;

    public bool $view = false;

    public bool $pay_offline = false;

    public ?Transaction $transaction;

    public Collection $statuses;

    public ?TransactionMetric $analytics = null;

    public ?string $receipt_number, $account_name;

    public ?string $confirm_message = "";

    public ?User $user;

    //Edit parameters
    public ?string $account_number, $requested_amount, $status_id;

    public ?array $selectedItems;

    public $listeners = [
        'viewFunction',
        'editFunction',
        'retryPayment',
        'paidOffline',
        'queryStatus',
        'reverse'
    ];

    public function mount()
    {
        $this->initializeValues();
        $this->resetView();
        $this->list = true;
    }

    private function initializeValues()
    {
        $this->content_title = "Transactions Manager";
        $this->user = Auth::user();
        $this->statuses = Status::get();
        // if ($this->user->hasRole(['Admin', 'Technical'])) {
            // count transactions.id
            $this->analytics = TransactionMetric::first();
        // }
    }

    public function resetView()
    {
        $this->view = false;
        $this->edit = false;
        $this->list = false;
        $this->pay_offline = false;
        $this->transaction = NULL;
    }

    public function viewFunction($form_id)
    {
        $this->resetView();
        $this->view = true;
        $this->transaction = Transaction::where("id", $form_id)->first();
    }

    public function editFunction($form_id = NULL)
    {
        $this->resetView();
        $this->edit = true;
        $this->transaction = Transaction::where("id", $form_id)->first();
        if ($this->transaction) {
            $this->account_number = $this->transaction->account_number;
            $this->requested_amount = $this->transaction->requested_amount;
            $this->status_id = $this->transaction->status_id;
        }
    }

    public function cancelEdit()
    {
        $this->resetView();
        $this->list = true;
    }

    public function retryPayment($form_id)
    {
        $this->transaction = Transaction::with("payload")->where("id", $form_id)->first();
        if ($this->transaction->status_id == 3 || ($this->transaction->status_id == 1 && get_elapsed_time($this->transaction->requested_on) > 120)) {
            $balance = ($this->transaction->account->utility_balance - ($this->transaction->disbursed_amount + $this->transaction->account->revenue));

            if ($balance < 0) $this->notify("Insufficient Balance. Please reload the account and retry.", "error");
            else {
                $reference = date('ymdHs') . rand(0, 99);
                $trx_payload = json_decode($this->transaction->trx_payload);
                $trx_payload->reference = $reference;
                $this->transaction->reference = $reference;
                $this->transaction->status_id = 1;
                $this->transaction->save();

                $this->transaction->payload->update([
                    "trx_payload" => json_encode($trx_payload)
                ]);


                ProcessPayment::dispatch($this->transaction->id, $this->transaction->paymentChannel->slug)->onQueue('process-payments');
                $this->notify("The retry request has been sent.", "success");
            }
        } else {
            $this->notify($this->transaction->order_number . " is still being processed.", "warning");
        }

        $this->transaction = NULL;
    }

    public function paidOffline($form_id)
    {
        $this->resetView();
        $this->transaction = Transaction::where("id", $form_id)->first();
        $this->pay_offline = true;
    }

    public function submitOfflinePayment()
    {
        if ($this->transaction) {
            $this->transaction->receipt_number = $this->receipt_number;
            $this->transaction->account_name = $this->account_name;
            $this->transaction->status_id = 2;
            $this->transaction->completed_at = date('Y-m-d H:i:s');

            if ($this->transaction->save()) {
                $this->notify("The transaction has been updated.", "success");
                $this->resetView();
                $this->list = true;
            } else {
                $this->notify("The transaction has not been updated.", "error");
            }
        }
    }

    public function queryStatus($form_id)
    {
        $transaction = Transaction::with(["account"])->where("id", "=", $form_id)->first();
        if (!$transaction) $this->notify("Transaction not found.", "warning");
        else {
            //Check status only for Daraja
            if ($transaction->account->account_type_id == 1) ProcessDarajaPaymentStatusCheck::dispatch($transaction->identifier);
            $this->notify("The transaction has been updated.", "success");
            $this->resetView();
            $this->list = true;
        }
    }

    public function queryStatusAll()
    {
        $transactions = Transaction::with(['account'])->where("status_id", 1)->get();
        if (sizeof($transactions) > 0) {
            foreach ($transactions as $transaction) {
                //Check status only for Daraja
                if ($transaction->account->account_type_id == 1) ProcessDarajaPaymentStatusCheck::dispatch($transaction->identifier);
            }
            $this->notify(sizeof($transactions) . " transactions status being requested.", "success");
        } else {
            $this->notify("No request made.", "warning");
        }
        $this->resetView();
        $this->list = true;
    }

    public function reverse($form_id)
    {
        $transaction = Transaction::with(['account'])->where("id", $form_id)->first();
        dd($transaction);
    }

    public function rules()
    {
        $rules =  [
            'account_number' => 'required',
            'requested_amount' => 'required|min:0',
            'status_id' => 'required:exists,statuses,status_id',
        ];

        return $rules;
    }

    public function store()
    {
        if ($this->transaction) {
            $this->validate();
        }

        $this->transaction->account_number = $this->account_number;
        $this->transaction->status_id = $this->status_id;

        if ($this->transaction->requested_amount != $this->requested_amount) {
            $this->transaction->requested_amount = $this->requested_amount;
            $this->transaction->disbursed_amount = $this->requested_amount;
            if ($this->transaction->payload->trx_payload) {
                $payload = json_decode($this->transaction->payload?->trx_payload);
                $payload["amount"] = $this->requested_amount;
                $this->transaction->payload->trx_payload = json_encode($payload);
            }
        }

        if (!$this->transaction->save()) $this->notify("The transaction has not been updated.", "success");
        else {
            $this->notify("The transaction has been updated.", "success");
            $this->resetView();
            $this->list = true;
        }
    }
    
    public function render()
    {
        return view('core::livewire.transactions-component')
            ->layout('core::layouts.app');
    }
}