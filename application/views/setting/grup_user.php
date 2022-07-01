<!-- <div class="nav-tabs-custom"> -->
<ul class="nav nav-tabs navtab-bg" id="myTab">
    <li class="active">
        <a href="#menu_grup" class="menu_grup">
            <i class="fa fa-group"></i> Grup</a>
    </li>
    <li>
        <a href="#menu_user" class="menu_user">
            <i class="fa fa-user"></i> User</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="menu_grup">
        <div class="panel-heading">
            <button class="btn btn-success btn-small" onclick="add_grup()">
                <i class="fa fa-plus"></i> Tambah</button>
            <button class="btn btn-warning btn-small" onclick="edit_grup()">
                <i class="fa fa-pencil"></i> Edit</button>
            <button class="btn btn-danger btn-small" onclick="del_grup()">
                <i class="fa fa-trash"></i> Hapus</button>
        </div>
        <div class="panel-body">
            <table id="tabel_grup" class="table table-bordered table-condensed table-hover table-striped nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th width="50px">No.</th>
                        <th>Nama Grup</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="menu_user">
        <div class="panel-heading">
            <button class="btn btn-success btn-small" onclick="add_user()">
                <i class="fa fa-plus"></i> Tambah</button>
            <button class="btn btn-warning btn-small" onclick="edit_user()">
                <i class="fa fa-pencil"></i> Edit</button>
            <button class="btn btn-danger btn-small" onclick="del_user()">
                <i class="fa fa-trash"></i> Hapus</button>
        </div>
        <div class="panel-body">
            <table id="tabel_user" class="table table-bordered table-condensed table-hover table-striped nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th width="50px">No.</th>
                        <th>Nama User</th>
                        <th>Username</th>
                        <th>Nama Grup</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- </div> -->
<!-- Modal -->
<div class="modal fade" id="myModal_grup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Grup</h4>
            </div>
            <div class="modal-body">
                <form id="fm_modal_grup" onsubmit="return false" class="cmxform">
                    <div class="form-group">
                        <label>Nama Grup</label>
                        <input type="text" id="nm_grup" name="nm_grup" class="form-control" required="" style="text-transform: uppercase;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpan_grup()">Simpan</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">User</h4>
            </div>
            <div class="modal-body">
                <form id="fm_modal_user" onsubmit="return false">
                    <div class="form-group">
                        <label>Nama User</label>
                        <input type="text" id="nama" name="nama" class="form-control" required="">
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="username" name="username" class="form-control" required="" style="text-transform: uppercase;">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="passwd" name="passwd" class="form-control">
                        <small>Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                    <div class="form-group">
                        <label>Grup</label>
                        <select id="id_grup" name="id_grup" class="form-control" required=""></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpan_user()">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
edit_mode = 0;

$("#wrapper").css("width", "100%");

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("menu_grup")) {
        get_grup();
    }

    if ($(this).hasClass("menu_user")) {
        get_user();
    }
});

function get_grup() {
    url_tabel_grup = situs + "setting/get_grup";
    id_tabel_grup = "tabel_grup";

    if ($.fn.DataTable.isDataTable("#" + id_tabel_grup)) {
        $("#" + id_tabel_grup).DataTable().ajax.url(url_tabel_grup).load(function() {
            // $('#tabel_piutang').DataTable().responsive.recalc().responsive.rebuild();
        }, false);
    } else {
        $("#" + id_tabel_grup).DataTable({
            scrollY: 350,
            scrollX: true,
            ordering: false,
            paging: true,
            searching: true,
            select: 'single',
            processing: true,
            serverSide: true,
            ajax: url_tabel_grup,
            columns: [{
                data: "nomor",
                className: "text-right",
                width: "50px"
            }, {
                data: "nm_grup"
            }],
            initComplete: function() {
                var input = $("#" + id_tabel_grup + "_filter input").unbind(),
                    self = this.api(),
                    $searchButton = $('<button>').addClass('btn btn-primary').text('Cari').click(function() {
                        self.search(input.val()).draw();
                    }),
                    $clearButton = $('<button>').addClass('btn btn-default').text('Reset').click(function() {
                        input.val('');
                        self.search('').draw();
                        // $searchButton.click();
                    });

                $("#" + id_tabel_grup + "_filter").append("&nbsp;", $searchButton, "&nbsp;", $clearButton);
                $("#" + id_tabel_grup + "_filter input").keyup(function(e) {
                    if (e.keyCode == "13") {
                        self.search(input.val()).draw();
                    }
                });
            }
        });
    }
}

