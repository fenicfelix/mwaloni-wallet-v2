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
                                        <x-wallet::form.input label="Account Number" wire:model.defer="withdraw_from.account_number" name="Account Number" required />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <x-wallet::form.input label="Account Name" wire:model.lazy="withdraw_from.account_name" required />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <x-wallet::form.select label="Channel" wire:model.live="withdraw_from.channel_id" :options="$payment_channels->pluck('name', 'slug')"
                                            placeholder="Choose Channel" />
                                    </div>
                                    @if (isset($withdraw_from['channel_id']) && $withdraw_from['channel_id'] == "daraja-paybill")
                                        <div id="paybill-option" class="col-sm-6">
                                            <x-wallet::form.input label="Account Reference" wire:model.defer="withdraw_from.account_reference" name="Account Reference" help="Only for Daraja Paybill"
                                                required />
                                        </div>
                                    @endif
                                    <div class="col-sm-12">
                                        <x-wallet::form.input type="number" label="Amount" wire:model.defer="withdraw_from.amount"
                                            name="Amount" help="Should not exceed {{ number_format($max_amount, 2) }}" required />
                                    </div>
                                </div>
                                
                                @if ($service->account->accountType->slug != 'daraja')
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <x-wallet::form.input label="Country Code" wire:model.lazy="withdraw_from.country_code" required />
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <x-wallet::form.input label="Currency Code" wire:model.lazy="withdraw_from.currency_code" required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <x-wallet::form.input label="Bank Name" wire:model.lazy="withdraw_from.bank_name" required />
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <x-wallet::form.input label="Bank CIF" wire:model.lazy="withdraw_from.bank_cif" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-wallet::form.input label="Address" wire:model.lazy="withdraw_from.address" required />
                                        </div>
                                    </div>
                                @endif
                                <div class="mt-4">
                                    <x-wallet::button class="w-sm" variant="danger" wire:click.prevent="backAction">
                                        Cancel
                                    </x-wallet::button>
                                    <x-wallet::button type="submit" class="w-sm" variant="dark">
                                        Submit
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