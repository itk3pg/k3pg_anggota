<!-- <div class="nav-tabs-custom"> -->
<ul class="nav nav-tabs navtab-bg" id="myTab">
    <li class="active">
        <a href="#pindah_prsh" class="pindah_prsh">Pindah Perusahaan</a>
    </li>
    <li>
        <a href="#view_data" class="view_data">View Data</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="pindah_prsh">
        <form id="fm_form">
            <div class="panel-heading with-border">
                <div class="panel-title">Data Anggota</div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>NAK</label>
                            <select id="no_ang" name="no_ang" class="form-control" required=""></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nama Anggota</label>
                            <input type="text" id="nm_ang" name="nm_ang" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>No. Pegawai</label>
                            <input type="text" id="no_peg" name="no_peg" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <fieldset>
                            <legend>Data Perusahaan Sekarang</legend>
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
                        </fieldset>
                    </div>
                    <div class="col-md-4">
                        <fieldset>
                            <legend>Pindah Ke Perusahaan</legend>
                            <div class="form-group">
                                <label>Perusahaan</label>
                                <select id="kd_prsh_baru" name="kd_prsh_baru" class="form-control" required=""></select>
                                <input type="hidden" id="nm_prsh_baru" name="nm_prsh_baru" />
                            </div>
                            <div class="form-group">
                                <label>Departemen</label>
                                <select id="kd_dep_baru" name="kd_dep_baru" class="form-control" required=""></select>
                                <input type="hidden" id="nm_dep_baru" name="nm_dep_baru" />
                            </div>
                            <div class="form-group">
                                <label>Bagian</label>
                                <select id="kd_bagian_baru" name="kd_bagian_baru" class="form-control" required=""></select>
                                <input type="hidden" id="nm_bagian_baru" name="nm_bagian_baru" />
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-4">
                        <fieldset>
                            <legend>Keterangan Pindah</legend>
                            <div class="form-group">
                                <label>Tanggal Pindah</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" id="tgl_pindah" name="tgl_pindah" class="form-control datepicker" required="" readonly="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <input type="text" name="ket_pindah" id="ket_pindah" class="form-control">
                            </div>
                            <div class="form-group text-center">
                                <a href="javascript: void(0)" class="btn btn-default btn-small" onclick="batal_pindah_anggota()">Batal</a>
                                <a href="javascript: void(0)" class="btn btn-primary btn-small" onclick="simpan_pindah_anggota()">Simpan</a>
                            </div>
                        </fieldset>
                    </div>
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
            <table id="tabel_anggota" class="table table-bordered table-condensed table-hover table-striped nowrap">
                <thead>
                    <tr>
                        <th width="50">No.</th>
                        <th>Tgl. Pindah</th>
                        <th>NAK</th>
                        <th>No. Pegawai</th>
                        <th>Nama</th>
                        <th>Perusahaan Lama</th>
                        <th>Departemen Lama</th>
                        <th>Bagian Lama</th>
                        <th>Perusahaan Baru</th>
                        <th>Departemen Baru</th>
                        <th>Bagian Baru</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- </div> -->
<script type="text/javascript">
$("body").css("width", "unset");

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("pindah_prsh")) {
        batal_pindah_anggota();
    }

    if ($(this).hasClass("view_data")) {
        get_anggota_pindah();
    }
});

$("#fm_form #no_ang").select2({
    ajax: {
        url: situs + 'anggota/select_anggota_by_noang',
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

$("#fm_form #kd_prsh_baru").select2({
    ajax: {
        url: situs + 'master/select_perusahaan',
        dataType: 'json',
        delay: 500
    }
}).on("select2:select", function(e) {
    if ($("#fm_form #kd_dep_baru").hasClass("select2-hidden-accessible")) {
        $("#fm_form #kd_dep_baru").val(null).trigger("change");
    }

    if ($("#fm_form #kd_bagian_baru").hasClass("select2-hidden-accessible")) {
        $("#fm_form #kd_bagian_baru").val(null).trigger("change");
    }

    var s2data = e.params.data;

    $("#fm_form #nm_prsh_baru").val(s2data.nm_prsh);

    $("#fm_form #kd_dep_baru").select2({
        ajax: {
            url: situs + 'master/select_departemen/' + s2data.kd_prsh,
            dataType: 'json',
            delay: 500
        }
    }).on("select2:select", function(e) {
        if ($("#fm_form #kd_bagian_baru").hasClass("select2-hidden-accessible")) {
            $("#fm_form #kd_bagian_baru").val(null).trigger("change");
        }

        var s2data1 = e.params.data;

        $("#fm_form #nm_dep_baru").val(s2data1.nm_dep);

        $("#fm_form #kd_bagian_baru").select2({
            ajax: {
                url: situs + 'master/select_bagian/' + s2data1.kd_prsh + "/" + s2data1.kd_dep,
                dataType: 'json',
                delay: 500
            }
        }).on("select2:select", function(e) {
            var s2data2 = e.params.data;

            $("#fm_form #nm_bagian_baru").val(s2data2.nm_bagian);
        });
    });
});

function simpan_pindah_anggota() {
    if ($('#fm_form').valid()) {
        konfirmasi = confirm('Anda yakin data sudah benar?');

        if (konfirmasi) {
            fm_data = $('#fm_form').serialize();
            fm_data += "&ket_pindah=" + $("#sts_pindah option:selected").text();

            $.ajax({
                url: situs + "anggota/add_anggota_pindah",
                data: fm_data,
                dataType: "json",
                type: "post",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        $("#fm_form").valid();

                        batal_pindah_anggota();
                    }
                }
            });
        }
    }
}

function batal_pindah_anggota() {
    clear_form("fm_form");

    $("#status_anggota").html('');
    $("html, body").animate({ scrollTop: 0 }, 500);
}

$("#status_anggota").html('');
$("html, body").animate({ scrollTop: 0 }, 500);

function get_anggota_pindah() {
    url_tabel = situs + "anggota/get_anggota_pindah";
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
                data: "tgl_pindah"
            }, {
                data: "no_ang"
            }, {
                data: "no_peg"
            }, {
                data: "nm_ang"
            }, {
                data: "nm_prsh_lama"
            }, {
                data: "nm_dep_lama"
            }, {
                data: "nm_bagian_lama"
            }, {
                data: "nm_prsh_baru"
            }, {
                data: "nm_dep_baru"
            }, {
                data: "nm_bagian_baru"
            }, {
                data: "ket_pindah"
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
                url: situs + "anggota/hapus_anggota_pindah",
                data: data_input,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(data) {
                    pesan(data.msg, 1);

                    if (data.status) {
                        get_anggota_pindah();
                    }
                }
            });
        }
    } else {
        alert("Pilih data di tabel");
    }
}
</script>