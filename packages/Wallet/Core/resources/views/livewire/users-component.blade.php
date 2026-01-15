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
                                        <div class="col-sm-6">
                                            <div class="md-form-group float-label">
                                                <input wire:model.lazy="form.first_name" type="text" class="md-input"
                                                    required>
                                                <label>First Name</label>
                                            </div>
                                            @error('form.first_name')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="md-form-group float-label">
                                                <input wire:model="form.last_name" type="text" class="md-input"
                                                    required>
                                                <label>Last Name</label>
                                            </div>
                                            @error('form.last_name')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="md-form-group float-label">
                                                <input wire:model="form.phone_number" type="text" class="md-input"
                                                    required>
                                                <label>Phone Number</label>
                                            </div>
                                            @error('form.phone_number')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="md-form-group float-label">
                                                <input wire:model="form.email" type="email" class="md-input" required>
                                                <label>Email</label>
                                            </div>
                                            @error('form.email')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="md-form-group float-label">
                                                <select wire:model="selectedRoles" class="md-input" name="selectedRoles"
                                                    required>
                                                    <option value="">Select Role</option>
                                                    @forelse ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @empty

                                                    @endforelse
                                                </select>
                                                <label>Role</label>
                                            </div>
                                            @error('selectedRoles')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-danger rounded"
                                            wire:click="backToList">Cancel</button>
                                        <button type="submit" class="btn btn-dark rounded">{{ ($editId) ? 'Save Changes'
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
    @elseif($displayApiDetails)
    <div class="mt-4 padding">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <p><strong>Username:</strong> {{ $user->username }}</p>
                            <p><strong>Password:</strong> {{ $form["password"] }}</p>
                            <p><strong>API Key:</strong> {{ $user->api_key }}</p>
                            <button type="button" class="btn btn-danger rounded" wire:click="backToList">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="page-title padding pb-0 ">
        <span class="float-left">
            <h2 class="text-md mb-0 headliner">{{ $content_title }}</h2>
        </span>
        @can('user-list')
        <span class="float-right">
            <button class="btn btn-dark rounded" wire:click="addFunction">{{ ($add) ? 'Back To List' : 'Register User'
                }}</button></a>
        </span>
        @endcan
    </div>
    <div class="mt-4 padding">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <div class="">
                                @if ($add)
                                <form wire:submit.prevent="store">
                                    <div class="p-lg">
                                        <div class="row row-sm">
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <input wire:model="name" class="md-input" name="name" value=""
                                                        required>
                                                    <label>Permission Name</label>
                                                </div>
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
                                    @livewire('core-datatables.users-table')
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>