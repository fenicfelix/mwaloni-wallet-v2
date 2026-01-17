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
                                                <x-wallet::form.input label="Account Number" wire:model.defer="cashout_form.account_number" name="account_number" required />
                                            </div>
                                            <div class="col-6">
                                                <x-wallet::form.input label="Account Name" wire:model.defer="cashout_form.account_name" name="account_name" required />
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
                                            <x-wallet::form.input label="Account Reference" wire:model.defer="cashout_form.account_reference" name="account_reference" help="Only for Daraja Paybill" required />
                                        </div>
                                    @endif
                                </div>
                                @if ($account->accountType->slug != 'daraja')
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <x-wallet::form.input label="Country Code" wire:model.defer="cashout_form.country_code"
                                                name="country_code" maxlength="3" required />
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <x-wallet::form.input label="Bank Code" wire:model.defer="cashout_form.bank_code" name="bank_code"
                                                type="number" help="Leave blank is sending to mobile." required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <x-wallet::form.input label="Bank Name" wire:model.defer="cashout_form.bank_name"
                                                name="bank_name" required />
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <x-wallet::form.input label="Bank CIF" wire:model.defer="cashout_form.bank_cif" name="bank_cif" type="number" required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <x-wallet::form.input label="Beneficiary Address" wire:model.defer="cashout_form.address" name="address" required />
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-12">
                                        <x-wallet::form.input label="Amount" wire:model.defer="cashout_form.amount" name="amount" type="number" help="Should not exceed {{ number_format($account->revenue) }}" required />
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <x-wallet::button class="w-sm" variant="danger" wire:click.prevent="backAction">
                                        Cancel
                                    </x-wallet::button>
                                    <x-wallet::button type="submit" class="w-sm" variant="dark">
                                        Submit Cashout
                                    </x-wallet::button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>