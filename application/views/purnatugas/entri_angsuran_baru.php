<div class="panel panel-default panel-color">
    <div class="panel-body">
        <div class="row">
            <form id="fm_data" onsubmit="return false">
                <div class="row" id="div_anggota">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>NAK</label>
                            <input type="text" name="no_ang" id="no_ang" class="form-control" data-rule-required="true" autocomplete="off" style="text-transform: uppercase;">
                        </div>
                        <div class="form-group">
                            <label>No. Pegawai</label>
                            <input type="text" id="no_peg" name="no_peg" class="form-control" readonly>
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Plafon</label>
                            <div class="input-group">
                                <div class="input-group-addon">Rp</div>
                                <input type="text" name="plafon" id="plafon" class="form-control number_format" value="0" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Sisa Plafon</label>
                            <div class="input-group">
                                <div class="input-group-addon">Rp</div>
                                <input type="text" name="sisa_plafon" id="sisa_plafon" class="form-control number_format" value="0" readonly="">
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
                                    <input type="text" name="tgl_trans" id="tgl_trans" class="form-control datepicker hitungAngsuran" required="" onchange="hitung_angsuran()" value="<?php echo date('d-m-Y'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>Jangka Waktu</label>
                                    <div class="input-group">
                                        <input type="text" name="tempo_bln" id="tempo_bln" class="form-control hitungAngsuran" required="" data-rule-number="true" onchange="hitung_angsuran()">
                                        <div class="input-group-addon">Bulan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Biaya Admin</label>
                                    <div class="input-group">
                                        <input type="text" name="persen_biaya_admin" id="persen_biaya_admin" class="form-control" required="" data-rule-number="true" onchange="hitung_biaya_admin();hitung_angsuran()" value="0">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>Jml. Admin</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">Rp</div>
                                        <input type="text" name="jml_biaya_admin" id="jml_biaya_admin" class="form-control number_format" readonly="">
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Jumlah Transaksi</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp</div>
                                    <input type="text" id="jml_awal_trans" name="jml_awal_trans" class="form-control number_format hitungAngsuran" required="" data-rule-number="true" onchange="hitung_angsuran();">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Biaya Admin</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp</div>
                                    <input type="text" id="jml_biaya_admin" name="jml_biaya_admin" class="form-control number_format hitungAngsuran" data-rule-number="true" required="" onchange="hitung_angsuran()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Jenis Tagihan</label>
                                <select id="kd_piutang" name="kd_piutang" class="form-control" required="">
                                    <option value="">[-PILIH-]</option>
                                    <option value="G">Bangunan</option>
                                    <option value="B">Belanja Kredit</option>
                                    <option value="E">Elektronik</option>
                                    <option value="M">Sepeda Motor</option>
                                    <option value="U">Pinjaman Uang</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <label>Margin</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">Ubah: <input type="checkbox" name="ubah_margin" id="ubah_margin" class="hitungAngsuran" onchange="hitung_angsuran()"> </div>
                                        <input type="text" name="margin" id="margin" class="form-control hitungAngsuran" data-rule-number="true" autocomplete="off" onchange="hitung_angsuran()">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                    <input type="hidden" name="jml_margin" id="jml_margin">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Jumlah Kredit</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp</div>
                                    <input type="text" id="jml_trans" name="jml_trans" class="form-control number_format" readonly="" data-rule-number="true">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Angsuran</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp</div>
                                    <input type="text" name="angsuran" id="angsuran" class="form-control number_format" readonly="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Keterangan</label>
                                <input type="text" id="ket" name="ket" class="form-control" required="" style="text-transform: uppercase;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary" onclick="simpan()">Simpan</button>
                        <button type="button" class="btn btn-default" onclick="batal()">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel-heading">
        <div class="pull-right">
            <button class="btn btn-info btn-small" onclick="cetak_transaksi()"> <i class="fa fa-print"></i> Cetak</button>
        </div>
        <button class="btn btn-danger btn-small" onclick="del()">
            <i class="fa fa-trash"></i> Hapus</button>
    </div>
    <div class="panel-body">
        <table id="tabel_transaksi" class="table table-bordered table-condensed table-hover table-striped nowrap" width="100%">
            <thead>
                <tr>
                    <th width="50">No.</th>
                    <th>Bukti</th>
                    <th>Tanggal</th>
                    <th>Jml. Transaksi</th>
                    <th>Uang Muka</th>
                    <th>Jumlah</th>
                    <th>Jangka</th>
                    <th>Tgl JT</th>
                    <th>Margin</th>
                    <th>Jml. Margin</th>
                    <th>Biaya Admin</th>
                    <th>Angsuran</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
