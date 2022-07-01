<ul class="nav nav-tabs navtab-bg" id="myTab">
    <li class="active">
        <a href="#proses_potga" class="proses_potga">Proses Potga</a>
    </li>
    <li>
        <a href="#view_data" class="view_data">View Data</a>
    </li>
    <li>
        <a href="#pot_per_nak" class="pot_per_nak">
            Potongan Per NAK
        </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="proses_potga">
        <div class="panel-body">
            <form id="fm_potga" onsubmit="return false">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Periode Tagihan/Angsuran</label>
                            <div class="row" style="margin: 0px;">
                                <div class="col-md-8" style="padding: 0px;">
                                    <select id="bulan" name="bulan" class="form-control">
                                        <?php echo $bulan; ?>
                                    </select>
                                </div>
                                <div class="col-md-4" style="padding: 0 0 0 5px;">
                                    <input type="text" name="tahun" id="tahun" class="form-control" value="<?php echo date('Y'); ?>" maxlength="4" size="4" placeholder="Tahun">
                                </div>
                            </div>
                        </div>
                        <a class="btn btn-primary" onclick="proses_potga()">Proses</a>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <div class="form-inline">
                                <label>Ambil Jadwal KKB/KPR : </label>
                                <br>
                                <button class="btn btn-primary" onclick="ambil_jadwal_kkbkpr()"><i class="fa fa-refresh"></i> Ambil Jadwal</button>
                            </div>
                            <br>
                            <label>Data Potongan Bonus/Insentif untuk pinjaman KKB dan KPR</label>
                            <div id="div_tabel_potongan"></div>
                            <small>* Anda dapat mengubah nilai yang ada ditabel, jika ada data yang tidak tampil, Anda bisa menambahkan data tsb di <a href="<?php echo site_url('master/index/potongan-bonus-pg'); ?>">master potongan hak diluar gaji</a></small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer" id="div_status">
            <h5>Ready!</h5>
        </div>
    </div>
    <div class="tab-pane" id="view_data">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-success btn-small" onclick="add()">
                        <i class="fa fa-plus"></i> Tambah</button>
                    <button class="btn btn-danger btn-small" onclick="del()">
                        <i class="fa fa-trash"></i> Hapus</button>
                </div>
                <div class="col-md-6 text-right">
                    <form id="fm_viewdata" class="form-inline" onsubmit="return false">
                        <select id="bulan" name="bulan" class="form-control">
                            <?php echo $bulan; ?>
                        </select>
                        <input type="text" name="tahun" id="tahun" class="form-control" value="<?php echo date('Y'); ?>" maxlength="4" size="4" placeholder="Tahun">
                        <button class="btn btn-info" onclick="get_potga()">Tampilkan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <table id="tabel_potga" class="table table-bordered table-condensed table-hover table-striped nowrap" width="100%">
                <thead>
                    <tr>
                        <th width="50">No.</th>
                        <th>Bukti</th>
                        <th>Tgl Potga</th>
                        <th>NAK</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Perusahaan</th>
                        <th>Departemen</th>
                        <th>Bagian</th>
                        <th>Jenis</th>
                        <th>No. Ref Bukti</th>
                        <th>Jumlah Pokok</th>
                        <th>Jangka (Bulan)</th>
                        <th>Angs. Ke</th>
                        <th>Jml Wajib</th>
                        <th>Jml Sukarela</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
            </table>
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Data Potong Gaji</h4>
            </div>
            <div class="modal-body">
                <form id="fm_modal" onsubmit="return false">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>NAK</label>
                                <input type="text" name="no_ang" id="no_ang" class="form-control" required="">
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
                                <input type="hidden" name="is_pensiun" id="is_pensiun">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="text" name="tgl_potga" id="tgl_potga" class="form-control datepicker" required="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jenis Potga</label>
                                <select id="kd_potga" name="kd_potga" class="form-control" required="">
                                    <option value="11">Simpanan Pokok</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp</div>
                                    <input type="text" name="jumlah" id="jumlah" class="form-control number_format" data-rule-number="true" required="">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpan()">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var $progress_timeout = null;

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');

    if ($(this).hasClass("proses_potga")) {
        get_jadwal_potongan_bonus();
    }
    if ($(this).hasClass("view_data")) {
        get_potga();
    }
});

$('#fm_potga #tahun, #fm_potga #bulan').on("change", function() {
    ambil_jadwal_kkbkpr();
})

function get_jadwal_potongan_bonus() {
    data_ajax = "tahun=" + $('#fm_potga #tahun').val() + "&bulan=" + $('#fm_potga #bulan').val();

    $.ajax({
        url: situs + "potga/get_jadwal_potongan_bonus",
        data: data_ajax,
        success: function(data) {
            $("#div_tabel_potongan").html(data);
        }
    });
}

