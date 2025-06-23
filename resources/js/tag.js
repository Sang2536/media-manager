
//  Delete Tag
function deleteTag(button) {
    const url = button.dataset.url;
    const tagId = button.dataset.tagId;

    if (!confirm('Bạn có chắc muốn xóa tag này?')) return;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            // Xoá tag khỏi DOM
            const tagElement = document.getElementById('tag-' + tagId);
            if (tagElement) tagElement.remove();
        } else {
            alert('Không thể xoá tag. Vui lòng thử lại.');
        }
    })
    .catch(() => {
        alert('Có lỗi xảy ra khi gửi yêu cầu.');
    });
}

window.deleteTag = deleteTag;

