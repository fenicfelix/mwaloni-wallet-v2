<?php

namespace Wallet\Core\Jobs\Stanbic;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Akika\LaravelStanbic\Data\AggregateRoots\Pain00100103;
use Akika\LaravelStanbic\Data\ValueObjects\CreditTransferTransactionInfo;
use Akika\LaravelStanbic\Data\ValueObjects\GroupHeader;
use Akika\LaravelStanbic\Data\ValueObjects\PaymentInfo;
use Akika\LaravelStanbic\Data\ValueObjects\PostalAddress;
use Akika\LaravelStanbic\Enums\ChargeBearerType;
use Akika\LaravelStanbic\Enums\CountryCode;
use Akika\LaravelStanbic\Enums\Currency;
use Akika\LaravelStanbic\Enums\InstructionPriority;
use Akika\LaravelStanbic\Enums\PaymentMethod;
use Wallet\Core\Http\Enums\TransactionStatus;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Repositories\TransactionRepository;

class ProcessStanbicPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transaction;

    public $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public int $transactionId) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->transaction = Transaction::with(["service", "account", "payload"])->findOrFail($this->transactionId);

        $this->payload = json_decode($this->transaction->payload?->trx_payload, true);

        $messageId = $this->payload['messageId'];
        $companyName = $this->transaction->account->name;
        $companyAcNo = $this->transaction->account->account_number;

        // 1. Create group header
        $groupHeader = GroupHeader::make()
            ->setMessageId($messageId)
            ->setCreationDate(now())
            ->setInitiatingParty(null, $companyName);

        $filePath = Pain00100103::make()
            ->setGroupHeader($groupHeader)
            ->addPaymentInfo($this->getPaymentInfo($companyName, $companyAcNo))
            ->store();

        $updateData = [
            "status" => TransactionStatus::SUBMITTED,
            "result_description" => "Transaction submitted"
        ];

        $payloaData = [
            'file_path' => $filePath
        ];

        // Update transaction and payload
        app(TransactionRepository::class)->updateWithPayload($this->transaction->id, $updateData, $payloaData);
    }

    public function getPaymentInfo(string $companyName, string $companyAcNo): PaymentInfo
    {
        $debtorBankCode = $this->transaction->account->bank_code;
        $paymentInfoId = $this->payload['paymentInfoId'];

        $paymentInfo = PaymentInfo::make()
            ->setPaymentInfoId($paymentInfoId)
            ->setPaymentMethod(PaymentMethod::CreditTransfer)
            ->setBatchBooking(true)
            ->setPaymentTypeInfo(InstructionPriority::Norm)
            ->setRequestedExecutionDate(now())
            ->setDebtor($companyName, new PostalAddress(countryCode: CountryCode::Ghana))
            ->setDebtorAccount($companyAcNo, Currency::Cedi)
            ->setDebtorAgent($debtorBankCode)
            ->setChargeBearer(ChargeBearerType::Debt)
            ->addCreditTransferTransactionInfo($this->getCreditTransferTransactionInfo());

        return $paymentInfo;
    }

    public function getCreditTransferTransactionInfo(): CreditTransferTransactionInfo
    {
        $paymentId = $this->payload['paymentId'];
        $instructionId = $this->payload['instructionId'];
        $amount = $this->payload['amount'];

        $creditorBankCode = $this->payload['creditorBankCode'];
        $bank = $this->payload['creditorBankName'];
        $beneficiaryName = $this->payload['beneficiaryName'];
        $beneficiaryAcNo = $this->payload['beneficiaryAcNo'];

        $paymentDescription = $this->payload['paymentDescription'];

        return CreditTransferTransactionInfo::make()
            ->setPaymentId($paymentId, $instructionId)
            ->setAmount($amount, Currency::Cedi)
            ->setCreditorAgent($creditorBankCode, $bank, new PostalAddress(countryCode: CountryCode::Ghana))
            ->setCreditor($beneficiaryName, new PostalAddress(
                countryCode: CountryCode::Ghana,
            ))
            ->setCreditorAccount($beneficiaryAcNo)
            ->setRemittanceInfo($paymentDescription);
    }
}
