<script>
  function convertDateFormat(dateString) {
        var parts = dateString.split('-');
        return parts[1] + '/' + parts[2] + '/' + parts[0];
    }

    function applyDateMask() {
        $('.date-mask').each(function() {
            var originalDate = $(this).text();
            var formattedDate = convertDateFormat(originalDate);
            $(this).text(formattedDate);
        });
        Inputmask({"mask": "99/99/9999"}).mask(".date-mask");
    }

  function applyPhoneMask() {
        Inputmask({"mask": "(999) 999-9999"}).mask(".phone-mask");
    }

    function applyHttpMask() {
        Inputmask({"mask": "http://*{1,250}"}).mask(".http-mask");
    }
</script>
