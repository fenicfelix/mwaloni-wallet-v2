<!-- Vendor js -->
<script src="{{ asset('assets/js/vendor.min.js') }}"></script>

<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>

@stack('js')

<!-- Toastr js -->
<script src="{{ asset('assets/vendor/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>

<!-- Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('livewire:init', () => {
                Livewire.on('alert', (event) => {
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

</body>

</html>