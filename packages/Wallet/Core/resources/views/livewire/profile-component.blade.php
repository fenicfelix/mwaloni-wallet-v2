<div>
    <div class="page-title padding pb-0 ">
        <span class="float-left">
            <h2 class="text-md mb-0 headliner">{{ $content_title }}</h2>
        </span>
    </div>
    <div class="mt-4 padding">
        <div class="row">
            <div class="col-xsm-12 col-sm-6 col-md-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link {{ ($changePassword) ? '' : 'active' }}" href="#" wire:click="toggleView">My
                        Information</a>
                    <a class="nav-link {{ ($changePassword) ? 'active' : '' }}" href="#" wire:click="toggleView">Change
                        Password</a>
                </div>
            </div>
            <div class="col-md-9 col-sm-12">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="card card-border">
                            <div class="card-body">
                                @if ($changePassword)
                                <form wire:submit.prevent="changePassword">
                                    <div class="modal-body p-lg">
                                        <div class="row row-sm">
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <input type="password" class="md-input"
                                                        wire:model="current_password" name="current_password" value=""
                                                        required>
                                                    <label>Current Password</label>
                                                </div>
                                                @error('current_password')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <input type="password" class="md-input" wire:model="new_password"
                                                        name="new_password" value="" required>
                                                    <label>New Password</label>
                                                </div>
                                                @error('new_password')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="md-form-group float-label">
                                                    <input type="password" class="md-input"
                                                        wire:model="confirm_password" name="confirm_password" value=""
                                                        required>
                                                    <label>Confirm Password</label>
                                                </div>
                                                @error('confirm_password')
                                                <small class="text-danger">{{ $message }} </small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger btn-rounded w-sm"
                                            data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-dark btn-rounded w-sm">Submit</button>
                                    </div>
                                </form>
                                @else
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <small class="text-muted">First Name</small>
                                        <div class="my-3 border-bottom">{{ $user->first_name }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Last Name</small>
                                        <div class="my-3 border-bottom">{{ $user->last_name }}</div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-12">
                                        <small class="text-muted">Phone Number</small>
                                        <div class="my-3 border-bottom">{{ $user->phone_number }}</div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-12">
                                        <small class="text-muted">Email Address</small>
                                        <div class="my-3 border-bottom">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <small class="text-muted">Role</small>
                                        <div class="my-3 border-bottom">{{ $user->role->name }}</div>
                                    </div>
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