function proses_potga() {
    konfirmasi = confirm("Anda yakin?");

    if (konfirmasi) {
        data_form = $("#fm_potga").serialize();

        $.ajax({
            url: situs + "potga/proses_potga",
            data: data_form,
            type: "post",
            beforeSend: function() {
                swal_progress();
                init_proses_potga();
            },
            success: function(data) {
                clearInterval($progress_timeout);
                no_proses();

                $("#div_status").html(data);
            }
        });
    }
}

function init_proses_potga() {
    $.ajax({
        url: situs + "potga/init_progress_potga",
        async: false,
        success: function() {
            $progress_timeout = setInterval(function() {
                get_proses_potga();
            }, 1000);
        }
    });
}

function get_proses_potga() {
    $.ajax({
        url: situs + "potga/get_progress_potga",
        cache: false,
        dataType: "json",
        timeout: 0,
        success: function(data) {
            $("#swal_pg").html("<b>" + data.persen + "% (" + data.data_now + "/" + data.data_total + ") </b>");
        }
    });
}

function get_potga() {
    url_tabel = situs + "potga/get_potga?" + $("#fm_viewdata").serialize();
    tabel_id = "tabel_potga";

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
                className: "text-right",
                width: "50px"
            }, {
                data: "bukti_potga"
            }, {
                data: "tgl_potga"
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
                data: "nm_potga"
            }, {
                data: "no_ref_bukti"
            }, {
                data: "jml_pokok",
                className: 'text-right',
                render: function(data) {
                    return number_format(data, 2);
                }
            }, {
                data: "tempo_bln",
                className: 'text-center'
            }, {
                data: "angs_ke",
                className: 'text-center'
            }, {
                data: "jml_wajib",
                className: 'text-right',
                render: function(data) {
                    return number_format(data, 2);
                }
            }, {
                data: "jml_sukarela",
                className: 'text-right',
                render: function(data) {
                    return number_format(data, 2);
                }
            }, {
                data: "jumlah",
                className: 'text-right',
                render: function(data) {
                    return number_format(data, 2);
                }
            }, {
                data: "ket"
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

function add() {
    edit_mode = 0;

    clear_form('fm_modal');
    $('#myModal').modal('show');
}

var ev_get_anggota = 1;

$("#fm_modal #no_ang").focus().on("change", function() {
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
    $no_ang = $("#fm_modal #no_ang").val();

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
                    data_nasabah = data.results;

                    $("#fm_modal #nm_ang").val(data_nasabah[0].nm_ang);
                    $("#fm_modal #no_peg").val(data_nasabah[0].no_peg);
                    $("#fm_modal #kd_prsh").val(data_nasabah[0].kd_prsh);
                    $("#fm_modal #nm_prsh").val(data_nasabah[0].nm_prsh);
                    $("#fm_modal #kd_dep").val(data_nasabah[0].kd_dep);
                    $("#fm_modal #nm_dep").val(data_nasabah[0].nm_dep);
                    $("#fm_modal #kd_bagian").val(data_nasabah[0].kd_bagian);
                    $("#fm_modal #nm_bagian").val(data_nasabah[0].nm_bagian);
                    $("#fm_modal #is_pensiun").val(data_nasabah[0].is_pensiun);
                } else {
                    $("#fm_modal #no_ang").val('');
                    pesan('Data tidak ditemukan');
                }
            }
        });
    }
}

function simpan() {
    validasi = $('#fm_modal').valid();

    if (validasi) {
        data_input = $('#fm_modal').serialize();

        $.ajax({
            url: situs + 'potga/add_data_potga',
            data: data_input,
            dataType: "JSON",
            type: "POST",
            beforeSend: function() {
                proses();
            },
            success: function(res) {
                pesan(res.msg, 1);

                if (res.status) {
                    if (edit_mode) {
                        $('#myModal').modal('hide');
                    } else {
                        clear_form("fm_modal");
                    }

                    get_potga();
                }
            }
        });
    }
}

function del() {
    row = $('#tabel_potga').DataTable().row({
        selected: true
    }).data();

    if (row) {
        prompt = confirm("Anda Yakin Ingin Menghapus Data Ini?");

        if (prompt) {
            $.ajax({
                url: situs + "potga/del_data_potga",
                data: row,
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    proses();
                },
                success: function(res) {
                    pesan(res.msg, 1);

                    if (res.status) {
                        get_potga();
                    }
                }
            });
        }
    } else {
        alert("Pilih data di tabel");
    }
}

function ambil_jadwal_kkbkpr() {
    v_data_ajax = "bulan=" + $("#bulan").val() + "&tahun=" + $("#tahun").val();

    $.ajax({
        url: situs + "potga/get_jadwal_kkbkpr",
        data: v_data_ajax,
        type: 'post',
        success: function(data) {
            // $("#status_jadwal").html('data jadwal ');

            get_jadwal_potongan_bonus();
        }
    });
}

ambil_jadwal_kkbkpr();

var ev_get_anggota_kredit = 1;

$("#fm_data_potga #no_ang").focus().on("change", function() {
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
</script>