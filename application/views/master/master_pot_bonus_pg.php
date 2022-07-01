<div class="nav-tabs-custom">
    <ul class="nav nav-tabs navtab-bg" id="myTab">
        <li class="active">
            <a href="#jadwal_potga_kkbkpr" class="jadwal_potga_kkbkpr">Jadwal Potga KKB/KPR</a>
        </li>
        <li>
            <a href="#jadwal_tetap_kkbkpr" class="jadwal_tetap_kkbkpr">Jadwal Tetap KKB/KPR</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="jadwal_potga_kkbkpr">
            <div class="panel-heading">
                <button class="btn btn-success btn-small" onclick="add()">
                    <i class="fa fa-plus"></i> Tambah</button>
                <button class="btn btn-warning btn-small" onclick="edit()">
                    <i class="fa fa-pencil"></i> Edit</button>
                <button class="btn btn-danger btn-small" onclick="del()">
                    <i class="fa fa-trash"></i> Hapus</button>
            </div>
            <div class="panel-body">
                <table id="tabel_pot_bonus_pg" class="table table-bordered table-condensed table-hover table-striped nowrap" width="100%">
                    <thead>
                        <tr>
                            <th width="50">No.</th>
                            <th>Perusahaan</th>
                            <th width="100">Tahun</th>
                            <th>Bulan</th>
                            <th>Nama Bonus/Insentif</th>
                            <th>Banyak Min Angsuran</th>
                            <th>Banyak Max Angsuran</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="myModal_potga" tabindex="-1" role="dialog" aria-labelledby="myModal_potgaLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModal_potgaLabel">Jadwal Potga KKB/KPR</h4>
                        </div>
                        <div class="modal-body">
                            <form id="fm_modal_potga" onsubmit="return false">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Perusahaan</label>
                                            <select id="kd_prsh" name="kd_prsh" class="form-control" required=""></select>
                                            <input type="hidden" name="nm_prsh" id="nm_prsh">
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Tahun</label>
                                                    <input type="text" id="tahun" name="tahun" class="form-control" maxlength="4" required data-rule-number="true">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Bulan</label>
                                                    <select id="bulan" name="bulan" class="form-control" required="">
                                                        <?php echo $bulan; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Bonus/Insentif</label>
                                            <input type="text" name="nm_pot_bonus" id="nm_pot_bonus" class="form-control" required="" style="text-transform: uppercase;">
                                        </div>
                                        <div class="form-group">
                                            <label>Banyak Min Angsuran</label>
                                            <div class="input-group">
                                                <input type="text" id="banyak_min_angsuran" name="banyak_min_angsuran" class="form-control" maxlength="4" data-rule-number="true" required>
                                                <div class="input-group-addon">Kali</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Banyak Max Angsuran</label>
                                            <div class="input-group">
                                                <input type="text" id="banyak_max_angsuran" name="banyak_max_angsuran" class="form-control" maxlength="4" data-rule-number="true" required>
                                                <div class="input-group-addon">Kali</div>
                                            </div>
                                        </div>
                                    </div>
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
        </div>
        <div class="tab-pane" id="jadwal_tetap_kkbkpr">
            <div class="panel-heading">
                <button class="btn btn-success btn-small" onclick="add_tetap()">
                    <i class="fa fa-plus"></i> Tambah</button>
                <button class="btn btn-warning btn-small" onclick="edit_tetap()">
                    <i class="fa fa-pencil"></i> Edit</button>
                <button class="btn btn-danger btn-small" onclick="del_tetap()">
                    <i class="fa fa-trash"></i> Hapus</button>
            </div>
            <div class="panel-body">
                <table id="tabel_pot_bonus_tetap" class="table table-bordered table-condensed table-hover table-striped nowrap" width="100%">
                    <thead>
                        <tr>
                            <th width="50">No.</th>
                            <th>Perusahaan</th>
                            <th>Bulan</th>
                            <th>Nama Bonus/Insentif</th>
                            <th>Banyak Min Angsuran</th>
                            <th>Banyak Max Angsuran</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="myModal_tetap" tabindex="-1" role="dialog" aria-labelledby="myModal_tetapLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModal_tetapLabel">Jadwal Tetap KKB/KPR</h4>
                        </div>
                        <div class="modal-body">
                            <form id="fm_modal_tetap" onsubmit="return false">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Perusahaan</label>
                                            <select id="kd_prsh" name="kd_prsh" class="form-control" required=""></select>
                                            <input type="hidden" name="nm_prsh" id="nm_prsh">
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Bulan</label>
                                                    <select id="bulan" name="bulan" class="form-control" required="">
                                                        <?php echo $bulan; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Bonus/Insentif</label>
                                            <input type="text" name="nm_pot_bonus" id="nm_pot_bonus" class="form-control" required="" style="text-transform: uppercase;">
                                        </div>
                                        <div class="form-group">
                                            <label>Banyak Min Angsuran</label>
                                            <div class="input-group">
                                                <input type="text" id="banyak_min_angsuran" name="banyak_min_angsuran" class="form-control" maxlength="4" data-rule-number="true" required>
                                                <div class="input-group-addon">Kali</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Banyak Max Angsuran</label>
                                            <div class="input-group">
                                                <input type="text" id="banyak_max_angsuran" name="banyak_max_angsuran" class="form-control" maxlength="4" data-rule-number="true" required>
                                                <div class="input-group-addon">Kali</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" onclick="simpan_tetap()">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
