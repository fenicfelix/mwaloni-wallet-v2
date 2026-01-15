<div class="flex">
    <!-- ############ Main START-->
    <div class="page-container">
        <div class="page-title padding pb-0 ">
            <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}</h2></span>
        </div>
        <div class="mt-4"></div>

        <div class="padding">
            <div class="row">
                <div class="col-sm-12 col-md-6 d-table h-100">
                    <div class="card card-border">
                        <div class="card-body">
                            <form wire:submit.prevent="doWithdrawCash">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="md-form-group float-label">
                                            <input type="phone" class="md-input" wire:model.lazy="withdraw_from.account_number" required>
                                            <label>Account Number</label>
                                        </div>
                                        @error('withdraw_from.account_number')
                                            <small class="text-danger">{{ $message }} </small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="md-form-group float-label">
                                            <input type="text" class="md-input" wire:model.lazy="withdraw_from.account_name" required>
                                            <label>Account Name</label>
                                        </div>
                                        @error('withdraw_from.account_name')
                                        <small class="text-danger">{{ $message }} </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="md-form-group float-label">
                                            <select class="md-input" name="channel_id" wire:model.lazy="withdraw_from.channel_id" required>
                                                <option value="">Select Transfer Channel</option>
                                                @forelse ($payment_channels as $channel)
                                                    @if ($service->account->accountType->id == $channel->account_type_id && $channel->active == 1)
                                                        <option value="{{ $channel->slug }}">{{ $channel->name }}</option>
                                                    @endif
                                                @empty
                                                    
                                                @endforelse
                                            </select>
                                            <label>Transfer Channel</label>
                                        </div>
                                        @error('withdraw_from.channel_id')
                                            <small class="text-danger">{{ $message }} </small>
                                        @enderror
                                    </div>
                                    @if (isset($withdraw_from['channel_id']) && $withdraw_from['channel_id'] == "daraja-paybill")
                                        <div id="paybill-option" class="col-sm-6">
                                            <div class="md-form-group float-label">
                                                <input type="text" class="md-input" wire:model.lazy="withdraw_from.account_reference" max="0">
                                                <label for="account_reference">Account Reference</label>
                                                <small class="float-left"><code>Only for Daraja Paybill</code></small>
                                            </div>
                                            @error('withdraw_from.account_reference')
                                                <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    @endif
                                    <div class="col-sm-12">
                                        <div class="md-form-group float-label">
                                            <input type="number" class="md-input" wire:model.lazy="withdraw_from.amount" name="amount" min="0" max="{{ $max_amount }}" required>
                                            <label>Amount</label>
                                            <small class="float-left"><code>Should not exceed <span id="cashout-amount-hint">{{ number_format($max_amount, 2) }}</span></code></small>
                                        </div>
                                        @error('withdraw_from.amount')
                                            <small class="text-danger">{{ $message }} </small>
                                        @enderror
                                    </div>
                                </div>
                                
                                @if ($service->account->accountType->slug != 'daraja')
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="md-form-group float-label">
                                                <input type="text" maxlength="3" class="md-input" wire:model.lazy="withdraw_from.country_code" required>
                                                <label for="country_code">Country Code</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="md-form-group float-label">
                                                <input type="text" maxlength="3" class="md-input" wire:model="withdraw_from.currency_code" value="">
                                                <label for="amount">Currency Code</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="md-form-group float-label">
                                                <input type="text" id="bank_name" class="md-input" wire:model.lazy="withdraw_from.bank_name" required>
                                                <label for="bank_name">Bank Name</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="md-form-group float-label">
                                                <input type="text" class="md-input" id="bank_cif" wire:model="withdraw_from.bank_cif" value="">
                                                <label for="bank_cif">Bank CIF</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="md-form-group float-label">
                                                <input type="text" id="address" class="md-input" wire:model.lazy="withdraw_from.address"
                                                    required>
                                                <label for="address">Address</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="mt-4">
                                    <button type="button" class="btn btn-danger rounded" data-dismiss="modal" wire:click="backToList">Cancel</button>
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
</div>