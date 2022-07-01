<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            <ul>
                <li>
                    <a href="<?php echo base_url(); ?>" class="waves-effect"><i class="md md-home"></i><span> Dashboard </span></a>
                </li>
                <?php if($this->session->userdata("username") != "" and !in_array($this->session->userdata("username"), array("201", "202", "203"))) { ?>
                <?php if($this->session->userdata("id_grup") == "1") { ?>
                <li class="has_sub">
                    <a href="javascript:void(0)" class="waves-effect"><i class="md md-settings"></i><span>Setting</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul>
                        <li>
                            <a href="<?php echo site_url('setting/index/grup-user'); ?>"><span>Grup & User</span></a>
                        </li>
                    </ul>
                </li>
                <?php } ?>
                <li class="has_sub">
                    <a href="javascript:void(0)" class="waves-effect"><i class="fa fa-table"></i><span>Master</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul>
                        <li>
                            <a href="<?php echo site_url('master/index/perusahaan'); ?>"><span>Perusahaan</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('master/index/departemen'); ?>"><span>Departemen</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('master/index/bagian'); ?>"><span>Bagian</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('master/index/simp-wajib'); ?>"><span>Simpanan Wajib</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('master/index/potga-ss1'); ?>"><span>Potga Simpanan Sukarela 1</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('master/index/potongan-bonus-pg'); ?>"><span>Potongan KKB/KPR Diluar Gaji</span></a>
                        </li>
                    </ul>
                </li>
                <li class="has_sub">
                    <a href="javascript:void(0)" class="waves-effect"><i class="fa fa-user"></i><span>Keanggotaan</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul>
                        <li>
                            <a href="<?php echo site_url('anggota/index/anggota-masuk'); ?>"><span>Anggota Masuk</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('anggota/index/anggota-keluar'); ?>"><span>Anggota Keluar</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('anggota/index/update-anggota'); ?>"><span>Update Data</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('anggota/index/upload-anggota-pkg'); ?>"><span>Upload Data Anggota Petrokimia</span></a>
                        </li>
                    </ul>
                </li>
                <li class="has_sub">
                    <a href="javascript:void(0)" class="waves-effect"><i class="fa fa-money"></i><span>Pelunasan & Potga</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul>
                        <li>
                            <a href="<?php echo site_url('pelunasan/index/pelunasan-dipercepat'); ?>"><span>Pelunasan Dipercepat</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('potga/index/proses-potga'); ?>"><span>Proses Potong Gaji</span></a>
                        </li>
                    </ul>
                </li>
                <!-- <li class="has_sub">
                    <a href="javascript:void(0)" class="waves-effect"><i class="fa fa-briefcase"></i><span>Anggota Purna Tugas</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul>
                        <li>
                            <a href="<?php echo site_url('purnatugas/index/entri-angsuran-baru'); ?>"><span>Entri Angsuran Baru</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('purnatugas/index/entri-tagihan-simp-wajib'); ?>"><span>Entri Tagihan Simp. Wajib</span></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('purnatugas/index/bayar-angsuran-purnatugas'); ?>"><span>Pembayaran Angsuran Anggota Purna Tugas</span></a>
                        </li>
                    </ul>
                </li> -->
                <li class="has_sub">
                    <a href="javascript:void(0)" class="waves-effect"><i class="md md-book"></i><span>Laporan</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul>
                        <li>
                            <a href="<?php echo site_url('setting/index/ttd-laporan'); ?>"><span>Setting TTD</span></a>
                        </li>
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><span>Keanggotaan</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul style="">
                                <li>
                                    <a href="<?php echo site_url('laporan/anggota_masuk'); ?>"><span>Anggota Masuk</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('laporan/anggota_keluar'); ?>"><span>Anggota Keluar</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('laporan/anggota_per_perusahaan'); ?>"><span>Anggota per Perusahaan</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><span>Potga</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul style="">
                                <li>
                                    <a href="<?php echo site_url('laporan/rekap_belanja'); ?>"><span>Rekapitulasi Belanja</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('laporan/rekap_potga'); ?>"><span>Rekapitulasi Potga</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('laporan/rincian_kredit_anggota'); ?>"><span>Rincian Kredit Anggota</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('pinjaman/index/view-kkbkpr'); ?>"><span>View Simulasi KKB/KPR</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php if($this->session->userdata("id_grup") == "1") { ?>
                <li class="has_sub">
                    <a href="javascript:void(0)" class="waves-effect"><i class="fa fa-refresh"></i><span>Proses</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul>
                        <li> <a href="<?php echo site_url('anggota/index/proses-sisa-plafon'); ?>"><span>Sisa Plafon</span></a> </li>
                    </ul>
                </li>
                <?php } ?>
                <?php } ?>
                <?php if($this->session->userdata("username") != "" and in_array($this->session->userdata("username"), array("201", "202", "203"))) { ?>
                <li>
                    <a href="<?php echo site_url('laporan/rincian_kredit_anggota'); ?>"><span>Rincian Kredit Anggota</span></a>
                </li>
                <?php } ?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- Left Sidebar End