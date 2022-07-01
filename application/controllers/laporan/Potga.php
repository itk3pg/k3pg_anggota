<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Potga extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("laporan_model");
    }

    public function index()
    {
        $arr_bulan = array_bulan();

        $data['judul_menu'] = "Laporan Potong Gaji";
        $data['bulan']      = get_option_tag($arr_bulan, "BULAN");

        $this->template->view("laporan/lap_potga", $data);
    }

    public function tampilkan()
    {
        $data_req = get_request();

        if ($data_req) {
            $lap = "<table class=\"table table-bordered table-condensed table-striped\" border=\"1\" style=\"white-space: nowrap;\">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tgl Masuk</th>
                            <th>No. Anggota</th>
                            <th>No. Pegawai</th>
                            <th>Nama</th>
                            <th>Perusahaan</th>
                            <th>Departemen</th>
                            <th>Bagian</th>
                        </tr>
                    </thead>
                    <tbody>";

            $data_anggota = $this->laporan_model->get_ang_masuk($data_req['tahun'], $data_req['bulan']);

            $no = 1;

            foreach ($data_anggota->result_array() as $key => $value) {
                $lap .= "
                        <tr>
                            <td>" . $no . "</td>
                            <td>" . $value['tgl_msk'] . "</td>
                            <td>" . $value['no_ang'] . "</td>
                            <td>" . $value['no_peg'] . "</td>
                            <td>" . $value['nm_ang'] . "</td>
                            <td>" . $value['nm_prsh'] . "</td>
                            <td>" . $value['nm_dep'] . "</td>
                            <td>" . $value['nm_bagian'] . "</td>
                        </tr>";

                $no++;
            }

            $lap .= "
                    </tbody>
                </table>";

            echo $lap;
        }
    }

    public function get_ang_masuk_xls()
    {
        $data_req = get_request();

        $file = "lap_ang_masuk_" . $data_req['bulan'] . "-" . $data_req['tahun'] . ".xls";

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file);

        $this->get_ang_masuk_html();
    }

}
