<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">{{ $content_title }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $content_title }}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="mt-4 padding">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between p-3">
                            <h5 class="modal-title">{{ ($add) ? ($formId) ? 'Update Charges' : 'Add Transaction Charge'
                                : 'All Transaction
                                Charges' }}</h5>
                            <button class="btn btn-dark btn-rounded px-4" wire:click="{{ ($add) ? 'backAction' : 'addFunction' }}">
                                <i class="{{ $add ? 'uil-arrow-left' : 'uil-plus' }}"></i>
                                {{ ($add) ? 'Back To List' : 'Add New' }}</button>
                        </div>

                        @if ($add)
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <p><span class="text-danger">Fields with (*) are required</span></p>
                                        <hr>
                                        <form wire:submit.prevent="store">
                                            <div class="p-lg">
                                                <div class="row row-sm">
                                                    <div class="col-sm-12 mb-3">
                                                        <x-wallet::form.select label="Payment Channel" wire:model.defer="formData.payment_channel_id"
                                                            :options="$payment_channels->pluck('name', 'id')" placeholder="Choose Channel" required/>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-6 mb-3">
                                                                <x-wallet::form.input type="number" label="Minimum" wire:model.defer="formData.minimum"
                                                                    name="title" required />
                                                            </div>
                                                            <div class="col-sm-6 mb-3">
                                                                <x-wallet::form.input type="number" label="Maximum" wire:model.defer="formData.maximum"
                                                                    name="maximum" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 mb-3">
                                                        <x-wallet::form.input type="number" label="Charges" wire:model.defer="formData.charge" name="charge" required />
                                                    </div>
                                                </div>
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