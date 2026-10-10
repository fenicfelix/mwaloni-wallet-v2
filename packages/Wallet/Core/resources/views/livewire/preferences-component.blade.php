<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
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
                <h5 class="header-title">{{ ($add) ? ($formId) ? 'Update Preference' : 'Add Preference' : 'All
                    Preference' }}</h5>
                <div class="float-end ms-2">
                    <button class="btn btn-dark btn-rounded px-4" wire:click="{{ ($add) ? 'backAction' : 'addFunction' }}">{{
                        ($add) ? 'Back To List' :
                        'Add New' }}</button>
                </div>
        
                <div class="clearfix"></div>
        
                @if ($add)
                <p class="text-muted font-14">
                    Here you can {{ $formId ? 'edit a' : 'add a new' }} role. Please fill in the details below.
                    <br>
                    <span class="text-danger">Fields with (*) are required</span>
                </p>
                <form wire:submit.prevent="store">
                    <div class="p-lg">
                        <div class="row row-sm">
                            <div class="col-sm-12">
                                <div class="md-form-group float-label">
                                    <x-wallet::form.input label="Title" wire:model.defer="formData.title" name="title"
                                        required />
                                </div>
                            </div>
                            <div class="col-sm-12 mt-4">
                                <x-wallet::form.input label="Value" wire:model.defer="formData.value" name="value" required />
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
                <div class="table-responsive">
                    @livewire('core-datatables.system-preferences-table')
                </div>
                @endif
            </div>
        </div>
    </div>
</div>