<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rekap_belanja extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("laporan_model");

        $this->querySelect = "a.*, b.no_peg, b.nm_ang, b.kd_prsh, b.nm_prsh, b.nm_dep, b.nm_bagian";
    }

    public function index()
    {
        $bulan = get_option_tag(array_bulan(), "BULAN");

        $data['judul_menu'] = "Laporan Rekapitulasi Belanja Anggota";
        $data['bulan']      = $bulan;

        $this->template->view("laporan/rekap_belanja", $data);
    }

    private function queryFrom($tahun, $bulan, $no_ang = "")
    {
        $whereAnggota1 = "";
        $whereAnggota2 = "";

        if ($no_ang != "") {
            $whereAnggota1 = " and no_ang = '" . $no_ang . "' ";
            $whereAnggota2 = " and a.pelanggan_kode = '" . $no_ang . "' ";

        }

        return "(
            SELECT no_ang, date_format(tgl_trans, '%d-%m-%Y') tgl_trans, if(unit_adm = 'AIR', 'AIR K', unit_adm) unit_adm, jml_trans
            FROM k3pg_sp.t_bridging_plafon
            WHERE tgl_trans LIKE '" . $tahun . "-" . $bulan . "%'
            AND tempo_bln = '1'
            AND no_ang REGEXP \"^[0-9.]+$\"
            " . $whereAnggota1 . "
            UNION
            SELECT a.pelanggan_kode, date_format(a.tanggal, '%d-%m-%Y') tanggal, b.nama, jumlah
            FROM db_wecode_smart.piutang a JOIN db_wecode_smart.toko b
            ON a.toko_kode=b.kode
            WHERE tanggal LIKE '" . $tahun . "-" . $bulan . "%'
            AND a.pelanggan_kode REGEXP \"^[0-9.]+$\"
            " . $whereAnggota2 . " 
            UNION
            SELECT a.pelanggan_kode, date_format(a.tanggal, '%d-%m-%Y') tanggal, b.nama, jumlah
            FROM db_bengkel.piutang a JOIN db_bengkel.toko b
            ON a.toko_kode=b.kode
            WHERE tanggal LIKE '" . $tahun . "-" . $bulan . "%'
            AND a.pelanggan_kode REGEXP \"^[0-9.]+$\"
            " . $whereAnggota2 . " 
            UNION
            SELECT a.pelanggan_kode, date_format(a.tanggal, '%d-%m-%Y') tanggal, b.nama, jumlah
            FROM db_pbb.piutang a JOIN db_pbb.toko b
            ON a.toko_kode=b.kode
            WHERE tanggal LIKE '" . $tahun . "-" . $bulan . "%'
            AND a.pelanggan_kode REGEXP \"^[0-9.]+$\"
            " . $whereAnggota2 . "
        ) a";
    }

    public function tampilkan()
    {
        set_time_limit(0);

        $data_req = get_request();

        if ($data_req) {
            $select = $this->querySelect;
            $from   = $this->queryFrom($data_req['tahun'], $data_req['bulan'], $data_req['no_ang']);

            $dataBelanja = $this->db->from($from)->join("t_anggota b", "a.no_ang=b.no_ang")
                ->select($select)
                ->order_by("a.no_ang, a.tgl_trans")->get();

            $dataAnggota = $this->db->from($from)->join("t_anggota b", "a.no_ang=b.no_ang")
                ->select($select)
                ->group_by("a.no_ang")->get();

            $view = "<table class=\"table table-bordered table-condensed\">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAK</th>
                        <th>NIK</th>
                        <th>NAMA</th>
                        <th>TANGGAL</th>
                        <th>KET</th>
                        <th>T/K</th>
                        <th>JUMLAH</th>
                    </tr>
                </thead>
                <tbody>";

            $no           = 1;
            $gt_jml_trans = 0;

            foreach ($dataAnggota->result_array() as $key => $value) {
                $t_jml_trans = 0;

                $teks_depbag = ($value['kd_prsh'] != "P01") ? $value['nm_prsh'] . " - " . $value['nm_prsh'] : $value['nm_dep'] . " - " . $value['nm_bagian'];

                $view .= "<tr>
                        <td class=\"text-right\">" . $no . "</td>
                        <td>" . $value['no_ang'] . "</td>
                        <td>" . $value['no_peg'] . "</td>
                        <td>" . $value['nm_ang'] . "</td>
                        <td colspan=\"4\"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan=\"7\">" . $teks_depbag . "</td>
                    </tr>";

                foreach ($dataBelanja->result_array() as $key1 => $value1) {
                    if ($value['no_ang'] == $value1['no_ang']) {
                        $view .= "<tr>
                            <td colspan=\"4\"></td>
                            <td>" . $value1['tgl_trans'] . "</td>
                            <td>" . $value1['unit_adm'] . "</td>
                            <td>K</td>
                            <td class=\"text-right\">" . number_format($value1['jml_trans'], 2) . "</td>
                        </tr>";

                        $t_jml_trans += $value1['jml_trans'];
                        $gt_jml_trans += $value1['jml_trans'];
                    }
                }

                $view .= "<tr>
                    <td colspan=\"6\" class=\"text-right\">TOTAL BELANJA</td>
                    <td colspan=\"2\" class=\"text-right\">" . number_format($t_jml_trans, 2) . "</td>
                </tr>
                <tr>
                    <td colspan=\"8\"></td>
                </tr>";

                $no++;
            }

            $view .= "
                </tbody>
                <tfoot>
                    <th colspan=\"6\" class=\"text-right\">TOTAL BELANJA KESELURUHAN</th>
                    <th colspan=\"2\" class=\"text-right\">" . number_format($gt_jml_trans, 2) . "</th>
                </tfoot>
            </table>";

            echo $view;
        }
    }

    public function cetak_rekap()
    {
        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = "A4";

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_rekapitulasi_belanja_" . $data_req['bulan'] . $data_req['tahun'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

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

        $pdf->SetMargins("5", "18", "5");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, "15");
        // $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddPage();

        $pdf->SetFontSize('11');

        $pdf->Cell(0, 0, "Rekapitulasi Belanja Anggota K3PG", 0, 0, "C");
        $pdf->SetFontSize('8');

        $pdf->Ln();

        $array_bln  = array_bulan();
        $nama_bulan = $array_bln[$data_req['bulan']];

        $pdf->Cell(0, 0, "Periode : " . $nama_bulan . " " . $data_req['tahun'], 0, 0, "C");
        $pdf->Ln();
        $pdf->Ln();

        $koleng[1] = "7";
        $koleng[2] = "10";
        $koleng[3] = "17";
        $koleng[4] = "60";
        $koleng[5] = "23";
        $koleng[6] = "25";
        $koleng[7] = "10";
        $koleng[8] = "30";

        $pdf->Cell($koleng[1], 0, "NO", "TB", 0, "C");
        $pdf->Cell($koleng[2], 0, "NAK", "TB", 0, "C");
        $pdf->Cell($koleng[3], 0, "NIK", "TB", 0, "C");
        $pdf->Cell($koleng[4], 0, "NAMA", "TB", 0, "C");
        $pdf->Cell($koleng[5], 0, "TANGGAL", "TB", 0, "C");
        $pdf->Cell($koleng[6], 0, "KET", "TB", 0, "C");
        $pdf->Cell($koleng[7], 0, "T/K", "TB", 0, "C");
        $pdf->Cell($koleng[8], 0, "JUMLAH", "TB", 0, "C");
        $pdf->Ln();

        $select = $this->querySelect;
        $from   = $this->queryFrom($data_req['tahun'], $data_req['bulan'], $data_req['no_ang']);

        $dataBelanja = $this->db->from($from)->join("t_anggota b", "a.no_ang=b.no_ang")
            ->select($select)
            ->order_by("a.no_ang, a.tgl_trans")->get();

        $dataAnggota = $this->db->from($from)->join("t_anggota b", "a.no_ang=b.no_ang")
            ->select($select)
            ->group_by("a.no_ang")->get();

        $no           = 1;
        $gt_jml_trans = 0;

        foreach ($dataAnggota->result_array() as $key => $value) {
            if ($pdf->GetY() > 273) {
                $pdf->AddPage();
            }

            $t_jml_trans = 0;

            $teks_depbag = ($value['kd_prsh'] != "P01") ? $value['nm_prsh'] . " - " . $value['nm_prsh'] : $value['nm_dep'] . " - " . $value['nm_bagian'];

            $pdf->Cell($koleng[1], 0, $no, "", 0, "R");
            $pdf->Cell($koleng[2], 0, $value['no_ang']);
            $pdf->Cell($koleng[3], 0, $value['no_peg'], 0, 0, "", "", "", 1);
            $pdf->Cell($koleng[4], 0, $value['nm_ang']);
            $pdf->Ln();

            $pdf->Cell($koleng[1], 0, "", "", 0, "R");
            $pdf->Cell($koleng[2] + $koleng[3] + $koleng[4] + $koleng[5] + $koleng[6] + $koleng[7] + $koleng[8], 0, $teks_depbag);
            $pdf->Ln();

            foreach ($dataBelanja->result_array() as $key1 => $value1) {
                if ($value['no_ang'] == $value1['no_ang']) {
                    $pdf->Cell($koleng[1] + $koleng[2] + $koleng[3] + $koleng[4], 0, "");
                    $pdf->Cell($koleng[5], 0, $value1['tgl_trans']);
                    $pdf->Cell($koleng[6], 0, $value1['unit_adm']);
                    $pdf->Cell($koleng[7], 0, "K");
                    $pdf->Cell($koleng[8], 0, number_format($value1['jml_trans'], 2), "", "", "R");
                    $pdf->Ln();

                    $t_jml_trans += $value1['jml_trans'];
                    $gt_jml_trans += $value1['jml_trans'];
                }
            }

            $pdf->Cell($koleng[1] + $koleng[2] + $koleng[3] + $koleng[4] + $koleng[5] + $koleng[6], 0, "TOTAL BELANJA", "T", 0, "R");
            $pdf->Cell($koleng[7] + $koleng[8], 0, number_format($t_jml_trans, 2), "T", 0, "R");

            $pdf->Ln();
            $pdf->Ln();

            $no++;
        }

        $pdf->Cell($koleng[1] + $koleng[2] + $koleng[3] + $koleng[4] + $koleng[5] + $koleng[6], 0, "TOTAL KESELURUHAN BELANJA", "TB", 0, "R");
        $pdf->Cell($koleng[7] + $koleng[8], 0, number_format($gt_jml_trans, 2), "TB", 0, "R");

        $pdf->Output($judul_file, 'I');
    }
}
