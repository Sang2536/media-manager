function handleDeleteDrop(event) {
    event.preventDefault();
    const data = event.dataTransfer.getData("text/plain");

    if (!data) return;

    const confirmDelete = confirm("Bạn có chắc muốn xoá mục này?");
    if (confirmDelete) {
        // Gửi yêu cầu xóa qua Ajax hoặc chuyển hướng
        fetch(data, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                alert("Đã xoá thành công!");
                location.reload();
            } else {
                alert("Xoá thất bại!");
            }
        });
    }
}

window.handleDeleteDrop = handleDeleteDrop;
