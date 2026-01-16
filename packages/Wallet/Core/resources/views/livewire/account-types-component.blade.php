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
                            <h5 class="modal-title">{{ ($add) ? ($edit_id) ? 'Update Transaction Type' : 'Add
                                Transaction Type' : 'All Transaction Types' }}</h5>
                            <button class="btn btn-dark btn-rounded px-4" wire:click="addFunction">{{ ($add) ? 'Back
                                To List' : 'Add New' }}</button>
                        </div>
                        @if ($add)
                        <div class="card">
                            <div class="card-body">
                                <form wire:submit.prevent="store">
                                    <div class="p-lg">
                                        <div class="row row-sm">
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <input wire:model.lazy="account_type" class="md-input"
                                                        name="account_type" value="" required>
                                                    <label>Type Name</label>
                                                </div>
                                                @error('account_type')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
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
                            @livewire('core-datatables.account-types-table')
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>