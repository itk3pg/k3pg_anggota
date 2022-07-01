<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pinjaman extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("pinjaman_model");
        $this->load->model("master_model");
    }

    public function index($page)
    {
        $data_tempo_reguler = $this->pinjaman_model->get_tempo_bln_reguler();
        $data_tempo_kkb     = $this->pinjaman_model->get_tempo_bln_kkb();
        $data_tempo_kpr     = $this->pinjaman_model->get_tempo_bln_kpr();
        $bulan              = get_option_tag(array_bulan(), "BULAN");

        if ($page == "view-kkbkpr") {
            $data['judul_menu'] = "Simulasi Angsuran KKB/KPR";
            $data['tempo_bln']  = get_option_tag($data_tempo_reguler);

            $this->template->view("pinjaman/view_kkbkpr", $data);
        }
    }

    public function get_pinjaman_kkbkpr()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->pinjaman_model->get_pinjaman(1, $cari, "", "", "", array("2", "4"))->row(0)->numrows;
        $data_item    = $this->pinjaman_model->get_pinjaman(0, $cari, "tgl_pinjam1 desc", $offset, $limit, array("2", "4"));

        $data_set = array();

        foreach ($data_item->result_array() as $value) {
            $offset++;
            $value['nomor'] = $offset;
            $data_set[]     = $value;
        }

        $array['recordsTotal']    = $data_numrows;
        $array['recordsFiltered'] = $array['recordsTotal'];
        $array['data']            = $data_set;

        echo json_encode($array);
    }
}
