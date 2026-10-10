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

{{-- <div class="row">
    <div class="col-12">
        <div class="page-title padding pb-0 ">
            <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}</h2></span>
            <span class="float-right">
                <button class="btn btn-dark btn-rounded px-4" wire:click="addFunction">{{ ($add) ? 'Back To List' : 'Register Service'}}</button>
            </span>
        </div>
    </div>
</div> --}}

<div class="mt-4 padding">
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                @livewire('core-datatables.services-table')
            </div>
        </div>
    </div>
</div>