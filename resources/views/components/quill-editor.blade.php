@props([
    'name' => 'content',
    'value' => '',
])

@php($id = $name . '_' . Str::random(6))

<div id="{{ $id }}_editor" class="quill-editor-container" style="min-height: 150px;"></div>
<textarea name="{{ $name }}" id="{{ $id }}" class="d-none">{{ $value }}</textarea>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quill = new Quill('#{{ $id }}_editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        const textarea = document.getElementById('{{ $id }}');
        if (textarea.value) {
            quill.clipboard.dangerouslyPasteHTML(textarea.value);
        }

        textarea.closest('form').addEventListener('submit', function () {
            textarea.value = quill.root.innerHTML;
        });
    });
</script>
