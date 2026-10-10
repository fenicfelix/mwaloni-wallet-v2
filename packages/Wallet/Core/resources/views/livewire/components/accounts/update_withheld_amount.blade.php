<!-- ############ Main START-->
<div class="page-container">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('accounts') }}">Accounts</a></li>
                        <li class="breadcrumb-item active">{{ $content_title }}</li>
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
                        <form wire:submit.prevent="saveWithheldAmount">
                            <div class="col-12">
                                <x-wallet::form.input type="number" label="Withheld Amount"
                                    wire:model.defer="formData.withheld_amount" name="withheld_amount" required />
                            </div>
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
            </div>
        </div>
    </div>
</div>