<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Proses extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        set_time_limit(0);
    }

    public function proses_sisa_plafon()
    {
        $this->load->model("anggota_model");

        if (date('d') == "01") {
            $tahun = date('Y');
            $bulan = date('m');

            $data['tahun'] = $tahun;
            $data['bulan'] = $bulan;

            $this->anggota_model->proses_sisa_plafon($data);
        }
    }
}
