<div class="flex">
    <!-- ############ Main START-->
    <div class="page-container">
        <div class="page-title padding pb-0 ">
            <span class="float-left">
                <h2 class="text-md mb-0 headliner">{{ $content_title }}</h2>
            </span>
        </div>
        <div class="mt-4"></div>

        <div class="padding">
            <div class="row">
                <div class="col-sm-12 col-md-6 d-table h-100">
                    <div class="card card-border">
                        <div class="card-body">
                            <form wire:submit.prevent="saveWithheldAmount">
                                <div class="col-12">
                                    <div class="md-form-group float-label">
                                        <input type="text" class="md-input" wire:model="form.withheld_amount" required>
                                        <label>Withheld Amount</label>
                                    </div>
                                    @error('form.withheld_amount')
                                    <small class="text-danger">{{ $message }} </small>
                                    @enderror
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-danger rounded"
                                        wire:click="backToList">Cancel</button>
                                    <button type="submit" class="btn btn-dark rounded">Update Withheld Amount</button>
                                    <span class="d-custom-none">
                                        <img src="{{ asset('themes/agile/img/working.gif') }}" width="20" alt="">
                                        <small>please wait...</small>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>