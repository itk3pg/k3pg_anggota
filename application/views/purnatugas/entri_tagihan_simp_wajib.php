<div class="nav-tabs-custom">
    <ul class="nav nav-tabs navtab-bg" id="myTab">
        <li class="active">
            <a href="#input" class="input">Data Tagihan Simp Wajib</a>
        </li>
        <li>
            <a href="#view_kredit" class="view_kredit">Rincian Kredit Anggota</a>
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
                    <br>
                    <div class="row" id="div_input">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Periode Simp Wajib</label>
                                <div class="input-group">
                                    <select class="form-control" id="tempo_bln" name="tempo_bln" required="">
                                        <option value="">[PILIH]</option>
                                        <option value="1">1</option>
                                        <option value="3">3</option>
                                        <option value="6">6</option>
                                        <option value="12">12</option>
                                    </select>
                                    <div class="input-group-addon">Bulan</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Periode Awal Belum Terbayar</label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <select id="bulan_tagihan" name="bulan_tagihan" class="form-control" required="">
                                            <?php echo $bulan; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="tahun_tagihan" id="tahun_tagihan" class="form-control" placeholder="Tahun" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp</div>
                                    <input type="text" name="jumlah" id="jumlah" class="form-control number_format" required="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sampai Dengan</label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="text" name="bulan_angsuran" id="bulan_angsuran" class="form-control" placeholder="Bulan" readonly="">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="tahun_angsuran" id="tahun_angsuran" class="form-control" placeholder="Tahun" readonly="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary btn-small" onclick="simpan()"> <i class="fa fa-save"></i> Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-danger" onclick="hapus()"><i class="fa fa-trash"></i> Hapus</button>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-condensed table-striped table-hover nowrap" id="tabel_simp_wajib" width="100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Periode Awal</th>
                                    <th>Sampai Dengan (Periode Angsuran)</th>
                                    <th>Jangka</th>
                                    <th>Jumlah</th>
                                    <th>Status Bayar</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
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
    </div>
</div>
<script type="text/javascript">
$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("input")) {
        get_tagihan_simp_wajib();
    } else
    if ($(this).hasClass("view")) {}
});

$('#tab-bawah a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("tab_bawah_pelunasan")) {
        get_tagihan_simp_wajib();
    } else
    if ($(this).hasClass("tab_bawah_cetak")) {
        // get_pinjaman_belum_lunas_cetak();
    }
});

var ev_get_anggota = 1;

$("#fm_data #no_ang").focus().on("change", function() {
    if (ev_get_anggota == 0) {
        ev_get_anggota = 1;

        get_anggota();
    }
}).keydown(function(e) {
    if (e.which == 13) {
        if (ev_get_anggota == 0) {
            ev_get_anggota = 1;

            get_anggota();
        }
    } else {
        ev_get_anggota = 0;
    }
});

function get_anggota() {
    $no_ang = $("#fm_data #no_ang").val();

    if ($no_ang) {
        $.ajax({
            url: situs + 'anggota/select_anggota_noang_pensiun_aktif/0',
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

                get_tagihan_simp_wajib();
            }
        });
    }
}

/*function cek_saldo_simpanan_sukarela1() {
    $data_form = $("#fm_data").serialize();

    $.ajax({
        url: situs + "purnatugas/cek_saldo_simpanan_sukarela1",
        data: $data_form,
        type: 'post',
        success: function(data) {
            $("#saldo_akhir").val(data).trigger("change");
        }
    });
}*/

function proses_data_simp_wajib_baru() {
    $.ajax({
        url: situs+"purnatugas/proses_data_simp_wajib_baru",
        beforeSend: function() {
            proses();
        }, success: function(data) {
            no_proses();
        }
    });
}

proses_data_simp_wajib_baru();

function get_tagihan_simp_wajib() {
    $fm_data = $("#fm_data").serialize();
    $fm_periode = $("#fm_periode").serialize();
    url_tabel = situs + "purnatugas/get_tagihan_simp_wajib?" + $fm_data;
    tabel_id = "tabel_simp_wajib";

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
            searching: false,
            select: 'single',
            processing: true,
            serverSide: true,
            ajax: url_tabel,
            columns: [{
                data: "nomor",
                className: "text-right"
            }, {
                data: "blth_rilis"
            }, {
                data: "blth_angsuran"
            }, {
                data: "tempo_bln"
            }, {
                data: "jumlah",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "is_bayar",
                render: function(data) {
                    if (data == "0") {
                        return "Belum Dibayar";
                    } else {
                        return "Sudah Dibayar";
                    }
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

$("#fm_data #tempo_bln, #fm_data #bulan_tagihan, #fm_data #tahun_tagihan").on("change", function() {
    data_form = "tempo_bln=" + $("#fm_data #tempo_bln").val() + "&bulan_tagihan=" + $("#fm_data #bulan_tagihan").val() + "&tahun_tagihan=" + $("#fm_data #tahun_tagihan").val();

    $.ajax({
        url: situs + "purnatugas/get_akhir_periode_wajib",
        data: data_form,
        dataType: 'json',
        success: function(data) {
            $("#fm_data #bulan_angsuran").val(data.bulan_angsuran);
            $("#fm_data #tahun_angsuran").val(data.tahun_angsuran);
        }
    });
});

function simpan() {
    validasi = $("#fm_data").valid();

    if (validasi) {
        konfirmasi = confirm("Anda Yakin?");

        if (konfirmasi) {
            data_form = $("#fm_data").serialize();

            $.ajax({
                url: situs + "purnatugas/simpan_tagihan_simp_wajib",
                type: "post",
                data: data_form,
                dataType: 'json',
                beforeSend: function() {
                    proses();
                },
                success: function(data) {
                    pesan(data.msg, 1);
                    $("#div_input select, #div_input input").val('');

                    if (data.status) {
                        get_tagihan_simp_wajib();
                    }
                }
            });
        }
    } else {
        alert("Isi data yang diperlukan");
    }
}

function hapus() {
    row = $("#tabel_simp_wajib").DataTable().row({
        selected: true
    }).data();

    if (row) {
        if (row.is_bayar == "1") {
            alert("Tidak bisa dihapus, Tagihan ini sudah dibayar");
            return false;
        }

        konfirmasi = confirm("Anda yakin hapus data ini?");

        if (konfirmasi) {
            $.ajax({
                url: situs + "purnatugas/hapus_tagihan",
                data: row,
                type: "post",
                dataType: "json",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_tagihan_simp_wajib();
                    }
                }
            });
        }
    } else {
        alert('Pilih data di tabel');
    }
}

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
</script>