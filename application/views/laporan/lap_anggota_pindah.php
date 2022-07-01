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
            <form id="form_laporan" onsubmit="return false">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Periode</label>
                            <div class="row">
                                <div class="col-md-8">
                                    <select id="bulan" name="bulan" class="form-control" required="">
                                        <?php echo $bulan; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" id="tahun" name="tahun" class="form-control text-center" maxlength="4" value="<?php echo date('Y');?>" required="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-body">
            <a href="javascript:void(0)" onclick="tampilkan_laporan()" class="btn btn-primary">
                    <i class="fa fa-file"></i> Tampilkan</a>
            <a href="javascript:void(0)" onclick="xls_laporan()" class="btn btn-success">
                    <i class="fa fa-file-excel-o"></i> Excel</a>
            <!--<a href="javascript:void(0)" onclick="cetak_laporan()" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a>-->
        </div>
        <div class="panel-body" id="div_laporan"></div>
    </div>
</div>
<script type="text/javascript">
laporan_mode();

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');
});

function tampilkan_laporan() {
    validasi = $("#form_laporan").valid();

    if (validasi) {
        dataForm = $("#form_laporan").serialize();

        $.ajax({
            url: situs + "laporan/get_ang_pindah_html",
            data: dataForm,
            beforeSend: function() {
                $("#div_laporan").html("<center>Harap Tunggu ...</center>");
            },
            success: function(res) {
                $("#div_laporan").html(res);
            }
        });
    }
}

function xls_laporan() {
    validasi = $("#form_laporan").valid();

    if (validasi) {
        dataForm = $("#form_laporan").serialize();

        window.open(situs + "laporan/get_ang_pindah_xls?" + dataForm);
    }
}

function pdf_laporan() {
    validasi = $("#form_laporan").valid();

    if (validasi) {
        dataForm = $("#form_laporan").serialize();

        window.open(situs + "laporan/get_ang_pindah_pdf?" + dataForm);
    }
}
</script>