<div class="flex">
    <!-- ############ Main START-->
    <div class="page-container">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('accounts') }}">Accounts</a></li>
                            <li class="breadcrumb-item active">{{ $content_title }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $content_title }}
                        @if (isset($formData['service_id']))
                        {{ ' - '.$formData['service_id'] }}
                        @endif
                    </h4>
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
                            <form wire:submit.prevent="submitWithdrawCash">
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <x-wallet::form.input label="Account Number" wire:model.defer="withdrawalForm.account_number" name="Account Number" required />
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <x-wallet::form.input label="Account Name" wire:model.defer="withdrawalForm.account_name" required />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <x-wallet::form.select label="Channel" wire:model.live="withdrawalForm.channel_id" :options="$payment_channels->pluck('name', 'slug')"
                                            placeholder="Choose Channel" />
                                    </div>
                                    @if (isset($withdrawalForm['channel_id']) && $withdrawalForm['channel_id'] == "daraja-paybill")
                                        <div id="paybill-option" class="col-sm-6 mb-3">
                                            <x-wallet::form.input label="Account Reference" wire:model.defer="withdrawalForm.account_reference" name="Account Reference" help="Only for Daraja Paybill"
                                                required />
                                        </div>
                                    @endif
                                    <div class="col-sm-12 mb-3">
                                        <x-wallet::form.input type="number" label="Amount" wire:model.defer="withdrawalForm.amount"
                                            name="Amount" help="Should not exceed {{ number_format($max_amount, 2) }}" required min="0" max="{{ $max_amount }}" />
                                    </div>
                                </div>
                                
                                @if ($service->account->accountType->slug != 'daraja')
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <x-wallet::form.input label="Country Code" wire:model.defer="withdrawalForm.country_code" required />
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <x-wallet::form.input label="Currency Code" wire:model.defer="withdrawalForm.currency_code" required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <x-wallet::form.input label="Bank Name" wire:model.defer="withdrawalForm.bank_name" required />
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <x-wallet::form.input label="Bank CIF" wire:model.defer="withdrawalForm.bank_cif" required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <x-wallet::form.input label="Address" wire:model.defer="withdrawalForm.address" required />
                                        </div>
                                    </div>
                                @endif
                                <hr>
                                <x-wallet::button class="w-sm" variant="danger" icon="uil-times" wire:click.prevent="backAction">
                                    Cancel
                                </x-wallet::button>
                                <x-wallet::button type="submit" class="w-sm" icon="uil-check" variant="dark">
                                    Submit
                                </x-wallet::button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>