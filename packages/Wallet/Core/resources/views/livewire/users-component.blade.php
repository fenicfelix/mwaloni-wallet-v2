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
                                            <x-wallet::form.input label="First Name" wire:model.defer="formData.first_name" name="first_name" required />
                                        </div>
                                        <div class="col-sm-6">
                                            <x-wallet::form.input label="Last Name" wire:model.defer="formData.last_name" name="last_name" required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <x-wallet::form.input label="Phone Number" wire:model.defer="formData.phone_number" name="phone_number" required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <x-wallet::form.input label="Email" wire:model.defer="formData.email" name="email" type="email" required />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <x-wallet::form.select label="Role" wire:model.live="formData.role_id" :options="$roles->pluck('name', 'id')"
                                                placeholder="Choose Role" />
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
    @elseif($displayApiDetails)
    <div class="mt-4 padding">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <p><strong>Username:</strong> {{ $user->username }}</p>
                            <p><strong>Password:</strong> {{ $formData["password"] }}</p>
                            <p><strong>API Key:</strong> {{ $user->api_key }}</p>
                            <button type="button" class="btn btn-danger btn-rounded w-sm" wire:click="backToList">Cancel</button>
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
        <span class="float-right">
            <button class="btn btn-dark btn-rounded px-4" wire:click="addFunction">{{ ($add) ? 'Back To List' : 'Register User'
                }}</button>
        </span>
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
                                        <button type="button" class="btn btn-danger btn-rounded w-sm" data-dismiss="modal"
                                            wire:click="addFunction">Cancel</button>
                                        <button type="submit" class="btn btn-dark btn-rounded w-sm">Submit</button>
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