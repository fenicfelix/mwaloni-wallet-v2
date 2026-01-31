<!-- ############ Content START-->
<div class="flex">
    <!-- ############ Main START-->
    <div class="page-container">
        <div class="page-title padding pb-0 ">
            <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}</h2></span>
            <span class="float-right">
                <button class="btn btn-dark btn-rounded w-sm" wire:click="backAction">Back to List</button></a>
            </span>
        </div>
        <div class="mt-4"></div>
        <!-- *************************** Top Analytics Data ************************** -->
        
        <div class="padding">
            <div class="row">
               <div class="col-sm-12 col-md-6 d-table h-100">
                  <div class="card card-border">
                     <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Order Number</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{ $transaction->order_number }}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Account Number</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{ $transaction->account_number }}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Recipient Name</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{ ucwords(strtolower($transaction->account_name)) ?? 'Not Provided' }}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Payment Channel</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{$transaction->paymentChannel->name }}</label>
                        </div>
                        @if ($transaction->account_reference)
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Account Ref</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ $transaction->account_reference}}</label>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Requested Amount</label>
                            <label class="col-sm-8 col-form-label transaction-label"> {{ $transaction->account->currency->code .' '. number_format($transaction->requested_amount, 2) }}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Payment Reason</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{$transaction->description }}</label>
                        </div>
                        @if ($isSuccessful)
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Disbursed Amount</label>
                                <label class="col-sm-8 col-form-label transaction-label">KSh. {{ number_format($transaction->disbursed_amount, 2) }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">System Charges</label>
                                <label class="col-sm-8 col-form-label transaction-label">KSh. {{ number_format(($transaction->system_charges+$transaction->sms_charges), 2) }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Total Cost</label>
                                <label class="col-sm-8 col-form-label transaction-label">KSh. {{ number_format(($transaction->disbursed_amount+$transaction->system_charges+$transaction->sms_charges), 2) }}</label>
                            </div>
                        @endif
                        @if (isset($transaction->service_id))
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Service Name</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ $transaction->service->name }}</label>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Transaction Date</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{ date('d M, Y h:i A', strtotime($transaction->transaction_date)) }}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Completed at</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{ $transaction->completed_at ? date('d M, Y h:i A', strtotime($transaction->completed_at)) : "-" }}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Receipt Number</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{ ($transaction->receipt_number) ?? strtoupper($transaction->status->name) }}</label>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="col-sm-12 col-md-6 d-table h-100">
                  <div class="card card-border">
                     <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Status</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{ strtoupper($transaction->status->name) }}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Status Message</label>
                            <label class="col-sm-8 col-form-label transaction-label">{{ $transaction->result_description ?? 'Waiting for callback' }}</label>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="col-sm-12 col-form-label font-weight-bold">Raw Request</label>
                            <code class="col-sm-12">{{ $transaction->payload?->raw_request }}</code>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="col-sm-12 col-form-label font-weight-bold">Transaction Payload</label>
                            <code class="col-sm-12">{{ $transaction->payload?->trx_payload }}</code>
                        </div>
                        @if ($transaction->payload?->file_path)
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label font-weight-bold">File Path</label>
                                <code class="col-sm-12">{{ $transaction->payload?->file_path }}</code>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-sm-12 col-form-label font-weight-bold">Raw Callback</label>
                            <code class="col-sm-12">{{ $transaction->payload?->raw_callback }}</code>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
        </div>
    </div>
    <!-- ############ Main END-->
</div>
<!-- ############ Content END-->