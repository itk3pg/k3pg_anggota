<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->login_model->is_login();
    }

    public function masuk()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $dataLogin = $this->login_model->masuk($data_post);

            if ($dataLogin) {
                echo "<script>
                    parent.window.open(parent.window.location.href, '_self');
                    </script>";
            } else {
                echo "<script>
                    parent.$('#username').focus();
                    parent.alert('Login Gagal');
                    </script>";
            }
        }
    }

    public function keluar()
    {
        session_destroy();

        redirect('');
    }

}
