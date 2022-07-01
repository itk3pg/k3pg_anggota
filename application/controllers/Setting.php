<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("setting_model");
    }

    public function index($page)
    {
        if ($page == "grup-user") {
            $data['judul_menu'] = "Setting Grup dan User";
            $this->template->view("setting/grup_user", $data);
        }
        if ($page == "session") {
            if (!in_array($this->session->userdata("username"), array("ADMIN"))) {
                exit("Anda tidak berhak mengakses menu ini");
            }

            $data_grup = $this->setting_model->get_grup()->result_array();
            $grup      = array();

            foreach ($data_grup as $key => $value) {
                $grup[$value['id_grup']] = $value['nm_grup'];
            }

            $data['judul_menu'] = "Setting Session";
            $data['grup']       = get_option_tag($grup);
            $data['session']    = json_encode($this->session->userdata());

            $this->template->view("setting/session", $data);
        }
        if ($page == "ttd-laporan") {
            $data['judul_menu'] = "Setting TTD Laporan";

            $data_ttd = $this->db->limit(1)->get("s_laporan");

            if ($data_ttd->num_rows() > 0) {
                $data = array_merge($data, $data_ttd->row_array(0));
            } else {
                $array_ttd = array("id" => "", "kabid_simpin" => "", "kaunit_simpin" => "", "kaunit_potga" => "", "manager_op4" => "", "manager_adm_keuangan" => "", "kabid_keuangan" => "");
                $data      = array_merge($data, $array_ttd);
            }

            $this->template->view("setting/ttd_laporan", $data);
        }
    }

    public function get_grup()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->setting_model->get_grup(1, $cari)->row(0)->numrows;
        $data_item    = $this->setting_model->get_grup("", $cari, "", $offset, $limit);

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

    public function add_grup()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->setting_model->insert_grup($data_post);

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

    public function edit_grup($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->setting_model->update_grup($data_post, $id);

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

    public function del_grup()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->setting_model->delete_grup($data_post);

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

    public function select_grup()
    {
        $data_req      = get_request();
        $value         = isset($data_req['value']) ? $data_req['value'] : "";
        $q             = isset($data_req['q']) ? $data_req['q'] : $value;
        $cari['value'] = $q;

        $data = $this->setting_model->get_grup("", $cari, "", 0, 100)->result_array();

        $arrData = array();

        foreach ($data as $key => $value) {
            $value['id']   = $value['id_grup'];
            $value['text'] = $value['nm_grup'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function get_user()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->setting_model->get_user(1, $cari)->row(0)->numrows;
        $data_item    = $this->setting_model->get_user("", $cari, "", $offset, $limit);

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

    public function add_user()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->setting_model->insert_user($data_post);

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

    public function edit_user($id)
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->setting_model->update_user($data_post, $id);

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

    public function del_user()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->setting_model->delete_user($data_post);

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

    public function select_user()
    {
        $data_req      = get_request();
        $value         = isset($data_req['value']) ? $data_req['value'] : "";
        $q             = isset($data_req['q']) ? $data_req['q'] : $value;
        $cari['value'] = $q;

        $data = $this->setting_model->get_user("", $cari, "", 0, 100)->result_array();

        echo json_encode($data);
    }

    public function setsession()
    {
        $data_req = get_request();

        $this->session->set_userdata($data_req);

        $output = "";

        foreach ($data_req as $key => $value) {
            $output .= $key . ": " . $value . "\n";
        }

        echo $output;
    }

    public function set_ttd_laporan()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $set_data = array(
                "id"                   => $data_post['id'],
                "kabid_simpin"         => $data_post['kabid_simpin'],
                "kaunit_simpin"        => $data_post['kaunit_simpin'],
                "kaunit_potga"         => $data_post['kaunit_potga'],
                "manager_op4"          => $data_post['manager_op4'],
                "manager_adm_keuangan" => $data_post['manager_adm_keuangan'],
                "kabid_keuangan"       => $data_post['kabid_keuangan'],
                "ketua_pengurus"       => $data_post['ketua_pengurus'],
            );

            $query_update = $this->db->set($set_data)->replace("s_laporan");

            $data_ttd = $this->db->limit(1)->get("s_laporan")->row_array(0);

            echo json_encode($data_ttd);
        }
    }

}
