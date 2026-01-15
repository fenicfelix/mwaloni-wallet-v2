<div class="flex">
    <!-- ############ Main START-->
    <div class="page-container">
        <div class="page-title padding pb-0 ">
            <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}
            @if (isset($form['service_id']))
                {{ ' - '.$form['service_id'] }}
            @endif    
            </h2></span>
        </div>
        <div class="mt-4"></div>

        <div class="padding">
            <div class="row">
                <div class="col-sm-12 col-md-6 d-table h-100">
                    <div class="card card-border">
                        <div class="card-body">
                            <form wire:submit.prevent="store">
                                    <div class="col-sm-12">
                                    <div class="md-form-group float-label">
                                        <input type="text" class="md-input" wire:model="form.name" id="add-name" wire:keyup="updateUsername" required>
                                        <label>Service Name</label>
                                    </div>
                                    @error('form.name')
                                        <small class="text-danger">{{ $message }} </small>
                                    @enderror
                                </div>
                                <div class="col-sm-12">
                                    <div class="md-form-group float-label">
                                        <textarea class="md-input" wire:model.lazy="form.description" rows="3"></textarea>
                                        <label>Service Description</label>
                                    </div>
                                    @error('form.description')
                                        <small class="text-danger">{{ $message }} </small>
                                    @enderror
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="md-form-group float-label">
                                                <select class="md-input" wire:model="form.client_id" id="add-client_id" >
                                                    <option value="">Select Client</option>
                                                    @forelse ($clients as $client)
                                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                                    @empty
                                                        
                                                    @endforelse
                                                </select>
                                            </div>
                                            @error('form.client_id')
                                                <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <div class="md-form-group float-label">
                                                <select class="md-input" wire:model="form.account_id" id="add-account_id" >
                                                <option value="">Select Account</option>
                                                    @forelse ($accounts as $account)
                                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                    @empty
                                                        
                                                    @endforelse
                                                </select>
                                            </div>
                                            @error('form.account_id')
                                                <small class="text-danger">{{ $message }} </small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="md-form-group float-label">
                                        <input type="url" class="md-input" wire:model.lazy="form.callback_url" id="add-callback_url" >
                                        <label>Callback URL</label>
                                    </div>
                                    @error('form.callback_url')
                                        <small class="text-danger">{{ $message }} </small>
                                    @enderror
                                </div>
                                <div class="row px-3">
                                    <div class="col-sm-6">
                                        <div class="md-form-group float-label">
                                            <input type="number" class="md-input" wire:model.lazy="form.system_charges" id="add-system_charges" required>
                                            <label>System Charges</label>
                                        </div>
                                        @error('form.system_charges')
                                            <small class="text-danger">{{ $message }} </small>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="md-form-group float-label">
                                            <input type="number" class="md-input" wire:model.lazy="form.sms_charges" id="add-sms_charges" required>
                                            <label>SMS Charges</label>
                                        </div>
                                        @error('form.sms_charges')
                                            <small class="text-danger">{{ $message }} </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="md-form-group float-label">
                                        <input type="number" class="md-input" wire:model.lazy="form.max_trx_amount" id="add-max_trx_amount" required>
                                        <label for="add-max_trx_amount">Max Transaction Amount</label>
                                    </div>
                                    @error('form.max_trx_amount')
                                        <small class="text-danger">{{ $message }} </small>
                                    @enderror
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-danger rounded" wire:click="backToList">Cancel</button>
                                    <button type="submit" wire:loading.attr="disabled" class="btn btn-dark rounded">{{ ($editId) ? 'Save Changes' : 'Submit' }}</button>
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