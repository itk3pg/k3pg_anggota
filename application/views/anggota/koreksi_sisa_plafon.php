<div class="panel panel-default panel-color">
    <div class="panel-body">
        <div class="row">
            <form id="fm_data" onsubmit="return false">
                <div class="row" id="div_anggota">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>NAK</label>
                            <input type="text" name="no_ang" id="no_ang" class="form-control" data-rule-required="true" autocomplete="off" style="text-transform: uppercase;">
                            <!-- <select id="no_ang" name="no_ang" class="form-control" required=""></select> -->
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
                                <div class="col-md-6">
                                    <label>Tanggal</label>
                                    <input type="text" name="tgl_penj" id="tgl_penj" class="form-control datepicker" required="" value="<?php echo date('d-m-Y'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>Mode Koreksi</label>
                                    <select id="jenis_debet" name="jenis_debet" class="form-control">
                                        <option value="TAMBAHPLAFON">TAMBAH</option>
                                        <option value="KURANGPLAFON">KURANG</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jumlah</label>
                            <div class="input-group">
                                <div class="input-group-addon">Rp</div>
                                <input type="text" id="jumlah" name="jumlah" class="form-control number_format" data-rule-required="true" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <br>
                            <button type="button" class="btn btn-primary" onclick="simpan()">Simpan</button>
                            <button type="button" class="btn btn-default" onclick="batal()">Batal</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Sisa Plafon</label>
                            <div class="input-group">
                                <div class="input-group-addon">Rp</div>
                                <input type="text" id="sisa_plafon" name="sisa_plafon" class="form-control input-lg number_format" readonly="" value="0" style="font-weight: bolder;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Total Plafon</label>
                            <div class="input-group">
                                <div class="input-group-addon">Rp</div>
                                <input type="text" id="plafon" name="plafon" class="form-control input-lg number_format" readonly="" value="0" style="font-weight: bolder;">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel-heading">
        <div class="row">
            <div class="pull-left">
                <button class="btn btn-danger btn-small" onclick="del()">
                    <i class="fa fa-trash"></i> Hapus</button>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <table id="tabel_koreksi_plafon" class="table table-bordered table-condensed table-hover table-striped nowrap" width="100%">
            <thead>
                <tr>
                    <th width="50">No.</th>
                    <th>Tanggal</th>
                    <th>Mode Koreksi</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Setor/Tarik Simpanan</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="simpan()">Simpan</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
edit_mode = 0;
mode_cetak = "off";

$(window).on("focus", function() {
    set_status_cetak();
});

function get_koreksi_plafon() {
    $fm_data = $("#fm_data").serialize();
    url_tabel = situs + "anggota/get_koreksi_plafon?" + $fm_data;
    tabel_id = "tabel_koreksi_plafon";

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
                data: "tgl_penj"
            }, {
                data: "jenis_debet1"
            }, {
                data: "jml_debet",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
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

        $("#" + tabel_id).DataTable().off("draw.dt");
    }
}

var ev_get_anggota = 1;

$("#div_anggota #no_ang").focus().on("change", function() {
    if (ev_get_anggota == 0) {
        ev_get_anggota = 1;

        get_anggota();
        get_koreksi_plafon();
    }
}).keydown(function(e) {
    if (e.which == 13) {
        if (ev_get_anggota == 0) {
            ev_get_anggota = 1;

            get_anggota();
            get_koreksi_plafon();
        }
    } else {
        ev_get_anggota = 0;
    }
});

function get_anggota() {
    $no_ang = $("#fm_data #no_ang").val();

    if ($no_ang) {
        $.ajax({
            url: situs + 'anggota/select_anggota_by_noang',
            data: "q=" + $no_ang,
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                proses();
            },
            success: function(data) {
                if (typeof(data.results) != "undefined" && data.results.length > 0) {
                    no_proses();
                    data_anggota = data.results;

                    $("#fm_data #nm_ang").val(data_anggota[0].nm_ang);
                    $("#fm_data #no_peg").val(data_anggota[0].no_peg);
                    $("#fm_data #kd_prsh").val(data_anggota[0].kd_prsh);
                    $("#fm_data #nm_prsh").val(data_anggota[0].nm_prsh);
                    $("#fm_data #kd_dep").val(data_anggota[0].kd_dep);
                    $("#fm_data #nm_dep").val(data_anggota[0].nm_dep);
                    $("#fm_data #kd_bagian").val(data_anggota[0].kd_bagian);
                    $("#fm_data #nm_bagian").val(data_anggota[0].nm_bagian);
                    $("#fm_data #sisa_plafon").val(data_anggota[0].sisa_plafon).trigger("change");
                    $("#fm_data #plafon").val(data_anggota[0].plafon).trigger("change");
                } else {
                    $("#fm_data #no_ang").val('');
                    pesan('Data tidak ditemukan');
                }
            }
        });
    }
}

function simpan() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        konfirmasi = confirm("Anda yakin data sudah benar?");

        if (konfirmasi) {
            data_input = $('#fm_data').serialize();

            $.ajax({
                url: situs + 'anggota/add_koreksi_plafon',
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
                            $("#jumlah, #sisa_plafon, #plafon").val('');
                            $("#div_anggota input").val('');
                            setTimeout(function() {
                                $("#div_anggota #no_ang").focus();
                            }, 300);
                        }

                        get_anggota();
                    }
                }
            });
        }
    }
}

function batal() {
    $("#jumlah").val('');
}

function del() {
    if ($.fn.DataTable.isDataTable("#tabel_koreksi_plafon")) {
        row = $('#tabel_koreksi_plafon').DataTable().row({
            selected: true
        }).data();

        if (row) {
            prompt = confirm("Anda Yakin Ingin Menghapus Data Ini?");

            if (prompt) {
                $.ajax({
                    url: situs + "anggota/del_koreksi_plafon",
                    data: row,
                    dataType: "JSON",
                    type: "POST",
                    beforeSend: function() {
                        proses();
                    },
                    success: function(res) {
                        pesan(res.msg, 1);

                        if (res.status) {
                            get_anggota();
                            get_koreksi_plafon();
                        }
                    }
                });
            }
        } else {
            alert("Pilih data di tabel");
        }
    }
}
</script>