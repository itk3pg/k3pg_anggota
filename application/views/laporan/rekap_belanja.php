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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Periode Belanja</label>
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
                            <label>NAK</label>
                            <input type="text" name="no_ang" id="no_ang" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" onclick="tampilkan()"><i class="fa fa-search"></i> Tampilkan</button>
                        <button type="button" class="btn btn-danger" onclick="cetak_rekap()"><i class="fa fa-print"></i> Cetak</button>
                    </div>
                </div>
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
});

function tampilkan() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = $('#fm_data').serialize();

        $.ajax({
            url: situs + "laporan/rekap_belanja/tampilkan",
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

        window.open(situs + "laporan/rekap_belanja/cetak_rekap?data=" + data_form);
    }
}
</script>