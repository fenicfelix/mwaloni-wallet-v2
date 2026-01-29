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

    public ?int $formId = null;

    public array $formIds = [];

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

    public function rules()
    {
        $rules =  [
            'formData.account_number' => 'required',
            'formData.requested_amount' => 'required|min:0',
        ];

        return $rules;
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

    public function queryStatusAll()
    {
        $transactions = Transaction::with(['account'])->where("status", TransactionStatus::SUBMITTED)->get();
        if (sizeof($transactions) > 0) {
            foreach ($transactions as $transaction) {
                //Check status only for Daraja
                if ($transaction->account->account_type_id == 1) ProcessDarajaPaymentStatusCheck::dispatch($transaction->id);
            }
            $this->notify(sizeof($transactions) . " transactions status being requested.", "success");
        } else {
            $this->notify("No request made.", "warning");
        }
        $this->resetValues();
        $this->list = true;
    }

    #[On('viewFunction')]
    public function viewFunction($form_id)
    {
        $this->resetValues();
        $this->list = false;
        $this->view = true;
        $this->transaction = app(TransactionRepository::class)->find($form_id);
    }

    #[On('editFunction')]
    public function editFunction($form_id = NULL)
    {
        $this->resetValues();
        $this->edit = true;
        $this->transaction = app(TransactionRepository::class)->find($form_id);
        $this->isSuccessful = $this->transaction->status == TransactionStatus::SUCCESS;
        if ($this->transaction) {
            $this->formData['account_number'] = $this->transaction->account_number;
            $this->formData['requested_amount'] = $this->transaction->requested_amount;
            $this->formData['status'] = $this->transaction->status;
        }
    }

    #[On('bulkRetry')]
    public function bulkRetry(?array $formIds)
    {
        if (!$formIds) {
            $this->notify("No transactions selected.", "warning");
            return;
        }

        $this->formIds = array_map('intval', $formIds);
        $this->confirm(
            'Confirm Action',
            'Are you sure you want to retry the selected transactions?',
            'warning',
            'Yes, Retry',
            'confirmedBulkRetry'
        );
    }

    #[On('confirmedBulkRetry')]
    public function confirmedBulkRetry()
    {
        foreach ($this->formIds as $formId) {
            $this->completeRetry($formId);
        }
    }

    #[On('retryPayment')]
    public function retryPayment($form_id)
    {
        $this->formId = (int) $form_id;
        $this->confirm(
            'Confirm Action',
            'Are you sure you want to retry this transaction?',
            'warning',
            'Yes, Retry',
            'confirmedRetryPayment'
        );
    }

    #[On('confirmedRetryPayment')]
    public function confirmedRetryPayment()
    {
        $this->completeRetry($this->formId);

        $this->resetValues();
        $this->dispatch('refreshDatatable');
    }

    private function completeRetry($formId)
    {
        $this->transaction = app(TransactionRepository::class)->find($formId);
        if ($this->transaction->status == TransactionStatus::FAILED || ($this->transaction->status == TransactionStatus::SUBMITTED && getElapsedTime($this->transaction->requested_on) > 120)) {
            $balance = ($this->transaction->account->utility_balance - ($this->transaction->disbursed_amount + $this->transaction->account->revenue));

            if ($balance < 0) $this->notify("Insufficient Balance. Please reload the account and retry.", "error");
            else {
                $retry = app(TransactionRepository::class)->retry($this->transaction->id);
                if ($retry === null) {
                    $this->notify("Unable to retry this transaction..", "error");
                    $this->resetValues();
                    $this->dispatch('refreshDatatable');
                    return;
                }
                $this->notify("The retry request has been sent.", "success");
            }
        } else {
            $this->notify($this->transaction->order_number . " is still being processed.", "warning");
        }
    }

    #[On('paidOffline')]
    public function paidOffline($form_id)
    {
        $this->resetValues();
        $this->transaction = app(TransactionRepository::class)->find($form_id);
        $this->list = false;
        $this->pay_offline = true;
    }

    #[On('markAsCompleted')]
    public function markAsCompleted($form_id)
    {
        $this->formId = $form_id;
        $this->confirm(
            'Confirm Action',
            'Are you sure you want to mark this transaction as completed?',
            'warning',
            'Yes, Mark As Completed',
            'confirmedMarkAsCompleted'
        );
    }

    #[On('confirmedMarkAsCompleted')]
    public function confirmedMarkAsCompleted()
    {
        $transactionRepository = app(TransactionRepository::class);
        $transaction = $transactionRepository->find($this->formId);
        if ($transaction) {
            $transactionRepository->update($transaction->id, [
                'completed_at' => date('Y-m-d H:i:s'),
                'status' => TransactionStatus::COMPLETED,
                'status_message' => 'Transaction marked as completed manually.'
            ]);
            $this->notify("Transaction marked as completed.", "success");
        } else {
            $this->notify("Transaction not found.", "warning");
        }

        $this->resetValues();
        $this->dispatch('refreshDatatable');
    }

    #[On('queryMultipleStatus')]
    public function queryMultipleStatus($formIds)
    {
        $this->formIds = array_map('intval', $formIds);
        $this->confirm(
            'Confirm Action',
            'Are you sure you want to query the status of these transactions?',
            'warning',
            'Yes, Query Status',
            'confirmedQueryMultipleStatus'
        );
    }

    #[On('confirmedQueryMultipleStatus')]
    public function confirmedQueryMultipleStatus() {
        foreach ($this->formIds as $formId) {
            $this->queryStatus($formId);
        }
    }

    #[On('queryStatus')]
    public function queryStatus($form_id)
    {
        $transaction = app(TransactionRepository::class)->find($form_id);
        if (!$transaction) {
            $this->notify("Transaction not found.", "warning");
            return;
        }
        //Check status only for Daraja
        if ($transaction->account->account_type_id == 1) ProcessDarajaPaymentStatusCheck::dispatch($transaction->id);
        $this->notify("Your request has been submitted.", "success");
        $this->dispatch('refreshDatatable');
    }

    #[On('confirmedQueryStatus')]
    public function confirmedQueryStatus() {}

    public function backAction()
    {
        $this->resetValues();
    }

    public function resetValues()
    {
        $this->reset('view', 'edit', 'list', 'pay_offline', 'transaction', 'formId', 'formData');
    }

    public function render()
    {
        return view('core::livewire.transactions-component')
            ->layout('core::layouts.app');
    }
}
