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
                            <form wire:submit.prevent="doCashout">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="cashout_form.account_number" value="" required>
                                                    <label for="account_number">Account Number</label>
                                                </div>
                                                @error('cashout_form.account_number')
                                                    <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="cashout_form.account_name" value="" required>
                                                    <label for="account_name">Account Name</label>
                                                </div>
                                                @error('cashout_form.account_name')
                                                    <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="md-form-group float-label">
                                            <select class="md-input" wire:model="cashout_form.channel_id" required>
                                                <option value="">Select Transfer Channel</option>
                                                @forelse ($payment_channels as $channel)
                                                    @if ($account->accountType->id == $channel->account_type_id && $channel->active == 1)
                                                        <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                                    @endif
                                                @empty
                                                    
                                                @endforelse
                                            </select>
                                            <label>Transfer Channel</label>
                                        </div>
                                        @error('cashout_form.channel_id')
                                            <small class="text-danger">{{ $message }} </small>
                                        @enderror
                                    </div>
                                    @if ($cashout_form['channel_id'] == "daraja-paybill")
                                        <div id="paybill-option" class="col-sm-6">
                                            <div class="md-form-group float-label">
                                                <input type="text" class="md-input" wire:model="cashout_form.account_reference" max="0" value="">
                                                <label for="account_reference">Account Reference</label>
                                                <small class="float-left"><code>Only for Daraja Paybill</code></small>
                                            </div>
                                            @error('cashout_form.account_reference')
                                                <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                                @if ($account->accountType->slug != 'daraja')
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="md-form-group float-label">
                                                <input type="text" maxlength="3" class="md-input" wire:model="cashout_form.country_code" value="" required>
                                                <label for="country_code">Country Code</label>
                                            </div>
                                            @error('cashout_form.country_code')
                                                <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="md-form-group float-label">
                                                <input type="number" class="md-input" wire:model="cashout_form.bank_code" value="">
                                                <label for="amount">Bank Code</label>
                                            </div>
                                            @error('cashout_form.bank_code')
                                                <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                            <small class="float-left"><code>Leave blank is sending to mobile.</code></small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="md-form-group float-label">
                                                <input type="text" class="md-input" wire:model="cashout_form.bank_name" value="" required>
                                                <label for="bank_name">Nank Name</label>
                                            </div>
                                            @error('cashout_form.bank_name')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="md-form-group float-label">
                                                <input type="number" class="md-input" wire:model="cashout_form.bank_cif" value="">
                                                <label for="bank_cif">Bank CIF</label>
                                            </div>
                                            @error('cashout_form.bank_cif')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="md-form-group float-label">
                                                <input type="text" class="md-input" wire:model="cashout_form.address" value="" required>
                                                <label for="amount">Beneficiary Address</label>
                                            </div>
                                            @error('cashout_form.address')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="md-form-group float-label">
                                            <input type="number" class="md-input" wire:model="cashout_form.amount" value="" required>
                                            <label for="amount">Amount</label>
                                        </div>
                                        @error('cashout_form.amount')
                                            <small class="text-danger">{{ $message }} </small>
                                        @enderror
                                        <small class="float-left"><code>Should not exceed <span wire:model="amount-hint">{{ number_format($account->revenue) }}</span></code></small>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-danger btn-rounded w-sm" wire:click="backToList">Cancel</button>
                                    <button type="submit" class="btn btn-dark btn-rounded w-sm">Submit</button>
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