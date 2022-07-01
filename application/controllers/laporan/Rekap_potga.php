<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rekap_potga extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("laporan_model");
    }

    public function index()
    {
        $bulan = get_option_tag(array_bulan(), "BULAN");

        $data['judul_menu'] = "Laporan Rekapitulasi dan Daftar Potong Gaji";
        $data['bulan']      = $bulan;

        $this->template->view("laporan/rekap_potga", $data);
    }

    public function tampilkan()
    {
        set_time_limit(0);

        $data_req = get_request();

        if ($data_req) {
            $laporan = "";

            $blth = $data_req['tahun'] . "-" . $data_req['bulan'];

            $data_anggota = $this->db->where("kd_prsh", $data_req['kd_prsh'])
                ->like("tgl_potga", $blth, "after")
                ->where("is_pensiun", "0")
                ->where("is_pot_bonus", "0")
                ->group_by("no_ang")
                ->get("t_potga");

            $data_potga = $this->db->select("*, (tempo_bln - angs_ke) sisa_angs")
                ->where("kd_prsh", $data_req['kd_prsh'])
                ->like("tgl_potga", $blth, "after")
                ->where("is_pensiun", "0")
                ->where("is_pot_bonus", "0")
                ->order_by("no_ang, tgl_rilis")
                ->get("t_potga")->result_array();

            foreach ($data_anggota->result_array() as $key => $value) {
                $laporan .= "
                <table class=\"table table-bordered table-condensed table-striped\" style=\"white-space: nowrap\">
                    <tbody>
                        <tr>
                            <td>NIK/NAK</td>
                            <td>" . $value['no_peg'] . " / " . $value['no_ang'] . "</td>
                            <td>Dept/Biro</td>
                            <td>" . $value['nm_dep'] . "</td>
                        </tr>
                        <tr>
                            <td>NAMA</td>
                            <td>" . $value['nm_ang'] . "</td>
                            <td>Bagian</td>
                            <td>" . $value['nm_bagian'] . "</td>
                        </tr>
                    </tbody>
                </table>
                <table class=\"table table-bordered table-condensed table-striped\" style=\"white-space: nowrap\">
                    <thead>
                        <tr>
                            <th style=\"text-align: center; vertical-align: middle;\">Tanggal</th>
                            <th style=\"text-align: center; vertical-align: middle;\">Keterangan</th>
                            <th style=\"text-align: center; vertical-align: middle;\">Perbulan</th>
                            <th style=\"text-align: center; vertical-align: middle;\">Masa</th>
                            <th style=\"text-align: center; vertical-align: middle;\">Angs Ke.</th>
                            <th style=\"text-align: center; vertical-align: middle;\">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>";

                $no       = 1;
                $t_jumlah = 0;

                $jml_wajib      = 0;
                $jml_sukarela   = 0;
                $jml_blj_kredit = 0;

                foreach ($data_potga as $key1 => $value1) {
                    if ($value1['no_ang'] == $value['no_ang']) {
                        if ($value1['kd_potga'] == "1") {
                            $jml_wajib    = $value1['jml_wajib'];
                            $jml_sukarela = $value1['jml_sukarela'];
                            continue;
                        }

                        if ($value1['kd_potga'] == "31") {
                            $jml_blj_kredit += $value1['jumlah'];
                            continue;
                        }

                        $laporan .= "
                            <tr>
                                <td>" . $value1['tgl_rilis'] . "</td>
                                <td>" . $value1['ket'] . "</td>
                                <td style=\"text-align: right\">" . number_format($value1['jumlah'], 2) . "</td>
                                <td style=\"text-align: center\">" . $value1['tempo_bln'] . "</td>
                                <td style=\"text-align: center\">" . $value1['angs_ke'] . "</td>
                                <td style=\"text-align: center\">" . $value1['sisa_angs'] . "</td>
                            </tr>
                        ";

                        $t_jumlah += $value1['jumlah'];

                        unset($data_potga[$key1]);
                    } else {
                        continue;
                    }
                }

                $laporan .= "
                    <tr>
                        <td colspan=\"6\"></td>
                    </tr>
                    <tr>
                        <th colspan=\"2\" style=\"text-align:right\">Potongan Kredit Angsuran</th>
                        <th style=\"text-align:right\">" . number_format($t_jumlah, 2) . "</th>
                        <th>Simp. Wajib</th>
                        <th colspan=\"2\" style=\"text-align:right\">" . number_format($jml_wajib, 2) . "</th>
                    </tr>
                    <tr>
                        <th colspan=\"2\" style=\"text-align:right\">Jumlah Belanja Kredit</th>
                        <th style=\"text-align:right\">" . number_format($jml_blj_kredit, 2) . "</th>
                        <th>Simp. Sukarela</th>
                        <th colspan=\"2\" style=\"text-align:right\">" . number_format($jml_sukarela, 2) . "</th>
                    </tr>
                    <tr>
                        <td colspan=\"6\"></td>
                    </tr>";

                $t_jumlah += ($jml_wajib + $jml_sukarela + $jml_blj_kredit);

                $laporan .= "
                    <tr>
                        <th colspan=\"2\" style=\"text-align:right\">Total Potongan</th>
                        <th style=\"text-align:right\">" . number_format($t_jumlah, 2) . "</th>
                        <th colspan=\"3\" style=\"text-align:right\"></th>
                    </tr>";

                $laporan .= "
                    </tbody>
                </table>
                <hr>";
            }
        }

        echo $laporan;
    }

    public function cetak_rekap()
    {
        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = array("400", "215");

        $pdf = new mypdf("L", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_rekapitulasi_potga_" . $data_req['nm_prsh'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

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

        $pdf->AddPage("L");

        $pdf->SetFontSize('11');

        $pdf->Cell(0, 0, "Rekapitulasi Potongan Gaji Anggota K3PG", 0, 0, "C");
        $pdf->SetFontSize('8');

        $pdf->Ln();

        $array_bln  = array_bulan();
        $nama_bulan = $array_bln[$data_req['bulan']];

        $pdf->Cell(0, 0, "Perusahaan : " . $data_req['nm_prsh'], 0, 0, "C");
        $pdf->Ln();

        $pdf->Cell(0, 0, "Periode : " . $nama_bulan . " " . $data_req['tahun'], 0, 0, "C");
        // $pdf->Cell(0, 0, "Periode : " . str_pad($data_req['tgl_awal'], 2, "0", STR_PAD_LEFT) . " - " . str_pad($data_req['tgl_akhir'], 2, "0", STR_PAD_LEFT) . " " . $nama_bulan . " " . $data_req['tahun'], 0, 0, "C");

        $pdf->Ln();
        $pdf->Ln();

        $koleng[1]  = "10";
        $koleng[2]  = "50";
        $koleng[3]  = "15";
        $koleng[4]  = "20";
        $koleng[5]  = "23";
        $koleng[6]  = "25";
        $koleng[7]  = "28";
        $koleng[8]  = "30";
        $koleng[9]  = "30";
        $koleng[10] = "30";
        $koleng[11] = "30";
        $koleng[12] = "30";
        $koleng[13] = "30";
        $koleng[14] = "30";

        $pdf->SetFontSize('8');

        $pdf->Cell($koleng[1], 0, "NO.", "TLR", 0, "C");
        $pdf->Cell($koleng[2], 0, "DEPT/BIRO", "TLR", 0, "C");
        $pdf->Cell($koleng[3], 0, "JUMLAH", "TLR", 0, "C", 0, 0, 1);
        $pdf->Cell($koleng[4] + $koleng[5] + $koleng[6], 0, "SIMPANAN", 1, 0, "C");
        $pdf->Cell($koleng[7], 0, "JUMLAH", "TLR", 0, "C");
        $pdf->Cell($koleng[8] + $koleng[9] + $koleng[10] + $koleng[11] + $koleng[12], 0, "PEMBELIAN/PINJAMAN", 1, 0, "C");
        $pdf->Cell($koleng[13], 0, "JUMLAH", "TLR", 0, "C");
        $pdf->Cell($koleng[14], 0, "Total", "TLR", 0, "C");

        $pdf->Ln();

        $pdf->Cell($koleng[1], 0, "", "BLR", 0, "C");
        $pdf->Cell($koleng[2], 0, "", "BLR", 0, "C");
        $pdf->Cell($koleng[3], 0, "ANGGOTA", "BLR", 0, "C", 0, 0, 1);
        $pdf->Cell($koleng[4], 0, "POKOK (A)", "BLR", 0, "C");
        $pdf->Cell($koleng[5], 0, "WAJIB (B)", "BLR", 0, "C");
        $pdf->Cell($koleng[6], 0, "SUKARELA (C)", "BLR", 0, "C");
        $pdf->Cell($koleng[7], 0, "(A+B+C)", "BLR", 0, "C");
        $pdf->Cell($koleng[8], 0, "BLJ TOKO (E)", "BLR", 0, "C");
        $pdf->Cell($koleng[9], 0, "ELEKTRO (F)", "BLR", 0, "C");
        $pdf->Cell($koleng[10], 0, "S. MOTOR (G)", "BLR", 0, "C");
        $pdf->Cell($koleng[11], 0, "BANGUNAN (H)", "BLR", 0, "C");
        $pdf->Cell($koleng[12], 0, "UANG (I)", "BLR", 0, "C");
        $pdf->Cell($koleng[13], 0, "(E+F+G+H+I)", "BLR", 0, "C");
        $pdf->Cell($koleng[14], 0, "", "BLR", 0, "C");

        $pdf->Ln();

        $query_rekap = "SELECT a.nm_dep, banyak_anggota,
                SUM(if(kd_potga in ('11', '111'), if(kd_prsh = 'P02', 0, jumlah), 0)) simp_pokok,
                SUM(if(kd_potga in ('1'), if(kd_prsh = 'P02', 0, jml_wajib), 0)) simp_wajib,
                SUM(if(kd_potga in ('1'), if(kd_prsh = 'P02', 0, jml_sukarela), 0)) simp_sukarela,
                SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
                SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
                SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
                SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
                SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang
            FROM t_potga a
            join (
                SELECT nm_dep, COUNT(*) banyak_anggota
                FROM (
                    SELECT no_ang, nm_dep
                    FROM t_potga
                    WHERE tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'
                    AND kd_prsh = '" . $data_req['kd_prsh'] . "'
                    AND is_pensiun = '0'
                    AND is_pot_bonus = '0'
                    GROUP by no_ang
                ) x
                GROUP BY nm_dep
            ) b
            ON a.nm_dep = b.nm_dep
            WHERE tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'
            AND kd_prsh = '" . $data_req['kd_prsh'] . "'
            AND is_pensiun = '0'
            AND is_pot_bonus = '0'
            GROUP BY nm_dep";

        $no              = 1;
        $t_jml_anggota   = 0;
        $t_simp_pokok    = 0;
        $t_simp_wajib    = 0;
        $t_simp_sukarela = 0;
        $t_jml_simpanan  = 0;
        $t_jml_toko      = 0;
        $t_jml_elektro   = 0;
        $t_jml_motor     = 0;
        $t_jml_bangunan  = 0;
        $t_jml_pj_uang   = 0;
        $t_jml_kredit    = 0;
        $t_jml_total     = 0;

        $data_rekap = $this->db->query($query_rekap);

        foreach ($data_rekap->result_array() as $key => $value) {
            if ($pdf->GetY() > 197) {
                $pdf->Cell($koleng[1], 0, "NO.", "TLR", 0, "C");
                $pdf->Cell($koleng[2], 0, "DEPT/BIRO", "TLR", 0, "C");
                $pdf->Cell($koleng[3], 0, "JUMLAH", "TLR", 0, "C", 0, 0, 1);
                $pdf->Cell($koleng[4] + $koleng[5] + $koleng[6], 0, "SIMPANAN", 1, 0, "C");
                $pdf->Cell($koleng[7], 0, "JUMLAH", "TLR", 0, "C");
                $pdf->Cell($koleng[8] + $koleng[9] + $koleng[10] + $koleng[11] + $koleng[12], 0, "PEMBELIAN/PINJAMAN", 1, 0, "C");
                $pdf->Cell($koleng[13], 0, "JUMLAH", "TLR", 0, "C");
                $pdf->Cell($koleng[14], 0, "Total", "TLR", 0, "C");

                $pdf->Ln();

                $pdf->Cell($koleng[1], 0, "", "BLR", 0, "C");
                $pdf->Cell($koleng[2], 0, "", "BLR", 0, "C");
                $pdf->Cell($koleng[3], 0, "ANGGOTA", "BLR", 0, "C", 0, 0, 1);
                $pdf->Cell($koleng[4], 0, "POKOK (A)", "BLR", 0, "C");
                $pdf->Cell($koleng[5], 0, "WAJIB (B)", "BLR", 0, "C");
                $pdf->Cell($koleng[6], 0, "SUKARELA (C)", "BLR", 0, "C");
                $pdf->Cell($koleng[7], 0, "(A+B+C)", "BLR", 0, "C");
                $pdf->Cell($koleng[8], 0, "BLJ TOKO (E)", "BLR", 0, "C");
                $pdf->Cell($koleng[9], 0, "ELEKTRO (F)", "BLR", 0, "C");
                $pdf->Cell($koleng[10], 0, "S. MOTOR (G)", "BLR", 0, "C");
                $pdf->Cell($koleng[11], 0, "BANGUNAN (H)", "BLR", 0, "C");
                $pdf->Cell($koleng[12], 0, "UANG (I)", "BLR", 0, "C");
                $pdf->Cell($koleng[13], 0, "(E+F+G+H+I)", "BLR", 0, "C");
                $pdf->Cell($koleng[14], 0, "", "BLR", 0, "C");

                $pdf->Ln();
            }

            $jml_simpanan = $value['simp_pokok'] + $value['simp_wajib'] + $value['simp_sukarela'];
            $jml_kredit   = $value['jml_blj_toko'] + $value['jml_elektronik'] + $value['jml_motor'] + $value['jml_bangunan'] + $value['jml_pj_uang'];
            $jml_total    = $jml_simpanan + $jml_kredit;

            $pdf->Cell($koleng[1], 0, $no, 0, 0, "R");
            $pdf->Cell($koleng[2], 0, $value['nm_dep'], 0, 0, "L", 0, 0, 1);
            $pdf->Cell($koleng[3], 0, $value['banyak_anggota'], 0, 0, "C");
            $pdf->Cell($koleng[4], 0, number_format($value['simp_pokok'], 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[5], 0, number_format($value['simp_wajib'], 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[6], 0, number_format($value['simp_sukarela'], 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[7], 0, number_format($jml_simpanan, 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[8], 0, number_format($value['jml_blj_toko'], 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[9], 0, number_format($value['jml_elektronik'], 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[10], 0, number_format($value['jml_motor'], 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[11], 0, number_format($value['jml_bangunan'], 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[12], 0, number_format($value['jml_pj_uang'], 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[13], 0, number_format($jml_kredit, 2), 0, 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[14], 0, number_format($jml_total, 2), 0, 0, "R", 0, 0, 1);

            $pdf->Ln();

            $no++;
            $t_jml_anggota += $value['banyak_anggota'];
            $t_simp_pokok += $value['simp_pokok'];
            $t_simp_wajib += $value['simp_wajib'];
            $t_simp_sukarela += $value['simp_sukarela'];
            $t_jml_simpanan += $jml_simpanan;
            $t_jml_toko += $value['jml_blj_toko'];
            $t_jml_elektro += $value['jml_elektronik'];
            $t_jml_motor += $value['jml_motor'];
            $t_jml_bangunan += $value['jml_bangunan'];
            $t_jml_pj_uang += $value['jml_pj_uang'];
            $t_jml_kredit += $jml_kredit;
            $t_jml_total += $jml_total;
        }

        $pdf->Ln();

        $pdf->Cell($koleng[1] + $koleng[2], 0, "Total", "TB", 0, "L", 0, 0, 1);
        $pdf->Cell($koleng[3], 0, $t_jml_anggota, "TB", 0, "C");
        $pdf->Cell($koleng[4], 0, number_format($t_simp_pokok, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[5], 0, number_format($t_simp_wajib, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[6], 0, number_format($t_simp_sukarela, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[7], 0, number_format($t_jml_simpanan, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[8], 0, number_format($t_jml_toko, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[9], 0, number_format($t_jml_elektro, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[10], 0, number_format($t_jml_motor, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[11], 0, number_format($t_jml_bangunan, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[12], 0, number_format($t_jml_pj_uang, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[13], 0, number_format($t_jml_kredit, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[14], 0, number_format($t_jml_total, 2), "TB", 0, "R", 0, 0, 1);

        $pdf->Output($judul_file, 'I');
    }

    public function excel_rekap_potga()
    {
        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $fileName = "rekapPotga" . $data_req['bulan'] . $data_req['tahun'] . $data_req['nm_prsh'] . ".xls";

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");

        $array_bln  = array_bulan();
        $nama_bulan = $array_bln[$data_req['bulan']];

        $view = "<center>
            Rekapitulasi Potongan Gaji Anggota K3PG
            <br>
            Perusahaan : " . $data_req['nm_prsh'] . "
            <br>
            Periode : " . $nama_bulan . " " . $data_req['tahun'] . "
        </center>
        <br>
        <br>
        <table width=\"100%\" border=\"1\">
            <thead>
                <tr>
                    <th rowspan=\"2\">No.</th>
                    <th rowspan=\"2\">DEPT/BIRO</th>
                    <th rowspan=\"2\">JUMLAH ANGGOTA</th>
                    <th colspan=\"3\">SIMPANAN</th>
                    <th rowspan=\"2\">JUMLAH (A+B+C)</th>
                    <th colspan=\"5\">PEMBELIAN/PINJAMAN</th>
                    <th rowspan=\"2\">JUMLAH (E+F+G+H+I)</th>
                    <th rowspan=\"2\">TOTAL</th>
                </tr>
                <tr>
                    <th>POKOK (A)</th>
                    <th>WAJIB (B)</th>
                    <th>SUKARELA (C)</th>
                    <th>BLJ TOKO (E)</th>
                    <th>ELEKTRO (F)</th>
                    <th>S. MOTOR (G)</th>
                    <th>BANGUNAN (H)</th>
                    <th>UANG (I)</th>
                </tr>
            </thead>
            <tbody>
        ";

        $query_rekap = "SELECT a.nm_dep, banyak_anggota,
                SUM(if(kd_potga in ('11', '111'), if(kd_prsh = 'P02', 0, jumlah), 0)) simp_pokok,
                SUM(if(kd_potga in ('1'), if(kd_prsh = 'P02', 0, jml_wajib), 0)) simp_wajib,
                SUM(if(kd_potga in ('1'), if(kd_prsh = 'P02', 0, jml_sukarela), 0)) simp_sukarela,
                SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
                SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
                SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
                SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
                SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang
            FROM t_potga a
            join (
                SELECT nm_dep, COUNT(*) banyak_anggota
                FROM (
                    SELECT no_ang, nm_dep
                    FROM t_potga
                    WHERE tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'
                    AND kd_prsh = '" . $data_req['kd_prsh'] . "'
                    AND is_pensiun = '0'
                    AND is_pot_bonus = '0'
                    GROUP by no_ang
                ) x
                GROUP BY nm_dep
            ) b
            ON a.nm_dep = b.nm_dep
            WHERE tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'
            AND kd_prsh = '" . $data_req['kd_prsh'] . "'
            AND is_pensiun = '0'
            AND is_pot_bonus = '0'
            GROUP BY nm_dep";

        $no              = 1;
        $t_jml_anggota   = 0;
        $t_simp_pokok    = 0;
        $t_simp_wajib    = 0;
        $t_simp_sukarela = 0;
        $t_jml_simpanan  = 0;
        $t_jml_toko      = 0;
        $t_jml_elektro   = 0;
        $t_jml_motor     = 0;
        $t_jml_bangunan  = 0;
        $t_jml_pj_uang   = 0;
        $t_jml_kredit    = 0;
        $t_jml_total     = 0;

        $data_rekap = $this->db->query($query_rekap);

        foreach ($data_rekap->result_array() as $key => $value) {
            $jml_simpanan = $value['simp_pokok'] + $value['simp_wajib'] + $value['simp_sukarela'];
            $jml_kredit   = $value['jml_blj_toko'] + $value['jml_elektronik'] + $value['jml_motor'] + $value['jml_bangunan'] + $value['jml_pj_uang'];
            $jml_total    = $jml_simpanan + $jml_kredit;

            $view .= "<tr>
                <td style=\"text-align: right\">" . $no . "</td>
                <td>" . $value['nm_dep'] . "</td>
                <td style=\"text-align: right\">" . $value['banyak_anggota'] . "</td>
                <td style=\"text-align: right\">" . number_format($value['simp_pokok'], 2) . "</td>
                <td style=\"text-align: right\">" . number_format($value['simp_wajib'], 2) . "</td>
                <td style=\"text-align: right\">" . number_format($value['simp_sukarela'], 2) . "</td>
                <td style=\"text-align: right\">" . number_format($jml_simpanan, 2) . "</td>
                <td style=\"text-align: right\">" . number_format($value['jml_blj_toko'], 2) . "</td>
                <td style=\"text-align: right\">" . number_format($value['jml_elektronik'], 2) . "</td>
                <td style=\"text-align: right\">" . number_format($value['jml_motor'], 2) . "</td>
                <td style=\"text-align: right\">" . number_format($value['jml_bangunan'], 2) . "</td>
                <td style=\"text-align: right\">" . number_format($value['jml_pj_uang'], 2) . "</td>
                <td style=\"text-align: right\">" . number_format($jml_kredit, 2) . "</td>
                <td style=\"text-align: right\">" . number_format($jml_total, 2) . "</td>
            </tr>
            ";

            $no++;
            $t_jml_anggota += $value['banyak_anggota'];
            $t_simp_pokok += $value['simp_pokok'];
            $t_simp_wajib += $value['simp_wajib'];
            $t_simp_sukarela += $value['simp_sukarela'];
            $t_jml_simpanan += $jml_simpanan;
            $t_jml_toko += $value['jml_blj_toko'];
            $t_jml_elektro += $value['jml_elektronik'];
            $t_jml_motor += $value['jml_motor'];
            $t_jml_bangunan += $value['jml_bangunan'];
            $t_jml_pj_uang += $value['jml_pj_uang'];
            $t_jml_kredit += $jml_kredit;
            $t_jml_total += $jml_total;
        }

        $view .= "</tbody>
            <thead>
                <tr>
                    <th colspan=\"2\">Total</th>
                    <th style=\"text-align: right\">" . $t_jml_anggota . "</th>
                    <th style=\"text-align: right\">" . number_format($t_simp_pokok, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_simp_wajib, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_simp_sukarela, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_jml_simpanan, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_jml_toko, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_jml_elektro, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_jml_motor, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_jml_bangunan, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_jml_pj_uang, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_jml_kredit, 2) . "</th>
                    <th style=\"text-align: right\">" . number_format($t_jml_total, 2) . "</th>
                </tr>
            </thead>
        </table>";

        echo $view;
    }

    public function cetak_daftar_potga()
    {
        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = array(290, 330);

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_daftar_potga_" . $data_req['nm_prsh'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

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

        $pdf->SetMargins("3", "18", "3");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, "15");
        // $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $koleng[1]  = "5";
        $koleng[2]  = "8";
        $koleng[3]  = "40";
        $koleng[4]  = "15";
        $koleng[5]  = "25";
        $koleng[6]  = "20";
        $koleng[7]  = "20";
        $koleng[8]  = "20";
        $koleng[9]  = "21";
        $koleng[10] = "21";
        $koleng[11] = "21";
        $koleng[12] = "21";
        $koleng[13] = "21";
        $koleng[14] = "25";

        $gt_pokok_sukarela_kredit = 0;
        $gt_simp_pokok            = 0;
        $gt_simp_wajib            = 0;
        $gt_simp_sukarela         = 0;
        $gt_jml_toko              = 0;
        $gt_jml_elektro           = 0;
        $gt_jml_motor             = 0;
        $gt_jml_bangunan          = 0;
        $gt_jml_pj_uang           = 0;
        $gt_jml_total             = 0;

        $select_daftar_potga = "no_ang, no_peg, nm_ang, nm_dep,
            SUM(if(kd_potga in ('11', '111'), if(kd_prsh = 'P02', 0, jumlah), 0)) simp_pokok,
            SUM(if(kd_potga in ('1'), if(kd_prsh = 'P02', 0, jml_wajib), 0)) simp_wajib,
            SUM(if(kd_potga in ('1'), if(kd_prsh = 'P02', 0, jml_sukarela), 0)) simp_sukarela,
            SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
            SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
            SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
            SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
            SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang";

        $this->db->select($select_daftar_potga)
            ->like("tgl_potga", ($data_req['tahun'] . "-" . $data_req['bulan']), "after")
            ->where("kd_prsh", $data_req['kd_prsh'])
            ->where("is_pensiun", "0")
            ->where("is_pot_bonus", "0")
            ->group_by("no_ang");

        if ($data_req['kd_prsh'] == "P01") {
            $this->db->order_by("nm_dep, no_ang");
        }

        $data_daftar_potga = $this->db->get("t_potga")->result_array();

        // $query_daftar_potga = "SELECT no_ang, no_peg, nm_ang, nm_dep,
        //         SUM(if(kd_potga in ('11', '111'), jumlah, 0)) simp_pokok,
        //         SUM(if(kd_potga in ('1'), jml_wajib, 0)) simp_wajib,
        //         SUM(if(kd_potga in ('1'), jml_sukarela, 0)) simp_sukarela,
        //         SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
        //         SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
        //         SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
        //         SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
        //         SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang
        //     FROM t_potga
        //     WHERE tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'
        //     AND kd_prsh = '" . $data_req['kd_prsh'] . "'
        //     AND is_pensiun = '0'
        //     AND is_pot_bonus = '0'
        //     GROUP BY no_ang
        //     ORDER BY nm_dep, no_ang";

        // $data_daftar_potga = $this->db->query($query_daftar_potga)->result_array();

        $this->db->select("nm_dep")
            ->like("tgl_potga", ($data_req['tahun'] . "-" . $data_req['bulan']), "after")
            ->where("kd_prsh", $data_req['kd_prsh'])
            ->where("is_pensiun", 0)
            ->group_by("nm_dep");

        if ($data_req['kd_prsh'] != "P01") {
            $this->db->limit("1");
        }

        $data_dep = $this->db->get("t_potga");

        foreach ($data_dep->result_array() as $key => $value) {
            $pdf->AddPage("P");

            $pdf->SetFontSize('11');

            $pdf->Cell(0, 0, "Daftar Potongan Gaji Anggota K3PG", 0, 0, "C");
            $pdf->SetFontSize('9');

            $pdf->Ln();

            $array_bln  = array_bulan();
            $nama_bulan = $array_bln[$data_req['bulan']];

            $pdf->Cell(0, 0, "Perusahaan : " . $data_req['nm_prsh'], 0, 0, "C");
            $pdf->Ln();

            $pdf->Cell(0, 0, "Periode : " . $nama_bulan . " " . $data_req['tahun'], 0, 0, "C");

            $pdf->Ln();
            $pdf->Ln();

            if ($data_req['kd_prsh'] == "P01") {
                $nm_dep = $value['nm_dep'];
            } else {
                $nm_dep = $data_req['nm_prsh'];
            }

            $pdf->Cell(0, 0, $nm_dep, "", 0, "L");
            $pdf->Ln();

            $pdf->SetFontSize('8');

            $pdf->Cell($koleng[1], 0, "NO.", "TLR", 0, "C", 0, 0, 1);
            $pdf->Cell($koleng[2], 0, "NAK", "TLR", 0, "C");
            $pdf->Cell($koleng[3], 0, "NAMA", "TLR", 0, "C", 0, 0, 1);
            $pdf->Cell($koleng[4], 0, "NIK", "TLR", 0, "C", 0, 0, 1);
            $pdf->Cell($koleng[5], 0, "Jml S. Pokok, S.Sukarela", "TLR", 0, "C", 0, 0, 1);
            $pdf->Cell($koleng[6] + $koleng[7] + $koleng[8], 0, "SIMPANAN", 1, 0, "C");
            $pdf->Cell($koleng[9] + $koleng[10] + $koleng[11] + $koleng[12] + $koleng[13], 0, "PEMBELIAN/PINJAMAN", 1, 0, "C");
            $pdf->Cell($koleng[14], 0, "Total", "TLR", 0, "C");

            $pdf->Ln();

            $pdf->Cell($koleng[1], 0, "", "BLR", 0, "C");
            $pdf->Cell($koleng[2], 0, "", "BLR", 0, "C");
            $pdf->Cell($koleng[3], 0, "ANGGOTA", "BLR", 0, "C", 0, 0, 1);
            $pdf->Cell($koleng[4], 0, "", "BLR", 0, "C");
            $pdf->Cell($koleng[5], 0, "PEMBELIAN", "BLR", 0, "C");
            $pdf->Cell($koleng[6], 0, "POKOK", "BLR", 0, "C");
            $pdf->Cell($koleng[7], 0, "WAJIB", "BLR", 0, "C");
            $pdf->Cell($koleng[8], 0, "SUKARELA", "BLR", 0, "C");
            $pdf->Cell($koleng[9], 0, "BLJ TOKO", "BLR", 0, "C");
            $pdf->Cell($koleng[10], 0, "ELEKTRO", "BLR", 0, "C");
            $pdf->Cell($koleng[11], 0, "S. MOTOR", "BLR", 0, "C");
            $pdf->Cell($koleng[12], 0, "BANGUNAN", "BLR", 0, "C");
            $pdf->Cell($koleng[13], 0, "UANG", "BLR", 0, "C");
            $pdf->Cell($koleng[14], 0, "", "BLR", 0, "C");

            $pdf->Ln();

            $no                      = 1;
            $t_pokok_sukarela_kredit = 0;
            $t_simp_pokok            = 0;
            $t_simp_wajib            = 0;
            $t_simp_sukarela         = 0;
            $t_jml_toko              = 0;
            $t_jml_elektro           = 0;
            $t_jml_motor             = 0;
            $t_jml_bangunan          = 0;
            $t_jml_pj_uang           = 0;
            $t_jml_total             = 0;

            foreach ($data_daftar_potga as $key1 => $value1) {
                if ((($value['nm_dep'] == $value1['nm_dep']) and ($data_req['kd_prsh'] == "P01")) or $data_req['kd_prsh'] != "P01") {
                    if ($pdf->GetY() > 311) {
                        $pdf->Cell(0, 0, $nm_dep, "", 0, "L");
                        $pdf->Ln();

                        $pdf->Cell($koleng[1], 0, "NO.", "TLR", 0, "C", 0, 0, 1);
                        $pdf->Cell($koleng[2], 0, "NAK", "TLR", 0, "C");
                        $pdf->Cell($koleng[3], 0, "NAMA", "TLR", 0, "C", 0, 0, 1);
                        $pdf->Cell($koleng[4], 0, "NIK", "TLR", 0, "C", 0, 0, 1);
                        $pdf->Cell($koleng[5], 0, "Jml S. Pokok, S.Sukarela", "TLR", 0, "C", 0, 0, 1);
                        $pdf->Cell($koleng[6] + $koleng[7] + $koleng[8], 0, "SIMPANAN", 1, 0, "C");
                        $pdf->Cell($koleng[9] + $koleng[10] + $koleng[11] + $koleng[12] + $koleng[13], 0, "PEMBELIAN/PINJAMAN", 1, 0, "C");
                        $pdf->Cell($koleng[14], 0, "Total", "TLR", 0, "C");

                        $pdf->Ln();

                        $pdf->Cell($koleng[1], 0, "", "BLR", 0, "C");
                        $pdf->Cell($koleng[2], 0, "", "BLR", 0, "C");
                        $pdf->Cell($koleng[3], 0, "ANGGOTA", "BLR", 0, "C", 0, 0, 1);
                        $pdf->Cell($koleng[4], 0, "", "BLR", 0, "C");
                        $pdf->Cell($koleng[5], 0, "PEMBELIAN", "BLR", 0, "C");
                        $pdf->Cell($koleng[6], 0, "POKOK", "BLR", 0, "C");
                        $pdf->Cell($koleng[7], 0, "WAJIB", "BLR", 0, "C");
                        $pdf->Cell($koleng[8], 0, "SUKARELA", "BLR", 0, "C");
                        $pdf->Cell($koleng[9], 0, "BLJ TOKO", "BLR", 0, "C");
                        $pdf->Cell($koleng[10], 0, "ELEKTRO", "BLR", 0, "C");
                        $pdf->Cell($koleng[11], 0, "S. MOTOR", "BLR", 0, "C");
                        $pdf->Cell($koleng[12], 0, "BANGUNAN", "BLR", 0, "C");
                        $pdf->Cell($koleng[13], 0, "UANG", "BLR", 0, "C");
                        $pdf->Cell($koleng[14], 0, "", "BLR", 0, "C");

                        $pdf->Ln();
                    }

                    $jml_simpanan              = $value1['simp_pokok'] + $value1['simp_wajib'] + $value1['simp_sukarela'];
                    $jml_kredit                = $value1['jml_blj_toko'] + $value1['jml_elektronik'] + $value1['jml_motor'] + $value1['jml_bangunan'] + $value1['jml_pj_uang'];
                    $jml_pokok_sukarela_kredit = ($value1['simp_pokok'] + $value1['simp_sukarela'] + $jml_kredit);
                    $jml_total                 = $jml_simpanan + $jml_kredit;

                    $pdf->Cell($koleng[1], 0, $no, "", 0, "R");
                    $pdf->Cell($koleng[2], 0, $value1['no_ang'], "", 0, "L", 0, 0, 1);
                    $pdf->Cell($koleng[3], 0, $value1['nm_ang'], "", 0, "L", 0, 0, 1);
                    $pdf->Cell($koleng[4], 0, $value1['no_peg'], "", 0, "L", 0, 0, 1);
                    $pdf->Cell($koleng[5], 0, number_format($jml_pokok_sukarela_kredit, 2), "", 0, "R", 0, 0, 1);
                    $pdf->Cell($koleng[6], 0, number_format($value1['simp_pokok'], 2), "", 0, "R", 0, 0, 1);
                    $pdf->Cell($koleng[7], 0, number_format($value1['simp_wajib'], 2), "", 0, "R", 0, 0, 1);
                    $pdf->Cell($koleng[8], 0, number_format($value1['simp_sukarela'], 2), "", 0, "R", 0, 0, 1);
                    $pdf->Cell($koleng[9], 0, number_format($value1['jml_blj_toko'], 2), "", 0, "R", 0, 0, 1);
                    $pdf->Cell($koleng[10], 0, number_format($value1['jml_elektronik'], 2), "", 0, "R", 0, 0, 1);
                    $pdf->Cell($koleng[11], 0, number_format($value1['jml_motor'], 2), "", 0, "R", 0, 0, 1);
                    $pdf->Cell($koleng[12], 0, number_format($value1['jml_bangunan'], 2), "", 0, "R", 0, 0, 1);
                    $pdf->Cell($koleng[13], 0, number_format($value1['jml_pj_uang'], 2), "", 0, "R", 0, 0, 1);
                    $pdf->Cell($koleng[14], 0, number_format($jml_total, 2), "", 0, "R", 0, 0, 1);

                    $pdf->Ln();

                    $no++;
                    $t_pokok_sukarela_kredit += $jml_pokok_sukarela_kredit;
                    $t_simp_pokok += $value1['simp_pokok'];
                    $t_simp_wajib += $value1['simp_wajib'];
                    $t_simp_sukarela += $value1['simp_sukarela'];
                    $t_jml_toko += $value1['jml_blj_toko'];
                    $t_jml_elektro += $value1['jml_elektronik'];
                    $t_jml_motor += $value1['jml_motor'];
                    $t_jml_bangunan += $value1['jml_bangunan'];
                    $t_jml_pj_uang += $value1['jml_pj_uang'];
                    $t_jml_total += $jml_total;

                    $gt_pokok_sukarela_kredit += $jml_pokok_sukarela_kredit;
                    $gt_simp_pokok += $value1['simp_pokok'];
                    $gt_simp_wajib += $value1['simp_wajib'];
                    $gt_simp_sukarela += $value1['simp_sukarela'];
                    $gt_jml_toko += $value1['jml_blj_toko'];
                    $gt_jml_elektro += $value1['jml_elektronik'];
                    $gt_jml_motor += $value1['jml_motor'];
                    $gt_jml_bangunan += $value1['jml_bangunan'];
                    $gt_jml_pj_uang += $value1['jml_pj_uang'];
                    $gt_jml_total += $jml_total;

                    unset($data_daftar_potga[$key1]);
                }
            }

            $pdf->Ln();

            $pdf->Cell($koleng[1] + $koleng[2] + $koleng[3] + $koleng[4], 0, "Sub Total", "TB", 0, "L");
            $pdf->Cell($koleng[5], 0, number_format($t_pokok_sukarela_kredit, 2), "TB", 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[6], 0, number_format($t_simp_pokok, 2), "TB", 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[7], 0, number_format($t_simp_wajib, 2), "TB", 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[8], 0, number_format($t_simp_sukarela, 2), "TB", 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[9], 0, number_format($t_jml_toko, 2), "TB", 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[10], 0, number_format($t_jml_elektro, 2), "TB", 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[11], 0, number_format($t_jml_motor, 2), "TB", 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[12], 0, number_format($t_jml_bangunan, 2), "TB", 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[13], 0, number_format($t_jml_pj_uang, 2), "TB", 0, "R", 0, 0, 1);
            $pdf->Cell($koleng[14], 0, number_format($t_jml_total, 2), "TB", 0, "R", 0, 0, 1);
        }

        $pdf->AddPage("P");

        $pdf->SetFontSize('11');

        $pdf->Cell(0, 0, "Daftar Potongan Gaji Anggota K3PG", 0, 0, "C");
        $pdf->SetFontSize('9');

        $pdf->Ln();

        $array_bln  = array_bulan();
        $nama_bulan = $array_bln[$data_req['bulan']];

        $pdf->Cell(0, 0, "Perusahaan : " . $data_req['nm_prsh'], 0, 0, "C");
        $pdf->Ln();

        $pdf->Cell(0, 0, "Periode : " . $nama_bulan . " " . $data_req['tahun'], 0, 0, "C");

        $pdf->Ln();
        $pdf->Ln();

        $pdf->Cell($koleng[1], 0, "NO.", "TLR", 0, "C", 0, 0, 1);
        $pdf->Cell($koleng[2], 0, "NAK", "TLR", 0, "C");
        $pdf->Cell($koleng[3], 0, "NAMA", "TLR", 0, "C", 0, 0, 1);
        $pdf->Cell($koleng[4], 0, "NIK", "TLR", 0, "C", 0, 0, 1);
        $pdf->Cell($koleng[5], 0, "Jml S. Pokok, S.Sukarela", "TLR", 0, "C", 0, 0, 1);
        $pdf->Cell($koleng[6] + $koleng[7] + $koleng[8], 0, "SIMPANAN", 1, 0, "C");
        $pdf->Cell($koleng[9] + $koleng[10] + $koleng[11] + $koleng[12] + $koleng[13], 0, "PEMBELIAN/PINJAMAN", 1, 0, "C");
        $pdf->Cell($koleng[14], 0, "Total", "TLR", 0, "C");

        $pdf->Ln();

        $pdf->Cell($koleng[1], 0, "", "BLR", 0, "C");
        $pdf->Cell($koleng[2], 0, "", "BLR", 0, "C");
        $pdf->Cell($koleng[3], 0, "ANGGOTA", "BLR", 0, "C", 0, 0, 1);
        $pdf->Cell($koleng[4], 0, "", "BLR", 0, "C");
        $pdf->Cell($koleng[5], 0, "PEMBELIAN", "BLR", 0, "C");
        $pdf->Cell($koleng[6], 0, "POKOK", "BLR", 0, "C");
        $pdf->Cell($koleng[7], 0, "WAJIB", "BLR", 0, "C");
        $pdf->Cell($koleng[8], 0, "SUKARELA", "BLR", 0, "C");
        $pdf->Cell($koleng[9], 0, "BLJ TOKO", "BLR", 0, "C");
        $pdf->Cell($koleng[10], 0, "ELEKTRO", "BLR", 0, "C");
        $pdf->Cell($koleng[11], 0, "S. MOTOR", "BLR", 0, "C");
        $pdf->Cell($koleng[12], 0, "BANGUNAN", "BLR", 0, "C");
        $pdf->Cell($koleng[13], 0, "UANG", "BLR", 0, "C");
        $pdf->Cell($koleng[14], 0, "", "BLR", 0, "C");

        $pdf->Ln();
        $pdf->Ln();

        $pdf->Cell($koleng[1] + $koleng[2] + $koleng[3] + $koleng[4], 0, "Grand Total", "TB", 0, "L");
        $pdf->Cell($koleng[5], 0, number_format($gt_pokok_sukarela_kredit, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[6], 0, number_format($gt_simp_pokok, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[7], 0, number_format($gt_simp_wajib, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[8], 0, number_format($gt_simp_sukarela, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[9], 0, number_format($gt_jml_toko, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[10], 0, number_format($gt_jml_elektro, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[11], 0, number_format($gt_jml_motor, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[12], 0, number_format($gt_jml_bangunan, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[13], 0, number_format($gt_jml_pj_uang, 2), "TB", 0, "R", 0, 0, 1);
        $pdf->Cell($koleng[14], 0, number_format($gt_jml_total, 2), "TB", 0, "R", 0, 0, 1);

        $pdf->Output($judul_file, 'I');
    }

    public function excel_rincian_potga()
    {
        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $fileName = "rincianPotga" . $data_req['bulan'] . $data_req['tahun'] . $data_req['nm_prsh'] . ".xls";

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");

        $array_bln  = array_bulan();
        $nama_bulan = $array_bln[$data_req['bulan']];

        $view = "<center>
            Daftar Potongan Gaji Anggota K3PG
            <br>
            Perusahaan : " . $data_req['nm_prsh'] . "
            <br>
            Periode : " . $nama_bulan . " " . $data_req['tahun'] . "
        </center>
        <br>
        <br>";

        $gt_pokok_sukarela_kredit = 0;
        $gt_simp_pokok            = 0;
        $gt_simp_wajib            = 0;
        $gt_simp_sukarela         = 0;
        $gt_jml_toko              = 0;
        $gt_jml_elektro           = 0;
        $gt_jml_motor             = 0;
        $gt_jml_bangunan          = 0;
        $gt_jml_pj_uang           = 0;
        $gt_jml_total             = 0;

        $select_daftar_potga = "no_ang, no_peg, nm_ang, nm_dep,
            SUM(if(kd_potga in ('11', '111'), if(kd_prsh = 'P02', 0, jumlah), 0)) simp_pokok,
            SUM(if(kd_potga in ('1'), if(kd_prsh = 'P02', 0, jml_wajib), 0)) simp_wajib,
            SUM(if(kd_potga in ('1'), if(kd_prsh = 'P02', 0, jml_sukarela), 0)) simp_sukarela,
            SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
            SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
            SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
            SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
            SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang";

        $this->db->select($select_daftar_potga)
            ->like("tgl_potga", ($data_req['tahun'] . "-" . $data_req['bulan']), "after")
            ->where("kd_prsh", $data_req['kd_prsh'])
            ->where("is_pensiun", "0")
            ->where("is_pot_bonus", "0")
            ->group_by("no_ang");

        if ($data_req['kd_prsh'] == "P01") {
            $this->db->order_by("nm_dep, no_ang");
        }

        $data_daftar_potga = $this->db->get("t_potga")->result_array();

        $this->db->select("nm_dep")
            ->like("tgl_potga", ($data_req['tahun'] . "-" . $data_req['bulan']), "after")
            ->where("kd_prsh", $data_req['kd_prsh'])
            ->where("is_pensiun", 0)
            ->group_by("nm_dep");

        if ($data_req['kd_prsh'] != "P01") {
            $this->db->limit("1");
        }

        $data_dep = $this->db->get("t_potga");

        foreach ($data_dep->result_array() as $key => $value) {
            $no                      = 1;
            $t_pokok_sukarela_kredit = 0;
            $t_simp_pokok            = 0;
            $t_simp_wajib            = 0;
            $t_simp_sukarela         = 0;
            $t_jml_toko              = 0;
            $t_jml_elektro           = 0;
            $t_jml_motor             = 0;
            $t_jml_bangunan          = 0;
            $t_jml_pj_uang           = 0;
            $t_jml_total             = 0;

            if ($data_req['kd_prsh'] == "P01") {
                $nm_dep = $value['nm_dep'];
            } else {
                $nm_dep = $data_req['nm_prsh'];
            }

            $view .= "<span>" . $nm_dep . "</span>";

            $view .= "<table width=\"100%\" border=\"1\">
                <thead>
                    <tr>
                        <th rowspan=\"2\">No.</th>
                        <th rowspan=\"2\">NAK</th>
                        <th rowspan=\"2\">NAMA</th>
                        <th rowspan=\"2\">NIK</th>
                        <th rowspan=\"2\">Jml S. Pokok+S.Sukarela+PEMBELIAN</th>
                        <th colspan=\"3\">SIMPANAN</th>
                        <th colspan=\"5\">PEMBELIAN/PINJAMAN</th>
                        <th rowspan=\"2\">TOTAL</th>
                    </tr>
                    <tr>
                        <th>POKOK</th>
                        <th>WAJIB</th>
                        <th>SUKARELA</th>
                        <th>BLJ TOKO</th>
                        <th>ELEKTRO</th>
                        <th>S. MOTOR</th>
                        <th>BANGUNAN</th>
                        <th>UANG</th>
                    </tr>
                </thead>
                <tbody>";

            foreach ($data_daftar_potga as $key1 => $value1) {
                if ((($value['nm_dep'] == $value1['nm_dep']) and ($data_req['kd_prsh'] == "P01")) or $data_req['kd_prsh'] != "P01") {
                    $jml_simpanan              = $value1['simp_pokok'] + $value1['simp_wajib'] + $value1['simp_sukarela'];
                    $jml_kredit                = $value1['jml_blj_toko'] + $value1['jml_elektronik'] + $value1['jml_motor'] + $value1['jml_bangunan'] + $value1['jml_pj_uang'];
                    $jml_pokok_sukarela_kredit = ($value1['simp_pokok'] + $value1['simp_sukarela'] + $jml_kredit);
                    $jml_total                 = $jml_simpanan + $jml_kredit;

                    $view .= "<tr>
                        <td style=\"text-align: right\">" . $no . "</td>
                        <td>" . $value1['no_ang'] . "</td>
                        <td>" . $value1['nm_ang'] . "</td>
                        <td>" . $value1['no_peg'] . "</td>
                        <td style=\"text-align: right\">" . number_format($jml_pokok_sukarela_kredit, 2) . "</td>
                        <td style=\"text-align: right\">" . number_format($value1['simp_pokok'], 2) . "</td>
                        <td style=\"text-align: right\">" . number_format($value1['simp_wajib'], 2) . "</td>
                        <td style=\"text-align: right\">" . number_format($value1['simp_sukarela'], 2) . "</td>
                        <td style=\"text-align: right\">" . number_format($value1['jml_blj_toko'], 2) . "</td>
                        <td style=\"text-align: right\">" . number_format($value1['jml_elektronik'], 2) . "</td>
                        <td style=\"text-align: right\">" . number_format($value1['jml_motor'], 2) . "</td>
                        <td style=\"text-align: right\">" . number_format($value1['jml_bangunan'], 2) . "</td>
                        <td style=\"text-align: right\">" . number_format($value1['jml_pj_uang'], 2) . "</td>
                        <td style=\"text-align: right\">" . number_format($jml_total, 2) . "</td>
                    </tr>
                    ";

                    $no++;
                    $t_pokok_sukarela_kredit += $jml_pokok_sukarela_kredit;
                    $t_simp_pokok += $value1['simp_pokok'];
                    $t_simp_wajib += $value1['simp_wajib'];
                    $t_simp_sukarela += $value1['simp_sukarela'];
                    $t_jml_toko += $value1['jml_blj_toko'];
                    $t_jml_elektro += $value1['jml_elektronik'];
                    $t_jml_motor += $value1['jml_motor'];
                    $t_jml_bangunan += $value1['jml_bangunan'];
                    $t_jml_pj_uang += $value1['jml_pj_uang'];
                    $t_jml_total += $jml_total;

                    $gt_pokok_sukarela_kredit += $jml_pokok_sukarela_kredit;
                    $gt_simp_pokok += $value1['simp_pokok'];
                    $gt_simp_wajib += $value1['simp_wajib'];
                    $gt_simp_sukarela += $value1['simp_sukarela'];
                    $gt_jml_toko += $value1['jml_blj_toko'];
                    $gt_jml_elektro += $value1['jml_elektronik'];
                    $gt_jml_motor += $value1['jml_motor'];
                    $gt_jml_bangunan += $value1['jml_bangunan'];
                    $gt_jml_pj_uang += $value1['jml_pj_uang'];
                    $gt_jml_total += $jml_total;

                    unset($data_daftar_potga[$key1]);
                }
            }

            $view .= "</tbody>
                <thead>
                    <tr>
                        <th colspan=\"4\">Sub Total</th>
                        <th style=\"text-align: right\">" . number_format($t_pokok_sukarela_kredit, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($t_simp_pokok, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($t_simp_wajib, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($t_simp_sukarela, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($t_jml_toko, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($t_jml_elektro, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($t_jml_motor, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($t_jml_bangunan, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($t_jml_pj_uang, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($t_jml_total, 2) . "</th>
                    </tr>
                </thead>
            </table>
            <br>
            ";
        }

        $view .= "
            <table width=\"100%\" border=\"1\">
                <thead>
                    <tr>
                        <th colspan=\"4\">Grand Total</th>
                        <th style=\"text-align: right\">" . number_format($gt_pokok_sukarela_kredit, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($gt_simp_pokok, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($gt_simp_wajib, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($gt_simp_sukarela, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($gt_jml_toko, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($gt_jml_elektro, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($gt_jml_motor, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($gt_jml_bangunan, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($gt_jml_pj_uang, 2) . "</th>
                        <th style=\"text-align: right\">" . number_format($gt_jml_total, 2) . "</th>
                    </tr>
                </thead>
            </table>
        ";

        echo $view;
    }

    public function cetak_slip_potga()
    {
        set_time_limit(0);

        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = 'letter';
        // $ukuran_kertas = array(210, 280);

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_slip_potga_" . $data_req['nm_prsh'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

        $pdf->SetCreator($kreator);
        $pdf->SetAuthor($kreator);
        $pdf->SetTitle($judul_file);

        $pdf->SetHeaderData("", "", $judul_header, $teks_header, "", "");
        $pdf->setFooterData("", "");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins("5", "5", "5");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $array_bln  = array_bulan();
        $nama_bulan = $array_bln[$data_req['bulan']];

        $blth = $data_req['tahun'] . "-" . $data_req['bulan'];

        if ($data_req['no_ang'] != "" and (isset($data_req['no_ang_akhir']) and $data_req['no_ang_akhir'] != "")) {
            $this->db->where("no_ang between '" . $data_req['no_ang'] . "' and '" . $data_req['no_ang_akhir'] . "'");
        } else if ($data_req['no_ang'] != "") {
            $this->db->where("no_ang", $data_req['no_ang']);
        }

        if (isset($data_req['kd_prsh']) and $data_req['kd_prsh'] != "") {
            $this->db->where("kd_prsh", $data_req['kd_prsh']);
        }

        if (isset($data_req['kd_prsh']) and $data_req['kd_prsh'] == "P01") {
            $this->db->order_by("nm_dep, no_ang");
        }

        if (isset($data_req['kd_prsh']) and $data_req['kd_prsh'] == "P02") {
            $this->db->where_not_in("kd_potga", array("11", "111"));
        }

        $this->db->select("*, sum(if(kd_prsh in ('P01', 'P02') and kd_potga = '1', (0+jml_sukarela), jumlah)) jml_potga")
            ->like("tgl_potga", $blth, "after")
            ->where("is_pensiun", "0")
            ->where("is_pot_bonus", "0")
            ->group_by("no_ang")
            ->having("jml_potga > 0");

        $data_anggota = $this->db->get("t_potga");

        if ($data_req['no_ang'] != "" and (isset($data_req['no_ang_akhir']) and $data_req['no_ang_akhir'] != "")) {
            $this->db->where("no_ang between '" . $data_req['no_ang'] . "' and '" . $data_req['no_ang_akhir'] . "'");
        } else if ($data_req['no_ang'] != "") {
            $this->db->where("no_ang", $data_req['no_ang']);
        }

        if (isset($data_req['kd_prsh']) and $data_req['kd_prsh'] != "") {
            $this->db->where("kd_prsh", $data_req['kd_prsh']);
        }

        if (isset($data_req['kd_prsh']) and $data_req['kd_prsh'] == "P02") {
            $this->db->where_not_in("kd_potga", array("11", "111"));
        }

        $data_potga = $this->db->select("*, (tempo_bln - angs_ke) sisa_angs")
            ->like("tgl_potga", $blth, "after")
            ->where("is_pensiun", "0")
            ->where("is_pot_bonus", "0")
            ->order_by("no_ang, tgl_rilis")
            ->get("t_potga")->result_array();

        if ($data_req['no_ang'] != "" and (isset($data_req['no_ang_akhir']) and $data_req['no_ang_akhir'] != "")) {
            $this->db->where("no_ang between '" . $data_req['no_ang'] . "' and '" . $data_req['no_ang_akhir'] . "'");
        } else if ($data_req['no_ang'] != "") {
            $this->db->where("no_ang", $data_req['no_ang']);
        }

        $dataMasterAnggota = $this->db->where("sts_instansi", "0")
            ->where("status_keluar", "0")
            ->get("t_anggota")->result_array();

        $arrPlafonAnggota = array();

        foreach ($dataMasterAnggota as $key => $value) {
            $arrPlafonAnggota[$value['no_ang']] = $value['plafon'];
        }

        $halaman = 1;

        foreach ($data_anggota->result_array() as $key => $value) {
            if (($halaman % 2) != 0) {
                $pdf->AddPage();
            } else {
                $pdf->SetY("145");
            }

            $pdf->Ln(5);

            $pdf->SetFontSize('11');

            $pdf->Cell(0, 0, $judul_header, 0, 0, "C");
            $pdf->SetFontSize('9');

            $pdf->Ln();

            $pdf->Cell(0, 0, "Periode : " . $nama_bulan . " " . $data_req['tahun'], 0, 0, "C");

            $pdf->Ln();
            $pdf->Ln();

            $pdf->Cell(15, 0, "NIK/NAK", "T", 0, "L");
            $pdf->Cell(70, 0, ": " . $value['no_peg'] . " / " . $value['no_ang'], "T", 0, "L");
            $pdf->Cell(30, 0, "DEPT/BIRO", "T", 0, "L");
            $pdf->Cell(85, 0, ": " . $value['nm_dep'], "T", 0, "L", 0, 0, 1);

            $pdf->Ln();

            $pdf->Cell(15, 0, "NAMA", 0, 0, "L");
            $pdf->Cell(70, 0, ": " . $value['nm_ang'], 0, 0, "L", 0, 0, 1);
            $pdf->Cell(30, 0, "BAGIAN", 0, 0, "L");
            $pdf->Cell(85, 0, ": " . $value['nm_bagian'], 0, 0, "L", 0, 0, 1);

            $pdf->Ln();
            $pdf->Ln();

            $lebar[1] = 20;
            $lebar[2] = 80;
            $lebar[3] = 40;
            $lebar[4] = 15;
            $lebar[5] = 20;
            $lebar[6] = 25;

            $pdf->Cell($lebar[1], 0, "TANGGAL", "TB", 0, "L");
            $pdf->Cell($lebar[2], 0, "KETERANGAN", "TB", 0, "L");
            $pdf->Cell($lebar[3], 0, "PERBULAN", "TB", 0, "C");
            $pdf->Cell($lebar[4], 0, "MASA", "TB", 0, "C");
            $pdf->Cell($lebar[5], 0, "ANGS. KE", "TB", 0, "C");
            $pdf->Cell($lebar[6], 0, "SISA", "TB", 0, "C");

            $pdf->Ln();

            $no       = 1;
            $t_jumlah = 0;

            $jml_wajib      = 0;
            $jml_sukarela   = 0;
            $jml_blj_kredit = 0;

            foreach ($data_potga as $key1 => $value1) {
                if ($value1['no_ang'] == $value['no_ang']) {
                    if ($value1['kd_potga'] == "1") {
                        $jml_wajib = $value1['jml_wajib'];

                        if (in_array($data_req['kd_prsh'], array("P01", "P02"))) {
                            $jml_wajib = 0;
                        }

                        $jml_sukarela = $value1['jml_sukarela'];
                        continue;
                    }

                    if ($value1['kd_potga'] == "31" or ($value1['kd_potga'] == "3" and $value1['tempo_bln'] == "1")) {
                        $jml_blj_kredit += $value1['jumlah'];
                        continue;
                    }

                    $pdf->Cell($lebar[1], 0, balik_tanggal($value1['tgl_rilis']), 0, 0, "L");
                    $pdf->Cell($lebar[2], 0, $value1['ket'], 0, 0, "L", 0, 0, 1);
                    $pdf->Cell($lebar[3], 0, number_format($value1['jumlah'], 2), 0, 0, "R");
                    $pdf->Cell($lebar[4], 0, $value1['tempo_bln'], 0, 0, "C");
                    $pdf->Cell($lebar[5], 0, $value1['angs_ke'], 0, 0, "C");
                    $pdf->Cell($lebar[6], 0, $value1['sisa_angs'], 0, 0, "C");

                    $pdf->Ln();

                    $t_jumlah += $value1['jumlah'];

                    unset($data_potga[$key1]);
                }
            }

            $pdf->Ln();

            $pdf->Cell($lebar[1] + $lebar[2], 0, "Potongan Kredit Angsuran :", "T", 0, "R");
            $pdf->Cell($lebar[3], 0, number_format($t_jumlah, 2), "T", 0, "R");
            $pdf->Cell($lebar[4] + $lebar[5], 0, "Simp. Wajib :", "T", 0, "R");
            $pdf->Cell($lebar[6], 0, number_format($jml_wajib, 2), "T", 0, "R");

            $pdf->Ln();

            $pdf->Cell($lebar[1] + $lebar[2], 0, "Jumlah Belanja Kredit :", "B", 0, "R");
            $pdf->Cell($lebar[3], 0, number_format($jml_blj_kredit, 2), "B", 0, "R");
            $pdf->Cell($lebar[4] + $lebar[5], 0, "Simp. Sukarela :", "B", 0, "R");
            $pdf->Cell($lebar[6], 0, number_format($jml_sukarela, 2), "B", 0, "R");

            $pdf->Ln();
            $pdf->Ln();

            $total_all          = $t_jumlah + $jml_wajib + $jml_sukarela + $jml_blj_kredit;
            $totalPinjamBelanja = $t_jumlah + $jml_blj_kredit;

            $totalPlafon = isset($arrPlafonAnggota[$value['no_ang']]) ? $arrPlafonAnggota[$value['no_ang']] : 0;
            $sisaPlafon  = $totalPlafon - $totalPinjamBelanja;

            $pdf->Cell($lebar[1] + $lebar[2], 0, "Total Potongan :", 0, 0, "R");
            $pdf->Cell($lebar[3], 0, number_format($total_all, 2), 0, 0, "R");
            $pdf->Cell($lebar[4] + $lebar[5], 0, "Sisa Plafon :", 0, 0, "R");
            $pdf->Cell($lebar[6], 0, number_format($sisaPlafon, 2), 0, 0, "R");

            $halaman++;
        }

        $pdf->Output($judul_file, 'I');
    }

    public function tampilkan_potga_nak()
    {
        set_time_limit(0);

        $data_req = get_request();

        $blth = $data_req['tahun'] . "-" . $data_req['bulan'];

        if ($data_req['no_ang'] != "" and (isset($data_req['no_ang_akhir']) and $data_req['no_ang_akhir'] != "")) {
            $this->db->where("no_ang between '" . $data_req['no_ang'] . "' and '" . $data_req['no_ang_akhir'] . "'");
        } else if ($data_req['no_ang'] != "") {
            $this->db->where("no_ang", $data_req['no_ang']);
        }

        // if (isset($data_req['kd_prsh']) and $data_req['kd_prsh'] != "") {
        //     $this->db->where("kd_prsh", $data_req['kd_prsh']);
        // }

        $data_potga = $this->db->select("*, (tempo_bln - angs_ke) sisa_angs")
            ->like("tgl_potga", $blth, "after")
            ->where("is_pensiun", "0")
            ->where("is_pot_bonus", "0")
            ->order_by("no_ang, tgl_rilis")
            ->get("t_potga")->result_array();

        $dataMasterAnggota = $this->db->where("sts_instansi", "0")
            ->where("status_keluar", "0")
            ->where("no_ang", $data_req['no_ang'])
            ->get("t_anggota");

        $view = "<table class=\"table table-bordered table-condensed table-striped\">
                <thead>
                    <tr>
                        <th>TANGGAL</th>
                        <th>KETERANGAN</th>
                        <th>PERBULAN</th>
                        <th>MASA</th>
                        <th>ANGS. KE</th>
                        <th>SISA</th>
                    </tr>
                </thead>
                <tbody> ";

        $no       = 1;
        $t_jumlah = 0;

        $jml_wajib      = 0;
        $jml_sukarela   = 0;
        $jml_blj_kredit = 0;

        foreach ($data_potga as $key1 => $value1) {
            if ($value1['kd_potga'] == "1") {
                $jml_wajib = $value1['jml_wajib'];

                if (in_array($data_req['kd_prsh'], array("P01", "P02"))) {
                    $jml_wajib = 0;
                }

                $jml_sukarela = $value1['jml_sukarela'];
                continue;
            }

            if ($value1['kd_potga'] == "31" or ($value1['kd_potga'] == "3" and $value1['tempo_bln'] == "1")) {
                $jml_blj_kredit += $value1['jumlah'];
                continue;
            }

            $view .= "<tr>
                    <td>" . balik_tanggal($value1['tgl_rilis']) . "</td>
                    <td>" . $value1['ket'] . "</td>
                    <td class=\"text-right\">" . number_format($value1['jumlah'], 2) . "</td>
                    <td>" . $value1['tempo_bln'] . "</td>
                    <td>" . $value1['angs_ke'] . "</td>
                    <td>" . $value1['sisa_angs'] . "</td>
                </tr>";

            $t_jumlah += $value1['jumlah'];
        }

        $total_all          = $t_jumlah + $jml_wajib + $jml_sukarela + $jml_blj_kredit;
        $totalPinjamBelanja = $t_jumlah + $jml_blj_kredit;

        $totalPlafon = $dataMasterAnggota->num_rows() > 0 ? $dataMasterAnggota->row(0)->plafon : 0;
        $sisaPlafon  = $totalPlafon - $totalPinjamBelanja;

        $view .= "
            <tr>
                <td colspan=\"2\" class=\"text-right\">Potongan Kredit Angsuran :</td>
                <td class=\"text-right\">" . number_format($t_jumlah, 2) . "</td>
                <td colspan=\"2\" class=\"text-right\">Simp. Wajib :</td>
                <td class=\"text-right\">" . number_format($jml_wajib, 2) . "</td>
            </tr>
            <tr>
                <td colspan=\"2\" class=\"text-right\">Jumlah Belanja Kredit :</td>
                <td class=\"text-right\">" . number_format($jml_blj_kredit, 2) . "</td>
                <td colspan=\"2\" class=\"text-right\">Simp. Sukarela :</td>
                <td class=\"text-right\">" . number_format($jml_sukarela, 2) . "</td>
            </tr>
            <tr>
                <td colspan=\"2\" class=\"text-right\">Total Potongan :</td>
                <td class=\"text-right\">" . number_format($total_all, 2) . "</td>
                <td colspan=\"2\" class=\"text-right\">Sisa Plafon :</td>
                <td class=\"text-right\">" . number_format($sisaPlafon, 2) . "</td>
            </tr>
        ";

        $view .= "</tbody>
            </table>";

        echo $view;
    }

    public function jumlah_hari($tanggal1, $tanggal2)
    {
        $datediff = strtotime($tanggal2) - (strtotime($tanggal1));
        return round($datediff / (60 * 60 * 24));
    }

    public function cetak_slip_kkbkpr()
    {
        set_time_limit(0);

        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = 'letter';
        // $ukuran_kertas = array(210, 280);

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_slip_kkbkpr_" . $data_req['nm_prsh'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

        $pdf->SetCreator($kreator);
        $pdf->SetAuthor($kreator);
        $pdf->SetTitle($judul_file);

        $pdf->SetHeaderData("", "", $judul_header, $teks_header, "", "");
        $pdf->setFooterData("", "");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins("5", "5", "5");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $array_bln  = array_bulan();
        $nama_bulan = $array_bln[$data_req['bulan']];

        $blth = $data_req['tahun'] . "-" . $data_req['bulan'];

        $tgl_akhir_skrg  = date("Y-m-t", mktime(0, 0, 0, $data_req['bulan'], 1, $data_req['tahun']));
        $tgl_akhir_depan = date("Y-m-t", mktime(0, 0, 0, $data_req['bulan'] + 1, 1, $data_req['tahun']));

        $hari = $this->jumlah_hari($tgl_akhir_skrg, $tgl_akhir_depan);

        $query_no_ang = "";

        if ($data_req['no_ang'] != "" and $data_req['no_ang_akhir'] != "") {
            $this->db->where("no_ang between '" . $data_req['no_ang'] . "' and '" . $data_req['no_ang_akhir'] . "'");

            $query_no_ang = " and no_ang between '" . $data_req['no_ang'] . "' and '" . $data_req['no_ang_akhir'] . "'";
        } else if ($data_req['no_ang'] != "") {
            $this->db->where("no_ang", $data_req['no_ang']);

            $query_no_ang = " and no_ang = '" . $data_req['no_ang'] . "'";
        }

        $this->db->where("kd_prsh", $data_req['kd_prsh'])
            ->like("tgl_potga", $blth, "after")
            ->where("is_pensiun", "0")
            ->where_in("kd_pinjaman", array("2", "4"))
            ->group_by("no_ang");

        if ($data_req['kd_prsh'] == "P01") {
            $this->db->order_by("nm_dep, no_ang");
        }

        $data_anggota = $this->db->get("t_potga");

        $query_data_potga = "SELECT a.no_ang, a.no_peg, a.nm_ang, b.no_pinjam, b.tgl_pinjam, b.jml_pinjam, b.margin, b.angs_ke, b.kesanggupan_bayar, b.blth_angsuran, b.pokok_awal, b.bunga, b.angsuran, b.jml_potga, b.jml_min_angsuran, b.jml_max_angsuran, b.pokok_akhir
            FROM (
                SELECT *
                FROM t_potga
                WHERE tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'
                AND kd_prsh = '" . $data_req['kd_prsh'] . "'
                AND kd_pinjaman in ('2', '4')
                GROUP BY no_ref_bukti
            ) a
            JOIN (
                SELECT a.no_pinjam, a.tgl_pinjam, a.no_ang, a.nm_ang, a.tempo_bln, a.margin, a.jml_pinjam, a.jml_max_angsuran kesanggupan_bayar, b.angs_ke, b.blth_angsuran, b.pokok_awal, b.bunga, b.angsuran, b.jml_potga, b.jml_min_angsuran, b.jml_max_angsuran, b.pokok_akhir
                FROM t_pinjaman_ang a
                JOIN t_pinjaman_ang_det b
                ON a.no_pinjam=b.no_pinjam
                WHERE b.blth_angsuran = '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                AND a.kd_prsh = '" . $data_req['kd_prsh'] . "'
                AND a.kd_pinjaman in ('2', '4')
                " . $query_no_ang . "
                ORDER BY no_ang, no_pinjam, b.blth_angsuran
            ) b
            ON a.no_ref_bukti=b.no_pinjam
            ORDER BY a.no_ang, b.no_pinjam";

        $data_potga = $this->db->query($query_data_potga)->result_array();

        $halaman = 1;

        foreach ($data_anggota->result_array() as $key => $value) {
            if (($halaman % 2) != 0) {
                $pdf->AddPage();
            } else {
                $pdf->SetY("145");
            }

            $pdf->Ln(5);

            $pdf->SetFontSize('11');

            $pdf->Cell(0, 0, $judul_header, 0, 0, "C");

            $pdf->Ln();

            $pdf->SetFontSize('10');

            $pdf->Cell(0, 0, "BUKTI POTONGAN PINJAMAN BERAGUNAN", 0, 0, "C");
            $pdf->SetFontSize('9');

            $pdf->Ln();

            $pdf->Cell(0, 0, "Periode : " . $nama_bulan . " " . $data_req['tahun'], 0, 0, "C");

            $pdf->Ln();
            $pdf->Ln();

            $pdf->Cell(20, 0, "NIK/NAK", "T", 0, "L");
            $pdf->Cell(70, 0, ": " . $value['no_peg'] . " / " . $value['no_ang'], "T", 0, "L");
            $pdf->Cell(30, 0, "DEPT/BIRO", "T", 0, "L");
            $pdf->Cell(80, 0, ": " . $value['nm_dep'], "T", 0, "L", 0, 0, 1);

            $pdf->Ln();

            $pdf->Cell(20, 0, "NAMA", 0, 0, "L");
            $pdf->Cell(70, 0, ": " . $value['nm_ang'], 0, 0, "L", 0, 0, 1);
            $pdf->Cell(30, 0, "BAGIAN", 0, 0, "L");
            $pdf->Cell(80, 0, ": " . $value['nm_bagian'], 0, 0, "L", 0, 0, 1);

            $pdf->Ln();
            $pdf->Ln();

            $pdf->SetFontSize('8');

            $lebar[1] = 23;
            $lebar[2] = 25;
            $lebar[3] = 15;
            $lebar[4] = 27;
            $lebar[5] = 27;
            $lebar[6] = 25;
            $lebar[7] = 27;
            $lebar[8] = 27;

            $pdf->Cell($lebar[1], 0, "POKOK", "T", 0, "C");
            $pdf->Cell($lebar[2], 0, "KESANGGUPAN", "T", 0, "C");
            $pdf->Cell($lebar[3], 0, "BULAN", "T", 0, "C", 0, 0, 1);
            $pdf->Cell($lebar[4] + $lebar[5], 0, "SALDO", "TB", 0, "C");
            $pdf->Cell($lebar[6], 0, "PEMBAYARAN", "T", 0, "C");
            $pdf->Cell($lebar[7] + $lebar[8], 0, "SALDO", "TB", 0, "C");

            $pdf->Ln();

            $pdf->Cell($lebar[1], 6, "PINJAMAN", "B", 0, "C");
            $pdf->Cell($lebar[2], 6, "BAYAR", "B", 0, "C");
            $pdf->Cell($lebar[3], 6, "KE", "B", 0, "C");
            $pdf->Cell($lebar[4], 6, "PINJAMAN BLN LALU", "TB", 0, "C", 0, 0, 1);
            $pdf->Cell($lebar[5], 6, "BUNGA BLN LALU", "TB", 0, "C", 0, 0, 1);
            $pdf->Cell($lebar[6], 6, "ANGSURAN", "B", 0, "C");
            $pdf->Cell($lebar[7], 6, "PINJAMAN BLN INI", "TB", 0, "C", 0, 0, 1);
            $pdf->Cell($lebar[8], 6, "BUNGA BLN INI", "TB", 0, "C", 0, 0, 1);

            $pdf->Ln(7);

            $no             = 1;
            $t_jumlah       = 0;
            $t_jumlah_pg    = 0;
            $t_jumlah_bonus = 0;

            $jml_wajib      = 0;
            $jml_sukarela   = 0;
            $jml_blj_kredit = 0;

            foreach ($data_potga as $key1 => $value1) {
                if ($value1['no_ang'] == $value['no_ang']) {
                    $margin_per_bulan = $value1['pokok_akhir'] * ($value1['margin'] / 100) * ($hari / 365);

                    $pdf->Cell($lebar[1], 0, number_format($value1['jml_pinjam'], 2), 0, 0, "R");
                    $pdf->Cell($lebar[2], 0, number_format($value1['kesanggupan_bayar'], 2), 0, 0, "R");
                    $pdf->Cell($lebar[3], 0, $value1['angs_ke'], 0, 0, "C");
                    $pdf->Cell($lebar[4], 0, number_format($value1['pokok_awal'], 2), 0, 0, "R");
                    $pdf->Cell($lebar[5], 0, number_format($value1['bunga'], 2), 0, 0, "R");
                    $pdf->Cell($lebar[6], 0, number_format($value1['angsuran'], 2), 0, 0, "R");
                    $pdf->Cell($lebar[7], 0, number_format($value1['pokok_akhir'], 2), 0, 0, "R");
                    $pdf->Cell($lebar[8], 0, number_format($margin_per_bulan, 2), 0, 0, "R");

                    $pdf->Ln();

                    $t_jumlah += $value1['angsuran'];
                    $t_jumlah_pg += $value1['jml_potga'];
                    $t_jumlah_bonus += ($value1['jml_min_angsuran'] + $value1['jml_max_angsuran']);

                    unset($data_potga[$key1]);
                }
            }

            $pdf->Ln();

            $lebar_judul_total = $lebar[1] + $lebar[2] + $lebar[3] + $lebar[4] + $lebar[5];

            $pdf->Cell($lebar_judul_total, 0, "Total Potong Gaji :", "T", 0, "R");
            $pdf->Cell($lebar[6], 0, number_format($t_jumlah_pg, 2), "T", 0, "R");
            $pdf->Cell($lebar[7] + $lebar[8], 0, "", "T", 0, "R");
            $pdf->Ln();

            $pdf->Cell($lebar_judul_total, 0, "Total Potongan diluar gaji :", "", 0, "R");
            $pdf->Cell($lebar[6], 0, number_format($t_jumlah_bonus, 2), "", 0, "R");
            $pdf->Cell($lebar[7] + $lebar[8], 0, "", "", 0, "R");
            $pdf->Ln();

            $pdf->Cell($lebar_judul_total, 0, "Total Pembayaran Angsuran :", "T", 0, "R");
            $pdf->Cell($lebar[6], 0, number_format($t_jumlah, 2), "T", 0, "R");
            $pdf->Cell($lebar[7] + $lebar[8], 0, "", "T", 0, "R");
            $pdf->Ln();

            $halaman++;
        }

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_invoice()
    {
        set_time_limit(0);

        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = "A4";

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_invoice_" . $data_req['nm_prsh'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

        $pdf->SetCreator($kreator);
        $pdf->SetAuthor($kreator);
        $pdf->SetTitle($judul_file);

        $pdf->SetHeaderData("", "", $judul_header, $teks_header, "", "");
        $pdf->setFooterData("", "");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins("5", "55", "5");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->SetFontSize("10");

        if ($data_req['kd_prsh'] == "ANPER") {
            $this->db->where("kd_prsh != ", 'P01');
        } else {
            $this->db->where("kd_prsh", $data_req['kd_prsh']);
        }

        $select = "no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, nm_dep,
                SUM(if(kd_potga in ('11', '111'), jumlah, 0)) simp_pokok,
                SUM(if(kd_potga in ('1'), jml_wajib, 0)) simp_wajib,
                SUM(if(kd_potga in ('1'), jml_sukarela, 0)) simp_sukarela,
                SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
                SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
                SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
                SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
                SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang";

        $this->db->select($select)
            ->where("tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'")
            ->where("is_pensiun", '0')
            ->where("is_pot_bonus", '0')
            ->group_by("kd_prsh")
            ->order_by("nm_prsh");

        $dataPotga = $this->db->get("t_potga");

        foreach ($dataPotga->result_array() as $key => $value) {
            $pdf->AddPage();

            $pdf->Cell(0, 0, "Kepada Yth.");
            $pdf->Ln();
            $pdf->Cell(0, 0, "Direktur " . $value['nm_prsh']);
            $pdf->Ln();
            $pdf->Cell(0, 0, "Di Gresik");
            $pdf->Ln();

            $tab = 120;

            $pdf->Cell($tab, 0, "");
            $pdf->Cell(0, 0, "SURAT PENGANTAR");
            $pdf->Ln();

            $array_bulan_romawi = array_bulan_romawi();
            $bulan_romawi       = $array_bulan_romawi[$data_req['bulan']];

            $setDataNomor = array(
                "tahun"   => $data_req['tahun'],
                "bulan"   => $data_req['bulan'],
                "kd_prsh" => $value['kd_prsh'],
                // "no_sp"       => $data_req['no_sp'],
                // "no_kuitansi" => $data_req['no_kuitansi'],
            );

            $dataNomor = $this->laporan_model->cek_nomor_lap_potga($setDataNomor);

            $setDataNomor['no_sp']       = $dataNomor['no_sp'];
            $setDataNomor['no_kuitansi'] = $dataNomor['no_kuitansi'];

            if ($dataNomor['ada_data'] < 1) {
                $this->laporan_model->simpan_nomor_lap_potga($setDataNomor);
            }

            $no_sp = $dataNomor['no_sp'] . "/" . $bulan_romawi . "/SP/KEU/K3PG/" . $data_req['tahun'];

            $pdf->Cell($tab, 0, "");
            $pdf->Cell(0, 0, "No: " . $no_sp);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();

            $pdf->Cell($tab, 0, "Bersama ini kami sampaikan/kirimkan kepada Saudara");
            $pdf->Cell(0, 0, "Bukti Potongan Anggota K3PG");

            $pdf->Ln();

            $array_bulan = array_bulan();
            $bulan       = $array_bulan[$data_req['bulan']];

            $pdf->Cell($tab, 0, "");
            $pdf->Cell(0, 0, "untuk bulan " . $bulan . " " . $data_req['tahun'] . " adalah sbb:");

            $pdf->Ln();
            $pdf->Ln();

            $lebar_kolom[1] = 10;
            $lebar_kolom[2] = 20;
            $lebar_kolom[3] = 125;
            $lebar_kolom[4] = 40;

            $pdf->Cell($lebar_kolom[1], 0, "No.", 1, 0, "C");
            $pdf->Cell($lebar_kolom[2], 0, "Jumlah Set", 1, 0, "C");
            $pdf->Cell($lebar_kolom[3], 0, "Uraian", 1, 0, "C");
            $pdf->Cell($lebar_kolom[4], 0, "Jumlah (Rp)", 1, 0, "C");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell($lebar_kolom[3], 0, "", "LR");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "1", "LR", 0, "C");
            $pdf->Cell($lebar_kolom[2], 0, "1 Bendel", "LR", 0, "C");
            $pdf->Cell($lebar_kolom[3], 0, "Bukti Pemotongan Gaji Anggota K3PG, dan daftar", "LR", 0, "L");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR", 0, "L");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell($lebar_kolom[3], 0, "Nama Anggota K3PG, dengan perincian sbb:", "LR");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            // $query_data_potga = "SELECT no_ang, no_peg, nm_ang, nm_dep,
            //     SUM(if(kd_potga in ('11', '111'), jumlah, 0)) simp_pokok,
            //     SUM(if(kd_potga in ('1'), jml_wajib, 0)) simp_wajib,
            //     SUM(if(kd_potga in ('1'), jml_sukarela, 0)) simp_sukarela,
            //     SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
            //     SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
            //     SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
            //     SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
            //     SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang
            // FROM t_potga
            // WHERE tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'
            // AND kd_prsh = '" . $data_req['kd_prsh'] . "'
            // AND is_pensiun = '0'
            // AND is_pot_bonus = '0'
            // GROUP BY kd_prsh";

            // $data_potga = $this->db->query($query_data_potga)->row_array(0);

            $jml_simpanan = $value['simp_pokok'] + $value['simp_wajib'] + $value['simp_sukarela'];
            $jml_kredit   = $value['jml_blj_toko'] + $value['jml_elektronik'] + $value['jml_motor'] + $value['jml_bangunan'] + $value['jml_pj_uang'];
            $jml_total    = ceil($jml_simpanan + $jml_kredit);

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "1.", "", 0, "R");
            $pdf->Cell(60, 0, "Simpanan Wajib", "");
            $pdf->Cell(10, 0, "Rp", "");
            $pdf->Cell(45, 0, number_format($value['simp_wajib'], 2), "R", 0, "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "2.", "", 0, "R");
            $pdf->Cell(60, 0, "Simpanan Pokok", "");
            $pdf->Cell(10, 0, "Rp", "");
            $pdf->Cell(45, 0, number_format($value['simp_pokok'], 2), "R", 0, "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "3.", "", 0, "R");
            $pdf->Cell(60, 0, "Simpanan Sukarela", "");
            $pdf->Cell(10, 0, "Rp", "");
            $pdf->Cell(45, 0, number_format($value['simp_sukarela'], 2), "R", 0, "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "4.", "", 0, "R");
            $pdf->Cell(60, 0, "Pembelian Kredit", "");
            $pdf->Cell(10, 0, "", "");
            $pdf->Cell(45, 0, "", "R", 0, "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "", "", 0, "R");
            $pdf->Cell(45, 0, "- Belanja Toko", "");
            $pdf->Cell(10, 0, "Rp", "");
            $pdf->Cell(45, 0, number_format($value['jml_blj_toko'], 2), "", 0, "R");
            $pdf->Cell(15, 0, "", "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "", "", 0, "R");
            $pdf->Cell(45, 0, "- Elektronik", "");
            $pdf->Cell(10, 0, "Rp", "");
            $pdf->Cell(45, 0, number_format($value['jml_elektronik'], 2), "", 0, "R");
            $pdf->Cell(15, 0, "", "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "", "", 0, "R");
            $pdf->Cell(45, 0, "- Sepeda Motor", "");
            $pdf->Cell(10, 0, "Rp", "");
            $pdf->Cell(45, 0, number_format($value['jml_motor'], 2), "", 0, "R");
            $pdf->Cell(15, 0, "", "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "", "", 0, "R");
            $pdf->Cell(45, 0, "- Bangunan", "");
            $pdf->Cell(10, 0, "Rp", "");
            $pdf->Cell(45, 0, number_format($value['jml_bangunan'], 2), "", 0, "R");
            $pdf->Cell(15, 0, "", "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "", "", 0, "R");
            $pdf->Cell(45, 0, "- Pinjaman Uang", "");
            $pdf->Cell(10, 0, "Rp", "B");
            $pdf->Cell(45, 0, number_format($value['jml_pj_uang'], 2), "B", 0, "R");
            $pdf->Cell(15, 0, "", "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell($lebar_kolom[3], 0, "", "LR");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell($lebar_kolom[3], 0, "", "LR");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell(5, 0, "", "L");
            $pdf->Cell(5, 0, "", "", 0, "R");
            $pdf->Cell(60, 0, "", "");
            $pdf->Cell(10, 0, "Rp", "B");
            $pdf->Cell(45, 0, number_format($jml_kredit, 2), "BR", 0, "R");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell($lebar_kolom[3], 0, "", "LR");
            $pdf->Cell($lebar_kolom[4], 0, "", "LR");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "LR");
            $pdf->Cell($lebar_kolom[2], 0, "", "LR");
            $pdf->Cell($lebar_kolom[3], 0, "", "LR");
            $pdf->Cell(10, 0, "Rp", "L");
            $pdf->Cell(($lebar_kolom[4] - 10), 0, number_format($jml_total, 2), "R", 0, "R");
            $pdf->Ln();

            $pdf->Cell($lebar_kolom[1], 0, "", "BLR");
            $pdf->Cell($lebar_kolom[2], 0, "", "BLR");
            $pdf->Cell($lebar_kolom[3], 0, "", "BLR");
            $pdf->Cell($lebar_kolom[4], 0, "", "BLR");
            $pdf->Ln();
            $pdf->Ln();

            $terbilang = ucwords(terbilang($jml_total)) . " Rupiah";

            $pdf->Cell(20, 0, "Terbilang : ");
            $pdf->MultiCell(175, 10, $terbilang, "0", "L");
            $pdf->Ln();

            $pdf->Cell(20, 0, "Demikian untuk dimaklumi.");

            $pdf->Ln();
            $pdf->Ln();

            $pecah_tgl_cetak = explode("-", $data_req['tgl_cetak']);
            $hari_cetak      = $pecah_tgl_cetak[0];
            $bulan_cetak     = $pecah_tgl_cetak[1];
            $tahun_cetak     = $pecah_tgl_cetak[2];

            $teks_bulan_cetak = $array_bulan[$bulan_cetak];

            $tgl_cetak = $hari_cetak . " " . $teks_bulan_cetak . " " . $tahun_cetak;

            $pdf->Cell($tab, 0, "");
            $pdf->Cell(75, 0, "Gresik, " . $tgl_cetak, 0, 0, "C");
            $pdf->Ln();
            $pdf->Ln();

            $pdf->Cell($tab, 0, "");
            $pdf->MultiCell(75, 0, "Koperasi Karyawan Keluarga Besar Petrokimia Gresik", 0, "C");
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();

            $data_ttd        = $this->db->limit("1")->get("s_laporan")->row_array(0);
            $ttd_mgr_adm_keu = $data_ttd['manager_adm_keuangan'];

            $pdf->Cell($tab, 0, "");
            $pdf->Cell(75, 0, $ttd_mgr_adm_keu, "", 0, "C");
            $pdf->Ln();
            $pdf->Cell($tab, 0, "");
            $pdf->Cell(75, 0, "(Manager Adm & Keuangan)", "", 0, "C");
            $pdf->Ln();
        }

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_bukti_masuk()
    {
        set_time_limit(0);

        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = "A4";

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_bukti_masuk_" . $data_req['nm_prsh'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

        $pdf->SetCreator($kreator);
        $pdf->SetAuthor($kreator);
        $pdf->SetTitle($judul_file);

        $pdf->SetHeaderData("", "", $judul_header, $teks_header, "", "");
        $pdf->setFooterData("", "");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins("5", "55", "5");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if ($data_req['kd_prsh'] == "ANPER") {
            $this->db->where("kd_prsh != ", 'P01');
        } else {
            $this->db->where("kd_prsh", $data_req['kd_prsh']);
        }

        $select = "no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, nm_dep,
                SUM(if(kd_potga in ('11', '111'), jumlah, 0)) simp_pokok,
                SUM(if(kd_potga in ('1'), jml_wajib, 0)) simp_wajib,
                SUM(if(kd_potga in ('1'), jml_sukarela, 0)) simp_sukarela,
                SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
                SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
                SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
                SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
                SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang";

        $this->db->select($select)
            ->where("tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'")
            ->where("is_pensiun", '0')
            ->where("is_pot_bonus", '0')
            ->group_by("kd_prsh")
            ->order_by("nm_prsh");

        $dataPotga = $this->db->get("t_potga");

        // $query_data_potga = "SELECT no_ang, no_peg, nm_ang, nm_dep,
        //         SUM(if(kd_potga in ('11', '111'), jumlah, 0)) simp_pokok,
        //         SUM(if(kd_potga in ('1'), jml_wajib, 0)) simp_wajib,
        //         SUM(if(kd_potga in ('1'), jml_sukarela, 0)) simp_sukarela,
        //         SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
        //         SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
        //         SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
        //         SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
        //         SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang
        //     FROM t_potga
        //     WHERE tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'
        //     AND kd_prsh = '" . $data_req['kd_prsh'] . "'
        //     AND is_pensiun = '0'
        //     AND is_pot_bonus = '0'
        //     GROUP BY kd_prsh";

        // $data_potga = $this->db->query($query_data_potga)->row_array(0);

        foreach ($dataPotga->result_array() as $key => $value) {
            $jml_simpanan = $value['simp_pokok'] + $value['simp_wajib'] + $value['simp_sukarela'];
            $jml_kredit   = $value['jml_blj_toko'] + $value['jml_elektronik'] + $value['jml_motor'] + $value['jml_bangunan'] + $value['jml_pj_uang'];
            $jml_total    = ceil($jml_simpanan + $jml_kredit);

            $pdf->AddPage();

            $pdf->SetFontSize("9");

            $pdf->SetY(20);

            $pdf->Cell(70, 0, "");
            $pdf->Cell(0, 0, $value['nm_prsh']);

            $pdf->Ln(10);

            $pdf->Cell(90, 0, "");
            $pdf->Cell(30, 0, number_format($jml_total, 2), 0, 0, "R");

            $pdf->Ln(10);

            $terbilang = ucwords(terbilang($jml_total));

            $pdf->Cell(70, 0, "");
            $pdf->MultiCell(120, 0, $terbilang, 0, "L");

            $pdf->SetY(60);

            $array_bulan = array_bulan();
            $bulan       = $array_bulan[$data_req['bulan']];

            $pdf->Cell(70, 0, "");
            $pdf->Cell(0, 0, "Pembayaran Potongan Gaji Anggota bulan " . $bulan . " " . $data_req['tahun']);
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "31.02");
            $pdf->Cell(40, 0, "1. Simpanan Wajib");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($value['simp_wajib'], 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "31.01");
            $pdf->Cell(40, 0, "2. Simpanan Pokok");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($value['simp_pokok'], 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "21.71");
            $pdf->Cell(40, 0, "3. Simpanan Sukarela");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($value['simp_sukarela'], 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "11.41");
            $pdf->Cell(40, 0, "4. Pembelian Kredit");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($jml_kredit, 2), "B", 0, "R");
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "");
            $pdf->Cell(40, 0, "");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($jml_total, 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Ln();
            $pdf->Ln();

            $array_bulan_romawi = array_bulan_romawi();
            $bulan_romawi       = $array_bulan_romawi[$data_req['bulan']];

            $setDataNomor = array(
                "tahun"   => $data_req['tahun'],
                "bulan"   => $data_req['bulan'],
                "kd_prsh" => $value['kd_prsh'],
                // "no_sp"       => $data_req['no_sp'],
                // "no_kuitansi" => $data_req['no_kuitansi'],
            );

            $dataNomor = $this->laporan_model->cek_nomor_lap_potga($setDataNomor);

            $setDataNomor['no_sp']       = $dataNomor['no_sp'];
            $setDataNomor['no_kuitansi'] = $dataNomor['no_kuitansi'];

            if ($dataNomor['ada_data'] < 1) {
                $this->laporan_model->simpan_nomor_lap_potga($setDataNomor);
            }

            $no_kuitansi = $dataNomor['no_kuitansi'] . "/" . $bulan_romawi . "/SP/KEU/K3PG/" . $data_req['tahun'];

            $pdf->Cell(70, 0, "");
            $pdf->Cell(0, 0, "No. Kuitansi : " . $no_kuitansi);
            $pdf->Ln();

            $pdf->Cell(70, 0, "");
            $pdf->Cell(0, 0, "Pembayaran sesuai bukti terlampir");
        }

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_kuitansi()
    {
        set_time_limit(0);

        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = "A4";

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_kuitansi_" . $data_req['nm_prsh'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

        $pdf->SetCreator($kreator);
        $pdf->SetAuthor($kreator);
        $pdf->SetTitle($judul_file);

        $pdf->SetHeaderData("", "", $judul_header, $teks_header, "", "");
        $pdf->setFooterData("", "");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins("5", "55", "5");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if ($data_req['kd_prsh'] == "ANPER") {
            $this->db->where("kd_prsh != ", 'P01');
        } else {
            $this->db->where("kd_prsh", $data_req['kd_prsh']);
        }

        $select = "no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, nm_dep,
                SUM(if(kd_potga in ('11', '111'), jumlah, 0)) simp_pokok,
                SUM(if(kd_potga in ('1'), jml_wajib, 0)) simp_wajib,
                SUM(if(kd_potga in ('1'), jml_sukarela, 0)) simp_sukarela,
                SUM(if(kd_potga in ('31', '32'), jumlah, 0)) jml_blj_toko,
                SUM(if(kd_piutang LIKE 'e%', jumlah, 0)) jml_elektronik,
                SUM(if(kd_piutang LIKE 'm%', jumlah, 0)) jml_motor,
                SUM(if(kd_piutang LIKE 'g%', jumlah, 0)) jml_bangunan,
                SUM(if(kd_piutang LIKE 'u%' or kd_piutang LIKE 'p%', jumlah, 0)) jml_pj_uang";

        $this->db->select($select)
            ->where("tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'")
            ->where("is_pensiun", '0')
            ->where("is_pot_bonus", '0')
            ->group_by("kd_prsh")
            ->order_by("nm_prsh");

        $dataPotga = $this->db->get("t_potga");

        foreach ($dataPotga->result_array() as $key => $value) {
            $jml_simpanan = $value['simp_pokok'] + $value['simp_wajib'] + $value['simp_sukarela'];
            $jml_kredit   = $value['jml_blj_toko'] + $value['jml_elektronik'] + $value['jml_motor'] + $value['jml_bangunan'] + $value['jml_pj_uang'];
            $jml_total    = $jml_simpanan + $jml_kredit;

            $pdf->AddPage();

            $pdf->SetFontSize("9");

            $pdf->SetY(40);

            $array_bulan_romawi = array_bulan_romawi();
            $bulan_romawi       = $array_bulan_romawi[$data_req['bulan']];

            $setDataNomor = array(
                "tahun"   => $data_req['tahun'],
                "bulan"   => $data_req['bulan'],
                "kd_prsh" => $value['kd_prsh'],
                // "no_sp"       => $data_req['no_sp'],
                // "no_kuitansi" => $data_req['no_kuitansi'],
            );

            $dataNomor = $this->laporan_model->cek_nomor_lap_potga($setDataNomor);

            $setDataNomor['no_sp']       = $dataNomor['no_sp'];
            $setDataNomor['no_kuitansi'] = $dataNomor['no_kuitansi'];

            if ($dataNomor['ada_data'] < 1) {
                $this->laporan_model->simpan_nomor_lap_potga($setDataNomor);
            }

            $no_kuitansi = $dataNomor['no_kuitansi'] . "/" . $bulan_romawi . "/SP/KEU/K3PG/" . $data_req['tahun'];

            $pdf->Cell(60, 0, "");
            $pdf->Cell(0, 0, $no_kuitansi);

            $pdf->Ln(10);

            $pdf->Cell(60, 0, "");
            $pdf->Cell(0, 0, $value['nm_prsh']);

            $pdf->Ln(10);

            $terbilang = ucwords(terbilang($jml_total));

            $pdf->Cell(60, 0, "");
            $pdf->MultiCell(120, 0, $terbilang, 0, "L");

            $pdf->SetY(73);

            $array_bulan = array_bulan();
            $bulan       = $array_bulan[$data_req['bulan']];

            $pdf->Cell(60, 0, "");
            $pdf->Cell(0, 0, "Potongan Gaji Anggota K3PG untuk bulan " . $bulan . " " . $data_req['tahun']);
            $pdf->Ln();

            $strbulan_lalu = mktime(0, 0, 0, $data_req['bulan'] - 1, 1, $data_req['tahun']);
            $bulan_lalu    = date("m", $strbulan_lalu);
            $tahun_lalu    = date("Y", $strbulan_lalu);

            $pdf->Cell(60, 0, "");
            $pdf->Cell(0, 0, "Periode Transaksi Belanja Kredit bulan " . $array_bulan[$bulan_lalu] . " " . $tahun_lalu);
            $pdf->Ln();

            $no_sp = $dataNomor['no_sp'] . "/" . $bulan_romawi . "/SP/KEU/K3PG/" . $data_req['tahun'];

            $pdf->Cell(60, 0, "");
            $pdf->Cell(0, 0, "Surat No. :" . $no_sp);
            $pdf->Ln();

            $pdf->SetY(85);

            $pecah_tgl_cetak = explode("-", $data_req['tgl_cetak']);
            $hari_cetak      = $pecah_tgl_cetak[0];
            $bulan_cetak     = $pecah_tgl_cetak[1];
            $tahun_cetak     = $pecah_tgl_cetak[2];

            $teks_bulan_cetak = $array_bulan[$bulan_cetak];

            $tgl_cetak = $hari_cetak . " " . $teks_bulan_cetak . " " . $tahun_cetak;

            $pdf->Cell(160, 0, "");
            $pdf->Cell(0, 0, $tgl_cetak);

            $pdf->SetY(95);

            $pdf->SetFontSize(12);

            $pdf->Cell(40, 0, "");
            $pdf->Cell(50, 0, number_format($jml_total, 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->SetFontSize("9");

            $pdf->SetY(105);

            $pdf->Cell(60, 0, "");
            $pdf->Cell(0, 0, $data_req['ket']);
            $pdf->Ln();

            $data_ttd        = $this->db->limit("1")->get("s_laporan")->row_array(0);
            $ttd_mgr_adm_keu = $data_ttd['manager_adm_keuangan'];

            $pdf->SetY(120);

            $pdf->Cell(140, 0, "");
            $pdf->Cell(55, 0, $ttd_mgr_adm_keu, 0, 0, "C");
            $pdf->Ln(6);

            $pdf->Cell(140, 0, "");
            $pdf->Cell(55, 0, "Mgr. Adm & Keuangan", 0, 0, "C");
            $pdf->Ln();
        }

        // $query_data_potga = "SELECT
        //     FROM t_potga
        //     WHERE tgl_potga LIKE '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "%'
        //     AND kd_prsh = '" . $data_req['kd_prsh'] . "'
        //     AND is_pensiun = '0'
        //     AND is_pot_bonus = '0'
        //     GROUP BY kd_prsh";

        // $data_potga = $this->db->query($query_data_potga)->row_array(0);

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_rekap_kkbkpr()
    {
        set_time_limit(0);

        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = "A4";

        $pdf = new TCPDF("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_rekap_potongan_kkbkpr_" . $data_req['nm_prsh'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

        $pdf->SetCreator($kreator);
        $pdf->SetAuthor($kreator);
        $pdf->SetTitle($judul_file);

        $pdf->SetHeaderData("", "", $judul_header, $teks_header, "", "");
        $pdf->setFooterData("", "");
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins("5", "10", "5");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->SetAutoPageBreak(true, "15");

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddPage();

        $pdf->SetFontSize("11");

        $data_jadwal_potong_kkbkpr = $this->db->where("kd_prsh", $data_req['kd_prsh'])
            ->where("tahun", $data_req['tahun'])
            ->where("bulan", $data_req['bulan'])
            ->where("is_jadwal_tetap", "0")
            ->get("m_pot_bonus_pg");

        $nm_pot_bonus = ($data_jadwal_potong_kkbkpr->num_rows() > 0) ? $data_jadwal_potong_kkbkpr->row(0)->nm_pot_bonus : "";

        $data_nama_bulan = array_bulan();
        $nama_bulan      = $data_nama_bulan[$data_req['bulan']];

        $pdf->Cell(0, 0, "REKAPITULASI PINJAMAN UANG BERAGUNAN ANGGOTA K3PG YANG BERSEDIA", 0, 0, "C");
        // $pdf->Cell(0, 0, "JUDUL", 0, 0, "C");
        $pdf->Ln();
        $pdf->Cell(0, 0, "DIPOTONGKAN MELALUI \"$nm_pot_bonus\"", 0, 0, "C");
        $pdf->Ln();
        $pdf->Cell(0, 0, "Periode " . $nama_bulan . " " . $data_req['tahun'], 0, 0, "C");
        $pdf->Ln();
        $pdf->Ln();

        $pdf->SetFontSize("8");

        $lebar_kolom[1] = "13";
        $lebar_kolom[2] = "13";
        $lebar_kolom[3] = "17";
        $lebar_kolom[4] = "74";
        $lebar_kolom[5] = "13";
        $lebar_kolom[6] = "13";
        $lebar_kolom[7] = "15";
        $lebar_kolom[8] = "30";
        $lebar_kolom[9] = "12";

        $pdf->Cell($lebar_kolom[1], 0, "NOMOR", "TLR", 0, "C");
        $pdf->Cell($lebar_kolom[2], 0, "COMPL", "TLR", 0, "C");
        $pdf->Cell($lebar_kolom[3], 0, "EMPLOYEE", "TLR", 0, "C", 0, 0, 1);
        $pdf->Cell($lebar_kolom[4], 0, "EMPLOYEE NAME", "TLR", 0, "C");
        $pdf->Cell($lebar_kolom[5], 0, "WAGE", "TLR", 0, "C", 0, 0, 1);
        $pdf->Cell($lebar_kolom[6], 0, "WAGE", "TLR", 0, "C", 0, 0, 1);
        $pdf->Cell($lebar_kolom[7], 0, "PAYROLL", "TLR", 0, "C", 0, 0, 1);
        $pdf->Cell($lebar_kolom[8], 0, "JUMLAH", "TLR", 0, "C");
        $pdf->Cell($lebar_kolom[9], 0, "NAK", "TLR", 0, "C");
        $pdf->Ln();

        $pdf->Cell($lebar_kolom[1], 0, "", "BLR", 0, "C");
        $pdf->Cell($lebar_kolom[2], 0, "ID", "BLR", 0, "C");
        $pdf->Cell($lebar_kolom[3], 0, "NO.", "BLR", 0, "C", 0, 0, 1);
        $pdf->Cell($lebar_kolom[4], 0, "", "BLR", 0, "C");
        $pdf->Cell($lebar_kolom[5], 0, "CLASS", "BLR", 0, "C", 0, 0, 1);
        $pdf->Cell($lebar_kolom[6], 0, "CODE", "BLR", 0, "C", 0, 0, 1);
        $pdf->Cell($lebar_kolom[7], 0, "PERIOD", "BLR", 0, "C", 0, 0, 1);
        $pdf->Cell($lebar_kolom[8], 0, "", "BLR", 0, "C");
        $pdf->Cell($lebar_kolom[9], 0, "", "BLR", 0, "C");
        $pdf->Ln();

        // $tabel_html = "<table border=\"1\">
        //         <tr>
        //             <th style=\"text-align: center\" width=\"1cm\">NOMOR URUT</th>
        //             <th style=\"text-align: center\" width=\"1cm\">COMPL ID</th>
        //             <th style=\"text-align: center\">EMPLOYEE NO. <br>NO. INDUK</th>
        //             <th style=\"text-align: center\" width=\"7cm\">EMPLOYEE NAME <br>NAMA</th>
        //             <th style=\"text-align: center\" width=\"1cm\">WAGE CLASS</th>
        //             <th style=\"text-align: center\" width=\"1cm\">WAGE CODE</th>
        //             <th style=\"text-align: center\" width=\"1.5cm\">PAYROLL PERIOD</th>
        //             <th style=\"text-align: center\" width=\"3.5cm\">JUMLAH (Rp)</th>
        //             <th style=\"text-align: center\" width=\"1.5cm\">NAK</th>
        //         </tr>";

        $data_potongan = $this->db->select("a.no_pinjam, a.tgl_pinjam, a.no_ang, a.no_peg, a.nm_ang, b.blth_angsuran, b.jml_min_angsuran, b.jml_max_angsuran, sum(b.jml_min_angsuran + b.jml_max_angsuran) jml_angs_bonus")
            ->from("t_pinjaman_ang a")->join("t_pinjaman_ang_det b", "a.no_pinjam=b.no_pinjam")
            ->where_in("a.kd_pinjaman", array("2", "4"))
            ->where("a.kd_prsh", $data_req['kd_prsh'])
            ->where("b.blth_angsuran", ($data_req['tahun'] . "-" . $data_req['bulan']))
            ->group_start()
            ->where("b.jml_min_angsuran >", 0)
            ->or_where("b.jml_max_angsuran >", 0)
            ->group_end()
            ->group_by("a.no_ang")
            ->get();

        $no        = 1;
        $jml_total = 0;

        foreach ($data_potongan->result_array() as $key => $value) {
            if ($pdf->GetY() > 277) {
                $pdf->Cell($lebar_kolom[1], 0, "NOMOR", "TLR", 0, "C");
                $pdf->Cell($lebar_kolom[2], 0, "COMPL", "TLR", 0, "C");
                $pdf->Cell($lebar_kolom[3], 0, "EMPLOYEE", "TLR", 0, "C", 0, 0, 1);
                $pdf->Cell($lebar_kolom[4], 0, "EMPLOYEE NAME", "TLR", 0, "C");
                $pdf->Cell($lebar_kolom[5], 0, "WAGE", "TLR", 0, "C", 0, 0, 1);
                $pdf->Cell($lebar_kolom[6], 0, "WAGE", "TLR", 0, "C", 0, 0, 1);
                $pdf->Cell($lebar_kolom[7], 0, "PAYROLL", "TLR", 0, "C", 0, 0, 1);
                $pdf->Cell($lebar_kolom[8], 0, "JUMLAH", "TLR", 0, "C");
                $pdf->Cell($lebar_kolom[9], 0, "NAK", "TLR", 0, "C");
                $pdf->Ln();

                $pdf->Cell($lebar_kolom[1], 0, "", "BLR", 0, "C");
                $pdf->Cell($lebar_kolom[2], 0, "ID", "BLR", 0, "C");
                $pdf->Cell($lebar_kolom[3], 0, "NO.", "BLR", 0, "C", 0, 0, 1);
                $pdf->Cell($lebar_kolom[4], 0, "", "BLR", 0, "C");
                $pdf->Cell($lebar_kolom[5], 0, "CLASS", "BLR", 0, "C", 0, 0, 1);
                $pdf->Cell($lebar_kolom[6], 0, "CODE", "BLR", 0, "C", 0, 0, 1);
                $pdf->Cell($lebar_kolom[7], 0, "PERIOD", "BLR", 0, "C", 0, 0, 1);
                $pdf->Cell($lebar_kolom[8], 0, "", "BLR", 0, "C");
                $pdf->Cell($lebar_kolom[9], 0, "", "BLR", 0, "C");
                $pdf->Ln();
            }

            $pdf->Cell($lebar_kolom[1], 0, $no, "BLR", 0, "R");
            $pdf->Cell($lebar_kolom[2], 0, "PG", "BLR", 0, "C");
            $pdf->Cell($lebar_kolom[3], 0, $value['no_peg'], "BLR", 0, "L");
            $pdf->Cell($lebar_kolom[4], 0, $value['nm_ang'], "BLR", 0, "L", 0, 0, 1);
            $pdf->Cell($lebar_kolom[5], 0, "GR", "BLR", 0, "C");
            $pdf->Cell($lebar_kolom[6], 0, "PK02", "BLR", 0, "C");
            $pdf->Cell($lebar_kolom[7], 0, $data_req['tahun'] . "-" . $data_req['bulan'], "BLR", 0, "C");
            $pdf->Cell($lebar_kolom[8], 0, number_format($value['jml_angs_bonus'], 2), "BLR", 0, "R");
            $pdf->Cell($lebar_kolom[9], 0, $value['no_ang'], "BLR", 0, "C");
            $pdf->Ln();

            // $tabel_html .= "
            //     <tr>
            //         <td style=\"text-align: right\">" . $no . "</td>
            //         <td style=\"text-align: center\">PG</td>
            //         <td style=\"text-align: center\">" . $value['no_peg'] . "</td>
            //         <td style=\"text-align: left\">" . $value['nm_ang'] . "</td>
            //         <td style=\"text-align: center\">GR</td>
            //         <td style=\"text-align: center\">PK02</td>
            //         <td style=\"text-align: center\">" . $data_req['tahun'] . "-" . $data_req['bulan'] . "</td>
            //         <td style=\"text-align: right\">" . number_format($value['jml_angs_bonus'], 2) . "</td>
            //         <td style=\"text-align: center\">" . $value['no_ang'] . "</td>
            //     </tr>";

            $no++;
            $jml_total += $value['jml_angs_bonus'];
        }

        $pdf->Cell($lebar_kolom[1], 0, "", "BL", 0, "R");
        $pdf->Cell($lebar_kolom[2], 0, "", "B", 0, "C");
        $pdf->Cell($lebar_kolom[3], 0, "", "B", 0, "L");
        $pdf->Cell($lebar_kolom[4], 0, "", "B", 0, "L", 0, 0, 1);
        $pdf->Cell($lebar_kolom[5], 0, "", "B", 0, "C");
        $pdf->Cell($lebar_kolom[6], 0, "", "B", 0, "C");
        $pdf->Cell($lebar_kolom[7], 0, "Total", "B", 0, "R");
        $pdf->Cell($lebar_kolom[8], 0, number_format($jml_total, 2), "BLR", 0, "R");
        $pdf->Cell($lebar_kolom[9], 0, "", "BLR", 0, "C");
        $pdf->Ln();

        // $tabel_html .= "
        //         <tr>
        //             <td style=\"text-align: right\" colspan=\"7\">Total</td>
        //             <td style=\"text-align: right\">" . number_format($jml_total, 2) . "</td>
        //             <td style=\"text-align: center\"></td>
        //         </tr>
        //     </table>";

        // $pdf->WriteHTML($tabel_html);

        $pdf->Output($judul_file, 'I');
    }

    public function excel_rekap_kkbkpr()
    {
        set_time_limit(0);

        $get_request = get_request();

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $fileName = "rekapPotKKBKPR" . $data_req['bulan'] . $data_req['tahun'] . $data_req['nm_prsh'] . ".xls";

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");

        $array_bln  = array_bulan();
        $nama_bulan = $array_bln[$data_req['bulan']];

        $data_jadwal_potong_kkbkpr = $this->db->where("kd_prsh", $data_req['kd_prsh'])
            ->where("tahun", $data_req['tahun'])
            ->where("bulan", $data_req['bulan'])
            ->where("is_jadwal_tetap", "0")
            ->get("m_pot_bonus_pg");

        $nm_pot_bonus = ($data_jadwal_potong_kkbkpr->num_rows() > 0) ? $data_jadwal_potong_kkbkpr->row(0)->nm_pot_bonus : "";

        $view = "<center>
            REKAPITULASI PINJAMAN UANG BERAGUNAN ANGGOTA K3PG YANG BERSEDIA
            <br>
            DIPOTONGKAN MELALUI \"$nm_pot_bonus\"
            <br>
            Perusahaan : " . $data_req['nm_prsh'] . "
            <br>
            Periode : " . $nama_bulan . " " . $data_req['tahun'] . "
        </center>
        <br>
        <br>";

        $view .= "<table width=\"100%\" border=\"1\">
                <thead>
                    <tr>
                        <th>NOMOR</th>
                        <th>COMPL ID</th>
                        <th>EMPLOYEE NO.</th>
                        <th>EMPLOYEE NAME</th>
                        <th>WAGE CLASS</th>
                        <th>WAGE CODE</th>
                        <th>PAYROLL PERIOD</th>
                        <th>JUMLAH</th>
                        <th>NAK</th>
                    </tr>
                </thead>
                <tbody>";

        $data_potongan = $this->db->select("a.no_pinjam, a.tgl_pinjam, a.no_ang, a.no_peg, a.nm_ang, b.blth_angsuran, b.jml_min_angsuran, b.jml_max_angsuran, sum(b.jml_min_angsuran + b.jml_max_angsuran) jml_angs_bonus")
            ->from("t_pinjaman_ang a")->join("t_pinjaman_ang_det b", "a.no_pinjam=b.no_pinjam")
            ->where_in("a.kd_pinjaman", array("2", "4"))
            ->where("a.kd_prsh", $data_req['kd_prsh'])
            ->where("b.blth_angsuran", ($data_req['tahun'] . "-" . $data_req['bulan']))
            ->group_start()
            ->where("b.jml_min_angsuran >", 0)
            ->or_where("b.jml_max_angsuran >", 0)
            ->group_end()
            ->group_by("a.no_ang")
            ->get();

        $no        = 1;
        $jml_total = 0;

        foreach ($data_potongan->result_array() as $key => $value) {
            $view .= "
                <tr>
                    <td style=\"text-align: right\">" . $no . "</td>
                    <td style=\"text-align: center\">PG</td>
                    <td style=\"text-align: center\">" . $value['no_peg'] . "</td>
                    <td style=\"text-align: left\">" . $value['nm_ang'] . "</td>
                    <td style=\"text-align: center\">GR</td>
                    <td style=\"text-align: center\">PK02</td>
                    <td style=\"text-align: center\">" . $data_req['tahun'] . "-" . $data_req['bulan'] . "</td>
                    <td style=\"text-align: right\">" . number_format($value['jml_angs_bonus'], 2) . "</td>
                    <td style=\"text-align: center\">" . $value['no_ang'] . "</td>
                </tr>";

            $no++;
            $jml_total += $value['jml_angs_bonus'];
        }

        $view .= "</tbody>
            <thead>
                <tr>
                    <th colspan=\"7\">Total</th>
                    <th>" . number_format($jml_total, 2) . "</th>
                    <th></th>
                </tr>
            </thead>
        </table> ";

        echo $view;
    }

    public function cek_nomor_cetak()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $data_nomor = $this->laporan_model->cek_nomor_lap_potga($data_post);
        } else {
            $data_nomor = array(
                'no_sp'       => 0,
                'no_kuitansi' => 0,
            );

        }

        echo json_encode($data_nomor);
    }

    public function simpan_nomor_cetak()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $this->laporan_model->simpan_nomor_lap_potga($data_post);
        }
    }
}
