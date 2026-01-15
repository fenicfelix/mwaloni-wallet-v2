(function ($) {
	"use strict";

  var init = function(){
    $('#datatable').DataTable( {
      dom: 'Bfrtip',
      buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
      ]
    });
    $('#dt-server-side').DataTable({
      "processing": true,
      "serverSide": true,
      "responsive": true,
      "order": [[ 0, "desc" ]],
      "ajax":{
          "url": table_processer,
          "dataType": "json",
          "type": "GET",
      }
  });
  }

  // for ajax to init again
  $.fn.dataTable.init = init;

})(jQuery);
