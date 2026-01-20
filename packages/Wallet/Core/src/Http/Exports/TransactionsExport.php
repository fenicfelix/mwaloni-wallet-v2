<?php

namespace Wallet\Core\Http\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Wallet\Core\Models\Transaction;

class TransactionsExport implements FromCollection
{
    public $transactions;

    public function __construct($transactions = NULL)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return Transaction::select([
            "transaction_date",
            "order_number",
            "receipt_number",
            "account_number",
            "account_name",
            "disbursed_amount",
            "system_charges",
            "system_charges",
            "revenue",
            "status",
        ])->get();
    }
}
