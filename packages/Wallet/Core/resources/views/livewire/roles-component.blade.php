<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">{{ $content_title }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $content_title }}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="mt-4 padding">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <h5 class="header-title">{{ ($add) ? ($formId) ? 'Update Role' : 'Add Role' : 'All
                            Roles' }}</h5>
                        <div class="float-end ms-2">
                            <button class="btn btn-dark btn-rounded px-4" wire:click="{{ ($add) ? 'backAction' : 'addFunction' }}">
                                <i class="{{ $add ? 'uil-arrow-left' : 'uil-plus' }}"></i>
                                {{ ($add) ? 'Back To List' : 'Add New' }}
                            </button>
                        </div>
                        
                        <div class="clearfix"></div>

                        @if ($add)
                        <div class="card mt-4">
                            <div class="card-body">
                                <p><span class="text-danger">Fields with (*) are required</span></p>
                                <hr>
                                <form wire:submit.prevent="store">
                                    <div class="p-lg">
                                        <div class="row row-sm">
                                            <div class="row">
                                                <div class="col-12 col-md-4 mb-3">
                                                    <x-wallet::form.input label="Role Name" wire:model.defer="formData.name" name="name" required />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-4 mb-3">
                                                    <x-wallet::form.select label="Permission Type" wire:model.live="formData.permission_type"
                                                        :options="$permissionTypes" placeholder="Choose Type" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <x-wallet::form.textarea label="Description" wire:model.lazy="formData.description" rows="3" />
                                                </div>
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
                                                        <x-wallet::form.switch :label="$group" wire:click.prevent="toggleGroup('{{ $group }}')" />
                                                    </div>
                                                    <div class="row row-sm">
                                                        @foreach ($permissions as $key => $value)
                                                        <div class="col-12 mb-3 ml-3">
                                                            <x-wallet::form.switch :label="$value" :value="$key"
                                                                wire:model.defer="formData.permissions" />
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
                                        <x-wallet::button variant="danger" icon="uil-times" wire:click.prevent="backAction">
                                            Cancel
                                        </x-wallet::button>
                                        <x-wallet::button type="submit" icon="uil-check" variant="dark">
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