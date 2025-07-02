
//  Folder filter
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filter-form');

    if (filterForm) {
        const inputs = filterForm.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            let debounceTimeout;
            input.addEventListener('input', () => {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => filterForm.submit(), 500);
            });
        });
    }
});
