<div class="nav-tabs-custom">
    <ul class="nav nav-tabs navtab-bg" id="myTab">
        <li class="active">
            <a href="#ang_keluar" class="ang_keluar">Anggota Keluar</a>
        </li>
        <li>
            <a href="#view_data" class="view_data">View Data</a>
        </li>
        <li>
            <a href="#view_laporan" class="view_laporan">Laporan</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="ang_keluar">
            <form id="fm_form">
                <div class="panel-heading with-border">
                    <h4 class="panel-title">Data Anggota</h4>
                </div>
                <div class="panel-body">
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
                </div>
                <div class="panel-heading with-border">
                    <h4 class="panel-title">Keterangan Keluar</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Keluar</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" id="tgl_keluar" name="tgl_keluar" class="form-control datepicker" readonly="" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Keterangan Keluar</label>
                                <textarea id="ket_keluar" name="ket_keluar" class="form-control" style="text-transform: uppercase;" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jumlah Hak</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp</div>
                                    <input type="text" id="jml_hak" name="jml_hak" class="form-control number_format" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center" style="margin: 0px;text-align: center;">
                        <a href="javascript: void(0)" class="btn btn-default btn-small" onclick="batal()">Batal</a>
                        <a href="javascript: void(0)" class="btn btn-primary btn-small" onclick="simpan()">Simpan</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane" id="view_data">
            <div class="panel-heading with-border">
                <button class="btn btn-danger btn-small" onclick="hapus()">
                    <i class="fa fa-trash"></i> Hapus
                </button>
            </div>
            <div class="panel-body">
                <table id="tabel_anggota" class="table table-bordered table-condensed table-hover table-striped nowrap" style="width: 100%">
                    <thead>
                        <tr>
                            <th width="50">No.</th>
                            <th>Tgl. Keluar</th>
                            <th>NAK</th>
                            <th>No. Pegawai</th>
                            <th>Nama</th>
                            <th>Perusahaan</th>
                            <th>Departemen</th>
                            <th>Bagian</th>
                            <th>Keterangan</th>
                            <th>Jml. Hak</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="tab-pane" id="view_laporan">
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
                    <!-- <a class="btn btn-danger" onclick="cetak()">Cetak</a> -->
                </form>
            </div>
            <div class="panel-body" id="div_laporan">
                <h5>Ready!</h5>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
$("body").css("width", "unset");

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("ang_keluar")) {
        batal();
    }

    if ($(this).hasClass("view_data")) {
        get_anggota_keluar();
    }
});

$("#fm_form #no_ang").select2({
    ajax: {
        url: situs + 'anggota/select_anggota_by_noang/0',
        dataType: 'json',
        delay: 500
    }
}).on("select2:select", function(e) {
    s2data = e.params.data;

    $("#fm_form #nm_ang").val(s2data.nm_ang);
    $("#fm_form #no_peg").val(s2data.no_peg);
    $("#fm_form #kd_prsh").val(s2data.kd_prsh);
    $("#fm_form #nm_prsh").val(s2data.nm_prsh);
    $("#fm_form #kd_dep").val(s2data.kd_dep);
    $("#fm_form #nm_dep").val(s2data.nm_dep);
    $("#fm_form #kd_bagian").val(s2data.kd_bagian);
    $("#fm_form #nm_bagian").val(s2data.nm_bagian);
});

function simpan() {
    if ($('#fm_form').valid()) {
        konfirmasi = confirm('Anda yakin data sudah benar?');

        if (konfirmasi) {
            data_form = $('#fm_form').serialize();

            $.ajax({
                url: situs + "anggota/add_anggota_keluar/",
                data: data_form,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        $("#fm_form").valid();

                        batal();
                    }
                }
            });
        }
    }
}

function batal() {
    clear_form("fm_form");

    $("html").animate({ scrollTop: 0 }, 500);
    $("#status_anggota").html('');
}

$("html").animate({ scrollTop: 0 }, 500);
$("#status_anggota").html('');

function get_anggota_keluar() {
    url_tabel = situs + "anggota/get_anggota_keluar";
    tabel_id = "tabel_anggota";

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
                data: "tgl_keluar"
            }, {
                data: "no_ang"
            }, {
                data: "no_peg"
            }, {
                data: "nm_ang"
            }, {
                data: "nm_prsh"
            }, {
                data: "nm_dep"
            }, {
                data: "nm_bagian"
            }, {
                data: "ket_keluar"
            }, {
                data: "ket_keluar",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2);
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

function hapus() {
    row = $('#tabel_anggota').DataTable().row({
        selected: true
    }).data();

    if (row) {
        konfirmasi = confirm("Anda yakin?");

        if (konfirmasi) {
            data_input = row;

            $.ajax({
                url: situs + "anggota/hapus_anggota_keluar",
                data: data_input,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(data) {
                    pesan(data.msg, 1);

                    if (data.status) {
                        get_anggota_keluar();
                    }
                }
            });
        }
    } else {
        alert("Pilih data di tabel");
    }
}

function tampilkan() {
    validasi = $('#fm_data').valid();

    if (validasi) {
        data_form = $('#fm_data').serialize();

        $.ajax({
            url: situs + "laporan/anggota_keluar/tampilkan",
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

        window.open(situs + "laporan/anggota_keluar/excel?" + data_form);
    }
}
</script>