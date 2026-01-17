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
                            <h5 class="modal-title">{{ ($add) ? ($formId) ? 'Update Role' : 'Add Role' : 'All Roles' }}
                            </h5>
                            <button class="btn btn-dark btn-rounded px-4" wire:click="{{ ($add) ? 'backAction' : 'addFunction' }}">{{ ($add) ? 'Back To List' :
                                'Add New' }}</button>
                        </div>

                        @if ($add)
                        <div class="card">
                            <div class="card-body">
                                <form wire:submit.prevent="store">
                                    <div class="p-lg">
                                        <div class="row row-sm">
                                            <div class="col-sm-12">
                                                <x-wallet::form.input label="Role Name" wire:model.defer="formData.name" name="name" required />
                                            </div>
                                            <div class="col-12">
                                                <x-wallet::form.textarea label="Description" wire:model.lazy="formData.description" rows="3" />
                                            </div>
                                            <div class="col-12">
                                                <x-wallet::form.select label="Permission Type" wire:model.live="formData.permission_type" :options="$permissionTypes"
                                                    placeholder="Choose Type" />
                                            </div>
                                            <hr>
                                            @if (isset($formData['permission_type']) && $formData['permission_type'] === 'all')
                                                <div class="alert alert-info">
                                                    This role has full access. Individual permissions are not required.
                                                </div>
                                            @elseif (isset($formData['permission_type']) && $formData['permission_type'] !== 'all')
                                                <div class="row row-sm">
                                                    @foreach ($permissionsConfig as $group => $permissions)
                                                    <div class="col-12 col-md-3 mb-3">
                                                        <div class="mb-2">
                                                            <x-wallet::form.switch :label="$group" wire:click.prevent="toggleGroup('{{ $group }}')"/>
                                                        </div>
                                                        <div class="row row-sm">
                                                            @foreach ($permissions as $key => $value)
                                                            <div class="col-12 mb-3 ml-3">
                                                                <x-wallet::form.switch :label="$value" :value="$key" wire:model.defer="formData.permissions" />
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                        </div>
                                    </div>
                                    <hr>
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
                        @else
                        <div class="table-responsive">
                            @livewire('core-datatables.roles-table')
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>