@include('core::includes.header')

    <div id="main" class="d-flex flex-column flex">
        <!-- ############ LAYOUT START-->

        @yield("main_body")

    </div>

    <footer>
        <div class="container mt-2">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2">
                    <p class="text-center py-2 pt-2 b-t">© 2026 | Mwaloni Wallet</p>
                </div>
            </div>
        </div>
    </footer>

@include('core::includes.footer')