edit_mode = 0;

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("jadwal_potga_kkbkpr")) {
        get_pot_bonus_pg();
        select_prsh_potga();
    } else if ($(this).hasClass("jadwal_tetap_kkbkpr")) {
        get_pot_bonus_tetap();
        select_prsh_tetap();
    }
});

$('#myModal_potga').on('shown.bs.modal', function() {
    $('#fm_modal_potga #tahun').focus();
    $("#fm_modal_potga").valid();
});

$('#myModal_tetap').on('shown.bs.modal', function() {
    $('#fm_modal_tetap #bulan').focus();
    $("#fm_modal_tetap").valid();
});

function select_prsh_potga() {
    $("#fm_modal_potga #kd_prsh").select2({
        ajax: {
            url: situs + 'master/select_perusahaan',
            dataType: 'json',
            delay: 500
        }
    }).on("select2:select", function(e) {
        var s2data_potga = e.params.data;

        $("#fm_modal_potga #nm_prsh").val(s2data_potga.nm_prsh);
    });
}

select_prsh_potga();

function select_prsh_tetap() {
    $("#fm_modal_tetap #kd_prsh").select2({
        ajax: {
            url: situs + 'master/select_perusahaan',
            dataType: 'json',
            delay: 500
        }
    }).on("select2:select", function(e) {
        var s2data_tetap = e.params.data;

        $("#fm_modal_tetap #nm_prsh").val(s2data_tetap.nm_prsh);
    });
}

// select_prsh_tetap();

function get_pot_bonus_pg() {
    url_tabel = situs + "master/get_pot_bonus_pg";
    tabel_id = "tabel_pot_bonus_pg";

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
                className: "text-right"
            }, {
                data: "nm_prsh"
            }, {
                data: "tahun"
            }, {
                data: "bulan"
            }, {
                data: "nm_pot_bonus"
            }, {
                data: "banyak_min_angsuran"
            }, {
                data: "banyak_max_angsuran"
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

function get_pot_bonus_tetap() {
    url_tabel = situs + "master/get_pot_bonus_tetap";
    tabel_id = "tabel_pot_bonus_tetap";

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
                className: "text-right"
            }, {
                data: "nm_prsh"
            }, {
                data: "bulan"
            }, {
                data: "nm_pot_bonus"
            }, {
                data: "banyak_min_angsuran"
            }, {
                data: "banyak_max_angsuran"
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
    get_pot_bonus_pg();
}, 700);

function add() {
    dataUrl = situs + 'master/add_pot_bonus_pg';
    edit_mode = 0;

    clear_form('fm_modal_potga');
    $('#myModal_potga').modal('show');
}

function edit() {
    row = $('#tabel_pot_bonus_pg').DataTable().row({
        selected: true
    }).data();

    if (row) {
        dataUrl = situs + 'master/edit_pot_bonus_pg/' + row.id;
        edit_mode = 1;

        set_form('fm_modal_potga', row);
        set_select2_value('#fm_modal_potga #kd_prsh', row.kd_prsh, row.nm_prsh);
        $('#myModal_potga').modal('show');

    } else {
        alert("Pilih data di tabel");
    }
}

function simpan() {
    validasi = $('#fm_modal_potga').valid();

    if (validasi) {
        data_input = $('#fm_modal_potga').serialize();

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
                        $('#myModal_potga').modal('hide');
                    } else {
                        clear_form("fm_modal_potga");
                    }

                    get_pot_bonus_pg();
                }
            }
        });
    }
}

function del() {
    row = $('#tabel_pot_bonus_pg').DataTable().row({
        selected: true
    }).data();

    if (row) {
        prompt = confirm("Anda Yakin Ingin Menghapus Data Ini?");

        if (prompt) {
            $.ajax({
                url: situs + "master/del_pot_bonus_pg",
                data: "id=" + row.id,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_pot_bonus_pg();
                    }
                }
            });
        }
    } else {
        alert("Pilih data di tabel");
    }
}

function add_tetap() {
    dataUrl = situs + 'master/add_pot_bonus_tetap';
    edit_mode = 0;

    clear_form('fm_modal_tetap');
    $('#myModal_tetap').modal('show');
}

function edit_tetap() {
    row = $('#tabel_pot_bonus_tetap').DataTable().row({
        selected: true
    }).data();

    if (row) {
        dataUrl = situs + 'master/edit_pot_bonus_tetap/' + row.id;
        edit_mode = 1;

        set_form('fm_modal_tetap', row);
        set_select2_value('#fm_modal_tetap #kd_prsh', row.kd_prsh, row.nm_prsh);
        $('#myModal_tetap').modal('show');

    } else {
        alert("Pilih data di tabel");
    }
}

function simpan_tetap() {
    validasi = $('#fm_modal_tetap').valid();

    if (validasi) {
        data_input = $('#fm_modal_tetap').serialize();

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
                        $('#myModal_tetap').modal('hide');
                    } else {
                        clear_form("fm_modal_tetap");
                    }

                    get_pot_bonus_tetap();
                }
            }
        });
    }
}

function del_tetap() {
    row = $('#tabel_pot_bonus_tetap').DataTable().row({
        selected: true
    }).data();

    if (row) {
        prompt = confirm("Anda Yakin Ingin Menghapus Data Ini?");

        if (prompt) {
            $.ajax({
                url: situs + "master/del_pot_bonus_tetap",
                data: "id=" + row.id,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_pot_bonus_tetap();
                    }
                }
            });
        }
    } else {
        alert("Pilih data di tabel");
    }
}
</script>