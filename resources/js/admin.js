// Shows a picked image in the product form's preview box before it is uploaded.
// A data: URL is used because the Content Security Policy allows data: images.
document.querySelectorAll('input[type=file][data-preview]').forEach((input) => {
    const box = document.getElementById(input.dataset.preview);
    const preview = box.querySelector('[data-preview-image]');
    const current = box.querySelector('[data-preview-current]');

    input.addEventListener('change', () => {
        const file = input.files[0];

        if (!file || !file.type.startsWith('image/')) {
            preview.hidden = true;
            preview.removeAttribute('src');
            current.hidden = false;
            return;
        }

        const reader = new FileReader();
        reader.onload = () => {
            preview.src = reader.result;
            preview.hidden = false;
            current.hidden = true;
        };
        reader.readAsDataURL(file);
    });
});
