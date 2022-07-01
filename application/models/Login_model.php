<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Login_model extends CI_Model
{
    public function masuk($data)
    {
        $dataLogin = $this->db->select("id_user, nama, username, a.id_grup, b.nm_grup")
            ->from("s_user a")->join("s_grup b ", "a.id_grup = b.id_grup")
            ->where("username", strtoupper($data['username']))
            ->where("passwd", $data['password'])
            ->get();

        if ($dataLogin->num_rows() > 0) {
            $this->session->set_userdata($dataLogin->row_array(0));

            return true;
        } else {
            return false;
        }
    }

    public function cek_login()
    {
        if ($this->session->userdata("username") == "") {
            $data['judul_web'] = judul_web();

            echo $this->load->view("home/header", $data, true);
            echo $this->load->view("home/login", $data, true);
            echo $this->load->view("home/footer", "", true);

            exit();
        }
    }

    public function is_login()
    {
        if ($this->session->userdata('username') != "") {
            header("location:" . base_url(''));
        } else {
            return true;
        }
    }

}
