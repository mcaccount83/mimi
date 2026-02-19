import $ from 'jquery';
window.$ = $;
window.jQuery = $;

// Bootstrap 5
import 'bootstrap';

// FontAwesome
import '@fortawesome/fontawesome-free/css/all.min.css';

// Bootstrap Icons
import 'bootstrap-icons/font/bootstrap-icons.css';

// AdminLTE 4 - MOVE CSS AFTER OTHER STYLES SO IT OVERRIDES
import 'admin-lte/dist/js/adminlte.js';

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Moment (required by daterangepicker)
import moment from 'moment';
window.moment = moment;

// DataTables
import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5.js';
import 'datatables.net-buttons/js/buttons.print.js';
import 'datatables.net-buttons/js/buttons.colVis.js';
window.DataTable = DataTable;
$.fn.dataTable = DataTable;

// JSZip (for DataTables Excel export)
import JSZip from 'jszip';
window.JSZip = JSZip;

// Summernote (BS5 version)
import 'summernote/dist/summernote-bs5.min.css';
import 'summernote/dist/summernote-bs5.min.js';

// Daterangepicker
import 'daterangepicker/daterangepicker.css';
import 'daterangepicker';

// BS-Stepper
import 'bs-stepper/dist/css/bs-stepper.min.css';
import 'bs-stepper/dist/js/bs-stepper.min.js';

// OverlayScrollbars
import 'overlayscrollbars/overlayscrollbars.css';
import { OverlayScrollbars } from 'overlayscrollbars';
window.OverlayScrollbars = OverlayScrollbars;

// Inputmask
import Inputmask from 'inputmask';
window.Inputmask = Inputmask;

// Make inputmask available as jQuery plugin
$.fn.inputmask = function(mask, options) {
    return this.each(function() {
        Inputmask(mask, options).mask(this);
    });
};

