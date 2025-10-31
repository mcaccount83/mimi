<script>
    function showChPrimary() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showPrimary").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::PRIMARY_COORDINATOR }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showChAllConf() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showAllConf").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::CONFERENCE_REGION }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showChAll() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showAll").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::INTERNATIONAL }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showChAllReReg() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showAllReReg").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::INTERNATIONALREREG }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showChReviewer() {
        var base_url = window.location.origin + window.location.pathname;
        if ($("#showReviewer").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::REVIEWER }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showCoordDirect() {
        var base_url = '{{ url("/coordinator/coordlist") }}';
        if ($("#showDirect").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CoordinatorCheckbox::DIRECT_REPORT }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showCoordAllConf() {
        var base_url = '{{ url("/coordinator/coordlist") }}';
        if ($("#showAllConf").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CoordinatorCheckbox::CONFERENCE_REGION }}=yes';
        } else {
            window.location.href = base_url;
        }
    }

    function showCoordAll() {
        var base_url = '{{ url("/coordinator/coordlist") }}';
        if ($("#showAll").prop("checked") == true) {
            window.location.href = base_url + '?{{ \App\Enums\CoordinatorCheckbox::INTERNATIONAL }}=yes';
        } else {
            window.location.href = base_url;
        }
    }
</script>
