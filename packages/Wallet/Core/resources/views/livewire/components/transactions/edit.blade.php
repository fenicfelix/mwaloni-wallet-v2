<!-- ############ Content START-->
<div class="flex">
    <!-- ############ Main START-->
    <div class="page-container">
        <div class="page-title padding pb-0 ">
            <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}</h2></span>
        </div>
        <div class="mt-4"></div>
        <!-- *************************** Top Analytics Data ************************** -->
        
        <div class="padding">
            <div class="row">
               <div class="col-sm-12 col-md-6 d-table h-100">
                  <div class="card card-border">
                     <div class="card-body">
                        <form wire:submit.prevent="{{ ($pay_offline) ? 'submitOfflinePayment' : 'store'}}">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Order Number</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ ucwords(strtolower($transaction->order_number)) ?? 'Not Provided' }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Account Name</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ ucwords(strtolower($transaction->account->name)) ?? 'Not Provided' }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Service Name</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ ($transaction->service) ?  ucwords(strtolower($transaction->service->name)) : 'Not Provided' }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Payment Channel</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{$transaction->paymentChannel->name }}</label>
                            </div>
                            @if ($edit)
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Requested Amount</label>
                                    <input type="number" class="col-sm-8 md-input" wire:model="requested_amount" name="requested_amount" />
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Account Number</label>
                                    <input type="number" class="col-sm-8 md-input" wire:model="account_number" name="requested_amount" />
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Payment Status</label>
                                    <select wire:model="status_id" class="col-sm-8 md-input" name="status_id" required>
                                        <option value="">Select Status</option>
                                        @forelse ($statuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @empty
                                            
                                        @endforelse
                                    </select>
                                </div>
                            @elseif($pay_offline)
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Receipt Number</label>
                                    <input type="text" class="col-sm-8 md-input" wire:model="receipt_number" name="receipt_number" />
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Account Name</label>
                                    <input type="text" class="col-sm-8 md-input" wire:model="account_name" name="account_name" />
                                </div>
                            @endif
                            <div class="mt-4">
                                <button type="button" class="btn btn-danger rounded" data-dismiss="modal" wire:click="cancelEdit">Cancel</button>
                                <button type="submit" class="btn btn-dark rounded">Submit</button>
                                <span class="d-custom-none">
                                    <img src="{{ asset('themes/agile/img/working.gif') }}" width="20" alt=""> <small>please wait...</small>
                                </span>
                            </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
        </div>
    </div>
    <!-- ############ Main END-->
</div>
<!-- ############ Content END-->