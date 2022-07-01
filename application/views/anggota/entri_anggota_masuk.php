<div class="nav-tabs-custom">
    <ul class="nav nav-tabs navtab-bg" id="myTab">
        <li class="active">
            <a href="#entri" class="entri">Entri Anggota Masuk</a>
        </li>
        <li>
            <a href="#laporan" class="laporan">Laporan</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="entri">
            <form id="fm_form">
                <div class="panel-heading with-border">
                    <h4 class="panel-title">Data Keanggotaan</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>NAK</label>
                                <input type="text" id="no_ang" name="no_ang" class="form-control" required="" style="text-transform: uppercase;" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Masuk</label>
                                <input type="text" id="tgl_msk" name="tgl_msk" class="form-control datepicker" required="" readonly="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Gaji</label>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="text" id="gaji" name="gaji" class="form-control number_format" value="0" required data-rule-number="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Plafon</label>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="text" id="plafon" name="plafon" class="form-control number_format" value="0" required data-rule-number="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jumlah Simpanan Pokok</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp</div>
                                    <input type="text" id="jml_simp_pokok" name="jml_simp_pokok" class="form-control number_format" value="0" required="" data-rule-number="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tgl. Potga Simp. Pokok</label>
                                <input type="text" id="tgl_potga_pokok" name="tgl_potga_pokok" class="form-control datepicker" required="" readonly="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-heading with-border">
                    <h4 class="panel-title">Data Pribadi</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nama Anggota</label>
                                <input type="text" id="nm_ang" name="nm_ang" class="form-control" autocomplete="off" style="text-transform: uppercase;" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <div class="form-inline">
                                    <select id="hari_lahir" name="hari_lahir" class="form-control" required="">
                                        <?php echo $hari_lahir; ?>
                                    </select> -
                                    <select id="bulan_lahir" name="bulan_lahir" class="form-control" required="">
                                        <?php echo $bulan; ?>
                                    </select> -
                                    <input type="text" name="tahun_lahir" id="tahun_lahir" class="form-control" data-rule-number="true" required="" placeholder="Tahun" size="4">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" id="kt_lhr" name="kt_lhr" class="form-control" style="text-transform: uppercase;" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select id="jns_kel" name="jns_kel" class="form-control">
                                    <option value="L">Laki-Laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>No. KTP</label>
                                <input type="text" id="no_ktp" name="no_ktp" class="form-control" data-rule-number="true">
                            </div>
                            <div class="form-group">
                                <label>No. Telp/HP</label>
                                <input type="text" id="tlp_hp" name="tlp_hp" class="form-control" data-rule-number="true">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nama Ibu</label>
                                <input type="text" id="nm_ibukdg" name="nm_ibukdg" class="form-control" style="text-transform: uppercase;">
                            </div>
                            <div class="form-group">
                                <label>Nama Pasangan (Suami/Istri)</label>
                                <input type="text" id="nm_psg" name="nm_psg" class="form-control" style="text-transform: uppercase;">
                            </div>
                            <div class="form-group">
                                <label>Alamat Rumah</label>
                                <textarea id="alm_rmh" name="alm_rmh" class="form-control" style="text-transform: uppercase" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-heading with-border">
                    <h4 class="panel-title">Data Perusahaan</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>No. Pegawai</label>
                                <input type="text" id="no_peg" name="no_peg" class="form-control" required="" style="text-transform: uppercase;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Perusahaan</label>
                                <select id="kd_prsh" name="kd_prsh" class="form-control" required=""></select>
                                <input type="hidden" id="nm_prsh" name="nm_prsh" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Departemen</label>
                                <div id="div_dep">
                                    <!-- <select id="kd_dep" name="kd_dep" class="form-control" required=""></select>
                            <input type="hidden" id="nm_dep" name="nm_dep" /> -->
                                    <input type="text" id="nm_dep" name="nm_dep" class="form-control" required="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bagian</label>
                                <div id="div_bagian">
                                    <!-- <select id="kd_bagian" name="kd_bagian" class="form-control" required=""></select>
                            <input type="hidden" id="nm_bagian" name="nm_bagian" /> -->
                                    <input type="text" id="nm_bagian" name="nm_bagian" class="form-control" required="" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <a href="javascript: void(0)" class="btn btn-default btn-small" onclick="batal()">
                            <i class="fa fa-times"></i> Batal</a>
                        <a href="javascript: void(0)" class="btn btn-primary btn-small" onclick="simpan()">
                            <i class="fa fa-floppy-o"></i> Simpan</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane" id="laporan">
            <div class="panel-body">
                <form id="fm_data" onsubmit="return false">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Periode</label>
                                <div class="row form-inline" style="margin: 0px;">
                                    <select id="bulan" name="bulan" class="form-control">
                                        <?php echo $bulan; ?>
                                    </select>
                                    <input type="text" name="tahun" id="tahun" class="form-control" value="<?php echo date('Y'); ?>" maxlength="4" size="4" placeholder="Tahun">
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-primary" onclick="tampilkan()">Tampilkan</a>
                    <a class="btn btn-success" onclick="excel()">Excel</a>
                    <a class="btn btn-danger" onclick="cetak()">Cetak</a>
                </form>
            </div>
            <div class="panel-body" id="div_laporan">
                <h5>Ready!</h5>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("entri")) {
        get_nak_baru();
    }
});

