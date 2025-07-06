
//  Tom Select Init
document.addEventListener('DOMContentLoaded', function () {
    const tagSelect = document.querySelector('#tag-select');
    if (tagSelect) {
        new TomSelect("#tag-select", {
            create: true,
            persist: false,
            plugins: ['remove_button'],
            maxItems: null,
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            render: {
                option_create: function (data, escape) {
                    return '<div class="create">➕ Thêm tag mới: <strong>' + escape(data.input) + '</strong></div>';
                }
            }
        });
    }
});

// Global index
let metaIndex = 0;

//  add Metadata
document.addEventListener('DOMContentLoaded', function () {
    // Đếm số metadata có sẵn khi DOM load xong
    metaIndex = document.querySelectorAll('#meta-wrapper input[name$="[key]"]').length;

    // Gắn function toàn cục
    window.addMetaField = function (limit = 10) {
        if (metaIndex >= limit) {
            alert('Đã đạt giới hạn số lượng metadata trong 1 lần.');
            return;
        }

        const wrapper = document.getElementById('meta-wrapper');
        if (!wrapper) return;

        const div = document.createElement('div');
        div.className = 'flex gap-2 mb-2';

        div.innerHTML = `
            <input type="text" name="metadata[${metaIndex}][key]" placeholder="Key" class="w-1/2 border px-2 py-1 rounded">
            <input type="text" name="metadata[${metaIndex}][value]" placeholder="Value" class="w-1/2 border px-2 py-1 rounded">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-lg">&times;</button>
        `;

        wrapper.appendChild(div);
        metaIndex++;
    };
});


//  handle File Select
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('file-select');
    const preview = document.getElementById('select-image');

    if (select && preview) {
        select.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const imgUrl = selectedOption.getAttribute('data-img') || '';
            preview.src = imgUrl;
        });
    }
});
