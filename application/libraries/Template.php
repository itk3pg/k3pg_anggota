<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Created By M. Bagus Rachmat Dianto
 */

class Template
{
    private $ci;
    private $template_data;
    private $template_dir = "home/";

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    public function set_data($key = "", $value = null)
    {
        $this->template_data[$key] = $value;
    }

    public function get_data($key = "")
    {
        if ($key) {
            return $this->template_data[$key];
        } else {
            return $this->template_data;
        }
    }

    public function view($page = null, $page_data = null, $return = false)
    {
        $this->set_data("judul_web", NAMA_PHP);

        $this->set_data("judul_menu", "");
        $this->set_data("view", "");

        if ($page_data != null) {
            $this->set_data("judul_menu", $page_data['judul_menu']);
        }

        if ($page != null) {
            $this->set_data("view", $this->ci->load->view($page, $page_data, true));
        }

        $this->ci->load->view($this->template_dir . "header", $this->get_data(), $return);

        $this->ci->load->view($this->template_dir . "body", "", $return);
        $this->ci->load->view($this->template_dir . "sidebar", "", $return);
        $this->ci->load->view($this->template_dir . "content", $this->get_data(), $return);

        $this->ci->load->view($this->template_dir . "footer", "", $return);
    }

}
