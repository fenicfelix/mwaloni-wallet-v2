
    <footer>
        <div class="container mt-2">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2">
                    <p class="text-center py-2 pt-2 b-t">&copy; {{ date('Y') }} | {{ config('app.name') }}</p>
                </div>
            </div>
        </div>
    </footer>
    
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

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- theme -->
    <script src="{{ asset('themes/agile/js/theme.js') }}"></script>

    <script>
        $("#action-logout").on('click', function (e) {
            e.preventDefault();
            $("#logout-form").submit();
        });
    </script>

    <script>
        document.addEventListener('livewire:init', () => {
            // Open modal
            window.addEventListener('open-modal', event => {
                const modalId = event.detail.id;
                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();
            });

            // Close modal
            window.addEventListener('close-modal', event => {
                const modalId = event.detail.id;
                const modalEl = document.getElementById(modalId);
                
                if (!modalEl) return;
                
                let modal = bootstrap.Modal.getInstance(modalEl);
                
                if (!modal) {
                    modal = new bootstrap.Modal(modalEl);
                }
                
                modal.hide();
            });

            Livewire.on('alert', (event) => {
                toastr[event[0].type](event[0].message, {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 3000
                });
                $.NotificationApp.send("Heads up!", event[0].message, "top-right", "rgba(0,0,0,0.2)", event[0].type);
            });
            Livewire.on('confirm', (event) => {
                Swal.fire({
                    title: event[0].title,
                    text: event[0].message,
                    icon: event[0].type,
                    confirmButtonText: event[0].confirmTitle,
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (event[0].callback) {
                            Livewire.dispatch(event[0].callback, event[0].data);
                        }
                    } else if (result.isDismissed) {
                        if (event[0].onCancel) {
                            Livewire.dispatch(event[0].onCancel, event[0].data);
                        }
                    }
                });
            });
        });
    </script>
    
    @stack('js')

    </body>

</html>