<?php

declare(strict_types=1);

namespace Wallet\Core\Http\Livewire\Components;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Traits\NotifyBrowser;
use Wallet\Core\Jobs\Daraja\ProcessDarajaPaymentStatusCheck;
use Wallet\Core\Jobs\ProcessPayment;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Models\TransactionMetric;
use Wallet\Core\Repositories\TransactionRepository;

class TransactionsComponent extends Component
{
    use NotifyBrowser;

    public ?string $content_title;

    public bool $list = true;

    public bool $edit = false;

    public bool $view = false;

    public bool $pay_offline = false;

    public ?array $formData = [];

    public bool $isSuccessful = false;

    public ?Transaction $transaction;

    public ?TransactionMetric $analytics = null;

    public ?string $confirm_message = "";

    public ?User $user;

    public ?array $selectedItems;

    public function mount()
    {
        $this->initializeValues();
        $this->resetValues();
        $this->list = true;
    }

    private function initializeValues()
    {
        $this->content_title = "Transactions Manager";
        $this->user = Auth::user();
        // if ($this->user->hasRole(['Admin', 'Technical'])) {
        // count transactions.id
        $this->analytics = TransactionMetric::first();
        // }
    }

    public function resetValues()
    {
        $this->view = false;
        $this->edit = false;
        $this->list = false;
        $this->pay_offline = false;
        $this->transaction = NULL;
    }

    #[On('viewFunction')]
    public function viewFunction($form_id)
    {
        $this->resetValues();
        $this->view = true;
        $this->transaction = Transaction::where("id", $form_id)->first();
    }

    #[On('editFunction')]
    public function editFunction($form_id = NULL)
    {
        $this->resetValues();
        $this->edit = true;
        $this->transaction = Transaction::where("id", $form_id)->first();
        $this->isSuccessful = $this->transaction->status == TransactionStatus::SUCCESS;
        if ($this->transaction) {
            $this->formData['account_number'] = $this->transaction->account_number;
            $this->formData['requested_amount'] = $this->transaction->requested_amount;
            $this->formData['status'] = $this->transaction->status;
        }
    }

    public function cancelEdit()
    {
        $this->resetValues();
        $this->list = true;
    }

    #[On('retryPayment')]
    public function retryPayment($form_id)
    {
        $this->transaction = Transaction::with("payload")->where("id", $form_id)->first();
        if ($this->transaction->status == TransactionStatus::FAILED || ($this->transaction->status == TransactionStatus::SUBMITTED && getElapsedTime($this->transaction->requested_on) > 120)) {
            $balance = ($this->transaction->account->utility_balance - ($this->transaction->disbursed_amount + $this->transaction->account->revenue));

            if ($balance < 0) $this->notify("Insufficient Balance. Please reload the account and retry.", "error");
            else {
                $reference = date('ymdHs') . rand(0, 99);
                $trx_payload = json_decode($this->transaction->trx_payload);
                $trx_payload->reference = $reference;
                $this->transaction->reference = $reference;
                $this->transaction->status = TransactionStatus::SUBMITTED;
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

    #[On('paidOffline')]
    public function paidOffline($form_id)
    {
        $this->resetValues();
        $this->transaction = Transaction::where("id", $form_id)->first();
        $this->pay_offline = true;
    }

    public function completePaymentOffline()
    {
        if ($this->transaction) {
            $this->formData['status'] = TransactionStatus::SUCCESS;
            $this->formData['completed_at'] = date('Y-m-d H:i:s');
            $this->formData['status_message'] = 'Payment completed offline';
            $update = app(TransactionRepository::class)->update($this->transaction->id, $this->formData);
            if ($update) {
                $this->notify("The transaction has been updated.", "success");
                $this->resetValues();
                $this->list = true;
            } else {
                $this->notify("The transaction has not been updated.", "error");
            }
        }
    }

    #[On('queryStatus')]
    public function queryStatus($form_id)
    {
        $transaction = Transaction::with(["account"])->where("id", "=", $form_id)->first();
        if (!$transaction) $this->notify("Transaction not found.", "warning");
        else {
            //Check status only for Daraja
            if ($transaction->account->account_type_id == 1) ProcessDarajaPaymentStatusCheck::dispatch($transaction->identifier);
            $this->notify("The transaction has been updated.", "success");
            $this->resetValues();
            $this->list = true;
        }
    }

    public function queryStatusAll()
    {
        $transactions = Transaction::with(['account'])->where("status", TransactionStatus::SUBMITTED)->get();
        if (sizeof($transactions) > 0) {
            foreach ($transactions as $transaction) {
                //Check status only for Daraja
                if ($transaction->account->account_type_id == 1) ProcessDarajaPaymentStatusCheck::dispatch($transaction->identifier);
            }
            $this->notify(sizeof($transactions) . " transactions status being requested.", "success");
        } else {
            $this->notify("No request made.", "warning");
        }
        $this->resetValues();
        $this->list = true;
    }

    public function rules()
    {
        $rules =  [
            'formData.account_number' => 'required',
            'formData.requested_amount' => 'required|min:0',
        ];

        return $rules;
    }

    public function render()
    {
        return view('core::livewire.transactions-component')
            ->layout('core::layouts.app');
    }
}
