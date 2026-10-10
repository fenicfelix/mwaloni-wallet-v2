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
            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link dark-link {{ ($changePassword) ? '' : 'active' }}" href="#" wire:click="toggleView">My
                        Information</a>
                    <a class="nav-link dark-link {{ ($changePassword) ? 'active' : '' }}" href="#" wire:click="toggleView">Change
                        Password</a>
                </div>
            </div>
            <div class="col-md-9 col-sm-12">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="card card-border">
                            <div class="card-body">
                                @if ($changePassword)
                                <p><span class="text-danger">Fields with (*) are required</span></p>
                                <hr>
                                <form wire:submit.prevent="changePassword">
                                    <div class="modal-body p-lg">
                                        <div class="row row-sm">
                                            <div class="col-sm-12 mb-3">
                                                <x-wallet::form.input label="Current Password" wire:model.defer="current_password" name="current_password" type="password" required />
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <x-wallet::form.input label="New Password" wire:model.defer="new_password" name="new_password" type="password" required />
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <x-wallet::form.input label="Confirm Password" wire:model.defer="confirm_password" name="confirm_password" type="password" required />
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <x-wallet::button type="submit" icon="uil-check" variant="dark">
                                        Submit
                                    </x-wallet::button>
                                </form>
                                @else
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">First Name</label>
                                    <label class="col-sm-8 col-form-label transaction-label">{{ $user->first_name }}</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Last Name</label>
                                    <label class="col-sm-8 col-form-label transaction-label">{{ $user->last_name }}</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Email</label>
                                    <label class="col-sm-8 col-form-label transaction-label">{{ $user->email }}</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Phone</label>
                                    <label class="col-sm-8 col-form-label transaction-label">{{ $user->phone_number }}</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Role</label>
                                    <label class="col-sm-8 col-form-label transaction-label">{{ $user->role->name }}</label>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($changePassword)
                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <h4>Password Rules</h4>
                                            </div>
                                            <div class="col-12">
                                                <ol>
                                                    <li>Must be at least 10 characters in length</li>
                                                    <li>Must contain at least one lowercase letter</li>
                                                    <li>Must contain at least one uppercase letter</li>
                                                    <li>Must contain at least one digit</li>
                                                    <li>Must contain a special character</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>