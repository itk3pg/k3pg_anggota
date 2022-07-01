<ul class="nav nav-tabs navtab-bg" id="myTab">
    <li class="active">
        <a href="#proses_sisa_plafon" class="proses_sisa_plafon">Proses Sisa Plafon</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="proses_sisa_plafon">
        <div class="panel-body">
            <div class="alert alert-primary">
                <h4>Gunakan Form Ini Hanya jika proses otomatis tidak berjalan.</h4>
            </div>
            <form id="fm_proses_sisa_plafon" onsubmit="return false">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Periode</label>
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
                    </div>
                </div>
                <a class="btn btn-primary" onclick="proses_sisa_plafon()">Proses</a>
            </form>
        </div>
        <div class="panel-footer" id="dv_status">
            <h5>Ready!</h5>
        </div>
    </div>
</div>
<script type="text/javascript">
var $progress_timeout = null

$('#myTab a').click(function(e) {
    e.preventDefault();
    $(this).tab('show');
});

function proses_sisa_plafon() {
    konfirmasi = confirm("Anda yakin?");

    if (konfirmasi) {
        data_form = $("#fm_proses_sisa_plafon").serialize();

        $.ajax({
            url: situs + "anggota/proses_sisa_plafon/",
            data: data_form,
            type: "post",
            beforeSend: function() {
                swal_progress();
                init_proses_sisa_plafon();
            },
            success: function(data) {
                no_proses();
                clearInterval($progress_timeout);

                $("#dv_status").html(data);
            }
        });
    }
}

function init_proses_sisa_plafon() {
    $.ajax({
        url: situs + "anggota/init_progress_sisa_plafon/",
        async: false,
        success: function() {
            $progress_timeout = setInterval(function() {
                get_proses_sisa_plafon();
            }, 1000);
        }
    });
}

function get_proses_sisa_plafon() {
    $.ajax({
        url: situs + "anggota/get_progress_sisa_plafon/",
        cache: false,
        dataType: "json",
        timeout: 0,
        success: function(data) {
            $("#swal_pg").html("<b>" + data.persen + "% (" + data.data_now + "/" + data.data_total + ") </b>");
        }
    });
}
</script>