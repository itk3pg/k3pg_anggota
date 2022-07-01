<div class="nav-tabs-custom">
    <ul class="nav nav-tabs navtab-bg" id="myTab">
        <li class="active">
            <a href="#input" class="input">Data Angsuran</a>
        </li>
        <li>
            <a href="#view" class="view">View Data Pelunasan</a>
        </li>
        <li>
            <a href="#view_kredit" class="view_kredit">Rincian Kredit Anggota</a>
        </li>
        <li>
            <a href="#pot_per_nak" class="pot_per_nak">Potongan Per NAK</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="input">
            <div class="panel-body">
                <form id="fm_data" onsubmit="return false">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>NAK</label>
                                <input type="text" name="no_ang" id="no_ang" class="form-control" data-rule-required="true" autocomplete="off" style="text-transform: uppercase;">
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
                </form>
            </div>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-8 text-right">
                        <form id="fm_periode" onsubmit="return false" class="form-inline">
                            <label>Periode Angsuran yang dilunasi Mulai : </label>
                            <select id="bulan" name="bulan" class="form-control" onchange="get_pinjaman_belum_lunas();get_pinjaman_belum_lunas_cetak()">
                                <?php echo $bulan_angsuran; ?>
                            </select>
                            <input type="text" name="tahun" id="tahun" class="form-control" value="<?php echo $tahun_angsuran; ?>" maxlength="4" size="4" placeholder="Tahun" onchange="get_pinjaman_belum_lunas();get_pinjaman_belum_lunas_cetak()">
                            <button class="btn btn-info" onclick="get_pinjaman_belum_lunas();get_pinjaman_belum_lunas_cetak()"><i class="fa fa-search"></i> Tampilkan</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="panel panel-info panel-border">
                <div class="panel-heading">
                    <ul class="nav nav-tabs navtab-bg" id="tab-bawah">
                        <li class="active">
                            <a href="#tab-bawah-cetak" class="tab_bawah_cetak"><i class="fa fa-print"></i> Cetak Perhitungan, Slip 1 & 2 (bisa lebih dari 1)</a>
                        </li>
                        <li>
                            <a href="#tab-bawah-pelunasan" class="tab_bawah_pelunasan"><i class="fa fa-money"></i> Pelunasan</a>
                        </li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-bawah-cetak">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-addon">Tgl Pelunasan</div>
                                        <input type="text" name="tgl_lunas_cetak" id="tgl_lunas_cetak" class="form-control datepicker" value="<?php echo date('d-m-Y'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-8 text-right">
                                    <button class="btn btn-success btn-small" onclick="pelunasan_dipercepat_ganda()"> <i class="fa fa-money"></i> Pelunasan</button>
                                    <button class="btn btn-info btn-small" onclick="cetak_perhitungan_blm_lunas_baru()"> <i class="fa fa-print"></i> Cetak Perhitungan</button>
                                    <button class="btn btn-info btn-small" onclick="cetak_slip1_blm_lunas_baru()"> <i class="fa fa-print"></i> Cetak Slip 1</button>
                                    <button class="btn btn-info btn-small" onclick="cetak_slip2_blm_lunas_baru()"> <i class="fa fa-print"></i> Cetak Slip 2</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <table class="table table-bordered table-condensed table-striped table-hover nowrap" id="tabel_belum_lunas_cetak" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>No. Bukti</th>
                                            <th>Tgl. Realisasi</th>
                                            <th>Keterangan</th>
                                            <th>Jenis Pinjaman</th>
                                            <th>Jangka</th>
                                            <th>Sudah Diangsur</th>
                                            <th>Sisa Waktu</th>
                                            <th>Jml Pokok</th>
                                            <th>Angsuran</th>
                                            <th>Saldo Akhir</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div style="display: none;">
                                <form id="fm_cetak_perhitungan" method="post" target="_blank" action="<?php echo site_url('laporan/bukti_pelunasan/cetak_perhitungan_blm_lunas_baru') ?>">
                                    <input type="hidden" name="data" id="data">
                                </form>
                                <form id="fm_cetak_slip1" method="post" target="_blank" action="<?php echo site_url('laporan/bukti_pelunasan/cetak_slip1_blm_lunas_baru') ?>">
                                    <input type="hidden" name="data" id="data">
                                </form>
                                <form id="fm_cetak_slip2" method="post" target="_blank" action="<?php echo site_url('laporan/bukti_pelunasan/cetak_slip2_blm_lunas_baru') ?>">
                                    <input type="hidden" name="data" id="data">
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-bawah-pelunasan">
                            <div class="row">
                                <button class="btn btn-success btn-small" onclick="open_pelunasan_dipercepat()"> <i class="fa fa-money"></i> Pelunasan</button>
                            </div>
                            <br>
                            <div class="row">
                                <table class="table table-bordered table-condensed table-striped table-hover nowrap" id="tabel_belum_lunas" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>No. Bukti</th>
                                            <th>Tgl. Realisasi</th>
                                            <th>Keterangan</th>
                                            <th>Jenis Pinjaman</th>
                                            <th>Jangka</th>
                                            <th>Sudah Diangsur</th>
                                            <th>Sisa Waktu</th>
                                            <th>Jml Pokok</th>
                                            <th>Angsuran</th>
                                            <th>Saldo Akhir</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="view">
            <div class="panel-body">
                <div class="col-md-2">
                    <button class="btn btn-danger btn-small" onclick="batalkan_pelunasan_dipercepat()"> <i class="fa fa-times"></i> Hapus Pelunasan</button>
                </div>
                <div class="col-md-10 text-right">
                    <!-- <button class="btn btn-info btn-small" onclick="cetak_perhitungan()"> <i class="fa fa-print"></i> Cetak Perhitungan</button> -->
                    <!-- <button class="btn btn-info btn-small" onclick="cetak_slip1()"> <i class="fa fa-print"></i> Cetak Slip 1</button> -->
                    <!-- <button class="btn btn-info btn-small" onclick="cetak_slip2()"> <i class="fa fa-print"></i> Cetak Slip 2</button> -->
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-condensed table-striped table-hover nowrap" id="tabel_sudah_lunas" width="100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Bukti Pelunasan</th>
                            <th>Tgl. Pelunasan</th>
                            <th>Periode Pelunasan</th>
                            <th>Keterangan</th>
                            <th>No. Bukti</th>
                            <th>Tgl. Rilis</th>
                            <th>Jenis Pinjaman</th>
                            <th>NAK</th>
                            <th>No. Pegawai</th>
                            <th>Nama</th>
                            <th>Perusahaan</th>
                            <th>Jangka</th>
                            <th>Jml Pokok</th>
                            <th>Angsuran</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="tab-pane" id="view_kredit">
            <div class="panel-body">
                <form id="fm_data_kredit" onsubmit="return false">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>NAK</label>
                                <input type="text" name="no_ang" id="no_ang" class="form-control" data-rule-required="true" autocomplete="off" style="text-transform: uppercase;">
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
                    <a class="btn btn-primary" onclick="tampilkan_kredit()">Tampilkan</a>
                    <a class="btn btn-success" onclick="singkron_sisa_plafon()"><i class="fa fa-refresh"></i> Singkron Sisa Plafon</a>
                </form>
            </div>
            <div class="panel-body" id="div_laporan">
                <h5>Ready!</h5>
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
            <div class="panel-body" id="div_laporan_potga">
                <h5>Ready!</h5>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Pelunasan Dipercepat</h4>
            </div>
            <div class="modal-body">
                <form id="fm_pelunasan">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>NAK</label>
                                <input type="text" name="no_ang" id="no_ang" class="form-control" readonly="">
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
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal Pelunasan</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="tgl_lunas" id="tgl_lunas" class="form-control datepicker" required="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-md-3">
                            <div class="form-group">
                                <label>Ubah Margin</label>
                                <div class="input-group">
                                    <input type="text" name="ubah_margin" id="ubah_margin" class="form-control">
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Keterangan</label>
                                <input type="text" name="ket" id="ket" class="form-control" readonly="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <button type="button" class="btn btn-info btn-small" onclick="cetak_perhitungan_blm_lunas()"> <i class="fa fa-print"></i> Cetak Perhitungan</button>
                            <button type="button" class="btn btn-info btn-small" onclick="cetak_slip1_blm_lunas()"> <i class="fa fa-print"></i> Cetak Slip1</button>
                            <button type="button" class="btn btn-info btn-small" onclick="cetak_slip2_blm_lunas()"> <i class="fa fa-print"></i> Cetak Slip2</button>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" class="btn btn-primary" onclick="proses_pelunasan_dipercepat()"><i class="fa fa-save"></i> Simpan Pelunasan</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        </div>
                    </div>
                    <hr>
                    <div id="div_pelunasan_dipercepat"></div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("input")) {
        get_pinjaman_belum_lunas();
        get_pinjaman_belum_lunas_cetak();
    } else if ($(this).hasClass("view")) {
        get_pinjaman_sudah_lunas();
    }
});

