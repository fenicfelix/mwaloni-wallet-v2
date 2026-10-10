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
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <h4 class="header-title">List of Messages</h4>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            @livewire('core-datatables.messages-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>