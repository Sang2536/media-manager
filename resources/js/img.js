
//  Preview hình ảnh
document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById('file-input');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('preview-image');

    fileInput?.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            // Nếu không phải ảnh hoặc không có file
            previewImg.src = '';
            previewContainer.classList.add('hidden');
        }
    });
});
