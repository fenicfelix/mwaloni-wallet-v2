// lazyload config
var getUrl = window.location;
var segments = getUrl.pathname.split('/');
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/";
//themes/agile/css/bootstrap.css
var MODULE_CONFIG = {
    /*chat:           [
                      baseUrl+'themes/agile/libs/list.js/dist/list.js',
                      baseUrl+'themes/agile/libs/notie/dist/notie.min.js',
                      baseUrl+'themes/agile/js/plugins/notie.js',
                      baseUrl+'themes/agile/js/app/chat.js'
                    ],
    mail:           [
                      baseUrl+'themes/agile/libs/list.js/dist/list.js',
                      baseUrl+'themes/agile/libs/notie/dist/notie.min.js',
                      baseUrl+'themes/agile/js/plugins/notie.js',
                      baseUrl+'themes/agile/js/app/mail.js'
                    ],
    user:           [
                      baseUrl+'themes/agile/libs/list.js/dist/list.js',
                      baseUrl+'themes/agile/libs/notie/dist/notie.min.js',
                      baseUrl+'themes/agile/js/plugins/notie.js',
                      baseUrl+'themes/agile/js/app/user.js'
                    ],*/
    /*search:         [
                      baseUrl+'themes/agile/libs/list.js/dist/list.js',
                      baseUrl+'themes/agile/js/app/search.js'
                    ],
    invoice:        [
                      baseUrl+'themes/agile/libs/list.js/dist/list.js',
                      baseUrl+'themes/agile/libs/notie/dist/notie.min.js',
                      baseUrl+'themes/agile/js/app/invoice.js'
                    ],*/
    screenfull: [
        baseUrl + 'themes/agile/libs/screenfull/dist/screenfull.js',
        baseUrl + 'themes/agile/js/plugins/screenfull.js'
    ],
    jscroll: [
        baseUrl + 'themes/agile/libs/jscroll/dist/jquery.jscroll.min.js'
    ],
    countTo: [
        baseUrl + 'themes/agile/libs/jquery-countto/jquery.countTo.js'
    ],
    stick_in_parent: [
        baseUrl + 'themes/agile/libs/sticky-kit/dist/sticky-kit.min.js'
    ],
    stellar: [
        baseUrl + 'themes/agile/libs/jquery.stellar/jquery.stellar.min.js',
        baseUrl + 'themes/agile/js/plugins/stellar.js'
    ],
    scrollreveal: [
        baseUrl + 'themes/agile/libs/scrollreveal/dist/scrollreveal.min.js',
        baseUrl + 'themes/agile/js/plugins/jquery.scrollreveal.js'
    ],
    masonry: [
        baseUrl + 'themes/agile/libs/masonry-layout/dist/masonry.pkgd.min.js'
    ],
    /*owlCarousel:    [
                      baseUrl+'themes/agile/libs/owl.carousel/dist/themes/agile/owl.carousel.min.css',
                      baseUrl+'themes/agile/libs/owl.carousel/dist/themes/agile/owl.theme.css',
                      baseUrl+'themes/agile/libs/owl.carousel/dist/owl.carousel.min.js'
                    ],*/
    html5sortable: [
        baseUrl + 'themes/agile/libs/html5sortable/dist/html.sortable.min.js',
        baseUrl + 'themes/agile/js/plugins/jquery.html5sortable.js',
        baseUrl + 'themes/agile/js/plugins/sortable.js'
    ],
    /*easyPieChart:   [
                      baseUrl+'themes/agile/libs/easy-pie-chart/dist/jquery.easypiechart.min.js' 
                    ],
    peity:          [
                      baseUrl+'themes/agile/libs/peity/jquery.peity.js',
                      baseUrl+'themes/agile/js/plugins/jquery.peity.tooltip.js'
                    ],*/
    chartjs: [
        baseUrl + 'themes/agile/libs/moment/min/moment-with-locales.min.js',
        baseUrl + 'themes/agile/libs/chart.js/dist/Chart.min.js',
        baseUrl + 'themes/agile/js/plugins/jquery.chartjs.js',
        baseUrl + 'themes/agile/js/plugins/chartjs.js'
    ],
    dataTable: [
        //baseUrl+'themes/agile/libs/datatables/media/js/jquery.dataTables.min.js',
        /*baseUrl+'themes/agile/libs/datatables.net-bs4/js/jquery.dataTables.min.js',
        baseUrl+'themes/agile/libs/datatables.net-bs4/js/dataTables.bootstrap4.js',
        baseUrl+'themes/agile/libs/datatables.net-bs4/css/dataTables.bootstrap4.css',
        baseUrl+'themes/agile/libs/datatables.net-bs4/css/buttons.dataTables.min.css',
        

        
        baseUrl+'themes/agile/libs/datatables.net-bs4/js/dataTables.buttons.min.js',
        baseUrl+'themes/agile/libs/datatables.net-bs4/js/buttons.flash.min.js',
        baseUrl+'themes/agile/libs/datatables.net-bs4/js/jszip.min.js',
        baseUrl+'themes/agile/libs/datatables.net-bs4/js/pdfmake.min.js',
        baseUrl+'themes/agile/libs/datatables.net-bs4/js/vfs_fonts.js',
        baseUrl+'themes/agile/libs/datatables.net-bs4/js/buttons.html5.min.js',
        baseUrl+'themes/agile/libs/datatables.net-bs4/js/buttons.print.min.js',
        baseUrl+'themes/agile/js/plugins/datatable.js'*/
    ],
    /*bootstrapTable: [
                      baseUrl+'themes/agile/libs/bootstrap-table/dist/bootstrap-table.min.js',
                      baseUrl+'themes/agile/libs/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js',
                      baseUrl+'themes/agile/libs/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min.js',
                      baseUrl+'themes/agile/js/plugins/tableExport.min.js',
                      baseUrl+'themes/agile/js/plugins/bootstrap-table.js'
                    ],*/
    bootstrapWizard: [
        baseUrl + 'themes/agile/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js'
    ],
    dropzone: [
        baseUrl + 'themes/agile/libs/dropzone/dist/min/dropzone.min.js',
        baseUrl + 'themes/agile/libs/dropzone/dist/min/dropzone.min.css'
    ],
    typeahead: [
        baseUrl + 'themes/agile/libs/typeahead.js/dist/typeahead.bundle.min.js',
        baseUrl + 'themes/agile/js/plugins/typeahead.js'
    ],
    datepicker: [
        baseUrl + "themes/agile/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js",
        baseUrl + "themes/agile/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css",
    ],
    daterangepicker: [
        baseUrl + "themes/agile/libs/daterangepicker/daterangepicker.css",
        baseUrl + 'themes/agile/libs/moment/min/moment-with-locales.min.js',
        baseUrl + "themes/agile/libs/daterangepicker/daterangepicker.js"
    ],
    fullCalendar: [
        baseUrl + 'themes/agile/libs/moment/min/moment-with-locales.min.js',
        baseUrl + 'themes/agile/libs/fullcalendar/dist/fullcalendar.min.js',
        baseUrl + 'themes/agile/libs/fullcalendar/dist/fullcalendar.min.css',
        baseUrl + 'themes/agile/js/app/calendar.js'
    ],
    parsley: [
        baseUrl + 'themes/agile/libs/parsleyjs/dist/parsley.min.js'
    ],
    select2: [
        baseUrl + 'themes/agile/libs/select2/dist/css/select2.min.css',
        baseUrl + 'themes/agile/libs/select2/dist/js/select2.min.js',
        baseUrl + 'themes/agile/js/plugins/select2.js'
    ],
    /*summernote:     [
                      baseUrl+'themes/agile/libs/summernote/dist/summernote.css',
                      baseUrl+'themes/agile/libs/summernote/dist/summernote-bs4.css',
                      baseUrl+'themes/agile/libs/summernote/dist/summernote.min.js',
                      baseUrl+'themes/agile/libs/summernote/dist/summernote-bs4.min.js'
                    ],*/
    /*vectorMap:      [
                      baseUrl+'themes/agile/libs/jqvmap/dist/jqvmap.min.css',
                      baseUrl+'themes/agile/libs/jqvmap/dist/jquery.vmap.js',
                      baseUrl+'themes/agile/libs/jqvmap/dist/maps/jquery.vmap.world.js',
                      baseUrl+'themes/agile/libs/jqvmap/dist/maps/jquery.vmap.usa.js',
                      baseUrl+'themes/agile/libs/jqvmap/dist/maps/jquery.vmap.france.js',
                      baseUrl+'themes/agile/js/plugins/jqvmap.js'
                    ],*/
    plyr: [
        baseUrl + 'themes/agile/libs/plyrist/src/plyrist.css',
        baseUrl + 'themes/agile/libs/plyrist/src/plyrist.js',
        baseUrl + 'themes/agile/libs/plyr/dist/plyr.css',
        baseUrl + 'themes/agile/libs/plyr/dist/plyr.polyfilled.min.js',
        baseUrl + 'themes/agile/js/plugins/plyr.js'
    ]
};

var MODULE_OPTION_CONFIG = {
    parsley: {
        errorClass: 'is-invalid',
        successClass: 'is-valid',
        errorsWrapper: '<ul class="list-unstyled text-sm mt-1 text-muted"></ul>'
    }
}
