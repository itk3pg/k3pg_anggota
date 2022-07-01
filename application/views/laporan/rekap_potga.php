<ul class="nav nav-tabs navtab-bg" id="myTab">
    <li class="active">
        <a href="#laporan" class="laporan">
            <?php echo $judul_menu; ?>
        </a>
    </li>
    <li>
        <a href="#pot_per_nak" class="pot_per_nak">
            Potongan Per NAK
        </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="laporan">
        <div class="panel-body">
            <form id="fm_data" onsubmit="return false">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Periode Potong Gaji</label>
                            <div class="row">
                                <div class="col-md-8">
                                    <select id="bulan" name="bulan" class="form-control">
                                        <?php echo $bulan; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="tahun" id="tahun" class="form-control" value="<?php echo date('Y'); ?>" maxlength="4" size="4" placeholder="Tahun">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Perusahaan</label>
                            <select id="kd_prsh" name="kd_prsh" class="form-control" required=""></select>
                            <input type="hidden" name="nm_prsh" id="nm_prsh">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>NAK</label>
                            <div class="row form-inline" style="margin: 0px;">
                                <input type="text" name="no_ang" id="no_ang" class="form-control" size="4">
                                s.d.
                                <input type="text" name="no_ang_akhir" id="no_ang_akhir" class="form-control" size="4">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tgl. Cetak</label>
                            <input type="text" name="tgl_cetak" id="tgl_cetak" class="form-control datepicker" value="<?php echo date('d-m-Y'); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>No. Surat Pengantar</label>
                            <input type="text" name="no_sp" id="no_sp" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>No. Kuitansi</label>
                            <input type="text" name="no_kuitansi" id="no_kuitansi" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Keterangan</label>
                            <input type="text" name="ket" id="ket" class="form-control" value="TRANSFER BNI 0044535912">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Cetak Laporan Rekap dan Rincian</label>
                        <br>
                        <a class="btn btn-danger" onclick="cetak_rekap()"><i class="fa fa-print"></i> Cetak Rekap Potga</a>
                        <a class="btn btn-danger" onclick="cetak_daftar()"><i class="fa fa-print"></i> Cetak Rincian Potga</a>
                        <a class="btn btn-danger" onclick="cetak_rekap_kkbkpr()"><i class="fa fa-print"></i> Cetak Rekap Potongan KKB/KPR</a>
                        <a class="btn btn-danger" onclick="cetak_invoice()"><i class="fa fa-print"></i> Cetak Invoice</a>
                        <a class="btn btn-danger" onclick="cetak_kuitansi()"><i class="fa fa-print"></i> Cetak Kuitansi</a>
                        <a class="btn btn-danger" onclick="cetak_bukti_masuk()"><i class="fa fa-print"></i> Cetak Bukti Masuk</a>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-success" onclick="excel_rekap_potga()"><i class="fa fa-file-excel-o"></i> Excel Rekap Potga</a>
                        <a class="btn btn-success" onclick="excel_rincian_potga()"><i class="fa fa-file-excel-o"></i> Excel Rincian Potga</a>
                        <a class="btn btn-success" onclick="excel_rekap_kkpkpr()"><i class="fa fa-file-excel-o"></i> Excel Rekap Potongan KKB/KPR</a>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <label>Cetak Slip</label>
                        <br>
                        <a class="btn btn-danger" onclick="cetak_slip_potga()"><i class="fa fa-print"></i> Cetak Slip Potga</a>
                        <a class="btn btn-danger" onclick="cetak_slip_kkbkpr()"><i class="fa fa-print"></i> Cetak Slip KKB/KPR</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="tab-pane" id="pot_per_nak">
        <div class="panel-body">
            <form id="fm_data_potga" onsubmit="return false">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Periode Potong Gaji</label>
                            <div class="row">
                                <div class="col-md-8">
                                    <select id="bulan" name="bulan" class="form-control">
                                        <?php echo $bulan; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="tahun" id="tahun" class="form-control" value="<?php echo date('Y'); ?>" maxlength="4" size="4" placeholder="Tahun">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>NAK</label>
                            <input type="text" name="no_ang" id="no_ang" class="form-control" data-rule-required="true" autocomplete="off" style="text-transform: uppercase;" required="">
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
                <a class="btn btn-primary" onclick="tampilkan_potga()">Tampilkan</a>
                <a class="btn btn-danger" onclick="cetak_potga_nak()">Cetak</a>
            </form>
        </div>
        <div class="panel-body" id="div_laporan">
            <h5>Ready!</h5>
        </div>
    </div>
</div>
<script type="text/javascript">
laporan_mode();

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    // if ($(this).hasClass("input")) {
    //     get_pinjaman_belum_lunas();
    // } else
    // if ($(this).hasClass("view")) {
    //     get_pinjaman_sudah_lunas();
    // }
});

$("#bulan, #tahun").on('change', function() {
    var_tahun = $("#tahun").val();
    var_bulan = $("#bulan").val();

    var_tgl_akhir_bulan = new Date(var_tahun, var_bulan, 0).getDate();

    $("#tgl_akhir").val(var_tgl_akhir_bulan);

    if ($("#fm_data #kd_prsh").val() != "ANPER") {
        cek_nomor_cetak();
    } else {
        $("#fm_data #no_sp, #fm_data #no_kuitansi").val('0');
    }
});

