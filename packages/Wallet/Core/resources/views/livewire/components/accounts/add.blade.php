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
                            <form wire:submit.prevent="store">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <x-wallet::form.select label="Account Type" wire:model.live="formData.account_type_id" :options="$account_types->pluck('account_type', 'id')"
                                                placeholder="Choose Type" />
                                        </div>
                                        <div class="col-6">
                                            <x-wallet::form.select label="Currency" wire:model.live="formData.currency_id"
                                                :options="$currencies->pluck('name', 'id')" placeholder="Choose Currency" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <x-wallet::form.input label="Name" wire:model.defer="formData.name" name="amount" type="text" required />
                                                </div>
                                                <div class="col-6">
                                                    <x-wallet::form.input label="Account Number" wire:model.defer="formData.account_number" name="account_number" type="text" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <x-wallet::form.select label="Managed By" wire:model.live="formData.managed_by"
                                                :options="$account_managers" placeholder="Choose Manager" />
                                        </div>
                                    </div>
                                    @if ($formData['account_type_id'] != "1")
                                        <div class="row">
                                            <div class="col-6">
                                                <x-wallet::form.input label="Country of Registration" wire:model.defer="formData.country_name" name="country_name"
                                                    type="text" required />
                                            </div>
                                            <div class="col-6">
                                                <x-wallet::form.input label="Country Code" wire:model.defer="formData.country_code" name="country_code"
                                                    type="text" required />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <x-wallet::form.input label="Bank Code" wire:model.defer="formData.bank_code" name="bank_code"
                                                    type="text" required />
                                            </div>
                                            <div class="col-6">
                                                <x-wallet::form.input label="Branch Code" wire:model.defer="formData.branch_code" name="branch_code"
                                                    type="text" required />
                                            </div>
                                        </div>
                                    @endif
                                    @if ($formData['account_type_id'] == "1")
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <x-wallet::form.input label="Consumer Key" wire:model.defer="formData.consumer_key" name="consumer_key"
                                                    type="text" required />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <x-wallet::form.input label="Consumer Secret" wire:model.defer="formData.consumer_secret" name="consumer_secret"
                                                    type="text" required />
                                            </div>
                                        </div>
                                    @else
                                        @if ($formData['account_type_id'] == "3")
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <x-wallet::form.input label="CIF" wire:model.defer="formData.cif" name="cif"
                                                        type="text" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <x-wallet::form.input label="Pesalink CIF" wire:model.defer="formData.pesalink_cif" name="pesalink_cif"
                                                        type="text" required />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <x-wallet::form.input label="API Key" wire:model.defer="formData.consumer_key" name="consumer_key"
                                                        type="text" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <x-wallet::form.input label="Pesalink CIF" wire:model.defer="formData.pesalink_cif" name="pesalink_cif" type="text"
                                                        required />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <x-wallet::form.input label="API Key" wire:model.defer="formData.consumer_key" name="consumer_key" type="text"
                                                        required />
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-12">
                                                <x-wallet::form.input label="Address" wire:model.defer="formData.address" name="address" type="text"
                                                    required />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <x-wallet::form.input label="API Username" wire:model.defer="formData.api_username" name="api_username" type="text"/>
                                            </div>
                                            <div class="col-6">
                                                <x-wallet::form.input label="API Password" wire:model.defer="formData.api_password" name="api_password" type="text"/>
                                            </div>
                                        </div>
                                    @endif
                                </div>
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