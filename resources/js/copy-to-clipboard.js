
//  copy to clipboard

//  Kiểm tra trình duyệt có hỗ trợ không
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text)
            .then(() => alert("✅ Đã copy vào clipboard! URL: " + text))
            .catch(err => alert("❌ Copy thất bại: " + err));
    } else {
        fallbackCopyText(text);
    }
}


//  Dự phòng cho trình duyệt cũ (fallback):
function fallbackCopyText(text) {
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.setAttribute("readonly", "");
    textarea.style.position = "absolute";
    textarea.style.left = "-9999px";
    document.body.appendChild(textarea);
    textarea.select();

    try {
        const successful = document.execCommand("copy");
        alert(successful ? ("✅ Đã copy! URL: " + text) : "❌ Không thể copy.");
    } catch (err) {
        alert("❌ Trình duyệt không hỗ trợ copy.");
    }

    document.body.removeChild(textarea);
}


// Gắn vào global scope
window.copyToClipboard = copyToClipboard;