$("#fm_form #kd_prsh").select2({
    ajax: {
        url: situs + 'master/select_perusahaan',
        dataType: 'json',
        delay: 500
    }
}).on("select2:select", function(e) {
    if ($("#fm_form #kd_dep").hasClass("select2-hidden-accessible")) {
        $("#fm_form #kd_dep").val(null).trigger("change");
    }

    if ($("#fm_form #kd_bagian").hasClass("select2-hidden-accessible")) {
        $("#fm_form #kd_bagian").val(null).trigger("change");
    }

    var s2data = e.params.data;

    $("#fm_form #nm_prsh").val(s2data.nm_prsh);

    // if (s2data.kd_prsh != "P01") {
    //     e_dep = "<select id=\"kd_dep\" name=\"kd_dep\" class=\"form-control\" required=\"\"></select><input type=\"hidden\" id=\"nm_dep\" name=\"nm_dep\" />";

    //     $("#div_dep").html(e_dep);

    //     e_bagian = "<select id=\"kd_bagian\" name=\"kd_bagian\" class=\"form-control\" required=\"\"></select> <input type=\"hidden\" id=\"nm_bagian\" name=\"nm_bagian\" />";

    //     $("#div_bagian").html(e_bagian);

    //     $("#fm_form #kd_dep").select2({
    //         ajax: {
    //             url: situs + 'master/select_departemen/' + s2data.kd_prsh,
    //             dataType: 'json',
    //             delay: 500
    //         }
    //     }).on("select2:select", function(e) {
    //         if ($("#fm_form #kd_bagian").hasClass("select2-hidden-accessible")) {
    //             $("#fm_form #kd_bagian").val(null).trigger("change");
    //         }

    //         var s2data1 = e.params.data;

    //         $("#fm_form #nm_dep").val(s2data1.nm_dep);

    //         $("#fm_form #kd_bagian").select2({
    //             ajax: {
    //                 url: situs + 'master/select_bagian/' + s2data1.kd_prsh + "/" + s2data1.kd_dep,
    //                 dataType: 'json',
    //                 delay: 500
    //             }
    //         }).on("select2:select", function(e) {
    //             var s2data2 = e.params.data;

    //             $("#fm_form #nm_bagian").val(s2data2.nm_bagian);
    //         });
    //     });

    //     $("#fm_form").validate().destroy();

    //     $("#fm_form").validate({
    //         rules: {
    //             kd_dep: {
    //                 required: true
    //             },
    //             kd_bagian: {
    //                 required: true
    //             }
    //         }
    //     });
    // } else {
    //     e_dep = "<input type=\"text\" id=\"nm_dep\" name=\"nm_dep\" class=\"form-control\" required=\"\" style=\"text-transform: uppercase;\" />";

    //     $("#div_dep").html(e_dep);

    //     e_bagian = "<input type=\"text\" id=\"nm_bagian\" name=\"nm_bagian\" class=\"form-control\" required=\"\" style=\"text-transform: uppercase;\" />";

    //     $("#div_bagian").html(e_bagian);

    //     $("#fm_form").validate().destroy();

    //     $("#fm_form").validate({
    //         rules: {
    //             nm_dep: {
    //                 required: true
    //             },
    //             nm_bagian: {
    //                 required: true
    //             }
    //         }
    //     });
    // }
});

function get_nak_baru() {
    $.ajax({
        url: situs + "anggota/get_nak_baru",
        success: function(data) {
            $("#no_ang").val(data);
        }
    });
}

function simpan() {
    if ($('#fm_form').valid()) {
        konfirmasi = confirm('Anda yakin data sudah benar?');

        if (konfirmasi) {
            fm_data = $('#fm_form').serialize();

            $.ajax({
                url: situs + "anggota/add_anggota_masuk",
                data: fm_data,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        batal();
                    }

                    if (res.get_no_ang) {
                        get_nak_baru();
                    }
                }
            });
        }
    }
}

function batal() {
    clear_form("fm_form");
    $("#fm_form #nm_ang").focus();
    $("html, body").animate({ scrollTop: 0 }, 500);

    get_nak_baru();
}

$("html, body").animate({ scrollTop: 0 }, 500);
get_nak_baru();

("#fm_data #bulan, #fm_data #tahun").on('change', function() {
    var_tahun = $("#tahun").val();
    var_bulan = $("#bulan").val();

    var_tgl_akhir_bulan = new Date(var_tahun, var_bulan, 0).getDate();

    $("#fm_data #tgl_akhir").val(var_tgl_akhir_bulan);
});

function tampilkan() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = $('#fm_data').serialize();

        $.ajax({
            url: situs + "laporan/anggota_masuk/tampilkan",
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

function excel() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = $('#fm_data').serialize();

        window.open(situs + "laporan/anggota_masuk/excel?" + data_form);
    }
}

function cetak() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/anggota_masuk/cetak?data=" + data_form);
    }
}
</script>