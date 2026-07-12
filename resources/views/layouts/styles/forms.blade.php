<style>
    .disabled-link {
        pointer-events: none; /* Prevent click events */
        cursor: default; /* Change cursor to default */
        color: #6c757d; /* Muted color */
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
        color: #b0b0b0;
        opacity: 0.6;
    }

    .form-control[readonly] {
        background-color: #e9ecef !important;
        opacity: 1;
    }
    .input-group:has(.form-control[readonly]) .input-group-text {
        background-color: #e9ecef;
    }
</style>
