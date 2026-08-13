<style>
    .quill-editor-container .ql-editor {
        min-height: 150px;
        max-height: 500px;
        overflow-y: auto;
        resize: vertical;
    }

    .badge-inherit-size {
        font-size: .875em;
        font-weight: normal;
        cursor: default;
    }

    .disabled-link {
        pointer-events: none;
        cursor: default;
        color: var(--bs-gray-600);
    }

    .badge-disabled {
        /* pointer-events: none; */
        /* cursor: default !important; */
        opacity: 0.6;
    }

    .custom-span {
        border: none !important;
        background-color: transparent !important;
        padding: 0.375rem 0 !important; /* Match the vertical padding of form-control */
        box-shadow: none !important;
    }

    label, .col-form-label {
        font-weight: bold;
    }

    .financial-summary label {
        font-weight: normal;
    }
    .ms-2 {
        margin-left: 0.5rem !important; /* Adjust the margin to control spacing for Vacant Buttons */
    }

    .form-switch .form-check-input:checked ~ .form-check-label {
        color: black;
    }

    .form-switch .form-check-input:not(:checked) ~ .form-check-label {
        color: var(--bs-gray-500);
        opacity: 0.6;
    }

    .form-control[readonly] {
        background-color: var(--bs-gray-200) !important;
        opacity: 1;
    }
    .input-group:has(.form-control[readonly]) .input-group-text {
        background-color: var(--bs-gray-200);
    }
</style>
