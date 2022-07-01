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
                            <label>NAK</label>
                            <select id="no_ang" name="no_ang" class="form-control" required=""></select>
                        </div>
                        <div class="form-group">
                            <label>Perusahaan</label>
                            <div class="row">
                                <div class="col-md-2" style="padding-right: 0px;">
                                    <input type="text" id="kd_prsh" name="kd_prsh" class="form-control" readonly>
                                </div>
                                <div class="col-md-10">
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
                            <div class="row">
                                <div class="col-md-3" style="padding-right: 0px;">
                                    <input type="text" id="kd_dep" name="kd_dep" class="form-control" readonly>
                                </div>
                                <div class="col-md-9">
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
                            <div class="row">
                                <div class="col-md-3" style="padding-right: 0px;">
                                    <input type="text" id="kd_bagian" name="kd_bagian" class="form-control" readonly>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="nm_bagian" name="nm_bagian" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-body">
            <a href="javascript:void(0)" class="btn btn-primary">
                    <i class="fa fa-file"></i> Tampilkan</a>
            <a href="javascript:void(0)" class="btn btn-success">
                    <i class="fa fa-file-excel-o"></i> Excel</a>
        </div>
        <div class="panel-body" id="div_laporan">
            <table class="table table-bordered table-condensed table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Jenis Hak</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1.</td>
                        <td>Simpanan Sukarela1</td>
                        <td class="text-right">2,000,000</td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th colspan="2">SubTotal</th>
                        <th class="text-right">2,000,000</th>
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Jenis Kewajiban</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1.</td>
                        <td>Sisa Pinjaman Reguler</td>
                        <td class="text-right">1,000,000</td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th colspan="2">SubTotal</th>
                        <th class="text-right">1,000,000</th>
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                        <th class="text-right"></th>
                    </tr>
                    <tr>
                        <th colspan="2">Total (Hak - Kewajiban)</th>
                        <th class="text-right">1,000,000</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
laporan_mode();

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');
});

$("#form_laporan #no_ang").select2({
    ajax: {
        url: situs + 'anggota/select_anggota_by_noang/0',
        dataType: 'json',
        delay: 500
    }
}).on("select2:select", function(e) {
    s2data = e.params.data;

    $("#form_laporan #nm_ang").val(s2data.nm_ang);
    $("#form_laporan #no_peg").val(s2data.no_peg);
    $("#form_laporan #kd_prsh").val(s2data.kd_prsh);
    $("#form_laporan #nm_prsh").val(s2data.nm_prsh);
    $("#form_laporan #kd_dep").val(s2data.kd_dep);
    $("#form_laporan #nm_dep").val(s2data.nm_dep);
    $("#form_laporan #kd_bagian").val(s2data.kd_bagian);
    $("#form_laporan #nm_bagian").val(s2data.nm_bagian);
});

function tampilkan_laporan() {
    validasi = $("#form_laporan").valid();

    if (validasi) {
        dataForm = $("#form_laporan").serialize();

        $.ajax({
            url: situs + "laporan/get_ang_keluar_html",
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

        window.open(situs + "laporan/get_ang_keluar_xls?" + dataForm);
    }
}

function pdf_laporan() {
    validasi = $("#form_laporan").valid();

    if (validasi) {
        dataForm = $("#form_laporan").serialize();

        window.open(situs + "laporan/get_ang_keluar_pdf?" + dataForm);
    }
}
</script>