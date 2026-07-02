import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import {
    BlockQuote,
    Bold,
    ClassicEditor,
    Essentials,
    Heading,
    Italic,
    Link,
    List,
    Paragraph,
} from 'ckeditor5';

document.querySelectorAll('[data-rich-text-editor]').forEach((element) => {
    ClassicEditor.create(element, {
        licenseKey: 'GPL',
        plugins: [
            Essentials,
            Paragraph,
            Bold,
            Italic,
            Heading,
            List,
            Link,
            BlockQuote,
        ],
        toolbar: [
            'undo',
            'redo',
            '|',
            'heading',
            '|',
            'bold',
            'italic',
            'link',
            'blockQuote',
            '|',
            'bulletedList',
            'numberedList',
        ],
        placeholder: element.dataset.editorPlaceholder ?? '',
        updateSourceElementOnDestroy: true,
        updateSourceElementOnSubmit: true,
    }).then((editor) => {
        const form = element.closest('form');

        if (form instanceof HTMLFormElement) {
            form.addEventListener('submit', () => {
                element.value = editor.getData();
            });
        }
    }).catch((error) => {
        console.error('CKEditor initialization failed.', error);
    });
});
