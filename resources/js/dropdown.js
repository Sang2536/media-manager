//  Btn Dropdown
document.addEventListener('DOMContentLoaded', function () {
    // Toggle dropdown
    document.querySelectorAll('.dropdown-toggle').forEach(function (button) {
        const targetId = button.dataset.target;
        const menu = document.getElementById(targetId);

        if (!menu) return;

        // Toggle menu khi click nút
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            // Ẩn tất cả menu khác
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });

            // Toggle menu này
            menu.classList.toggle('hidden');
        });

        // Click ngoài → ẩn dropdown
        document.addEventListener('click', function (e) {
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    });
});
