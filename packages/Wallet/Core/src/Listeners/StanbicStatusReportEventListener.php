<?php

namespace Wallet\Core\Listeners;

use Akika\LaravelStanbic\Enums\GroupStatusType;
use Akika\LaravelStanbic\Events\Pain00200103ReportReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Http\Traits\MwaloniWallet;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Repositories\TransactionRepository;

class StanbicStatusReportEventListener implements ShouldQueue
{
    use MwaloniWallet;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param  Pain00200103ReportReceived  $event
     * @return void
     */
    public function handle(Pain00200103ReportReceived $event)
    {
        info("Received Pain00200103ReportReceived event");
        info($event->report->originalGroupInfoAndStatus->originalMessageId);
        info($event->report->originalGroupInfoAndStatus->groupStatus->value); // enums: ACSP, RJCT, PDNG, PARTIAL

        $transaction = Transaction::with(['account', 'payload'])->where('message_id', $event->report->originalGroupInfoAndStatus->originalMessageId)->first();
        if ($transaction) {
            $successMessage = "";
            $account = $transaction->account;
            $payloadData = [
                'raw_callback' => json_encode($event->report->xml)
            ];
            $updateData = [];
            switch ($event->report->originalGroupInfoAndStatus->groupStatus) {
                case GroupStatusType::Acsp:
                    $updateData = [
                        'status' => TransactionStatus::SUCCESS,
                        'result_description' => $event->report->originalGroupInfoAndStatus->statusReasonInfos->additionalInfos->first(),
                        'receipt_number' => $event->report->groupHeader->messageId,
                        'completed_at' => $event->report->groupHeader->creationDateTime
                    ];
                    if ($transaction->account_name == "") {
                        $rawRequest = json_decode($transaction->payload->raw_request, true);
                        $additionalData['account_name'] = $rawRequest['account_name'] ?? '';
                    }

                    $successMessage = getOption("SMS-B2C-SUCCESS-MESSAGE");
                    $successMessage = str_replace("{receipt_number}", $event->report->groupHeader->messageId, $successMessage);
                    $successMessage = str_replace("{amount}", $transaction->disbursed_amount, $successMessage);
                    $successMessage = str_replace("{to}", $transaction->account_number . ' ' . $transaction->account_name, $successMessage);
                    $successMessage = str_replace("{datetime}", date('d m, Y h:1 A', strtotime($event->report->groupHeader->creationDateTime)), $successMessage);
                    $successMessage = (isset($transaction->service)) ? str_replace("{service}", $transaction->service->name, $successMessage) : str_replace("{service}", $transaction->account->name, $successMessage);

                    break;
                case GroupStatusType::Rjct:
                    $updateData = [
                        'status' => TransactionStatus::FAILED,
                        'result_description' => $event->report->originalGroupInfoAndStatus->statusReasonInfos->additionalInfos->first()
                    ];

                    $successMessage = getOption("DARAJA-ERROR-MESSAGE");
                    $successMessage = str_replace('{account}', $account->name, $successMessage);
                    $successMessage = str_replace('{error}', 'rejected', $successMessage);
                    $successMessage = str_replace('{transaction}', $transaction->order_number, $successMessage);

                    break;
                case GroupStatusType::Pdng:
                    $updateData = [
                        'status' => TransactionStatus::SUBMITTED
                    ];
                    break;
                case GroupStatusType::Rcvd:
                    info($event->report->groupHeader->messageId . " : Received status RCVD - no status change applied");
                    break;
                default:
                    // Unknown status
                    break;
            }

            $this->completeOperation(
                $transaction,
                $event->report->originalGroupInfoAndStatus->groupStatus,
                $updateData,
                $payloadData,
                $successMessage
            );

            info("Transaction status updated to: " . $transaction->status_id);
        } else {
            info("No transaction found with message_id: " . $event->report->groupHeader->messageId);
        }
    }

    private function completeOperation($transaction, $groupStatus, $updateData, $payloadData, $successMessage)
    {
        $updatedTransaction = app(TransactionRepository::class)->updateWithPayload(
            $transaction->id,
            $updateData,
            $payloadData
        );

        if ($updatedTransaction && $groupStatus == GroupStatusType::Acsp) {
            // Handle successful transaction
            app(TransactionRepository::class)->completeTransaction($transaction->id);
        }

        //Send SMS
        if ($successMessage != "") {
            $successMessageTo = getOption("DARAJA-ALERT-CONTACT");
            $this->sendSMS($successMessageTo, $successMessage);
        }
    }
}
