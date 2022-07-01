<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . '/libraries/tcpdf/tcpdf.php';

class Mypdf_header extends TCPDF
{
    public function __construct($orientasi = "P", $unit = "mm", $ukuran_kertas = "A4")
    {
        parent::__construct($orientasi, $unit, $ukuran_kertas);

        // Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        // MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = 0, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0);
    }

    public function Header()
    {
        $this->SetY(5);

        $this->SetFont('ltype', '', 7);

        $this->Cell(0, 5, NAMA_PHP, 0, 1, 'L');
    }

    public function Footer()
    {
        $this->SetFont('ltype', '', 7);

        $this->Cell(0, 5, "Halaman " . $this->getAliasNumPage() . " dari " . $this->getAliasNbPages(), 0, 1, 'R');
    }

}
