<div class="container-fluid">
    @if ($add)
    <!-- ############ Main START-->
    <div class="page-container">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users') }}">{{ $content_title }}</a></li>
                            <li class="breadcrumb-item active">{{ $add && !$formId ? 'Add User' : 'Edit User' }} </li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $content_title }}</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
    
        <div class="padding">
            <div class="row">
                <div class="col-sm-12 col-md-6 d-table h-100">
                    <div class="card card-border">
                        <div class="card-body">
                            <p><span class="text-danger">Fields with (*) are required</span></p>
                            <hr>
                            <form wire:submit.prevent="store">
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <x-wallet::form.input label="First Name" wire:model.defer="formData.first_name"
                                            name="first_name" required />
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <x-wallet::form.input label="Last Name" wire:model.defer="formData.last_name"
                                            name="last_name" required />
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12">
                                        <x-wallet::form.input label="Phone Number" wire:model.defer="formData.phone_number"
                                            name="phone_number" required />
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12">
                                        <x-wallet::form.input label="Email" wire:model.defer="formData.email" name="email"
                                            type="email" required />
                                    </div>
                                </div>
    
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <x-wallet::form.select label="Role" wire:model.live="formData.role_id"
                                            :options="$roles->pluck('name', 'id')" placeholder="Choose Role" />
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
    @elseif($view)
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users') }}">{{ $content_title }}</a></li>
                        <li class="breadcrumb-item active">View User </li>
                    </ol>
                </div>
                <h4 class="page-title">View User</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
        <div class="page-content">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">First Name</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ $formData['first_name'] }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Last Name</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ $formData['last_name'] }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Email</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ $formData['email'] }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Phone</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ $formData['phone_number'] }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Role</label>
                                <label class="col-sm-8 col-form-label transaction-label">{{ $formData['role']['name'] }}</label>
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
                            <x-wallet::button class="w-sm" variant="danger" wire:click.prevent="backAction">
                                Cancel
                            </x-wallet::button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
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
                <div class="">
                    <div class="">
                        @if ($add)
                        <form wire:submit.prevent="store">
                            <div class="p-lg">
                                <div class="row row-sm">
                                    <div class="col-sm-12">
                                        <div class="md-form-group float-label">
                                            <input wire:model="name" class="form-control" name="name" value="" required>
                                            <label>Permission Name</label>
                                        </div>
                                    </div>
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
                        @else
                        <h4 class="header-title">List of System Users</h4>
                        <div class="float-end ms-2">
                            <a href="#" wire:click.prevent="addFunction" class="btn btn-dark btn-sm">
                                <i class="uil-plus"></i> Add System User
                            </a>
                        </div>
                        <p class="text-muted font-14 d-none d-md-block">
                            Here you can manage all accounts. You can add, edit, delete and view accounts.
                            <br>
                        </p>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            @livewire('core-datatables.users-table')
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>