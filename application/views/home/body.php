<body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Top Bar Start -->
        <div class="topbar">
            <!-- LOGO -->
            <div class="topbar-left">
                <div class="text-center">
                    <a href="<?php echo base_url(); ?>" class="logo"><i class="md md-devices"></i> <span>K3PG</span></a>
                </div>
            </div>
            <!-- Button mobile view to collapse sidebar menu -->
            <div class="navbar navbar-default" role="navigation">
                <div class="container">
                    <div class="">
                        <div class="pull-left">
                            <button class="button-menu-mobile open-left">
                                <i class="fa fa-bars"></i>
                            </button>
                            <span class="clearfix"></span>
                        </div>
                        <div class="logo" style="display: inline-block;">
                            <?php echo $judul_web; ?>
                        </div>
                        <ul class="nav navbar-nav navbar-right pull-right">
                            <li class="dropdown">
                                <a href="" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="true"><i class="md md-face-unlock"></i> <?php echo $this->session->userdata("nama"); ?></a>
                                <ul class="dropdown-menu">
                                    <?php if ($this->session->userdata("nama") != "") {?>
                                    <li><a href="<?php echo site_url('login/keluar'); ?>"><i class="md md-settings-power"></i> Logout</a></li>
                                    <?php } else {?>
                                    <li><a href="javascript:void(0)">Belum Login</a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <!-- Top Bar End -->