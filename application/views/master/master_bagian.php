<div class="panel panel-border panel-default">
    <div class="panel-heading with-border">
        <button class="btn btn-success btn-small" onclick="add()">
            <i class="fa fa-plus"></i> Tambah</button>
        <button class="btn btn-warning btn-small" onclick="edit()">
            <i class="fa fa-pencil"></i> Edit</button>
        <button class="btn btn-danger btn-small" onclick="del()">
            <i class="fa fa-trash"></i> Hapus</button>
    </div>
    <div class="panel-body">
        <table id="tabel_bagian" class="table table-bordered table-condensed table-hover table-striped nowrap" width="100%">
            <thead>
                <tr>
                    <th width="50">No.</th>
                    <th width="150">Kode Bagian</th>
                    <th>Bagian</th>
                    <th width="150">Kode Departemen</th>
                    <th>Departemen</th>
                    <th width="150">Kode Perusahaan</th>
                    <th>Perusahaan</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Bagian</h4>
            </div>
            <div class="modal-body">
                <form id="fm_modal" onsubmit="return false">
                    <div class="form-group">
                        <label>Perusahaan</label>
                        <div class="row" style="margin: 0px;">
                            <div class="col-md-2" style="padding: 0px">
                                <input type="text" id="kd_prsh" name="kd_prsh" class="form-control" readonly="">
                            </div>
                            <div class="col-md-10" style="padding: 0 0 0 5px;">
                                <input type="text" id="nm_prsh" name="nm_prsh" class="form-control" readonly="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Departemen</label>
                        <select id="kd_dep" name="kd_dep" class="form-control" required=""></select>
                        <input type="hidden" id="nm_dep" name="nm_dep" class="form-control" readonly="">
                    </div>
                    <div class="form-group">
                        <label>Kode Bagian</label>
                        <input type="text" id="kode_nomor" name="kode_nomor" class="form-control" required="" style="text-transform: uppercase;">
                    </div>
                    <div class="form-group">
                        <label>Nama Bagian</label>
                        <input type="text" id="nm_bagian" name="nm_bagian" class="form-control" required="" style="text-transform: uppercase;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpan()">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
edit_mode = 0;

function get_bagian() {
    url_tabel = situs + "master/get_bagian";
    tabel_id = "tabel_bagian";

    if ($.fn.DataTable.isDataTable("#" + tabel_id)) {
        $("#" + tabel_id).DataTable().ajax.url(url_tabel).load(function() {
            // $('#tabel_piutang').DataTable().responsive.recalc().responsive.rebuild();
        }, false);
    } else {
        $("#" + tabel_id).DataTable({
            scrollY: 350,
            scrollX: true,
            ordering: false,
            paging: true,
            searching: true,
            select: 'single',
            processing: true,
            serverSide: true,
            ajax: url_tabel,
            columns: [{
                data: "nomor",
                className: "text-right",
                width: "50px"
            }, {
                data: "kd_bagian"
            }, {
                data: "nm_bagian"
            }, {
                data: "kd_dep"
            }, {
                data: "nm_dep"
            }, {
                data: "kd_prsh"
            }, {
                data: "nm_prsh"
            }],
            initComplete: function() {
                var input = $("#" + tabel_id + "_filter input").unbind(),
                    self = this.api(),
                    $searchButton = $('<button>').addClass('btn btn-primary').text('Cari').click(function() {
                        self.search(input.val()).draw();
                    }),
                    $clearButton = $('<button>').addClass('btn btn-default').text('Reset').click(function() {
                        input.val('');
                        self.search('').draw();
                        // $searchButton.click();
                    });

                $("#" + tabel_id + "_filter").append("&nbsp;", $searchButton, "&nbsp;", $clearButton);
                $("#" + tabel_id + "_filter input").keyup(function(e) {
                    if (e.keyCode == "13") {
                        self.search(input.val()).draw();
                    }
                });
            }
        });
    }
}

setTimeout(function() {
    get_bagian();
}, 700);

$("#fm_modal #kd_dep").select2({
    ajax: {
        url: situs + 'master/select_departemen',
        dataType: 'json',
        delay: 500
    }
}).on("select2:select", function(e) {
    var s2data = e.params.data;

    $("#fm_modal #nm_dep").val(s2data.nm_dep);
    $("#fm_modal #kd_prsh").val(s2data.kd_prsh);
    $("#fm_modal #nm_prsh").val(s2data.nm_prsh);
});

$('#myModal').on('shown.bs.modal', function() {
    $('#fm_modal').valid();
    $('#fm_modal #kd_bagian').focus();
});

function add() {
    dataUrl = situs + 'master/add_bagian';
    edit_mode = 0;

    clear_form('fm_modal');

    $('#myModal').modal('show');
}

function edit() {
    row = $('#tabel_bagian').DataTable().row({
        selected: true
    }).data();

    if (row) {
        dataUrl = situs + 'master/edit_bagian/' + row.id_bagian;
        edit_mode = 1;

        set_form('fm_modal', row);
        set_select2_value("#fm_modal #kd_dep", row.kd_dep, row.nm_dep + " | " + row.nm_prsh);

        $('#myModal').modal('show');
    } else {
        alert("Pilih data di tabel");
    }
}

function simpan() {
    validasi = $('#fm_modal').valid();

    if (validasi) {
        data_input = $('#fm_modal').serialize();

        $.ajax({
            url: dataUrl,
            data: data_input,
            dataType: "JSON",
            type: "POST",
            beforeSend: function() {
                proses();
            },
            success: function(res) {
                pesan(res.msg, 1);

                if (res.status) {
                    if (edit_mode) {
                        $('#myModal').modal('hide');
                    } else {
                        clear_form('fm_modal');
                    }

                    get_bagian();
                }
            }
        });
    }
}

function del() {
    row = $('#tabel_bagian').DataTable().row({
        selected: true
    }).data();

    if (row) {
        prompt = confirm("Anda Yakin Ingin Menghapus Data Ini?");

        if (prompt) {
            $.ajax({
                url: situs + "master/del_bagian",
                data: "id_bagian=" + row.id_bagian,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_bagian();
                    }
                }
            });
        }
    } else {
        alert("Pilih data di tabel");
    }
}
</script>