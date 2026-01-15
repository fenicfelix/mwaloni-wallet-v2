<div class="row">
    <div class="col-12">
        <div class="page-title padding pb-0 ">
            <span class="float-left"><h2 class="text-md mb-0 headliner">{{ $content_title }}</h2></span>
            @can('user-list')
                <span class="float-right">
                    <button class="btn btn-dark rounded" wire:click="addFunction">{{ ($add) ? 'Back To List' : 'Register Service' }}</button></a>
                </span>
            @endcan
        </div>
    </div>
</div>

<div class="mt-4 padding">
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                @livewire('core-datatables.services-table')
            </div>
        </div>
    </div>
</div>