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
                            <h5 class="modal-title">{{ ($add) ? ($edit_id) ? 'Update Charges' : 'Add Transaction Charge'
                                : 'All Transaction
                                Charges' }}</h5>
                            <button class="btn btn-dark btn-rounded px-4" wire:click="addFunction">{{ ($add) ? 'Back To List' :
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
                                                    <select wire:model='payment_channel' class="md-input"
                                                        name="payment_channel" id="add-payment_channel" required>
                                                        <option value="">Transfer Channel</option>
                                                        @forelse ($payment_channels as $channel)
                                                        <option value="{{ $channel->slug }}">{{ $channel->name }}
                                                        </option>
                                                        @empty

                                                        @endforelse
                                                    </select>
                                                    <label>Cashout Reason</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="md-form-group float-label">
                                                            <input wire:model='minimum' type="number" class="md-input"
                                                                id="add-minimum" name="minimum" min="0" value=""
                                                                required>
                                                            <label>Minimum Value</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="md-form-group float-label">
                                                            <input wire:model='maximum' type="number" class="md-input"
                                                                id="add-maximum" name="maximum" value="" required>
                                                            <label>Maximum Value</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <input wire:model='charge' type="text" class="md-input"
                                                        id="add-charge" name="charge" value="" required>
                                                    <label>Charges</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-danger btn-rounded w-sm" data-dismiss="modal"
                                            wire:click="addFunction">Cancel</button>
                                        <button type="submit" class="btn btn-dark btn-rounded w-sm">Submit</button>
                                        <span class="d-custom-none">
                                            <img src="{{ asset('themes/agile/img/working.gif') }}" width="20" alt="">
                                            <small>please wait...</small>
                                        </span>
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