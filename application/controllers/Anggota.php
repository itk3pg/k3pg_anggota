<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Anggota extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("anggota_model");
    }

    public function index($page)
    {
        $option_hari = "";

        for ($i = 1; $i <= 31; $i++) {
            $option_hari .= "<option value=\"" . $i . "\">" . $i . "</option>";
        }

        $option_bulan = get_option_tag(array_bulan(), "BULAN");

        if ($page == "anggota-masuk") {
            $data['judul_menu'] = "Entri Anggota Masuk";
            $data['hari_lahir'] = $option_hari;
            $data['bulan']      = $option_bulan;

            $this->template->view("anggota/entri_anggota_masuk", $data);
        }
        if ($page == "anggota-pindah") {
            $data['judul_menu'] = "Entri Anggota Pindah";
            $this->template->view("anggota/entri_anggota_pindah", $data);
        }
        if ($page == "anggota-keluar") {
            $data['judul_menu'] = "Entri Anggota Keluar";
            $data['bulan']      = $option_bulan;

            $this->template->view("anggota/entri_anggota_keluar", $data);
        }
        if ($page == "update-anggota") {
            $data['judul_menu']  = "Update Data Anggota";
            $data['option_hari'] = $option_hari;
            $data['bulan']       = $option_bulan;

            $this->template->view("anggota/update_data_anggota", $data);
        }
        if ($page == "upload-anggota-pkg") {
            $data['judul_menu'] = "Upload Data Anggota Petrokimia";
            $this->template->view("anggota/upload_anggota_pkg", $data);
        }
        if ($page == "proses-sisa-plafon") {
            $data['judul_menu'] = "Proses Sisa Plafon";
            $data['bulan']      = $option_bulan;
            $this->template->view("proses/proses_sisa_plafon", $data);
        }
    }

    public function get_nak_baru()
    {
        $data_nak = $this->db->select("ifnull(max(no_ang), 0)+1 nak")
            ->where("no_ang regexp '^[0-9]+$'")
            ->get("t_anggota");

        echo $data_nak->row()->nak;
    }

    public function add_anggota_masuk()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['tgl_msk']         = balik_tanggal($data_post['tgl_msk']);
            $data_post['tgl_potga_pokok'] = balik_tanggal($data_post['tgl_potga_pokok']);

            $ada_anggota = $this->db->where("no_ang", $data_post['no_ang'])->get("t_anggota")->num_rows();

            if ($ada_anggota > 0) {
                $hasil['status']  = false;
                $hasil['msg']     = "No. Anggota sudah terdaftar";
                $hasil['get_nak'] = "1";
                exit(json_encode($hasil));
            }

            $query = $this->anggota_model->insert_anggota_masuk($data_post);

            if ($query) {
                $hasil['status']  = true;
                $hasil['msg']     = "Data Berhasil Ditambah";
                $hasil['get_nak'] = "1";
            } else {
                $hasil['status']  = false;
                $hasil['msg']     = "Data Gagal Ditambah";
                $hasil['get_nak'] = "1";
            }

            echo json_encode($hasil);
        }
    }

    public function select_anggota_by_noang($status_anggota = "")
    {
        $data_req = get_request();

        $value = isset($data_req['value']) ? $data_req['value'] : "";
        $q     = isset($data_req['q']) ? $data_req['q'] : $value;

        $cari['value'] = $q;
        $cari['field'] = array("no_ang");

        $data = $this->anggota_model->get_anggota("", "", "", 0, 50, $status_anggota, $q)->result_array();

        $arrData = array();

        foreach ($data as $key => $value) {
            $value['id']   = $value['no_ang'];
            $value['text'] = $value['no_ang'] . " | " . $value['no_peg'] . " | " . $value['nm_ang'] . " | " . $value['nm_prsh'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function select_anggota_noang($status_anggota = "")
    {
        $data_req = get_request();

        $value = isset($data_req['value']) ? $data_req['value'] : "";
        $q     = isset($data_req['q']) ? $data_req['q'] : $value;

        $cari['value'] = $q;
        $cari['field'] = array("no_ang");

        $data = $this->anggota_model->get_anggota("", "", "", 0, 50, $status_anggota, $q)->result_array();

        $arrData = array();

        foreach ($data as $key => $value) {
            $value['id']   = $value['no_ang'];
            $value['text'] = $value['no_ang'] . " | " . $value['no_peg'] . " | " . $value['nm_ang'] . " | " . $value['nm_prsh'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function select_anggota_noang_pensiun_aktif($status_anggota = "")
    {
        $data_req = get_request();

        $value = isset($data_req['value']) ? $data_req['value'] : "";
        $q     = isset($data_req['q']) ? $data_req['q'] : $value;

        $cari['value'] = $q;
        $cari['field'] = array("no_ang");

        $data = $this->anggota_model->get_anggota("", "", "", 0, 50, $status_anggota, $q, 1, "1", "1")->result_array();

        $arrData = array();

        foreach ($data as $key => $value) {
            $value['id']   = $value['no_ang'];
            $value['text'] = $value['no_ang'] . " | " . $value['no_peg'] . " | " . $value['nm_ang'] . " | " . $value['nm_prsh'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function select_anggota_by_nopeg($status_anggota = "")
    {
        $data_req = get_request();

        $value = isset($data_req['value']) ? $data_req['value'] : "";
        $q     = isset($data_req['q']) ? $data_req['q'] : $value;

        $cari['value'] = $q;
        $cari['field'] = array("no_ang", "no_peg", "nm_ang");

        $data = $this->anggota_model->get_anggota("", $cari, "", 0, 50, $status_anggota)->result_array();

        $arrData = array();

        foreach ($data as $key => $value) {
            $value['id']   = $value['no_peg'];
            $value['text'] = $value['no_ang'] . " | " . $value['no_peg'] . " | " . $value['nm_ang'] . " | " . $value['nm_prsh'];

            $arrData['results'][] = $value;
        }

        echo json_encode($arrData);
    }

    public function add_anggota_pindah()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['tgl_pindah'] = balik_tanggal($data_post['tgl_pindah']);

            if (!cek_tanggal_entri($data_post['tgl_pindah'])) {
                $hasil['status'] = false;
                $hasil['msg']    = "Tanggal tidak boleh bulan lalu";
            } else {
                $query = $this->anggota_model->insert_anggota_pindah($data_post);

                if ($query) {
                    $hasil['status'] = true;
                    $hasil['msg']    = "Data Berhasil Disimpan";
                } else {
                    $hasil['status'] = false;
                    $hasil['msg']    = "Data Gagal Disimpan";
                }
            }

            echo json_encode($hasil);
        }
    }

    public function get_anggota_pindah()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->anggota_model->get_anggota_pindah(1, $cari)->row(0)->numrows;
        $data_item    = $this->anggota_model->get_anggota_pindah(0, $cari, "", $offset, $limit);

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

    public function hapus_anggota_pindah()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            if (!cek_tanggal_entri($data_post['tgl_pindah'])) {
                $hasil['status'] = false;
                $hasil['msg']    = "Data bulan lalu tidak boleh dihapus";
            } else {
                $query = $this->anggota_model->hapus_anggota_pindah($data_post);

                if ($query) {
                    $hasil['status'] = true;
                    $hasil['msg']    = "Data Berhasil Dihapus";
                } else {
                    $hasil['status'] = false;
                    $hasil['msg']    = "Data Gagal Dihapus";
                }
            }

            echo json_encode($hasil);
        }
    }

    public function add_anggota_keluar()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['tgl_keluar'] = balik_tanggal($data_post['tgl_keluar']);

            $query = $this->anggota_model->insert_anggota_keluar($data_post);

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

    public function get_anggota_keluar()
    {
        $data = get_request();

        $cari['field'] = array("no_ang");
        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->anggota_model->get_anggota_keluar(1, $cari)->row(0)->numrows;
        $data_item    = $this->anggota_model->get_anggota_keluar(0, $cari, "", $offset, $limit);

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

    public function hapus_anggota_keluar()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            if (!cek_tanggal_entri($data_post['tgl_keluar'])) {
                $hasil['status'] = false;
                $hasil['msg']    = "Data bulan lalu tidak boleh dihapus";
            } else {
                $query = $this->anggota_model->hapus_anggota_keluar($data_post);

                if ($query) {
                    $hasil['status'] = true;
                    $hasil['msg']    = "Data Berhasil Dihapus";
                } else {
                    $hasil['status'] = false;
                    $hasil['msg']    = "Data Gagal Dihapus";
                }
            }

            echo json_encode($hasil);
        }
    }

    public function get_anggota($status_anggota = "")
    {
        $data = get_request();

        $cari['field'] = array("no_ang");
        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $data_numrows = $this->anggota_model->get_anggota(1, $cari, "", "", "", $status_anggota, "", "1")->row(0)->numrows;
        $data_item    = $this->anggota_model->get_anggota(0, $cari, "", $offset, $limit, $status_anggota, "", "1");

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

    public function update_data_anggota($id)
    {
        $data_post = $this->input->post();

        // baca_array($data_post);exit();

        if ($data_post) {
            $query = $this->anggota_model->update_data_anggota($data_post, $id);

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

    public function delete_data_anggota()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query = $this->anggota_model->delete_data_anggota($data_post);

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

    public function init_upload_data_anggota_pkg()
    {
        $this->cache->file->save("upload_anggota_pkg_" . session_id(), "0;0;0");

        $data_json['data_now']   = 0;
        $data_json['data_total'] = 0;
        $data_json['persen']     = 0;

        echo json_encode($data_json);
    }

    public function get_upload_data_anggota_pkg()
    {
        $data_progress = $this->cache->file->get("upload_anggota_pkg_" . session_id());

        $explode = explode(";", $data_progress);

        $data_json['persen']     = $explode[0];
        $data_json['data_now']   = $explode[1];
        $data_json['data_total'] = $explode[2];

        echo json_encode($data_json);
    }

    public function upload_data_anggota_pkg()
    {
        set_time_limit(0);

        $data_file = $_FILES;

        if ($data_file) {
            $nama_file = strtolower($data_file['file_upload']['name']);

            $ex_nama_file = explode(".", $nama_file);

            if (end($ex_nama_file) != "xls") {
                echo "<h5>Harus File Excel 97/2003 (.xls)!!</h5>";
                exit;
            }

            if (!$data_file['file_upload']['tmp_name']) {
                echo "<h5>Maaf, isi file tidak dapat diakses, mohon upload ulang file-nya</h5>";
                exit;
            }

            $this->anggota_model->upload_data_anggota_pkg($data_file);
        }
    }

    public function get_koreksi_plafon()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];
        $no_ang        = (isset($data['no_ang']) and $data['no_ang'] != "") ? $data['no_ang'] : "xxx";

        $data_numrows = $this->anggota_model->get_koreksi_plafon(1, $cari, "", "", "", $no_ang)->row(0)->numrows;
        $data_item    = $this->anggota_model->get_koreksi_plafon(0, $cari, "", $offset, $limit, $no_ang);

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

    public function add_koreksi_plafon()
    {
        $data_post = get_request('post');

        if ($data_post) {
            if ($data_post['jenis_debet'] == "TAMBAHPLAFON") {
                $jml_debet = (0 - hapus_koma($data_post['jumlah']));
            } else if ($data_post['jenis_debet'] == "KURANGPLAFON") {
                $jml_debet = hapus_koma($data_post['jumlah']);
            }

            $set_data = array(
                "no_ang"      => $data_post['no_ang'],
                "tgl_penj"    => balik_tanggal($data_post['tgl_penj']),
                "jenis_debet" => $data_post['jenis_debet'],
                "jml_debet"   => $jml_debet,
            );

            $query = $this->db->set($set_data)->insert("t_plafon_debet");

            $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data_post['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

            $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data_post['no_ang'])->update("t_anggota");

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

    public function del_koreksi_plafon()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $query = $this->db->where("id", $data_post['id'])->delete("t_plafon_debet");

            $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data_post['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

            $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data_post['no_ang'])->update("t_anggota");

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

    public function init_progress_sisa_plafon()
    {
        $this->cache->file->save('proses_sisa_plafon_' . session_id(), "0;0;0");
    }

    public function get_progress_sisa_plafon()
    {
        $data_proses = explode(";", $this->cache->file->get('proses_sisa_plafon_' . session_id()));

        $json['persen']     = $data_proses[0];
        $json['data_now']   = $data_proses[1];
        $json['data_total'] = $data_proses[2];

        echo json_encode($json);
    }

    public function proses_sisa_plafon()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $this->anggota_model->proses_sisa_plafon($data_post);
        }
    }

}
