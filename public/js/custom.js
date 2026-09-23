document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal[data-auto-show="true"]').forEach((element) => {
        bootstrap.Modal.getOrCreateInstance(element).show();
    });
});
