<div>
    <div class="page-title padding pb-0 ">
        <span class="float-left">
            <h2 class="text-md mb-0 headliner">{{ $content_title }}</h2>
        </span>
    </div>
    <div class="mt-4 padding">
        <div class="row">
            @include('_includes.technical-nav')
            <div class="col-md-9 col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <div class="card-head">
                                <div class="d-flex justify-content-between p-3">
                                    <h5 class="modal-title">{{ ($add) ? ($edit_id) ? 'Update Status' : 'Add Status' :
                                        'All Status' }}</h5>
                                    <button class="btn btn-dark rounded" wire:click="addFunction">{{ ($add) ? 'Back To
                                        List' : 'Add New' }}</button>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($add)
                                <form wire:submit.prevent="store">
                                    <div class="p-lg">
                                        <div class="row row-sm">
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <input wire:model="name" class="md-input" name="name" value=""
                                                        required>
                                                    <label>Status Name</label>
                                                </div>
                                                @error('name')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-danger rounded" data-dismiss="modal"
                                            wire:click="addFunction">Cancel</button>
                                        <button type="submit" class="btn btn-dark rounded">Submit</button>
                                        <span class="d-custom-none">
                                            <img src="{{ asset('themes/agile/img/working.gif') }}" width="20" alt="">
                                            <small>please wait...</small>
                                        </span>
                                    </div>
                                </form>
                                @else
                                <div class="table-responsive">
                                    @livewire('core-datatables.statuses-table')
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>