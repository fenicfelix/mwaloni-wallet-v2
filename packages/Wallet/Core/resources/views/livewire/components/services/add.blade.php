<div class="flex">
    <!-- ############ Main START-->
    <div class="page-container">
        <div class="page-title padding pb-0 ">
            <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}
            @if (isset($form['service_id']))
                {{ ' - '.$form['service_id'] }}
            @endif    
            </h2></span>
        </div>
        <div class="mt-4"></div>

        <div class="padding">
            <div class="row">
                <div class="col-sm-12 col-md-6 d-table h-100">
                    <div class="card card-border">
                        <div class="card-body">
                            <form wire:submit.prevent="store">
                                <div class="col-sm-12">
                                    <x-wallet::form.input label="Name" wire:model.defer="formData.name" name="name" required />
                                </div>
                                <div class="col-sm-12">
                                    <x-wallet::form.textarea label="Description" wire:model.defer="formData.description" rows="3" />
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <x-wallet::form.select label="Client" wire:model.defer="formData.client_id" :options="$clients->pluck('name', 'id')" placeholder="Choose Client" />
                                        </div>
                                        <div class="col-6">
                                            <x-wallet::form.select label="Account" wire:model.defer="formData.account_id" :options="$accounts->pluck('name', 'id')" placeholder="Choose Account" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <x-wallet::form.input type="url" label="Callback URL" wire:model.defer="formData.callback_url"/>
                                </div>
                                <div class="row px-3">
                                    <div class="col-sm-6">
                                        <x-wallet::form.input type="number" label="System Charges" wire:model.defer="formData.system_charges" id="add-system_charges" required/>
                                    </div>
                                    <div class="col-sm-6">
                                        <x-wallet::form.input type="number" label="SMS Charges" wire:model.defer="formData.sms_charges" id="add-sms_charges" required/>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <x-wallet::form.input type="number" label="Max Transaction Amount" wire:model.defer="formData.max_trx_amount" id="add-max_trx_amount" required/>
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