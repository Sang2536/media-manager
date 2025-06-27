
//  Handle Delete
async function handleDelete(deleteUrl, options = {}) {
    const {
        confirmMessage = 'Bạn có chắc chắn muốn xóa mục này?',
        onSuccess = null,
        reload = true
    } = options;

    if (!confirm(confirmMessage)) return;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            if (typeof onSuccess === 'function') onSuccess();
            if (reload) location.reload();
        } else {
            const result = await response.json();
            alert(result.message || 'Xóa thất bại.');
        }
    } catch (error) {
        console.error(error);
        alert('Đã có lỗi xảy ra khi xoá.');
    }
}

window.handleDelete = handleDelete;