$('#tab-bawah a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("tab_bawah_pelunasan")) {
        get_pinjaman_belum_lunas();
    } else if ($(this).hasClass("tab_bawah_cetak")) {
        get_pinjaman_belum_lunas_cetak();
    }
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
                } else {
                    $("#fm_data #no_ang").val('');
                    pesan('Data tidak ditemukan');
                }

                get_pinjaman_belum_lunas();
                get_pinjaman_belum_lunas_cetak();
            }
        });
    }
}

function get_pinjaman_belum_lunas() {
    $fm_data = $("#fm_data").serialize();
    $fm_periode = $("#fm_periode").serialize();
    url_tabel = situs + "pelunasan/get_pinjaman_belum_lunas?" + $fm_data + "&" + $fm_periode;
    tabel_id = "tabel_belum_lunas";

    if ($.fn.DataTable.isDataTable("#" + tabel_id)) {
        $("#" + tabel_id).DataTable().ajax.url(url_tabel).load(function() {
            // $('#tabel_piutang').DataTable().responsive.recalc().responsive.rebuild();
        }, false);
    } else {
        $("#" + tabel_id).DataTable({
            scrollY: 350,
            scrollX: true,
            ordering: false,
            paging: false,
            searching: false,
            select: 'single',
            processing: true,
            serverSide: true,
            ajax: url_tabel,
            columns: [{
                data: "nomor",
                className: "text-right"
            }, {
                data: "no_pinjam"
            }, {
                data: "tgl_pinjam1"
            }, {
                data: "ket"
            }, {
                data: "nm_pinjaman"
            }, {
                data: "tempo_bln"
            }, {
                data: "sudah_diangsur"
            }, {
                data: "sisa_bln"
            }, {
                data: "jml_pinjam",
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
                data: "posisi_akhir",
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
    }
}

get_pinjaman_belum_lunas();

function get_pinjaman_belum_lunas_cetak() {
    $fm_data = $("#fm_data").serialize();
    $fm_periode = $("#fm_periode").serialize();
    url_tabel = situs + "pelunasan/get_pinjaman_belum_lunas_cetak?" + $fm_data + "&" + $fm_periode;
    tabel_id = "tabel_belum_lunas_cetak";

    if ($.fn.DataTable.isDataTable("#" + tabel_id)) {
        $("#" + tabel_id).DataTable().ajax.url(url_tabel).load(function() {
            // $('#tabel_piutang').DataTable().responsive.recalc().responsive.rebuild();
        }, false);
    } else {
        $("#" + tabel_id).DataTable({
            scrollY: 350,
            scrollX: true,
            ordering: false,
            paging: false,
            searching: false,
            select: 'multiple',
            processing: true,
            serverSide: true,
            ajax: url_tabel,
            columns: [{
                data: "nomor",
                className: "text-right"
            }, {
                data: "no_pinjam"
            }, {
                data: "tgl_pinjam1"
            }, {
                data: "ket"
            }, {
                data: "nm_pinjaman"
            }, {
                data: "tempo_bln"
            }, {
                data: "sudah_diangsur"
            }, {
                data: "sisa_bln"
            }, {
                data: "jml_pinjam",
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
                data: "posisi_akhir",
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
    }
}

function pelunasan_dipercepat_ganda() {
    dataRows = $("#tabel_belum_lunas_cetak").DataTable().rows({ selected: true }).data();

    if (dataRows.length > 0) {
        konfirmasi = confirm("Anda Yakin?");

        if (konfirmasi) {
            dataAjax = {};

            for (i = 0; i < dataRows.length; i++) {
                dataAjax[i] = dataRows[i];
                dataAjax[i]['tgl_lunas'] = $("#tgl_lunas_cetak").val();
            }

            $.ajax({
                url: situs + "pelunasan/pelunasan_dipercepat_ganda",
                data: dataAjax,
                type: "post",
                dataType: "json",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_pinjaman_belum_lunas_cetak();
                    }
                }
            });
        }
    } else {
        alert('Pilih data di tabel');
    }
}

function open_pelunasan_dipercepat() {
    row = $("#tabel_belum_lunas").DataTable().row({
        selected: true
    }).data();

    if (row) {
        $("#myModal").modal('show');

        $('#tgl_lunas').val('');
        $('#tgl_lunas').on('change', function() {
            get_var_pelunasan_dipercepat(row);
        });

        form_data = get_form_array("fm_data");
        form_data['ket'] = row.ket;

        set_form("fm_pelunasan", form_data);

        get_var_pelunasan_dipercepat(row);

        proses();
    } else {
        alert('Pilih data di tabel');
    }
}

function get_var_pelunasan_dipercepat(data_var) {
    data_var['tgl_lunas'] = $('#tgl_lunas').val();

    $.ajax({
        url: situs + "pelunasan/get_var_pelunasan",
        data: data_var,
        type: 'post',
        success: function(data) {
            $("#div_pelunasan_dipercepat").html(data);
            no_proses();
        }
    });
}

function proses_pelunasan_dipercepat() {
    validasi = $("#fm_pelunasan").valid();

    if (validasi) {
        konfirmasi = confirm("Anda yakin data sudah benar?");

        if (konfirmasi) {
            data_ajax = {};
            data_ajax[0] = row;

            if (row.kd_pinjaman == '2' || row.kd_pinjaman == '4') {
                data_ajax[0]['jml_sisa_angsuran'] = $('#div_pelunasan_dipercepat #jml_sisa_angsuran').val();
                data_ajax[0]['persen_denda'] = $('#div_pelunasan_dipercepat #persen_denda').val();
                data_ajax[0]['jml_denda'] = $('#div_pelunasan_dipercepat #jml_denda').val();
                data_ajax[0]['persen_asuransi'] = $('#div_pelunasan_dipercepat #persen_asuransi').val();
                data_ajax[0]['jml_asuransi'] = $('#div_pelunasan_dipercepat #jml_asuransi').val();
                data_ajax[0]['jml_bayar'] = $('#div_pelunasan_dipercepat #jml_bayar').val();

            } else if (row.kd_pinjaman == '3') {
                data_ajax[0]['jml_pokok'] = $('#div_pelunasan_dipercepat #jml_pokok').val();
                data_ajax[0]['persen_denda'] = $('#div_pelunasan_dipercepat #persen_denda').val();
                data_ajax[0]['jml_denda'] = $('#div_pelunasan_dipercepat #jml_denda').val();
                data_ajax[0]['jml_bunga_1bulan'] = $('#div_pelunasan_dipercepat #jml_bunga_1bulan').val();
                data_ajax[0]['jml_bayar'] = $('#div_pelunasan_dipercepat #jml_bayar').val();
            }

            $.ajax({
                url: situs+"pelunasan/pelunasan_dipercepat_ganda",
                data: data_ajax,
                type: 'post',
                dataType: "json",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_pinjaman_belum_lunas();
                        $("#myModal").modal('hide');
                    }
                }
            });
        }
    }
}

function get_pinjaman_sudah_lunas() {
    // $fm_dt = "?jns_pelunasan=PINJAMAN";
    url_tabel = situs + "pelunasan/get_pinjaman_lunas";
    tabel_id = "tabel_sudah_lunas";

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
                data: "bukti_lunas"
            }, {
                data: "tgl_lunas"
            }, {
                data: "blth_angsuran"
            }, {
                data: "ket"
            }, {
                data: "no_ref_bukti"
            }, {
                data: "tgl_ref_bukti"
            }, {
                data: "nm_pinjaman"
            }, {
                data: "no_ang"
            }, {
                data: "no_peg"
            }, {
                data: "nm_ang"
            }, {
                data: "nm_prsh"
            }, {
                data: "tempo_bln"
            }, {
                data: "jml_pokok",
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

function batalkan_pelunasan_dipercepat() {
    row = $("#tabel_sudah_lunas").DataTable().row({
        selected: true
    }).data();

    if (row) {
        konfirmasi = confirm("Anda yakin hapus pelunasan ini?");

        if (konfirmasi) {
            $.ajax({
                url: situs + "pelunasan/hapus_pelunasan_dipercepat",
                data: row,
                type: "post",
                dataType: "json",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_pinjaman_sudah_lunas();
                    }
                }
            });
        }
    } else {
        alert('Pilih data di tabel');
    }
}

$("#fm_data_kredit #bulan, #fm_data_kredit #tahun").on('change', function() {
    var_tahun = $("#tahun").val();
    var_bulan = $("#bulan").val();

    var_tgl_akhir_bulan = new Date(var_tahun, var_bulan, 0).getDate();

    $("#tgl_akhir").val(var_tgl_akhir_bulan);
});

var ev_get_anggota_kredit = 1;

$("#fm_data_kredit #no_ang").focus().on("change", function() {
    if (ev_get_anggota_kredit == 0) {
        ev_get_anggota_kredit = 1;

        get_anggota_kredit();
        // get_pinjaman_belum_lunas();
    }
}).keydown(function(e) {
    if (e.which == 13) {
        if (ev_get_anggota_kredit == 0) {
            ev_get_anggota_kredit = 1;

            get_anggota_kredit();
            // get_pinjaman_belum_lunas();
        }
    } else {
        ev_get_anggota_kredit = 0;
    }
});

function get_anggota_kredit() {
    $no_ang = $("#fm_data_kredit #no_ang").val();

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

                    $("#fm_data_kredit #nm_ang").val(data_anggota[0].nm_ang);
                    $("#fm_data_kredit #no_peg").val(data_anggota[0].no_peg);
                    $("#fm_data_kredit #kd_prsh").val(data_anggota[0].kd_prsh);
                    $("#fm_data_kredit #nm_prsh").val(data_anggota[0].nm_prsh);
                    $("#fm_data_kredit #kd_dep").val(data_anggota[0].kd_dep);
                    $("#fm_data_kredit #nm_dep").val(data_anggota[0].nm_dep);
                    $("#fm_data_kredit #kd_bagian").val(data_anggota[0].kd_bagian);
                    $("#fm_data_kredit #nm_bagian").val(data_anggota[0].nm_bagian);

                    tampilkan_kredit();
                } else {
                    $("#fm_data_kredit #no_ang").val('');
                    pesan('Data tidak ditemukan');
                }
            }
        });
    }
}

