<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . '/libraries/phpexcel/PHPExcel.php';

class Php_excel extends PHPExcel
{

    public function __construct()
    {
        parent::__construct();
    }

}
