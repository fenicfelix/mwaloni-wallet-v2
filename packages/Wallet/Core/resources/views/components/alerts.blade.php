<!-- Toastr -->
<script src="{{ asset('themes/agile/libs/toastr/toastr.min.js') }}"></script>
<script>
    window.addEventListener('alert', ({
		detail: {
			type,
			message
		}
	}) => {
        toastr[type](message, type.toUpperCase()+"!", {
            closeButton: true,
            progressBar: true,
            timeOut: 3000
        });
	})

</script>