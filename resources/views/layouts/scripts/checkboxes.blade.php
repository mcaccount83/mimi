<script>
    // Chapter/Coordinator List Checkboxes
    function showPrimary() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showPrimary").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::PC_DIRECT }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showDirect() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showDirect").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::PC_DIRECT }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showReviewer() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showReviewer").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::REVIEWER }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showConfReg() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showConfReg").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::CONFERENCE_REGION }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    // Specialty Job List Checkboxes
    function showM2M() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showM2M").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::M2MDONATIONS }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showInquiries() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showInquiries").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::INQUIRIES }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    // Internatioal List Checkboxes
    function showIntl() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showIntl").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::INTERNATIONAL }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showIntlReReg() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showIntlReReg").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::INTERNATIONALREREG }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showIntlM2M() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showIntlM2M").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::INTERNATIONALM2MDONATIONS }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showIntlInquiries() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showIntlInquiries").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::INTERNATIONALINQUIRIES }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    // Misc/Admin List Checkboxes
    function showAdminAll() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showAdminAll").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CheckboxFilterEnum::ADMIN }}=yes';
        } else {
            window.location.href = base_url;
        }
    }
</script>
