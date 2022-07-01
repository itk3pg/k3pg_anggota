<ul class="nav nav-tabs navtab-bg" id="myTab">
    <li class="active">
        <a href="#laporan" class="laporan">
            <?php echo $judul_menu; ?>
        </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="laporan">
        <div class="panel-body">
            <form id="fm_data" onsubmit="return false">
                <div class="row">
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
                <a class="btn btn-primary" onclick="tampilkan()">Tampilkan</a>
                <a class="btn btn-success" onclick="singkron_sisa_plafon()"><i class="fa fa-refresh"></i> Singkron Sisa Plafon</a>
                <!-- <a class="btn btn-success" onclick="excel()">Excel</a> -->
                <!-- <a class="btn btn-danger" onclick="cetak()">Cetak</a> -->
            </form>
        </div>
        <div class="panel-body" id="div_laporan">
            <h5>Ready!</h5>
        </div>
    </div>
</div>
<script type="text/javascript">
laporan_mode();

$("#fm_data #bulan, #fm_data #tahun").on('change', function() {
    var_tahun = $("#tahun").val();
    var_bulan = $("#bulan").val();

    var_tgl_akhir_bulan = new Date(var_tahun, var_bulan, 0).getDate();

    $("#tgl_akhir").val(var_tgl_akhir_bulan);
});

var ev_get_anggota = 1;

$("#fm_data #no_ang").focus().on("change", function() {
    if (ev_get_anggota == 0) {
        ev_get_anggota = 1;

        get_anggota();
        // get_pinjaman_belum_lunas();
    }
}).keydown(function(e) {
    if (e.which == 13) {
        if (ev_get_anggota == 0) {
            ev_get_anggota = 1;

            get_anggota();
            // get_pinjaman_belum_lunas();
        }
    } else {
        ev_get_anggota = 0;
    }
});

function get_anggota() {
    $no_ang = $("#fm_data #no_ang").val();

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

                    $("#fm_data #nm_ang").val(data_anggota[0].nm_ang);
                    $("#fm_data #no_peg").val(data_anggota[0].no_peg);
                    $("#fm_data #kd_prsh").val(data_anggota[0].kd_prsh);
                    $("#fm_data #nm_prsh").val(data_anggota[0].nm_prsh);
                    $("#fm_data #kd_dep").val(data_anggota[0].kd_dep);
                    $("#fm_data #nm_dep").val(data_anggota[0].nm_dep);
                    $("#fm_data #kd_bagian").val(data_anggota[0].kd_bagian);
                    $("#fm_data #nm_bagian").val(data_anggota[0].nm_bagian);

                    tampilkan();
                } else {
                    $("#fm_data #no_ang").val('');
                    pesan('Data tidak ditemukan');
                }
            }
        });
    }
}

function tampilkan() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = $('#fm_data').serialize();

        $.ajax({
            url: situs + "laporan/rincian_kredit_anggota/tampilkan",
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

        window.open(situs + "laporan/rincian_kredit_anggota/excel?" + data_form);
    }
}

function cetak() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = base64_encode(JSON.stringify(get_form_array('fm_data')));

        window.open(situs + "laporan/rincian_kredit_anggota/cetak?data=" + data_form);
    }
}

function singkron_sisa_plafon() {
    validasi = $('#fm_data').valid();

    konfirmasi = confirm("Anda Yakin?");

    if (validasi && konfirmasi) {
        data_form = $('#fm_data').serialize();

        $.ajax({
            url: situs + "laporan/rincian_kredit_anggota/singkron_sisa_plafon",
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
</script>