$("#fm_data #kd_prsh").select2({
    ajax: {
        url: situs + 'master/select_perusahaan_plusAnper',
        dataType: 'json',
        delay: 500
    }
}).on("select2:select", function(e) {
    s2data = e.params.data;

    $("#fm_data #nm_prsh").val(s2data.nm_prsh);

    if ($("#fm_data #kd_prsh").val() != "ANPER") {
        cek_nomor_cetak();
    } else {
        $("#fm_data #no_sp, #fm_data #no_kuitansi").val('0');
    }
});

function tampilkan() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = $('#fm_data').serialize();

        $.ajax({
            url: situs + "laporan/rekap_potga/tampilkan",
            data: data_form,
            type: "POST",
            beforeSend: function() {
                proses();
            },
            success: function(data) {
                no_proses();

                $("#div_laporan").html(data);
            }
        });
    }
}

function cetak_rekap() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/cetak_rekap?data=" + data_form);
    }
}

function cetak_daftar() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/cetak_daftar_potga?data=" + data_form);
    }
}

function cetak_invoice() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        if ($('#fm_data #kd_prsh').val() != "ANPER") {
            simpan_nomor_cetak();
        }

        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/cetak_invoice?data=" + data_form);
    }
}

function cetak_kuitansi() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        if ($('#fm_data #kd_prsh').val() != "ANPER") {
            simpan_nomor_cetak();
        }

        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/cetak_kuitansi?data=" + data_form);
    }
}

function cetak_bukti_masuk() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        if ($('#fm_data #kd_prsh').val() != "ANPER") {
            simpan_nomor_cetak();
        }

        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/cetak_bukti_masuk?data=" + data_form);
    }
}

function cetak_slip_potga() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/cetak_slip_potga?data=" + data_form);
    }
}

function cetak_slip_kkbkpr() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/cetak_slip_kkbkpr?data=" + data_form);
    }
}

function cetak_rekap_kkbkpr() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/cetak_rekap_kkbkpr?data=" + data_form);
    }
}

var ev_get_anggota_kredit_potga = 1;

$("#fm_data_potga #no_ang").focus().on("change", function() {
    if (ev_get_anggota_kredit_potga == 0) {
        ev_get_anggota_kredit_potga = 1;

        get_anggota_kredit();
        // get_pinjaman_belum_lunas();
    }
}).keydown(function(e) {
    if (e.which == 13) {
        if (ev_get_anggota_kredit_potga == 0) {
            ev_get_anggota_kredit_potga = 1;

            get_anggota_kredit();
            // get_pinjaman_belum_lunas();
        }
    } else {
        ev_get_anggota_kredit_potga = 0;
    }
});

function get_anggota_kredit() {
    $no_ang = $("#fm_data_potga #no_ang").val();

    if ($no_ang) {
        $.ajax({
            url: situs + 'anggota/select_anggota_noang/0',
            data: "q=" + $no_ang,
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                // proses();
            },
            success: function(data) {
                if (typeof(data.results) != "undefined" && data.results.length > 0) {
                    no_proses();
                    data_anggota = data.results;

                    $("#fm_data_potga #nm_ang").val(data_anggota[0].nm_ang);
                    $("#fm_data_potga #no_peg").val(data_anggota[0].no_peg);
                    $("#fm_data_potga #kd_prsh").val(data_anggota[0].kd_prsh);
                    $("#fm_data_potga #nm_prsh").val(data_anggota[0].nm_prsh);
                    $("#fm_data_potga #kd_dep").val(data_anggota[0].kd_dep);
                    $("#fm_data_potga #nm_dep").val(data_anggota[0].nm_dep);
                    $("#fm_data_potga #kd_bagian").val(data_anggota[0].kd_bagian);
                    $("#fm_data_potga #nm_bagian").val(data_anggota[0].nm_bagian);

                    tampilkan_potga();
                } else {
                    $("#fm_data_potga #no_ang").val('');
                    pesan('Data tidak ditemukan');
                }
            }
        });
    }
}

function tampilkan_potga() {
    validasi = $('#fm_data_potga').valid();

    if (validasi) {
        data_form = $('#fm_data_potga').serialize();

        $.ajax({
            url: situs + "laporan/rekap_potga/tampilkan_potga_nak",
            data: data_form,
            type: "POST",
            beforeSend: function() {
                proses();
            },
            success: function(data) {
                no_proses();

                $("#div_laporan").html(data);
            }
        });
    }
}

function cetak_potga_nak() {
    validasi = $('#fm_data_potga').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data_potga')));

        window.open(situs + "laporan/rekap_potga/cetak_slip_potga?data=" + data_form);
    }
}

function cek_nomor_cetak() {
    data_form = $('#fm_data').serialize();

    $.ajax({
        url: situs + "laporan/rekap_potga/cek_nomor_cetak",
        data: data_form,
        type: "POST",
        dataType: 'json',
        beforeSend: function() {
            // proses();
        },
        success: function(data) {
            // no_proses();

            $("#fm_data #no_sp").val(data.no_sp);
            $("#fm_data #no_kuitansi").val(data.no_kuitansi);
        }
    });
}

function simpan_nomor_cetak() {
    data_form = $('#fm_data').serialize();

    $.ajax({
        url: situs + "laporan/rekap_potga/simpan_nomor_cetak",
        data: data_form,
        type: "POST",
        // beforeSend: function() {},
        // success: function(data) {}
    });
}

function excel_rekap_potga() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/excel_rekap_potga?data=" + data_form);
    }
}

function excel_rincian_potga() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/excel_rincian_potga?data=" + data_form);
    }
}

function excel_rekap_kkpkpr() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rekap_potga/excel_rekap_kkbkpr?data=" + data_form);
    }
}
</script>