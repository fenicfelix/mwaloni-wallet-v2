<div class="flex">
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
                            <form wire:submit.prevent="store">
                                <div class="col-sm-12 mb-3">
                                    <x-wallet::form.input label="Name" wire:model.defer="formData.name" name="name" required />
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <x-wallet::form.textarea label="Description" wire:model.defer="formData.description" rows="3" />
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <x-wallet::form.select label="Client" wire:model.defer="formData.client_id" :options="$clients->pluck('name', 'id')" placeholder="Choose Client" />
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <x-wallet::form.select label="Account" wire:model.defer="formData.account_id" :options="$accounts->pluck('name', 'id')" placeholder="Choose Account" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <x-wallet::form.input type="url" label="Callback URL" wire:model.defer="formData.callback_url"/>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <x-wallet::form.input type="number" label="System Charges" wire:model.defer="formData.system_charges" id="add-system_charges" required/>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <x-wallet::form.input type="number" label="SMS Charges" wire:model.defer="formData.sms_charges" id="add-sms_charges" required/>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <x-wallet::form.input type="number" label="Max Transaction Amount" wire:model.defer="formData.max_trx_amount" id="add-max_trx_amount" required/>
                                </div>
                                <hr>
                                <x-wallet::button variant="danger" icon="uil-times" wire:click.prevent="backAction">
                                    Cancel
                                </x-wallet::button>
                                <x-wallet::button type="submit" icon="uil-check" variant="dark">
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