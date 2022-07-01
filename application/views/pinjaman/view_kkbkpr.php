<div class="panel panel-border panel-default">
    <div class="panel-body">
        <button class="btn btn-info btn-small" onclick="cetak_pinjaman_terealisasi('tabel_sudah_realisasi')">
            <i class="fa fa-print"></i> Cetak</button>
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-condensed table-striped table-hover nowrap" id="tabel_sudah_realisasi" width="100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tgl Pinjam</th>
                    <th>Jenis Pinjaman</th>
                    <th>NAK</th>
                    <th>No. Pegawai</th>
                    <th>Nama</th>
                    <th>Kode Prsh</th>
                    <th>Perusahaan</th>
                    <th>Kode Dep</th>
                    <th>Departemen</th>
                    <th>Jml Pinjam</th>
                    <th>Tempo Bln</th>
                    <th>Jml Biaya Admin</th>
                    <th>Jml Margin</th>
                    <th>Jml Angsuran</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Perusahaan</h4>
            </div>
            <div class="modal-body">
                <form id="fm_modal" onsubmit="return false">
                    <div class="form-group">
                        <label>Kode Perusahaan</label>
                        <input type="text" id="kd_prsh" name="kd_prsh" class="form-control" readonly="" style="text-transform: uppercase;">
                    </div>
                    <div class="form-group">
                        <label>Nama Perusahaan</label>
                        <input type="text" id="nm_prsh" name="nm_prsh" class="form-control" required="" style="text-transform: uppercase;">
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

function get_pinjaman_sudah_realisasi() {
    url_tabel = situs + "pinjaman/get_pinjaman_kkbkpr";
    tabel_id = "tabel_sudah_realisasi";

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
                data: "tgl_pinjam"
            }, {
                data: "nm_pinjaman"
            }, {
                data: "no_ang"
            }, {
                data: "no_peg"
            }, {
                data: "nm_ang"
            }, {
                data: "kd_prsh"
            }, {
                data: "nm_prsh"
            }, {
                data: "kd_dep"
            }, {
                data: "nm_dep"
            }, {
                data: "jml_pinjam",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "tempo_bln",
                className: "text-center"
            }, {
                data: "jml_biaya_admin",
                className: "text-right",
                render: function(data) {
                    return number_format(data, 2, '.', ',');
                }
            }, {
                data: "jml_margin",
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

setTimeout(function() {
    get_pinjaman_sudah_realisasi();
}, 700);

function cetak_pinjaman_terealisasi($tabel_id) {
    row = $("#" + $tabel_id).DataTable().row({
        selected: true
    }).data();

    if (row) {
        window.open(situs + "cetak/cetak_pinjaman_sudah_realisasi/" + row.no_pinjam);
    } else {
        alert('Pilih data di tabel');
    }
}
</script>