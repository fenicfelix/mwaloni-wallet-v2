<div class="container-fluid">
    @if ($add)
    <!-- ############ Main START-->
    <div class="page-container">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ $content_title }}</h4>
                </div>
            </div>
        </div>
    
        <div class="padding">
            <div class="row">
                <div class="col-sm-12 col-md-6 d-table h-100">
                    <div class="card card-border">
                        <div class="card-body">
                            <form wire:submit.prevent="store">
                                <div class="row">
                                    <div class="mb-3">
                                        <x-wallet::form.input label="Name" wire:model.defer="formData.name" name="name" required />
                                    </div>

                                    <div class="mb-3">
                                        <x-wallet::form.select label="Account Manager" wire:model.live="formData.account_manager" :options="$managers"
                                            placeholder="Choose Account Manager" />
                                    </div>
                                </div>
                                <hr>
                                <x-wallet::button class="w-sm" variant="danger" icon="uil-times" wire:click.prevent="backAction">
                                    Cancel
                                </x-wallet::button>
                                <x-wallet::button type="submit" class="w-sm" icon="uil-check" variant="dark">
                                    Submit
                                </x-wallet::button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- <div class="row">
        <div class="col-12">
            <div class="page-title padding pb-0 ">
                <span class="float-left">
                    <h2 class="text-md mb-0 headliner">{{ $content_title }}</h2>
                </span>
                <span class="float-right">
                    <button class="btn btn-dark btn-rounded px-4" wire:click="addFunction">{{ ($add) ? 'Back To List' : 'Add
                        Client' }}</button>
                </span>
            </div>
        </div>
    </div> --}}

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
            <div class="col-sm-12">
                <h4 class="header-title">List of Clients</h4>
                <div class="float-end ms-2">
                    <a href="#" wire:click.prevent="addFunction" class="btn btn-dark btn-sm">
                        <i class="uil-plus"></i> Add Client
                    </a>
                </div>
                <p class="text-muted font-14 d-none d-md-block">
                    Here you can manage all accounts. You can add, edit, delete and view accounts.
                    <br>
                </p>
                <div class="clearfix"></div>
                <div class="table-responsive">
                    @livewire('core-datatables.clients-table')
                </div>
            </div>
        </div>
    </div>
    @endif
</div>