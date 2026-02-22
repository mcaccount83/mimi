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

    .ms-2 {
        margin-left: 0.5rem !important; /* Adjust the margin to control spacing for Vacant Buttons */
    }

    .form-check-input:checked ~ .form-check-label {
        color: black; /* Label color when toggle is ON for Vacant Buttons */
    }

    .form-check-input:not(:checked) ~ .form-check-label {
        color: #b0b0b0; /* Subdued label color when toggle is OFF for Vacant Buttons */
        opacity: 0.6;   /* Optional: Adds a subdued effectfor Vacant Buttons */
    }
</style>
