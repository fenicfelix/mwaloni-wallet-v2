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
                <h5 class="header-title">{{ ($add) ? ($formId) ? 'Update Preference' : 'Add Preference' : 'All
                    Preference' }}</h5>
                <div class="float-end ms-2">
                    <button class="btn btn-dark btn-rounded px-4" wire:click="{{ ($add) ? 'backAction' : 'addFunction' }}">
                        <i class="{{ $add ? 'uil-arrow-left' : 'uil-plus' }}"></i>
                        {{ ($add) ? 'Back To List' : 'Add New' }}
                    </button>
                </div>
                
                <div class="clearfix"></div>
        
                @if ($add)
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <p><span class="text-danger">Fields with (*) are required</span></p>
                                <hr>
                                <form wire:submit.prevent="store">
                                    <div class="p-lg">
                                        <div class="row row-sm">
                                            <div class="col-sm-12 mb-3">
                                                <x-wallet::form.input label="Title" wire:model.defer="formData.title" name="title" required />
                                            </div>
                                            <div class="col-sm-12">
                                                <x-wallet::form.textarea label="Value" wire:model.defer="formData.value" name="value" required />
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <x-wallet::button variant="danger" icon="uil-times" wire:click.prevent="backAction">
                                        Cancel
                                    </x-wallet::button>
                                    <x-wallet::button type="submit" icon="uil-check" variant="dark">
                                        Submit
                                    </x-wallet::button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="table-responsive">
                    @livewire('core-datatables.system-preferences-table')
                </div>
                @endif
            </div>
        </div>
    </div>
</div>