edit_mode = 0;

function get_transaksi() {
    $fm_data = $("#fm_data").serialize();
    url_tabel = situs + "purnatugas/get_transaksi?" + $fm_data;
    tabel_id = "tabel_transaksi";

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
                data: "no_trans"
            }, {
                data: "tgl_trans"
            }, {
                data: "jml_awal_trans",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "jml_uang_muka",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "jml_trans",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "tempo_bln",
                className: "text-center"
            }, {
                data: "tgl_jt",
                className: "text-center"
            }, {
                data: "margin",
                className: "text-center"
            }, {
                data: "jml_margin",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "jml_biaya_admin",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "angsuran",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "ket"
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
                        $("html, body").animate({ scrollTop: $(document).height() }, 0);
                    }
                });
            }
        });

        $("#" + tabel_id).DataTable().off("draw.dt");
    }
}

var ev_get_anggota = 1;

$("#fm_data #no_ang").focus().on("change", function() {
    if (ev_get_anggota == 0) {
        ev_get_anggota = 1;

        get_anggota();
        get_transaksi();
    }
}).keydown(function(e) {
    if (e.which == 13) {
        if (ev_get_anggota == 0) {
            ev_get_anggota = 1;

            get_anggota();
            get_transaksi();
        }
    } else {
        ev_get_anggota = 0;
    }
});

function get_anggota() {
    $no_ang = $("#fm_data #no_ang").val();

    if ($no_ang) {
        $.ajax({
            url: situs + 'anggota/select_anggota_by_noang/0',
            data: "q=" + $no_ang,
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                proses();
            },
            success: function(data) {
                if (typeof(data.results) != "undefined" && data.results.length > 0) {
                    data_nasabah = data.results;

                    $("#fm_data #nm_ang").val(data_nasabah[0].nm_ang);
                    $("#fm_data #no_peg").val(data_nasabah[0].no_peg);
                    $("#fm_data #kd_prsh").val(data_nasabah[0].kd_prsh);
                    $("#fm_data #nm_prsh").val(data_nasabah[0].nm_prsh);
                    $("#fm_data #kd_dep").val(data_nasabah[0].kd_dep);
                    $("#fm_data #nm_dep").val(data_nasabah[0].nm_dep);
                    $("#fm_data #kd_bagian").val(data_nasabah[0].kd_bagian);
                    $("#fm_data #nm_bagian").val(data_nasabah[0].nm_bagian);
                    $("#fm_data #plafon").val(data_nasabah[0].plafon).trigger('change');
                    $("#fm_data #sisa_plafon").val(data_nasabah[0].sisa_plafon).trigger('change');

                    no_proses();
                } else {
                    $("#fm_data #no_ang").val('');
                    pesan('Data tidak ditemukan');
                }
            }
        });
    }
}

// function hitung_biaya_admin() {
//     $jml_trans = parseFloat(hapus_koma($("#jml_trans").val()));
//     $persen_biaya_admin = parseFloat($("#persen_biaya_admin").val());

//     $jml_biaya_admin = $jml_trans * ($persen_biaya_admin / 100);

//     $('#jml_biaya_admin').val($jml_biaya_admin).trigger("change");
// }

// function ambil_margin() {
//     $data_ajax = "tgl_trans=" + $('#tgl_trans').val() + "&tempo_bln=" + $('#tempo_bln').val();

//     $.ajax({
//         url: situs + 'purnatugas/get_margin_bp',
//         data: $data_ajax,
//         type: 'post',
//         success: function(data) {
//             $('#margin').val(data);

//             hitung_angsuran();
//         }
//     });
// }

