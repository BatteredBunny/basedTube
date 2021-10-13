const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true
})

function share(link) {
    navigator.clipboard.writeText(link)

    Toast.fire({
        icon: 'success',
        title: 'Copied link'
    })
}