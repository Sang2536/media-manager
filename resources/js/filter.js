
//  Folder filter
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filter-form');
    const inputs = form.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
        let debounceTimeout;
        input.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => form.submit(), 500);
        });
    });
});
