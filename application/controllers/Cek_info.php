<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cek_info extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        baca(PHP_OS);
        baca(phpinfo());
        baca(php_uname("a"));
        baca($_SERVER['SERVER_ADDR']);
        baca($_SERVER['SERVER_NAME']);
        baca($_SERVER['HTTP_HOST']);
        baca($_SERVER['PATH_INFO']);
        baca($_SERVER['REQUEST_URI']);
        // baca(pathinfo(path));
        echo $url_page = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function tes()
    {
        echo strtotime("2017-03-31") . "<br>";
        echo strtotime("2017-04-01") . "<br>";
        echo strtotime("2017-04-02") . "<br>";
    }

    public function escape()
    {
        echo $this->db->escape_str("\"33\"");
        echo "\"33\"";
    }

    public function jam()
    {
        echo date("Y-m-d H:i:s");
    }

}
