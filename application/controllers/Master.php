<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Master extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("master_model");
    }

    public function index($page)
    {
        $data_bulan = array_bulan();

        if ($page == "perusahaan") {
            $data['judul_menu'] = "Master Perusahaan";
            $this->template->view("master/master_perusahaan", $data);
        }
        if ($page == "departemen") {
            $data['judul_menu'] = "Master Departemen";
            $this->template->view("master/master_departemen", $data);
        }
        if ($page == "bagian") {
            $data['judul_menu'] = "Master Bagian";
            $this->template->view("master/master_bagian", $data);
        }
        if ($page == "potga-ss1") {
            $data['judul_menu'] = "Master Potongan Gaji Simpanan Sukarela 1";
            $data['bulan']      = get_option_tag($data_bulan, "BULAN");

            $this->template->view("master/master_potga_ss1", $data);
        }
        if ($page == "potongan-bonus-pg") {
            $data['judul_menu'] = "Master Jadwal Potongan KKB/KPR";
            $data['bulan']      = get_option_tag($data_bulan);

            $this->template->view("master/master_pot_bonus_pg", $data);
        }
        if ($page == "simp-wajib") {
            $data['judul_menu'] = "Master Simpanan Wajib";
            $data['bulan']      = get_option_tag($data_bulan);

            $this->template->view("master/master_pokok_wajib", $data);
        }
    }

    public function get_perusahaan()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->master_model->get_perusahaan(1, $cari)->row(0)->numrows;
        $data_item    = $this->master_model->get_perusahaan(0, $cari, "", $offset, $limit);

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

    public function add_perusahaan()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->insert_perusahaan($data_post);

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

    public function edit_perusahaan($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->update_perusahaan($data_post, $id);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diubah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diubah";
            }

            echo json_encode($hasil);
        }
    }

    public function del_perusahaan()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->delete_perusahaan($data_post);

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

    public function select_perusahaan()
    {
        $data_req = get_request();

        $value = isset($data_req['value']) ? $data_req['value'] : "";
        $q     = isset($data_req['q']) ? $data_req['q'] : $value;

        $cari['value'] = $q;

        $data = $this->master_model->get_perusahaan("", $cari, "nm_prsh", 0, 100)->result_array();

        $arrData = array();

        foreach ($data as $key => $value) {
            $value['id']   = $value['kd_prsh'];
            $value['text'] = $value['nm_prsh'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function select_perusahaan_plusAnper()
    {
        $data_req = get_request();

        $value = isset($data_req['value']) ? $data_req['value'] : "";
        $q     = isset($data_req['q']) ? $data_req['q'] : $value;

        $cari['value'] = $q;

        $data = $this->master_model->get_perusahaan("", $cari, "nm_prsh", 0, 100)->result_array();

        $arrData              = array();
        $arrData['results'][] = array("id" => "ANPER", "text" => "Semua Anak Perusahaan", "kd_prsh" => "ANPER", "nm_prsh" => "Semua Anak Perusahaan");

        foreach ($data as $key => $value) {
            $value['id']   = $value['kd_prsh'];
            $value['text'] = $value['nm_prsh'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function get_departemen()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->master_model->get_departemen(1, $cari)->row(0)->numrows;
        $data_item    = $this->master_model->get_departemen(0, $cari, "", $offset, $limit);

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

    public function add_departemen()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $cari['field'] = array("kd_dep");
            $cari['value'] = "D" . str_pad($data_post['kode_nomor'], 4, "0", STR_PAD_LEFT);

            $data_dep = $this->master_model->get_departemen(1, $cari, "", "", "", $data_post['kd_prsh']);

            if ($data_dep->row(0)->numrows > 0) {
                $hasil['status'] = false;
                $hasil['msg']    = "Kode sudah terdaftar";
                exit(json_encode($hasil));
            }

            $query = $this->master_model->insert_departemen($data_post);

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

    public function edit_departemen($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $cari['field'] = array("kd_dep");
            $cari['value'] = "D" . str_pad($data_post['kode_nomor'], 4, "0", STR_PAD_LEFT);

            $data_dep = $this->master_model->get_departemen(1, $cari, "", "", "", $data_post['kd_prsh']);

            if ($data_dep->row(0)->numrows > 0) {
                $hasil['status'] = false;
                $hasil['msg']    = "Kode sudah terdaftar";
                exit(json_encode($hasil));
            }

            $query = $this->master_model->update_departemen($data_post, $id);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diubah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diubah";
            }

            echo json_encode($hasil);
        }
    }

    public function del_departemen()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->delete_departemen($data_post);

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

    public function select_departemen($kd_prsh = "")
    {
        $data_req = get_request();

        $value = isset($data_req['value']) ? $data_req['value'] : "";
        $q     = isset($data_req['q']) ? $data_req['q'] : $value;

        $cari['value'] = $q;

        $data = $this->master_model->get_departemen("", $cari, "", 0, 100, $kd_prsh)->result_array();

        $arrData = array();

        foreach ($data as $key => $value) {
            $value['id']   = $value['kd_dep'];
            $value['text'] = $value['nm_dep'] . " | " . $value['nm_prsh'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function get_bagian()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->master_model->get_bagian(1, $cari)->row(0)->numrows;
        $data_item    = $this->master_model->get_bagian("", $cari, "", $offset, $limit);

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

    public function add_bagian()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $cari['field'] = array("kd_bagian");
            $cari['value'] = "B" . str_pad($data_post['kode_nomor'], 4, "0", STR_PAD_LEFT);

            $data_bagian = $this->master_model->get_bagian(1, $cari, "", "", "", $data_post['kd_prsh'], $data_post['kd_dep']);

            if ($data_bagian->row(0)->numrows > 0) {
                $hasil['status'] = false;
                $hasil['msg']    = "Kode sudah terdaftar";
                exit(json_encode($hasil));
            }

            $query = $this->master_model->insert_bagian($data_post);

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

    public function edit_bagian($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $cari['field'] = array("kd_bagian");
            $cari['value'] = "B" . str_pad($data_post['kode_nomor'], 4, "0", STR_PAD_LEFT);

            $data_bagian = $this->master_model->get_bagian(1, $cari, "", "", "", $data_post['kd_prsh'], $data_post['kd_dep']);

            if ($data_bagian->row(0)->numrows > 0) {
                $hasil['status'] = false;
                $hasil['msg']    = "Kode sudah terdaftar";
                exit(json_encode($hasil));
            }

            $query = $this->master_model->update_bagian($data_post, $id);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diubah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diubah";
            }

            echo json_encode($hasil);
        }
    }

    public function del_bagian()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->delete_bagian($data_post);

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

    public function select_bagian($kd_prsh = "", $kd_dep = "")
    {
        $data_req = get_request();

        $value = isset($data_req['value']) ? $data_req['value'] : "";
        $q     = isset($data_req['q']) ? $data_req['q'] : $value;

        $cari['value'] = $q;

        $data = $this->master_model->get_bagian("", $cari, "", 0, 100, $kd_prsh, $kd_dep)->result_array();

        $arrData = array();

        foreach ($data as $key => $value) {
            $value['id']   = $value['kd_bagian'];
            $value['text'] = $value['nm_bagian'] . " | " . $value['nm_dep'] . " | " . $value['nm_prsh'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function get_kelompok()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->master_model->get_kelompok(1, $cari)->row(0)->numrows;
        $data_item    = $this->master_model->get_kelompok("", $cari, "", $offset, $limit);

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

    public function add_kelompok()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->insert_kelompok($data_post);

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

    public function edit_kelompok($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->update_kelompok($data_post, $id);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diubah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diubah";
            }

            echo json_encode($hasil);
        }
    }

    public function del_kelompok()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->delete_kelompok($data_post);

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

    public function select_kelompok()
    {
        $data_req = get_request();

        $value = isset($data_req['value']) ? $data_req['value'] : "";
        $q     = isset($data_req['q']) ? $data_req['q'] : $value;

        $cari['value'] = $q;

        $data = $this->master_model->get_kelompok("", $cari, "", 0, 100)->result_array();

        $arrData = array();

        foreach ($data as $key => $value) {
            $value['id']   = $value['id_klp'];
            $value['text'] = $value['kd_klp'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function get_potga_ss1()
    {
        $data = get_request();

        $cari['field'] = array("no_ang");
        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->master_model->get_potga_ss1(1, $cari)->row(0)->numrows;
        $data_item    = $this->master_model->get_potga_ss1(0, $cari, "", $offset, $limit);

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

    public function add_potga_ss1()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->insert_potga_ss1($data_post);

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

    public function edit_potga_ss1($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->update_potga_ss1($data_post, $id);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diubah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diubah";
            }

            echo json_encode($hasil);
        }
    }

    public function del_potga_ss1()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->delete_potga_ss1($data_post);

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

    public function get_pot_bonus_pg()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->master_model->get_pot_bonus_pg(1, $cari, "", "", "", "0")->row(0)->numrows;
        $data_item    = $this->master_model->get_pot_bonus_pg(0, $cari, "", $offset, $limit, "0");

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

    public function add_pot_bonus_pg()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['is_jadwal_tetap'] = "0";

            $query = $this->master_model->insert_pot_bonus_pg($data_post);

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

    public function edit_pot_bonus_pg($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['is_jadwal_tetap'] = "0";

            $query = $this->master_model->update_pot_bonus_pg($data_post, $id);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diubah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diubah";
            }

            echo json_encode($hasil);
        }
    }

    public function del_pot_bonus_pg()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->delete_pot_bonus_pg($data_post);

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

    public function get_pot_bonus_tetap()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->master_model->get_pot_bonus_pg(1, $cari, "", "", "", "1")->row(0)->numrows;
        $data_item    = $this->master_model->get_pot_bonus_pg(0, $cari, "", $offset, $limit, "1");

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

    public function add_pot_bonus_tetap()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['is_jadwal_tetap'] = "1";
            $data_post['tahun']           = null;

            $query = $this->master_model->insert_pot_bonus_pg($data_post);

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

    public function edit_pot_bonus_tetap($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['is_jadwal_tetap'] = "1";
            $data_post['tahun']           = null;

            $query = $this->master_model->update_pot_bonus_pg($data_post, $id);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diubah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diubah";
            }

            echo json_encode($hasil);
        }
    }

    public function del_pot_bonus_tetap()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->delete_pot_bonus_pg($data_post);

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

    public function get_pokok_wajib()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->master_model->get_pokok_wajib(1, $cari)->row(0)->numrows;
        $data_item    = $this->master_model->get_pokok_wajib(0, $cari, "", $offset, $limit);

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

    public function add_pokok_wajib()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['tgl_berlaku'] = balik_tanggal($data_post['tgl_berlaku']);

            $query = $this->master_model->insert_pokok_wajib($data_post);

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

    public function edit_pokok_wajib($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['tgl_berlaku'] = balik_tanggal($data_post['tgl_berlaku']);

            $query = $this->master_model->update_pokok_wajib($data_post, $id);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diubah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diubah";
            }

            echo json_encode($hasil);
        }
    }

    public function del_pokok_wajib()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->master_model->delete_pokok_wajib($data_post);

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

}
