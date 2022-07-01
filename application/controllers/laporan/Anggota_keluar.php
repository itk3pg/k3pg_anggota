<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Anggota_keluar extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();
    }

    public function index()
    {
        $bulan = get_option_tag(array_bulan(), "BULAN");

        $data['judul_menu'] = "Laporan Anggota Keluar";
        $data['bulan']      = $bulan;

        $this->template->view("laporan/lap_anggota_keluar", $data);
    }

    public function tampilkan()
    {
        $data_req = get_request();

        if ($data_req) {
            $this->load->model("laporan_model");

            $laporan = "<table class=\"table table-bordered table-condensed table-striped\" border=\"1\" style=\"white-space: nowrap;\">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tgl Keluar</th>
                            <th>No. Anggota</th>
                            <th>No. Pegawai</th>
                            <th>Nama</th>
                            <th>Perusahaan</th>
                            <th>Departemen</th>
                            <th>Bagian</th>
                            <th>Keterangan</th>
                            <th>Jumlah Hak</th>
                        </tr>
                    </thead>
                    <tbody>";

            $data_anggota = $this->laporan_model->get_ang_keluar($data_req['tahun'], $data_req['bulan']);

            $no = 1;

            foreach ($data_anggota->result_array() as $key => $value) {
                $laporan .= "
                        <tr>
                            <td>" . $no . "</td>
                            <td>" . $value['tgl_keluar'] . "</td>
                            <td>" . $value['no_ang'] . "</td>
                            <td>" . $value['no_peg'] . "</td>
                            <td>" . $value['nm_ang'] . "</td>
                            <td>" . $value['nm_prsh'] . "</td>
                            <td>" . $value['nm_dep'] . "</td>
                            <td>" . $value['nm_bagian'] . "</td>
                            <td>" . $value['ket_keluar'] . "</td>
                            <td style=\"text-align: right\">" . number_format($value['jml_hak'], 2) . "</td>
                        </tr>";

                $no++;
            }

            $laporan .= "
                    </tbody>
                </table>";

            echo $laporan;
        }
    }

    public function excel()
    {
        $data_req = get_request();

        $file = "lap_ang_keluar_" . $data_req['bulan'] . "-" . $data_req['tahun'] . ".xls";

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $file);

        $this->tampilkan();
    }

    public function cetak()
    {
        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $pdf = new mypdf("P", "mm", array("350", "215"));

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_ss1_harian_" . date("Y-m-d_H-i-s") . ".pdf";

        $pdf->SetCreator($kreator);
        $pdf->SetAuthor($kreator);
        $pdf->SetTitle($judul_file);

        $pdf->SetHeaderData("", "", $judul_header, $teks_header, "", "");
        $pdf->setFooterData("", "");
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins("10", "18", "10");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddPage();

        $pdf->SetFontSize('12');

        $pdf->Cell(0, 0, "Laporan Daftar Pajak SS1 dan SS2", 0, 0, "C");
        $pdf->SetFontSize('9');

        $pdf->Ln();

        $array_bln  = array_bulan();
        $nama_bulan = $array_bln[$data_req['bulan']];

        $pdf->Cell(0, 0, "Periode : " . $nama_bulan . " " . $data_req['tahun'], 0, 0, "C");

        $pdf->Ln();
        $pdf->Ln();

        $koleng[1] = "10";
        $koleng[2] = "60";
        $koleng[3] = "20";
        $koleng[4] = "30";
        $koleng[5] = "30";
        $koleng[6] = "30";

        $koleng_sub = $koleng[1] + $koleng[2] + $koleng[3] + $koleng[4];
        $koleng_all = $koleng[1] + $koleng[2] + $koleng[3] + $koleng[4] + $koleng[5] + $koleng[6];

        $laporan = "<table class=\"table table-bordered table-condensed table-striped\" style=\"white-space: nowrap\">
                    <thead>
                        <tr style=\"\">
                            <th style=\"text-align: center; vertical-align: middle;\">No.</th>
                            <th style=\"text-align: center; vertical-align: middle;\">NAMA</th>
                            <th style=\"text-align: center; vertical-align: middle;\">NAK</th>
                            <th style=\"text-align: center; vertical-align: middle;\">NIK</th>
                            <th style=\"text-align: center; vertical-align: middle;\">DPP</th>
                            <th style=\"text-align: center; vertical-align: middle;\">Pajak</th>
                        </tr>
                    </thead>
                    <tbody>";

        $pdf->Cell($koleng[1], 0, "No.", 1, 0, "C");
        $pdf->Cell($koleng[2], 0, "NAMA", 1, 0, "C");
        $pdf->Cell($koleng[3], 0, "NAK", 1, 0, "C");
        $pdf->Cell($koleng[4], 0, "NIK", 1, 0, "C");
        $pdf->Cell($koleng[5], 0, "DPP", 1, 0, "C");
        $pdf->Cell($koleng[6], 0, "Pajak", 1, 0, "C");

        $pdf->Ln();

        $grandtotal_dpp   = 0;
        $grandtotal_pajak = 0;

        $no = 1;

        $query_jns_transaksi = $this->db->where("year(tgl_simpan)", $data_req['tahun'])->where("month(tgl_simpan)", $data_req['bulan'])
            ->where("kd_jns_transaksi", "10")
            ->order_by("no_ang")
            ->get("t_simpanan_ang");

        foreach ($query_jns_transaksi->result_array() as $key => $value) {
            $dpp = $value['jumlah'] * 10;

            $pdf->Cell($koleng[1], 0, $no, 0, 0, "R");
            $pdf->Cell($koleng[2], 0, $value['nm_ang'], 0, 0, "L", 0, '', 1);
            $pdf->Cell($koleng[3], 0, $value['no_ang'], 0, 0, "L");
            $pdf->Cell($koleng[4], 0, $value['no_peg'], 0, 0, "L");
            $pdf->Cell($koleng[5], 0, number_format($dpp, 2), 0, 0, "R", 0, '', 1);
            $pdf->Cell($koleng[6], 0, number_format($value['jumlah'], 2), 0, 0, "R", 0, '', 1);

            $pdf->Ln();

            $no++;
            $grandtotal_dpp += $dpp;
            $grandtotal_pajak += $value['jumlah'];
        }

        $pdf->SetFont("", "B");

        $pdf->Cell($koleng_sub, 0, "Grand Total", 'TB', 0, "R");
        $pdf->Cell($koleng[5], 0, number_format($grandtotal_dpp, 2), 'TB', 0, "R", 0, '', 1);
        $pdf->Cell($koleng[6], 0, number_format($grandtotal_pajak, 2), 'TB', 0, "R", 0, '', 1);

        $pdf->Output($judul_file, 'I');
    }
}
