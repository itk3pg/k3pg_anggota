<ul class="nav nav-tabs navtab-bg" id="myTab">
    <li class="active">
        <a href="#proses_margin_simpanan" class="proses_margin_simpanan">Upload Data</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="proses_margin_simpanan">
        <div class="panel-body">
            <form id="fm_upload_file" onsubmit="return false">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Upload Data</label>
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <label class="btn btn-info">
                                        <input type="file" name="file_upload" id="file_upload" style="display: none;">
                                        <i class="fa fa-file"></i> Pilih File
                                    </label>
                                </div>
                                <input type="text" name="file_name" id="file_name" class="form-control" readonly="">
                            </div>
                        </div>
                        <div class="form-group">
                            <a class="btn btn-primary" onclick="upload_file()"><i class="fa fa-upload"></i> Upload</a>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mohon Perhatian</label>
                            <p>Untuk melakukan upload data anggota PT. Petrokimia Gresik, gunakan format file excel yang telah disediakan di bawah ini</p>
                            <a class="btn btn-success" href="<?php echo base_url('aset/download/data_pkg.xls'); ?>"><i class="fa fa-download"></i> Download</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer" id="dv_status">
            <h5>Ready!</h5>
        </div>
    </div>
</div>
<script type="text/javascript">
var $intval_progress = null;

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');
});

$("#file_upload").on("change", function() {
    file_data = $(this).get(0).files[0].name;

    $('#file_name').val(file_data);
});

function upload_file() {
    jumlah_file = $("#file_upload").get(0).files.length;

    if (jumlah_file > 0) {
        konfirmasi = confirm("Anda yakin?");

        if (konfirmasi) {
            data_form = new FormData($('#fm_upload_file')[0]);

            $.ajax({
                url: situs + "anggota/upload_data_anggota_pkg",
                data: data_form,
                type: 'post',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    swal_progress();
                    init_proses_upload();
                },
                success: function(data) {
                    no_proses();
                    clearInterval($intval_progress);

                    $("#dv_status").html(data);
                    clear_form("fm_upload_file");
                }
            });
        }
    } else {
        alert('Pilih File');
    }
}

function init_proses_upload() {
    $.ajax({
        url: situs + "anggota/init_upload_data_anggota_pkg/",
        async: false,
        success: function() {
            $intval_progress = setInterval(function() {
                get_proses_upload();
            }, 1000);
        }
    });
}

function get_proses_upload() {
    $.ajax({
        url: situs + "anggota/get_upload_data_anggota_pkg/",
        cache: false,
        dataType: "json",
        timeout: 0,
        success: function(data) {
            $("#swal_pg").html("<b>" + data.persen + "% (" + data.data_now + "/" + data.data_total + ") </b>");
        }
    });
}
</script>