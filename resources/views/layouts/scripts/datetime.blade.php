<script>
$(function () {
    //Datemask dd/mm/yyyy
    if ($('#datemask').length) {
        $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
    }

    //Datemask2 mm/dd/yyyy hh:mm:ss
    if ($('#datemask2').length) {
        $('#datemask2').inputmask('mm/dd/yyyy hh:mm:ss', { 'placeholder': 'mm/dd/yyyy hh:mm:ss' });
    }

    //Money Euro
    if ($('[data-mask]').length) {
        $('[data-mask]').inputmask();
    }

    //Date picker
    if ($('#datepicker').length) {
        $('#datepicker').datetimepicker({
            format: 'L'
        });
    }

    //Date picker
    if ($('#datepicker1').length) {
        $('#datepicker1').datetimepicker({
            format: 'L'
        });
    }

    //Date and time picker
    if ($('#reservationdatetime').length) {
        $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });
    }

    //Date range picker
    if ($('#reservation').length) {
        $('#reservation').daterangepicker();
    }

    //Date range picker with time picker
    if ($('#reservationtime').length) {
        $('#reservationtime').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'MM/DD/YYYY hh:mm A'
            }
        });
    }

    //Date range as a button
    if ($('#daterange-btn').length) {
        $('#daterange-btn').daterangepicker(
            {
                ranges   : {
                    'Today'       : [moment(), moment()],
                    'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                    'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate  : moment()
            },
            function (start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
    }

    //Timepicker
    if ($('#timepicker').length) {
        $('#timepicker').datetimepicker({
            format: 'LT'
        });
    }
});
</script>
