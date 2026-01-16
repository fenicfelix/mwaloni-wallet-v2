<div class="flex">
    <!-- ############ Main START-->
    <div class="page-container">
        <div class="page-title padding pb-0 ">
            <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}</h2></span>
        </div>
        <div class="mt-4"></div>

        <div class="padding">
            <div class="row">
                <div class="col-sm-12 col-md-6 d-table h-100">
                    <div class="card card-border">
                        <div class="card-body">
                            <form wire:submit.prevent="store">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="md-form-group float-label">
                                                <select class="md-input" wire:model="form.account_type_id">
                                                    <option value="">Account Type</option>
                                                    @forelse ($account_types as $account_type)
                                                        <option value="{{ $account_type->id }}">{{ $account_type->account_type}}</option>
                                                    @empty
                                                        
                                                    @endforelse
                                                </select>
                                                <label>Account Type</label>
                                            </div>
                                            @error('form.account_type_id')
                                                <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <div class="md-form-group float-label">
                                                <select class="md-input" wire:model="form.currency_id">
                                                    <option value="">Select Currency</option>
                                                    @forelse ($currencies as $currency)
                                                        <option value="{{ $currency->id }}">{{ $currency->name}}</option>
                                                    @empty
                                                        
                                                    @endforelse
                                                </select>
                                                <label>Currency</label>
                                            </div>
                                            @error('form.currency_id')
                                                <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="md-form-group float-label">
                                                        <input type="text" class="md-input" wire:model="form.name" required>
                                                        <label>Account Name</label>
                                                    </div>
                                                    @error('form.name')
                                                        <small class="text-danger">{{ $message }} </small>
                                                    @enderror
                                                </div>
                                                <div class="col-6">
                                                    <div class="md-form-group float-label">
                                                        <input type="text" class="md-input" wire:model="form.account_number" required>
                                                        <label>{{ ($form['account_type_id'] == "1") ?  'Daraja Shortcode' : 'Account Number'}}</label>
                                                    </div>
                                                    @error('form.account_number')
                                                        <small class="text-danger">{{ $message }} </small>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="md-form-group float-label">
                                                <select wire:model="form.managed_by" class="md-input"required>
                                                    <option value="">Account Manager</option>
                                                    @forelse ($account_managers as $manager)
                                                    <option value="{{ $manager->id }}">{{ $manager->first_name." ".$manager->last_name }}</option>
                                                    @empty
                                    
                                                    @endforelse
                                                </select>
                                                <label>Account Manager</label>
                                            </div>
                                            @error('form.managed_by')
                                            <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                    @if ($form['account_type_id'] != "1")
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="form.country_name" required>
                                                    <label>Country of Registration</label>
                                                </div>
                                                @error('form.country_name')
                                                    <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="form.country_code" required>
                                                    <label>Country Code</label>
                                                </div>
                                                @error('form.country_code')
                                                    <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="form.bank_code" required>
                                                    <label>Bank Code</label>
                                                </div>
                                                @error('form.bank_code')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="form.branch_code" required>
                                                    <label>Branch Code</label>
                                                </div>
                                                @error('form.branch_code')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                    @if ($form['account_type_id'] == "1")
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="form.consumer_key" required>
                                                    <label>Consumer Key</label>
                                                </div>
                                                @error('form.consumer_key')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="form.consumer_secret" required>
                                                    <label>Consumer Secret</label>
                                                </div>
                                                @error('form.consumer_secret')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                        </div>
                                    @else
                                        @if ($form['account_type_id'] == "3")
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="md-form-group float-label">
                                                        <input type="text" class="md-input" wire:model="form.cif" required>
                                                        <label>CIF</label>
                                                    </div>
                                                    @error('form.cif')
                                                    <small class="text-danger">{{ $message }} </small>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="md-form-group float-label">
                                                        <input type="text" class="md-input" wire:model="form.pesalink_cif" required>
                                                        <label>Pesalink CIF</label>
                                                    </div>
                                                    @error('form.pesalink_cif')
                                                    <small class="text-danger">{{ $message }} </small>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="md-form-group float-label">
                                                        <input type="text" class="md-input" wire:model="form.consumer_key" required>
                                                        <label>API Key</label>
                                                    </div>
                                                    @error('form.consumer_key')
                                                    <small class="text-danger">{{ $message }} </small>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="form.address" required>
                                                    <label>Address</label>
                                                </div>
                                                @error('form.address')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="form.api_username">
                                                    <label>API Username</label>
                                                </div>
                                                @error('form.api_username')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <div class="md-form-group float-label">
                                                    <input type="text" class="md-input" wire:model="form.api_password">
                                                    <label>API Password</label>
                                                </div>
                                                @error('form.api_password')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-danger btn-rounded w-sm" wire:click="backToList">Cancel</button>
                                    <button type="submit" class="btn btn-dark btn-rounded w-sm">{{ ($editId) ? 'Save Changes' : 'Submit' }}</button>
                                    <span class="d-custom-none">
                                        <img src="{{ asset('themes/agile/img/working.gif') }}" width="20" alt=""> <small>please wait...</small>
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