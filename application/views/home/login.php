<body>
    <div class="wrapper-page">
        <div class="panel panel-border panel-border panel-primary panel-pages">
            <div class="panel-heading bg-img">
                <div class="bg-overlay"></div>
                <h3 class="text-center m-t-10 text-white">Monggo..Login..</h3>
            </div>
            <div class="panel-body">
                <form id="form_login" class="form-horizontal m-t-20" method="post" action="<?php echo site_url('login/masuk'); ?>" target="frame_login">
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control input-lg " type="text" required="" placeholder="Username" id="username" name="username" autocomplete="off" style="text-transform: uppercase;">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control input-lg" type="password" required="" placeholder="Password" id="password" name="password">
                        </div>
                    </div>
                    <div class="form-group text-center m-t-40">
                        <div class="col-xs-12">
                            <button class="btn btn-primary btn-lg w-lg waves-effect waves-light" type="submit">Log In</button>
                        </div>
                    </div>
                    <div class="form-group text-center m-t-30 m-b-0">
                        <div class="col-xs-12">
                            <h6>PUSLAHDA K3PG &copy; 2020 | <a href="<?php echo base_url(); ?>"><?php echo $judul_web; ?></a></h6>
                        </div>
                    </div>
                </form>
                <iframe id="frame_login" name="frame_login" style="display: none;"></iframe>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        $("#username").focus();

        $("#form_login").on("submit", function() {
            varUsername = $("#username").val();
            varPassword = $("#password").val();

            if (!varUsername) {
                $("#username").focus();

                return false;
            }

            if (!varPassword) {
                $("#password").focus();

                return false;
            }
        });
    });
    </script>
</body>