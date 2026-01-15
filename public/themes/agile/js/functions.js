$(document).ready(function () {

    $("#action-logout").on('click', function (e) {
        e.preventDefault();
        $("#logout-form").submit();
    });
    if (dt_serverside) {
        var table = $('#dt-server-side').DataTable({
            "dom": 'lBfrtip',
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [tableDefaultFilter],
            "ajax": {
                "url": table_processer,
                "dataType": "json",
                "type": "GET",
            },
            "language": {
                "searchPlaceholder": "Search records"
            }
        });
    } else {
        var table = $('#datatable').DataTable({
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            language: {
                searchPlaceholder: "Search records"
            }
        });
    }




    $("#add-ajax-form").submit(function (e) {
        e.preventDefault();
        $("#loader-add").show();
        var form_data = $("#add-ajax-form").serialize();
        do_save(submit_add_url, form_data, 'add');
    });

    $("#edit-ajax-form").submit(function (e) {
        e.preventDefault();
        $("#loader-edit").show();
        var form_data = $("#edit-ajax-form").serialize();
        var url = submit_edit_url;
        do_save(url, form_data, 'edit');
    });

    $("#cashout-modal-form").submit(function (e) {
        e.preventDefault();
        $("#loader-cashout").show();
        do_save(cashout_url, $("#cashout-modal-form").serialize(), 'cashout');
    });

    $("#btn-generate-keys").click(function (e) {
        e.preventDefault();
        $("#loader-keys").show();
        var url = submit_generate_api_url + $(this).data('id');
        do_save(url, {}, 'keys');
    });

    $(".slugify").on('keyup', function (e) {
        let title = this.value;
        let slug = "";
        if (title != '') {
            slug = get_slug(title);
        }
        $("#" + $(this).data('task') + "-slug").val(slug);
    });

    function get_slug(title) {
        return slugify(title, {
            remove: /[*+~.()'"!:@,]/g, // remove symbols
            lower: true // convert to lowercase
        });
    }

    function slugify(str) {
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();

        // remove accents, swap ñ for n, etc
        var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
        var to = "aaaaeeeeiiiioooouuuunc------";
        for (var i = 0, l = from.length; i < l; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes

        return str;
    }


    function do_save(submit_url, form_data, task) {
        $.ajax({
            url: submit_url,
            type: 'POST',
            data: form_data,
            success: function (obj) {
                console.log(obj);
                $("#loader-" + task).hide();

                if (obj["status"] == "00") {
                    //table.DataTable().ajax.reload();
                    if (dt_serverside) {
                        $('#' + task + 'Modal').modal('hide');
                        $("#" + task + "-ajax-form")[0].reset();
                        toastr["success"]("Operation succeeded", "Success!", { closeButton: true, progressBar: true, timeOut: 2000 });
                        table.draw();
                    }
                    else {
                        location.reload();
                    }
                } else if (obj["status"] == "01") {
                    toastr["error"](obj["message"], "Sorry!", { closeButton: true, progressBar: true, timeOut: 5000 });
                }
            }
        });
    }
});