    <footer>
        <div class="container mt-2">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2">
                    <p class="text-center py-2 pt-2 b-t">&copy; {{ date('Y') }} | {{ config('app.name') }}</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        var fetch_mpesa_balance_url = "";
        var table_processer = "";
        var dt_serverside = false;
    </script>

    
    
    <!-- Bootstrap -->
    <script src="{{ asset('themes/agile/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('themes/agile/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- ajax page -->
    <script src="{{ asset('themes/agile/libs/pace-progress/pace.min.js') }}"></script>
    <script src="{{ asset('themes/agile/libs/pjax/pjax.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('themes/agile/libs/toastr/toastr.min.js') }}"></script>
    <!-- lazyload plugin -->
    <script src="{{ asset('themes/agile/js/lazyload.config.js') }}"></script>
    <script src="{{ asset('themes/agile/js/lazyload.js') }}"></script>
    <script src="{{ asset('themes/agile/js/plugin.js') }}"></script>


    <!-- theme -->
    <script src="{{ asset('themes/agile/js/theme.js') }}"></script>
    {{-- <script src="{{ asset('themes/agile/js/functions.js?v=1.0.2') }}"></script> --}}

    {{-- @livewireScripts --}}

    @stack('js')
    @yield('js')

    <script>
        $("#action-logout").on('click', function (e) {
            e.preventDefault();
            $("#logout-form").submit();
        });
    </script>


    <?php
    if (session()->has('success')) {
    ?>
        <script>
            toastr["success"]('{{ session()->get('success') }}', "Success!", {
                closeButton: true,
                progressBar: true,
                timeOut: 3000
            });
        </script>
    <?php
    }
    if (session()->has('error')) {
    ?>
        <script>
            toastr["error"]("{{ session()->get('error') }}", "Error!", {
                closeButton: true,
                progressBar: true,
                timeOut: 3000
            });
        </script>
    <?php
    }
    ?>
    </body>

    </html>