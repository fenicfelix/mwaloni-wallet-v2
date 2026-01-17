<div>
    <div class="page-title padding pb-0 ">
        <span class="float-left">
            <h2 class="text-md mb-0 headliner">{{ $content_title }}</h2>
        </span>
    </div>
    <div class="mt-4 padding">
        <div class="row">
            @include('core::_includes.technical-nav')
            <div class="col-md-9 col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between p-3">
                            <h5 class="modal-title">{{ ($add) ? ($formId) ? 'Update Charges' : 'Add Transaction Charge'
                                : 'All Transaction
                                Charges' }}</h5>
                            <button class="btn btn-dark btn-rounded px-4" wire:click="{{ ($add) ? 'backAction' : 'addFunction' }}">{{ ($add) ? 'Back To List' :
                                'Add New' }}</button>
                        </div>

                        @if ($add)
                        <div class="card">
                            <div class="card-body">
                                <form wire:submit.prevent="store">
                                    <div class="p-lg">
                                        <div class="row row-sm">
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <x-wallet::form.select label="Payment Channel" wire:model.defer="formData.payment_channel_id" :options="$payment_channels->pluck('name', 'id')"
                                                        placeholder="Choose Channel" />
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <x-wallet::form.input type="number" label="Minimum" wire:model.defer="formData.minimum" name="title" required />
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <x-wallet::form.input type="number" label="Maximum" wire:model.defer="formData.maximum" name="maximum" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <x-wallet::form.input type="number" label="Charges" wire:model.defer="formData.charge" name="charge" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
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
                        @else
                        <div class="table-responsive">
                            @livewire('core-datatables.transaction-charges-table')
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>