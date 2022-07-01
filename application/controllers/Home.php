<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        // $this->load->model("pesan_model");
        // if ($this->session->userdata('username') != "") {
        //     if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
        //         bg_proses("node " . FCPATH . "node_modules/server.js");
        //     } else {
        //         bg_proses("");
        //     }
        // }
    }

    public function index()
    {
        $this->template->view();
    }

    public function kosong()
    {
        echo "halaman kosong";
    }

    public function nodata()
    {
        $array['recordsTotal']    = 0;
        $array['recordsFiltered'] = 0;
        $array['data']            = array();

        echo json_encode($array);
    }

    public function select_kosong()
    {
        $array_kosong = array("id" => "", "text" => "");

        return array($array_kosong);
    }

}
