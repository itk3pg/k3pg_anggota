<div class="panel panel-color panel-default" style="max-width: 100%">
    <div class="panel-heading with-border">
        <button class="btn btn-warning btn-small" onclick="edit()">
            <i class="fa fa-pencil"></i> Edit</button>
        <button class="btn btn-danger btn-small" onclick="hapus()">
            <i class="fa fa-trash"></i> Hapus</button>
    </div>
    <div class="panel-body">
        <table id="tabel_anggota" class="table table-bordered table-condensed table-hover table-striped nowrap">
            <thead>
                <tr>
                    <th width="50">No.</th>
                    <th>NAK</th>
                    <th>No. Pegawai</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Kota Lahir</th>
                    <th>Tgl Lahir</th>
                    <th>Nama Ibu</th>
                    <th>Nama Pasangan</th>
                    <th>No. KTP</th>
                    <th>Alamat</th>
                    <th>Telp/HP</th>
                    <th>Kode Prsh</th>
                    <th>Perusahaan</th>
                    <th>Kode Dep</th>
                    <th>Departemen</th>
                    <th>Kode Bagian</th>
                    <th>Bagian</th>
                    <th>Gaji</th>
                    <th>Plafon</th>
                    <th>Tgl Masuk</th>
                    <th>Tgl Keluar</th>
                    <th>Ket Keluar</th>
                    <th>Last User Update</th>
                    <th>Last Update</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Data Anggota</h4>
            </div>
            <div class="modal-body">
                <form id="fm_modal" onsubmit="return false">
                    <div class="panel panel-border panel-info">
                        <div class="panel-heading with-border">
                            <h4 class="panel-title">Data Keanggotaan</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>NAK</label>
                                        <input type="text" id="no_ang" name="no_ang" class="form-control" readonly="" style="text-transform: uppercase;" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tanggal Masuk</label>
                                        <input type="text" id="tgl_msk" name="tgl_msk" class="form-control datepicker">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Gaji</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp</span>
                                            <input type="text" id="gaji" name="gaji" class="form-control number_format" value="0" required data-rule-number="true">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Status Meninggal</label>
                                                <select name="is_meninggal" id="is_meninggal" class="form-control">
                                                    <option value="0">Tidak</option>
                                                    <option value="1">Meninggal</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Status Pensiun</label>
                                                <select name="is_pensiun" id="is_pensiun" class="form-control">
                                                    <option value="0">Tidak</option>
                                                    <option value="1">Ya</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Tanggal Pensiun</label>
                                                <input type="text" name="tgl_pensiun" id="tgl_pensiun" class="form-control datepicker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Status Pensiun Aktif</label>
                                                <select name="is_pensiun_aktif" id="is_pensiun_aktif" class="form-control">
                                                    <option value="0">Tidak</option>
                                                    <option value="1">Ya</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Tanggal Pensiun Aktif</label>
                                                <input type="text" name="tgl_pensiun_aktif" id="tgl_pensiun_aktif" class="form-control datepicker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Jumlah Simpanan Pokok</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">Rp</div>
                                            <input type="text" id="jml_simp_pokok" name="jml_simp_pokok" class="form-control number_format" value="0" required="" data-rule-number="true">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tgl. Potga Simp. Pokok</label>
                                        <input type="text" id="tgl_potga_pokok" name="tgl_potga_pokok" class="form-control datepicker">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Status Blokir Plafon</label>
                                        <select class="form-control" id="is_blokir_plafon" name="is_blokir_plafon">
                                            <option value="0">Tidak Blokir</option>
                                            <option value="1">Blokir</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Keterangan Blokir</label>
                                        <input type="text" name="ket_blokir_plafon" id="ket_blokir_plafon" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Keterangan Kehilangan Buku</label>
                                        <input type="text" name="ket_buku_hilang" id="ket_buku_hilang" class="form-control">
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
                                            <select id="hari_lahir" name="hari_lahir" class="form-control">
                                                <?php echo $option_hari; ?>
                                            </select> -
                                            <select id="bulan_lahir" name="bulan_lahir" class="form-control">
                                                <?php echo $bulan; ?>
                                            </select> -
                                            <input type="text" name="tahun_lahir" id="tahun_lahir" class="form-control" data-rule-number="true" placeholder="Tahun" size="4">
                                        </div>
                                        <!-- <input type="text" id="tgl_lhr" name="tgl_lhr" class="form-control datepicker" required=""> -->
                                        <!-- </div> -->
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
                                        <textarea id="alm_rmh" name="alm_rmh" class="form-control" style="text-transform: uppercase"></textarea>
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
                                            <input type="text" id="nm_dep" name="nm_dep" class="form-control" required="" style="text-transform: uppercase;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Bagian</label>
                                        <div id="div_bagian">
                                            <input type="text" id="nm_bagian" name="nm_bagian" class="form-control" required="" style="text-transform: uppercase;" />
                                        </div>
                                    </div>
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
edit_mode = 0;
$("body").css("width", "unset");

