<div class="panel panel-default panel-color">
    <div class="panel-heading">
        <button class="btn btn-success btn-small" onclick="add()">
            <i class="fa fa-plus"></i> Tambah</button>
        <button class="btn btn-warning btn-small" onclick="edit()">
            <i class="fa fa-pencil"></i> Edit</button>
        <button class="btn btn-danger btn-small" onclick="del()">
            <i class="fa fa-trash"></i> Hapus</button>
    </div>
    <div class="panel-body">
        <table id="tabel_potga_ss1" class="table table-bordered table-condensed table-hover table-striped nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th style="width: 50px">No.</th>
                    <th>NAK</th>
                    <th>No. Pegawai</th>
                    <th>Nama</th>
                    <th>Perusahaan</th>
                    <th>Departemen</th>
                    <th>Bagian</th>
                    <th>Jumlah</th>
                    <th>Mulai Berlaku Potga</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Potongan Gaji SS1</h4>
            </div>
            <div class="modal-body">
                <form id="fm_modal" onsubmit="return false">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>NAK</label>
                                <select id="no_ang" name="no_ang" class="form-control" required=""></select>
                            </div>
                            <div class="form-group">
                                <label>Perusahaan</label>
                                <div class="row" style="margin: 0px;">
                                    <div class="col-md-2" style="padding: 0px;">
                                        <input type="text" id="kd_prsh" name="kd_prsh" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-10" style="padding: 0 0 0 5px;">
                                        <input type="text" id="nm_prsh" name="nm_prsh" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nama Anggota</label>
                                <input type="text" id="nm_ang" name="nm_ang" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Departemen</label>
                                <div class="row" style="margin: 0px;">
                                    <div class="col-md-3" style="padding: 0px;">
                                        <input type="text" id="kd_dep" name="kd_dep" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-9" style="padding: 0 0 0 5px;">
                                        <input type="text" id="nm_dep" name="nm_dep" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>No. Pegawai</label>
                                <input type="text" id="no_peg" name="no_peg" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Bagian</label>
                                <div class="row" style="margin: 0px;">
                                    <div class="col-md-3" style="padding: 0px;">
                                        <input type="text" id="kd_bagian" name="kd_bagian" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-9" style="padding: 0 0 0 5px;">
                                        <input type="text" id="nm_bagian" name="nm_bagian" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label>Bulan</label>
                                        <select id="bulan" name="bulan" class="form-control" required="">
                                            <?php echo $bulan; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Tahun</label>
                                        <input type="text" id="tahun" name="tahun" class="form-control" maxlength="4" minlength="4" required data-rule-number="true" value="<?php echo date('Y'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp</div>
                                    <input type="text" name="jumlah" id="jumlah" class="form-control number_format" data-rule-number="true" required="">
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
<script type="text/javascript">
edit_mode = 0;

$('#myModal').on('shown.bs.modal', function() {
    $('#fm_modal #no_ang').focus();
});

function get_potga_ss1() {
    url_tabel = situs + "master/get_potga_ss1";
    tabel_id = "tabel_potga_ss1";

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
                data: "no_ang"
            }, {
                data: "no_peg"
            }, {
                data: "nm_ang"
            }, {
                data: "nm_prsh",
                // render: function(data, type, row, meta) {
                //     return data + " " + row.nm_prsh;
                // }
            }, {
                data: "nm_dep",
                // render: function(data, type, row, meta) {
                //     return data + " " + row.nm_dep;
                // }
            }, {
                data: "nm_bagian",
                // render: function(data, type, row, meta) {
                //     return data + " " + row.nm_dep;
                // }
            }, {
                data: "jumlah",
                render: function(data, type, row, meta) {
                    return number_format(data, 2);
                }
            }, {
                data: null,
                defaultContent: "",
                render: function(data, type, row, meta) {
                    return row.bulan + "-" + row.tahun;
                }
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
    get_potga_ss1();
}, 700);

$("#fm_modal #no_ang").select2({
    ajax: {
        url: situs + 'anggota/select_anggota_by_noang',
        dataType: 'json',
        delay: 500
    }
}).on("select2:select", function(e) {
    s2data = e.params.data;

    $("#fm_modal #nm_ang").val(s2data.nm_ang);
    $("#fm_modal #no_peg").val(s2data.no_peg);
    $("#fm_modal #kd_prsh").val(s2data.kd_prsh);
    $("#fm_modal #nm_prsh").val(s2data.nm_prsh);
    $("#fm_modal #kd_dep").val(s2data.kd_dep);
    $("#fm_modal #nm_dep").val(s2data.nm_dep);
    $("#fm_modal #kd_bagian").val(s2data.kd_bagian);
    $("#fm_modal #nm_bagian").val(s2data.nm_bagian);
});

function add() {
    dataUrl = situs + 'master/add_potga_ss1';
    edit_mode = 0;

    clear_form('fm_modal');
    $("#bulan").val("<?php echo date('m'); ?>");
    $("#tahun").val("<?php echo date('Y'); ?>");
    $('#myModal').modal('show');
}

function edit() {
    row = $('#tabel_potga_ss1').DataTable().row({
        selected: true
    }).data();

    if (row) {
        dataUrl = situs + 'master/edit_potga_ss1/' + row.id;
        edit_mode = 1;

        set_form('fm_modal', row);
        set_select2_value("#fm_modal #no_ang", row.no_ang, row.no_ang);
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
                        clear_form("fm_modal");
                    }

                    get_potga_ss1();
                }
            }
        });
    }
}

function del() {
    row = $('#tabel_potga_ss1').DataTable().row({
        selected: true
    }).data();

    if (row) {
        prompt = confirm("Anda Yakin Ingin Menghapus Data Ini?");

        if (prompt) {
            $.ajax({
                url: situs + "master/del_potga_ss1",
                data: "id=" + row.id,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_potga_ss1();
                    }
                }
            });
        }
    } else {
        alert("Pilih data di tabel");
    }
}
</script>