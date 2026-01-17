<div>
    @if ($add)
    <div class="flex">
        <!-- ############ Main START-->
        <div class="page-container">
            <div class="page-title padding pb-0 ">
                <span class="float-left">
                    <h2 class="text-md mb-0 headliner">{{ $content_title }}</h2>
                </span>
            </div>
            <div class="mt-4"></div>

            <div class="padding">
                <div class="row">
                    <div class="col-sm-12 col-md-6 d-table h-100">
                        <div class="card card-border">
                            <div class="card-body">
                                <form wire:submit.prevent="store">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <x-wallet::form.input label="Name" wire:model.defer="formData.name" name="name" required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <x-wallet::form.select label="Account Manager" wire:model.live="formData.account_manager" :options="$managers"
                                                placeholder="Choose Account Manager" />
                                        </div>
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
    @else
    <div class="row">
        <div class="col-12">
            <div class="page-title padding pb-0 ">
                <span class="float-left">
                    <h2 class="text-md mb-0 headliner">{{ $content_title }}</h2>
                </span>
                <span class="float-right">
                    <button class="btn btn-dark btn-rounded px-4" wire:click="addFunction">{{ ($add) ? 'Back To List' : 'Add
                        Client' }}</button>
                </span>
            </div>
        </div>
    </div>

    <div class="mt-4 padding">
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    @livewire('core-datatables.clients-table')
                </div>
            </div>
        </div>
    </div>
    @endif
</div>