var popoverTrigger = new bootstrap.Popover(document.querySelector('#confirm-password'), {
    trigger: 'manual'
});

document.querySelector('#confirm-password').addEventListener('blur', function() {
    if (this.value == document.querySelector('#password').value) {
        popoverTrigger.hide();
    } else {
        popoverTrigger.show();
    }
})