function get_user() {
    url_tabel_user = situs + "setting/get_user";
    id_tabel_user = "tabel_user";

    if ($.fn.DataTable.isDataTable("#" + id_tabel_user)) {
        $("#" + id_tabel_user).DataTable().ajax.url(url_tabel_user).load(function() {
            // $('#tabel_piutang').DataTable().responsive.recalc().responsive.rebuild();
        }, false);
    } else {
        $("#" + id_tabel_user).DataTable({
            scrollY: 350,
            scrollX: true,
            ordering: false,
            paging: true,
            searching: true,
            select: 'single',
            processing: true,
            serverSide: true,
            ajax: url_tabel_user,
            columns: [{
                data: "nomor",
                className: "text-right",
                width: "50px"
            }, {
                data: "nama"
            }, {
                data: "username"
            }, {
                data: "nm_grup"
            }],
            initComplete: function() {
                var input = $("#" + id_tabel_user + "_filter input").unbind(),
                    self = this.api(),
                    $searchButton = $('<button>').addClass('btn btn-primary').text('Cari').click(function() {
                        self.search(input.val()).draw();
                    }),
                    $clearButton = $('<button>').addClass('btn btn-default').text('Reset').click(function() {
                        input.val('');
                        self.search('').draw();
                        // $searchButton.click();
                    });

                $("#" + id_tabel_user + "_filter").append("&nbsp;", $searchButton, "&nbsp;", $clearButton);
                $("#" + id_tabel_user + "_filter input").keyup(function(e) {
                    if (e.keyCode == "13") {
                        self.search(input.val()).draw();
                    }
                });
            }
        });
    }
}

setTimeout(function() {
    get_grup();
}, 700);

function add_grup() {
    dataUrl = situs + 'setting/add_grup';
    edit_mode = 0;

    clear_form('fm_modal_grup');

    $('#myModal_grup').modal('show');
    $('#myModal_grup').on('shown.bs.modal', function() {
        $('#fm_modal_grup').valid();
        $('#fm_modal_grup #nm_grup').focus();
    });
}

function edit_grup() {
    row = $('#tabel_grup').DataTable().row({
        selected: true
    }).data();

    if (row) {
        dataUrl = situs + 'setting/edit_grup/' + row.id_grup;
        edit_mode = 1;

        set_form('fm_modal_grup', row);

        $('#myModal_grup').modal('show');
        $('#myModal_grup').on('shown.bs.modal', function() {
            $('#fm_modal_grup').valid();
            $('#fm_modal_grup #nm_grup').focus();
        });
    } else {
        alert("Pilih data di tabel");
    }
}

function simpan_grup() {
    validasi = $('#fm_modal_grup').valid();

    if (validasi) {
        $.ajax({
            url: dataUrl,
            data: $('#fm_modal_grup').serialize(),
            dataType: "JSON",
            type: "POST",
            beforeSend: function() {
                proses();
            },
            success: function(res) {
                pesan(res.msg, 1);

                if (res.status) {
                    if (edit_mode) {
                        $('#myModal_grup').modal('hide');
                    } else {
                        clear_form('fm_modal_grup');
                        $('#fm_modal_grup').valid();
                        $('#fm_modal_grup #nm_grup').focus();
                    }

                    get_grup();
                }
            }
        });
    }
}

function del_grup() {
    row = $('#tabel_grup').DataTable().row({
        selected: true
    }).data();

    if (row) {
        prompt = confirm("Anda Yakin Ingin Menghapus Data Ini?");

        if (prompt) {
            $.ajax({
                url: situs + "setting/del_grup",
                data: "id_grup=" + row.id_grup,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_grup();
                    }
                }
            });
        }
    } else {
        alert("Pilih data di tabel");
    }
}

$("#fm_modal_user #id_grup").select2({
    ajax: {
        url: situs + 'setting/select_grup',
        dataType: 'json',
        delay: 500
    }
});

function add_user() {
    dataUrl = situs + 'setting/add_user';
    edit_mode = 0;

    clear_form('fm_modal_user');

    $('#myModal_user').modal('show');
    $('#myModal_user').on('shown.bs.modal', function() {
        $('#fm_modal_user').valid();
        $('#fm_modal_user #nm_user').focus();
    });
}

function edit_user() {
    row = $('#tabel_user').DataTable().row({
        selected: true
    }).data();

    if (row) {
        dataUrl = situs + 'setting/edit_user/' + row.id_user;
        edit_mode = 1;

        set_form('fm_modal_user', row);
        set_select2_value("#fm_modal_user #id_grup", row.id_grup, row.nm_grup);

        $('#myModal_user').modal('show');
        $('#myModal_user').on('shown.bs.modal', function() {
            $('#fm_modal_user').valid();
            $('#fm_modal_user #nama').focus();
        });
    } else {
        alert("Pilih data di tabel");
    }
}

function simpan_user() {
    validasi = $('#fm_modal_user').valid();

    if (validasi) {
        $.ajax({
            url: dataUrl,
            data: $('#fm_modal_user').serialize(),
            dataType: "JSON",
            type: "POST",
            beforeSend: function() {
                proses();
            },
            success: function(res) {
                pesan(res.msg, 1);

                if (res.status) {
                    if (edit_mode) {
                        $('#myModal_user').modal('hide');
                    } else {
                        clear_form('fm_modal_user');
                        $("#fm_modal_user").valid();
                        $('#fm_modal_user #nama').focus();
                    }

                    get_user();
                }
            }
        });
    }
}

function del_user() {
    row = $('#tabel_user').DataTable().row({
        selected: true
    }).data();

    if (row) {
        prompt = confirm("Anda Yakin Ingin Menghapus Data Ini?");

        if (prompt) {
            $.ajax({
                url: situs + "setting/del_user",
                data: "id_user=" + row.id_user,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_user();
                    }
                }
            });
        }
    } else {
        alert("Pilih data di tabel");
    }
}
</script>