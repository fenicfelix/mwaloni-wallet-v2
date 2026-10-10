@include('core::includes.header')

<body class="authentication-bg position-relative">
    <div class="position-absolute start-0 end-0 start-0 bottom-0 w-100 h-100">
        <svg xmlns='http://www.w3.org/2000/svg' width='100%' height='100%' viewBox='0 0 800 800'>
            <g fill-opacity='0.22'>
                <circle style="fill: rgba(35, 179, 157, 0.1);" cx='400' cy='400' r='600' />
                <circle style="fill: rgba(35, 179, 157, 0.2);" cx='400' cy='400' r='500' />
                <circle style="fill: rgba(35, 179, 157, 0.3);" cx='400' cy='400' r='300' />
                <circle style="fill: rgba(35, 179, 157, 0.4);" cx='400' cy='400' r='200' />
                <circle style="fill: rgba(35, 179, 157, 0.5);" cx='400' cy='400' r='100' />
            </g>
        </svg>
    </div>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header p-0 pt-4 text-center bg-transparent">
                            <span class="guest-logo"><img src="{{ asset('assets/images/logo.webp') }}"
                                    alt="logo"></span>
                        </div>

                        <div class="card-body p-4">

                            {{ $slot }}
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt">
        <script>
            document.write(new Date().getFullYear())
        </script> © {{ config('app.name') }}
    </footer>
    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jsencrypt/bin/jsencrypt.min.js"></script>

    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            const passwordInput = document.getElementById('password');
        
            const encrypt = new JSEncrypt();
            encrypt.setPublicKey(`{!! file_get_contents(storage_path('keys/public.pem')) !!}`);
        
            const encrypted = encrypt.encrypt(passwordInput.value);
        
            passwordInput.value = encrypted;
        });
    </script>

</body>

</html>