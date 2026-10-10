<!-- ############ Main START-->
<div class="page-container">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('accounts') }}">Accounts</a></li>
                        <li class="breadcrumb-item active">{{ $content_title }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $content_title }}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="padding">
        <div class="row">
            <div class="col-sm-12 col-md-6 d-table h-100">
                <div class="card card-border">
                    <div class="card-body">
                        <p><span class="text-danger">Fields with (*) are required</span></p>
                        <hr>
                        <form wire:submit.prevent="submitCashout">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <x-wallet::form.input label="Account Number"
                                                wire:model.defer="cashoutFormData.account_number" name="account_number"
                                                required />
                                        </div>
                                        <div class="col-6 mb-3">
                                            <x-wallet::form.input label="Account Name"
                                                wire:model.defer="cashoutFormData.account_name" name="account_name"
                                                required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <x-wallet::form.select label="Channel" wire:model.live="cashoutFormData.channel_id"
                                        :options="$payment_channels->pluck('name', 'slug')"
                                        placeholder="Choose Channel" />
                                </div>
                                @if (isset($cashoutFormData['channel_id']) && $cashoutFormData['channel_id'] ==
                                "daraja-paybill")
                                <div id="paybill-option" class="col-sm-6 mb-3">
                                    <x-wallet::form.input label="Account Reference"
                                        wire:model.defer="cashoutFormData.account_reference" name="account_reference"
                                        help="Only for Daraja Paybill" required />
                                </div>
                                @endif
                            </div>
                            @if ($account->accountType->slug != 'daraja')
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <x-wallet::form.input label="Country Code"
                                        wire:model.defer="cashoutFormData.country_code" name="country_code"
                                        maxlength="3" required />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <x-wallet::form.input label="Bank Code" wire:model.defer="cashoutFormData.bank_code"
                                        name="bank_code" type="number" help="Leave blank is sending to mobile."
                                        required />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <x-wallet::form.input label="Bank Name" wire:model.defer="cashoutFormData.bank_name"
                                        name="bank_name" required />
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <x-wallet::form.input label="Bank CIF" wire:model.defer="cashoutFormData.bank_cif"
                                        name="bank_cif" type="text" required />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 mb-3">
                                    <x-wallet::form.input label="Beneficiary Address"
                                        wire:model.defer="cashoutFormData.address" name="address" required />
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-sm-12 mb-3">
                                    <x-wallet::form.input label="Amount" wire:model.defer="cashoutFormData.amount"
                                        name="amount" type="number"
                                        help="Should not exceed {{ number_format($max_cashout_amount) }}" min="0"
                                        max="{{ $max_cashout_amount }}" required />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-wallet::button class="w-sm" variant="danger" icon="uil-times" wire:click.prevent="backAction">
                                    Cancel
                                </x-wallet::button>
                                <x-wallet::button type="submit" class="w-sm" icon="uil-check" variant="dark">
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