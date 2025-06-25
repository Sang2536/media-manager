//  Btn Dropdown
document.addEventListener('DOMContentLoaded', function () {
    // Toggle dropdown
    document.querySelectorAll('[data-toggle="dropdown"]').forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation(); // Ngăn đóng ngay sau khi mở
            const targetId = this.getAttribute('data-target');
            const menu = document.getElementById(targetId);

            if (!menu) return;

            // Ẩn tất cả dropdown khác trước
            document.querySelectorAll('[data-toggle="dropdown"]').forEach(btn => {
                const otherId = btn.getAttribute('data-target');
                if (otherId !== targetId) {
                    const otherMenu = document.getElementById(otherId);
                    otherMenu?.classList.add('hidden');
                }
            });

            // Toggle menu hiện tại
            menu.classList.toggle('hidden');
        });
    });

    // Ẩn dropdown nếu click ra ngoài
    document.addEventListener('click', function () {
        document.querySelectorAll('[data-toggle="dropdown"]').forEach(button => {
            const targetId = button.getAttribute('data-target');
            const menu = document.getElementById(targetId);
            menu?.classList.add('hidden');
        });
    });

    // Ngăn dropdown đóng khi click vào chính nó
    document.querySelectorAll('[id^="dropdownMenu-"]').forEach(menu => {
        menu.addEventListener('click', e => {
            e.stopPropagation();
        });
    });
});


//  Btn Collapse
document.addEventListener('DOMContentLoaded', function () {
    //  Toggle Collapse
    document.querySelectorAll('[data-toggle="collapse"]').forEach(button => {
        button.addEventListener('click', function () {
            const selector = this.getAttribute('data-target');
            if (!selector) return;

            const target = document.querySelector(selector);
            if (!target) return;

            console.log(target);
            target.classList.toggle('hidden');
        });
    });
});



//  Btn Offcanvas
document.addEventListener('DOMContentLoaded', function () {
    // Toggle Offcanvas
    document.querySelectorAll('[data-toggle="collapse"]').forEach(function (button) {
        //
    });
});