function hitung_angsuran() {
    // $margin = parseFloat($('#margin').val());
    // $jml_awal_trans = parseFloat(hapus_koma($('#jml_awal_trans').val()));
    // $jml_uang_muka = parseFloat(hapus_koma($('#jml_uang_muka').val()));
    // $jml_awal_trans = ($jml_awal_trans) ? $jml_awal_trans : 0;
    // $jml_uang_muka = ($jml_uang_muka) ? $jml_uang_muka : 0;
    // $jml_trans = $jml_awal_trans - $jml_uang_muka;

    // $("#jml_trans").val($jml_trans).trigger("change");

    // $tempo_bln = parseFloat($('#tempo_bln').val());
    // // $jml_biaya_admin = parseFloat(hapus_koma($('#jml_biaya_admin').val()));

    // $jml_margin_setahun = $jml_trans * ($margin / 100);

    // // $jml_margin_bln = Math.round($jml_margin_setahun / 12).toFixed(2);
    // $jml_margin_bln = Math.round($jml_margin_setahun).toFixed(2);
    // // $jml_margin = $jml_margin_bln * $tempo_bln;

    // // $angsuran = ($jml_trans + $jml_margin) / $tempo_bln;
    // $angsuran = Math.round(($jml_trans + parseFloat($jml_margin_bln)) / $tempo_bln);

    // $('#angsuran').val($angsuran).trigger('change');

    classHitungAngsuran = $('.hitungAngsuran').val();

    if (classHitungAngsuran != "") {
        data_input = $('#fm_data').serialize();

        $.ajax({
            url: situs + "purnatugas/hitung_angsuran_bp",
            data: data_input,
            dataType: 'json',
            success: function(data) {
                $("#fm_data #margin").val(data.margin);
                $("#fm_data #jml_margin").val(data.jml_margin);
                $("#fm_data #jml_trans").val(data.jml_trans).trigger("change");
                $("#fm_data #angsuran").val(data.angsuran).trigger("change");
            }
        });
    }
}

function simpan() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        // $tempo_bln = parseFloat($('#tempo_bln').val());

        // if ($tempo_bln > 36 || $tempo_bln < 1) {
        //     alert('jangka waktu harus 1 s.d. 36');

        //     return false;
        // }

        $angsuran = parseFloat(hapus_koma($('#angsuran').val()));
        $sisa_plafon = parseFloat(hapus_koma($('#sisa_plafon').val()));

        if ($angsuran > $sisa_plafon) {
            alert('sisa plafon tidak mencukupi');
            return false;
        }

        konfirmasi = confirm("Anda yakin data sudah benar?");

        if (konfirmasi) {
            data_input = $('#fm_data').serialize();

            $.ajax({
                url: situs + 'purnatugas/add_transaksi',
                data: data_input,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        // if (edit_mode) {
                        //     $('#myModal').modal('hide');
                        // } else {
                        // }

                        $("#tempo_bln, #margin, #jml_trans, #jml_awal_trans, #jml_uang_muka").val('');
                        $("#no_ang").focus();
                        $("#persen_biaya_admin, #jml_biaya_admin, #angsuran").val('0').trigger("change");
                        $("#div_anggota input, #kd_piutang, #ket").val('');
                        $("#ubah_margin").removeAttr('checked');

                        get_transaksi();
                    }
                }
            });
        }
    }
}

function batal() {
    $("#jml_awal_trans, #jml_uang_muka, #jml_trans, #tempo_bln, #margin, #angsuran").val('');
}

function del() {
    if ($.fn.DataTable.isDataTable("#tabel_transaksi")) {
        row = $('#tabel_transaksi').DataTable().row({
            selected: true
        }).data();

        if (row) {
            prompt = confirm("Anda Yakin Ingin Menghapus Data Ini?");

            if (prompt) {
                $.ajax({
                    url: situs + "purnatugas/hapus_transaksi",
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
                            get_transaksi();
                        }
                    }
                });
            }
        } else {
            alert("Pilih data di tabel");
        }
    }
}

function cetak_transaksi() {
    if ($.fn.DataTable.isDataTable("#tabel_transaksi")) {
        row = $('#tabel_transaksi').DataTable().row({
            selected: true
        }).data();

        if (row) {
            data_cetak = base64_encode(JSON.stringify(row));

            window.open(situs + "purnatugas/cetak_transaksi?data=" + data_cetak);
        }
    }
}
</script>