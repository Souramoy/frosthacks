<script>
    Swal.fire({
    title: 'Preparing the exam...',
    html: 'You have <b></b> seconds to start!',
    timer: 10000, // Timer set to 10 seconds
    timerProgressBar: true, // Shows the progress bar
    allowOutsideClick: false,
    didOpen: () => {
        Swal.showLoading();
        const b = Swal.getHtmlContainer().querySelector('b');
        let timerInterval = setInterval(() => {
            b.textContent = Math.floor(Swal.getTimerLeft() / 1000);
        }, 100);
    },
    willClose: () => {
        clearInterval(timerInterval);
    }
});

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>