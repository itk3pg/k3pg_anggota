<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purnatugas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("simpanan_model");
        $this->load->model("potga_model");
        $this->load->model("master_model");
    }

    public function index($page)
    {
        if ($page == "entri-angsuran-baru") {
            $data['judul_menu'] = "Entri Angsuran Baru";
            // $data['tempo_bln']  = get_option_tag($data_tempo_reguler);

            // $this->template->view("purnatugas/entri_angsuran_baru", $data);
        }
        if ($page == "entri-tagihan-simp-wajib") {
            $data['judul_menu'] = "Entri Tagihan Simpanan Wajib";
            $data['bulan']      = get_option_tag(array_bulan());

            // $this->template->view("purnatugas/entri_tagihan_simp_wajib", $data);
        }
        if ($page == "bayar-angsuran-purnatugas") {
            $data['judul_menu'] = "Pembayaran Angsuran Anggota Purna Tugas";

            // $this->template->view("purnatugas/pelunasan_purnatugas", $data);
        }
    }

    public function cek_saldo_simpanan_sukarela1()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['tgl_simpan']      = date("Y-m-d");
            $data_post['kd_jns_simpanan'] = "3000";

            echo $this->simpanan_model->cek_saldo_simpanan($data_post);
        }
    }

    public function get_tagihan_belum_lunas()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $no_ang = (isset($data['no_ang']) and $data['no_ang'] != "") ? $data['no_ang'] : "xxx";

        $data_numrows = $this->potga_model->get_potga_pensiun(1, $cari, "", "", "", "", "", $no_ang, "BELUM")->row(0)->numrows;
        $data_item    = $this->potga_model->get_potga_pensiun(0, $cari, "", $offset, $limit, "", "", $no_ang, "BELUM");

        $array['recordsTotal']    = $data_numrows;
        $array['recordsFiltered'] = $array['recordsTotal'];
        $array['data']            = $data_item->result_array();

        echo json_encode($array);
    }

    public function get_tagihan_simp_wajib()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $no_ang = (isset($data['no_ang']) and $data['no_ang'] != "") ? $data['no_ang'] : "xxx";

        $data_numrows = $this->potga_model->get_potga_pensiun(1, $cari, "", "", "", "", "", $no_ang, "", "1")->row(0)->numrows;
        $data_item    = $this->potga_model->get_potga_pensiun(0, $cari, "", $offset, $limit, "", "", $no_ang, "", "1");

        $array['recordsTotal']    = $data_numrows;
        $array['recordsFiltered'] = $array['recordsTotal'];
        $array['data']            = $data_item->result_array();

        echo json_encode($array);
    }

    public function get_akhir_periode_wajib()
    {
        $data_request = get_request();

        if (isset($data_request['tempo_bln']) and isset($data_request['bulan_tagihan']) and isset($data_request['tahun_tagihan'])) {
            $bulan_tagihan = $data_request['bulan_tagihan'];
            $tahun_tagihan = $data_request['tahun_tagihan'];
            $termin_wajib  = ($data_request['tempo_bln'] - 1);

            $strAngsuran    = mktime(0, 0, 0, $bulan_tagihan + $termin_wajib, 1, $tahun_tagihan);
            $bulan_angsuran = date("m", $strAngsuran);
            $tahun_angsuran = date("Y", $strAngsuran);

            $hasil['bulan_angsuran'] = $bulan_angsuran;
            $hasil['tahun_angsuran'] = $tahun_angsuran;

            echo json_encode($hasil);
        }
    }

    public function simpan_tagihan_simp_wajib()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $query = $this->potga_model->simpanTagihanSimpWajib($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Disimpan";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Disimpan";
            }

            echo json_encode($hasil);
        }
    }

    public function hapus_tagihan()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $query = $this->potga_model->hapusTagihanSimpWajib($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Dihapus";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Dihapus";
            }

            echo json_encode($hasil);
        }
    }

    public function get_transaksi()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];
        $no_ang        = (isset($data['no_ang']) and $data['no_ang'] != "") ? $data['no_ang'] : "xxx";

        $data_numrows = $this->potga_model->get_transaksi(1, $cari, "", "", "", $no_ang)->row(0)->numrows;
        $data_item    = $this->potga_model->get_transaksi(0, $cari, "", $offset, $limit, $no_ang);

        // $data_set = array();

        // foreach ($data_item->result_array() as $value) {
        //     $offset++;
        //     $value['nomor'] = $offset;
        //     $data_set[]     = $value;
        // }

        $array['recordsTotal']    = $data_numrows;
        $array['recordsFiltered'] = $array['recordsTotal'];
        $array['data']            = $data_item->result_array();

        echo json_encode($array);
    }

    public function hitung_angsuran_bp()
    {
        $data = get_request();

        // baca_array($data);
        // exit();

        $tgl_berlaku     = balik_tanggal($data['tgl_trans']);
        $jml_awal_trans  = $data['jml_awal_trans'] != "" ? hapus_koma($data['jml_awal_trans']) : 0;
        $jml_uang_muka   = (isset($data['jml_uang_muka']) and $data['jml_uang_muka'] != "") ? hapus_koma($data['jml_uang_muka']) : 0;
        $jml_biaya_admin = (isset($data['jml_biaya_admin']) and $data['jml_biaya_admin'] != "") ? hapus_koma($data['jml_biaya_admin']) : 0;
        $tempo_bln       = $data['tempo_bln'] != "" ? $data['tempo_bln'] : 0;
        $ubah_margin     = isset($data['ubah_margin']) ? true : false;

        $margin = $this->potga_model->get_margin_trans_berlaku($tempo_bln, $tgl_berlaku);

        if ($ubah_margin) {
            $margin = $data['margin'];
        }

        $jml_trans = $jml_awal_trans - $jml_uang_muka;

        $jml_margin = $jml_trans * ($margin / 100);

        @$angsuran = round(($jml_trans + $jml_biaya_admin + $jml_margin) / $data['tempo_bln']);

        $arrayHasil = array(
            "margin"     => $margin,
            "jml_margin" => $jml_margin,
            "jml_trans"  => ($jml_trans + $jml_biaya_admin),
            "angsuran"   => $angsuran,
        );

        echo json_encode($arrayHasil);
    }

    public function add_transaksi()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $data_post['tgl_trans'] = balik_tanggal($data_post['tgl_trans']);

            $query = $this->potga_model->insert_bridging_plafon($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Ditambah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Ditambah";
            }

            echo json_encode($hasil);
        }
    }

    public function hapus_transaksi()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $query = $this->potga_model->delete_bridging_plafon($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Dihapus";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Dihapus";
            }

            echo json_encode($hasil);
        }
    }

    public function proses_data_simp_wajib_baru()
    {
        $this->potga_model->proses_simp_wajib_purnatugas_baru();
    }

}