function tampilkan_kredit() {
    validasi = $('#fm_data_kredit').valid();

    if (validasi) {
        data_form = $('#fm_data_kredit').serialize();

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

function cetak_perhitungan_blm_lunas() {
    if ($("#tgl_lunas").val() == "") {
        alert("Tanggal pelunasan harus diisi");
    } else {
        xdata_form = row;
        xdata_form['tgl_lunas'] = $("#tgl_lunas").val();
        xdata_form['ubah_margin'] = $('#ubah_margin').val();

        xdata_form_baru = {};
        xdata_form_baru[0] = xdata_form;

        data_form = base64_encode(JSON.stringify(xdata_form_baru));

        window.open(situs + "laporan/bukti_pelunasan/cetak_perhitungan_blm_lunas_baru?data=" + data_form);
    }
}

function cetak_slip1_blm_lunas() {
    if ($("#tgl_lunas").val() == "") {
        alert("Tanggal pelunasan harus diisi");
    } else {
        xdata_form = row;
        xdata_form['tgl_lunas'] = $("#tgl_lunas").val();
        xdata_form['ubah_margin'] = $('#ubah_margin').val();

        xdata_form_baru = {};
        xdata_form_baru[0] = xdata_form;

        data_form = base64_encode(JSON.stringify(xdata_form_baru));

        window.open(situs + "laporan/bukti_pelunasan/cetak_slip1_blm_lunas_baru?data=" + data_form);
    }
}

function cetak_slip2_blm_lunas() {
    if ($("#tgl_lunas").val() == "") {
        alert("Tanggal pelunasan harus diisi");
    } else {
        xdata_form = row;
        xdata_form['tgl_lunas'] = $("#tgl_lunas").val();
        xdata_form['ubah_margin'] = $('#ubah_margin').val();

        xdata_form_baru = {};
        xdata_form_baru[0] = xdata_form;

        data_form = base64_encode(JSON.stringify(xdata_form_baru));

        window.open(situs + "laporan/bukti_pelunasan/cetak_slip2_blm_lunas_baru?data=" + data_form);
    }
}

function cetak_perhitungan_blm_lunas_baru() {
    if ($("#tgl_lunas_cetak").val() == "") {
        alert("Tanggal pelunasan harus diisi");
    } else {
        xdata_form = $("#tabel_belum_lunas_cetak").DataTable().rows({ selected: true }).data();

        if (xdata_form.length > 5) {
            alert('data yang dipilih tidak boleh lebih dari 5');
            return false;
        }

        if (xdata_form.length > 0) {
            xdata_form_baru = {};

            for (i = 0; i < xdata_form.length; i++) {
                xdata_form_baru[i] = xdata_form[i];
                xdata_form_baru[i]['tgl_lunas'] = $("#tgl_lunas_cetak").val();
            }

            data_form = base64_encode(JSON.stringify(xdata_form_baru));

            window.open(situs + "laporan/bukti_pelunasan/cetak_perhitungan_blm_lunas_baru?data=" + data_form);
        } else {
            alert('Pilih data di tabel');
        }
    }
}

function cetak_slip1_blm_lunas_baru() {
    if ($("#tgl_lunas_cetak").val() == "") {
        alert("Tanggal pelunasan harus diisi");
    } else {
        xdata_form = $("#tabel_belum_lunas_cetak").DataTable().rows({ selected: true }).data();

        if (xdata_form.length > 5) {
            alert('data yang dipilih tidak boleh lebih dari 5');
            return false;
        }

        if (xdata_form.length > 0) {
            xdata_form_baru = {};

            for (i = 0; i < xdata_form.length; i++) {
                xdata_form_baru[i] = xdata_form[i];
                xdata_form_baru[i]['tgl_lunas'] = $("#tgl_lunas_cetak").val();
            }

            data_form = base64_encode(JSON.stringify(xdata_form_baru));

            window.open(situs + "laporan/bukti_pelunasan/cetak_slip1_blm_lunas_baru?data=" + data_form);
        } else {
            alert('Pilih data di tabel');
        }
    }
}

function cetak_slip2_blm_lunas_baru() {
    if ($("#tgl_lunas_cetak").val() == "") {
        alert("Tanggal pelunasan harus diisi");
    } else {
        xdata_form = $("#tabel_belum_lunas_cetak").DataTable().rows({ selected: true }).data();

        if (xdata_form.length > 5) {
            alert('data yang dipilih tidak boleh lebih dari 5');
            return false;
        }

        if (xdata_form.length > 0) {
            xdata_form_baru = {};

            for (i = 0; i < xdata_form.length; i++) {
                xdata_form_baru[i] = xdata_form[i];
                xdata_form_baru[i]['tgl_lunas'] = $("#tgl_lunas_cetak").val();
            }

            data_form = base64_encode(JSON.stringify(xdata_form_baru));

            window.open(situs + "laporan/bukti_pelunasan/cetak_slip2_blm_lunas_baru?data=" + data_form);

            // $('#fm_cetak_slip2 #data').val(data_form);

            // $('#fm_cetak_slip2').submit();
        } else {
            alert('Pilih data di tabel');
        }
    }
}

function cetak_perhitungan() {
    row = $("#tabel_sudah_lunas").DataTable().row({
        selected: true
    }).data();

    if (row) {
        data_form = base64_encode(JSON.stringify(row));

        window.open(situs + "laporan/bukti_pelunasan/cetak_perhitungan?data=" + data_form);
    } else {
        alert('Pilih data di tabel');
    }
}

function cetak_slip1() {
    row = $("#tabel_sudah_lunas").DataTable().row({
        selected: true
    }).data();

    if (row) {
        data_form = base64_encode(JSON.stringify(row));

        window.open(situs + "laporan/bukti_pelunasan/cetak_slip1?data=" + data_form);
    } else {
        alert('Pilih data di tabel');
    }
}

function cetak_slip2() {
    row = $("#tabel_sudah_lunas").DataTable().row({
        selected: true
    }).data();

    if (row) {
        data_form = base64_encode(JSON.stringify(row));

        window.open(situs + "laporan/bukti_pelunasan/cetak_slip2?data=" + data_form);
    } else {
        alert('Pilih data di tabel');
    }
}

function singkron_sisa_plafon() {
    validasi = $('#fm_data_kredit').valid();

    konfirmasi = confirm("Anda Yakin?");

    if (validasi && konfirmasi) {
        data_form = $('#fm_data_kredit').serialize();

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

var ev_get_anggota_kredit_potga = 1;

$("#fm_data_potga #no_ang").focus().on("change", function() {
    if (ev_get_anggota_kredit_potga == 0) {
        ev_get_anggota_kredit_potga = 1;

        get_anggota_potga();
        // get_pinjaman_belum_lunas();
    }
}).keydown(function(e) {
    if (e.which == 13) {
        if (ev_get_anggota_kredit_potga == 0) {
            ev_get_anggota_kredit_potga = 1;

            get_anggota_potga();
            // get_pinjaman_belum_lunas();
        }
    } else {
        ev_get_anggota_kredit_potga = 0;
    }
});

function get_anggota_potga() {
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

                $("#div_laporan_potga").html(data);
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
</script>