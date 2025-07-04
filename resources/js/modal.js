//  JS Modal


//  Mở Modal
function openModal(route) {
    const modal = document.getElementById('wrapperModal');
    const modalContent = document.getElementById('modalContent');

    if (!modal || !modalContent) return;

    const view = modalContent.getAttribute('data-view');

    let url = (!view) ? route : route + "?view=" + view;

    fetch(url)
        .then(res => res.text())
        .then(html => {
            modalContent.innerHTML = html;  // data là response từ fetch API
            modal.classList.remove('hidden');
        })
        .catch(() => {
            modalContent.innerHTML = '<div class="text-red-500">Không thể tải nội dung.</div>';
        });
}


//  Đóng Modal
function closeModal() {
    document.getElementById('wrapperModal').classList.add('hidden');
}


// Gắn vào global scope
window.openModal = openModal;
window.closeModal = closeModal;