$('#myModal').on('shown.bs.modal', function() {
    $("#fm_modal").valid();
    // $('#fm_modal #nm_ang').focus();
});

$("#fm_modal #kd_prsh").select2({
    ajax: {
        url: situs + 'master/select_perusahaan',
        dataType: 'json',
        delay: 500
    }
}).on("select2:select", function(e) {
    if ($("#fm_modal #kd_dep").hasClass("select2-hidden-accessible")) {
        $("#fm_modal #kd_dep").val(null).trigger("change");
    }

    if ($("#fm_modal #kd_bagian").hasClass("select2-hidden-accessible")) {
        $("#fm_modal #kd_bagian").val(null).trigger("change");
    }

    var s2data = e.params.data;

    $("#fm_modal #nm_prsh").val(s2data.nm_prsh);
});

$("#fm_modal #is_pensiun").on("change", function() {
    if ($("#fm_modal #is_pensiun").val() == "0") {
        $("#fm_modal #tgl_pensiun").val('');
    }
});

$("#fm_modal #is_pensiun_aktif").on("change", function() {
    if ($("#fm_modal #is_pensiun_aktif").val() == "0") {
        $("#fm_modal #tgl_pensiun_aktif").val('');
    }
});

function get_anggota() {
    url_tabel = situs + "anggota/get_anggota/0";
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
                data: "no_ang"
            }, {
                data: "no_peg"
            }, {
                data: "nm_ang"
            }, {
                data: "jns_kel",
                defaultContent: "",
                render: function(data) {
                    if (data == "L") {
                        return "Laki-laki";
                    } else if (data == "P") {
                        return "Perempuan";
                    }
                }
            }, {
                data: "kt_lhr"
            }, {
                data: "tgl_lhr"
            }, {
                data: "nm_ibukdg"
            }, {
                data: "nm_psg"
            }, {
                data: "no_ktp"
            }, {
                data: "alm_rmh"
            }, {
                data: "tlp_hp"
            }, {
                data: "kd_prsh"
            }, {
                data: "nm_prsh"
            }, {
                data: "kd_dep"
            }, {
                data: "nm_dep"
            }, {
                data: "kd_bagian"
            }, {
                data: "nm_bagian"
            }, {
                data: "gaji",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "plafon",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "tgl_msk"
            }, {
                data: "tgl_keluar"
            }, {
                data: "ket_keluar"
            }, {
                data: "user_edit"
            }, {
                data: "tgl_update"
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

setTimeout(function() {
    get_anggota();
}, 700);

function edit() {
    row = $('#tabel_anggota').DataTable().row({
        selected: true
    }).data();

    if (row) {
        dataUrl = situs + 'anggota/update_data_anggota/' + row.id_ang;
        edit_mode = 1;

        set_select2_value('#fm_modal #kd_prsh', row.kd_prsh, row.nm_prsh);
        set_form('fm_modal', row);
        $("#hari_lahir").val(row.hari_lahir);
        $("#bulan_lahir").val(row.bulan_lahir);

        $('#myModal').modal('show');

    } else {
        alert("Pilih data di tabel");
    }
}

function simpan() {
    validasi = $('#fm_modal').valid();

    if (($("#is_pensiun").val() == "1") && ($("#tgl_pensiun").val() == "")) {
        alert("Tanggal Pensiun harus diisi");
        return false;
    }

    if (($("#is_pensiun_aktif").val() == "1") && ($("#tgl_pensiun_aktif").val() == "")) {
        alert("Tanggal Pensiun Aktif harus diisi");
        return false;
    }

    if (validasi) {
        data_input = $('#fm_modal').serialize();

        $.ajax({
            url: dataUrl,
            data: data_input,
            dataType: "JSON",
            type: "POST",
            beforeSend: function() {
                proses();
            },
            success: function(data) {
                pesan(data.msg, 1);

                if (data.status) {
                    if (edit_mode) {
                        $('#myModal').modal('hide');
                    } else {
                        clear_form("#fm_modal");
                    }

                    get_anggota();
                }
            }
        });
    } else {
        alert("isi data yang diperlukan");
    }
}

function hapus() {
    row = $('#tabel_anggota').DataTable().row({
        selected: true
    }).data();

    if (row) {
        konfirmasi = confirm("Anda yakin?");

        if (konfirmasi) {
            $.ajax({
                url: situs + 'anggota/delete_data_anggota',
                data: row,
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    proses();
                },
                success: function(data) {
                    pesan(data.msg, 1);

                    if (data.status) {
                        get_anggota();
                    }
                }
            });
        }

    } else {
        alert("Pilih data di tabel");
    }
}
</script>