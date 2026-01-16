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
                                            <div class="md-form-group float-label">
                                                <input wire:model="name" type="text" class="md-input" name="name"
                                                    value="" required>
                                                <label>Client Name</label>
                                            </div>
                                            @error('name')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="md-form-group float-label">
                                                <select wire:model="account_manager" class="md-input"
                                                    name="account_manager" required>
                                                    <option value="">Select Account Manager</option>
                                                    @forelse ($managers as $manager)
                                                    <option value="{{ $manager->id }}">{{ $manager->first_name."
                                                        ".$manager->last_name }}</option>
                                                    @empty

                                                    @endforelse
                                                </select>
                                                <label>Role</label>
                                            </div>
                                            @error('account_manager')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-danger btn-rounded w-sm"
                                            wire:click="addFunction">Cancel</button>
                                        <button type="submit" class="btn btn-dark btn-rounded w-sm">{{ ($editId) ? 'Save Changes'
                                            : 'Submit' }}</button>
                                        <span class="d-custom-none">
                                            <img src="{{ asset('themes/agile/img/working.gif') }}" width="20" alt="">
                                            <small>please wait...</small>
                                        </span>
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