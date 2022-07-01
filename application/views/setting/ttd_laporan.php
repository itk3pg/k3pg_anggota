<div class="panel panel-default panel-color">
    <div class="panel-body">
        <form id="fm_input" onsubmit="return false">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>KA. Bid. Simpin</label>
                        <input type="text" name="kabid_simpin" id="kabid_simpin" class="form-control" required="" value="<?php echo $kabid_simpin; ?>">
                        <input type="hidden" name="id" id="id" class="form-control" required="" value="<?php echo $id; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>KA. Unit Simpin</label>
                        <input type="text" name="kaunit_simpin" id="kaunit_simpin" class="form-control" required="" value="<?php echo $kaunit_simpin; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Unit Potga</label>
                        <input type="text" name="kaunit_potga" id="kaunit_potga" class="form-control" required="" value="<?php echo $kaunit_potga; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Manager OP4</label>
                        <input type="text" name="manager_op4" id="manager_op4" class="form-control" required="" value="<?php echo $manager_op4; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Manager Adm & Keuangan</label>
                        <input type="text" name="manager_adm_keuangan" id="manager_adm_keuangan" class="form-control" required="" value="<?php echo $manager_adm_keuangan; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Kabid Keuangan</label>
                        <input type="text" name="kabid_keuangan" id="kabid_keuangan" class="form-control" required="" value="<?php echo $kabid_keuangan; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Ketua Pengurus</label>
                        <input type="text" name="ketua_pengurus" id="ketua_pengurus" class="form-control" required="" value="<?php echo $ketua_pengurus; ?>">
                        <input type="hidden" name="id" id="id" class="form-control" required="" value="<?php echo $id; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary" onclick="simpan_ttd()"> <i class="fa fa-save"></i> Simpan </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
function simpan_ttd() {
    var data_ajax = $("#fm_input").serialize();

    $.ajax({
        url: situs + "setting/set_ttd_laporan",
        data: data_ajax,
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            proses();
        },
        success: function(data) {
            set_form('fm_input', data);

            pesan('TTD berhasil disetting');
        }
    });
}
</script>