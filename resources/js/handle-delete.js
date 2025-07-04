
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

function clearAll(route, options = {}) {
    const {
        confirmText = 'Bạn có chắc chắn muốn xoá toàn bộ dữ liệu?',
        successMessage = '🗑️ Đã xoá thành công!',
        onSuccess = () => location.reload(), // Có thể thay bằng function khác
    } = options;

    if (!confirm(confirmText)) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        alert('❌ Thiếu CSRF token.');
        return;
    }

    fetch(route, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
        .then(async (response) => {
            if (!response.ok) throw new Error('Lỗi khi gửi yêu cầu xoá.');

            // Nếu không có body JSON (204 No Content), không gọi .json()
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                return response.json();
            } else {
                return { message: 'Thao tác xoá đã được thực hiện.' };
            }
        })
        .then(data => {
            alert(data.message);
            onSuccess();
        })
        .catch(error => {
            alert("❌ Xoá thất bại: " + error.message);
        });
}


window.handleDelete = handleDelete;
window.clearAll = clearAll;
