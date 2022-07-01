<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cetak extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("anggota_model");
        $this->load->model("pinjaman_model");
        // $this->load->model("simpanan_model");
    }

    public function cetak_pinjaman($no_pinjam)
    {
        $this->load->library('mypdf');

        $pdf = new mypdf("P", "mm", array("350", "215"));

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_pinjaman_" . $no_pinjam . "_" . date("Y-m-d_H:i:s") . ".pdf";

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

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddPage();

        $cari['field'] = array("no_pinjam");
        $cari['value'] = $no_pinjam;

        $data_pinjaman = $this->pinjaman_model->get_simulasi_pinjaman(0, $cari)->row_array(0);

        $cari['field'] = array("no_ang");
        $cari['value'] = $data_pinjaman['no_ang'];

        $data_anggota = $this->anggota_model->get_anggota(0, $cari)->row_array(0);

        $pdf->SetFontSize('15');
        $pdf->Cell("0", "5", "Bukti Pinjaman " . $data_pinjaman['nm_pinjaman'], 0, 1, 'C');

        $pdf->Ln();

        $pdf->SetFontSize('9');

        $view = "<table width=\"100%\">
                <tr>
                    <td width=\"100px\">Nama</td>
                    <td width=\"10px\">:</td>
                    <td width=\"270px\">" . $data_anggota['nm_ang'] . "</td>
                    <td width=\"80px\">Perusahaan</td>
                    <td width=\"10px\">:</td>
                    <td width=\"250px\">" . $data_anggota['nm_prsh'] . "</td>
                </tr>
                <tr>
                    <td>No. Anggota / Peg</td>
                    <td>:</td>
                    <td>" . $data_anggota['no_ang'] . " / " . $data_anggota['no_peg'] . "</td>
                    <td>Departemen</td>
                    <td>:</td>
                    <td>" . $data_anggota['nm_dep'] . "</td>
                </tr>
            </table><br><br>";

        /*<tr>
        <td>Plafon Pot. Gaji</td>
        <td>:</td>
        <td>" . number_format($data_anggota['plafon'], 2) . "</td>
        <td>Sisa Plafon</td>
        <td>:</td>
        <td>" . number_format(($data_anggota['plafon'] - $data_anggota['plafon_pakai']), 2) . "</td>
        </tr>*/

        $view .= "<table width=\"100%\">
                <tr>
                    <td width=\"100px\">Tgl. Pinjam</td>
                    <td width=\"10px\">:</td>
                    <td width=\"120px\">" . $data_pinjaman['tgl_pinjam'] . "</td>
                    <td width=\"100px\">Jumlah Pinjam</td>
                    <td width=\"10px\">:</td>
                    <td width=\"120px\" align=\"right\">" . number_format($data_pinjaman['jml_pinjam'], 2) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td width=\"80px\">Jangka</td>
                    <td width=\"10px\">:</td>
                    <td width=\"100px\">" . $data_pinjaman['tempo_bln'] . " Bulan</td>
                </tr>
                <tr>
                    <td>Margin</td>
                    <td>:</td>
                    <td>" . $data_pinjaman['margin'] . "%</td>
                    <td>Jml. Margin</td>
                    <td>:</td>
                    <td align=\"right\">" . number_format($data_pinjaman['jml_margin'], 2) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Angsuran</td>
                    <td>:</td>
                    <td>" . number_format($data_pinjaman['angsuran'], 2) . "</td>
                </tr>
                <tr>
                    <td>Tgl. Jatuh Tempo</td>
                    <td>:</td>
                    <td>" . balik_tanggal($data_pinjaman['tgl_jt']) . "</td>
                    <td>Biaya Admin</td>
                    <td>:</td>
                    <td align=\"right\">" . number_format($data_pinjaman['jml_biaya_admin'], 2) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
            </table><br><br>";

        $pdf->writeHTML($view, true, false, true, false, '');

        $view_angsuran = "<table border=\"1\" cellpadding=\"3px\">
            <tr>
                <th width=\"30px\">No.</th>
                <th width=\"55px\">Periode</th>
                <th width=\"35px\">Hari</th>
                <th width=\"95px\">Pokok Awal</th>
                <th width=\"90px\">Pokok</th>
                <th width=\"90px\">Margin</th>
                <th width=\"90px\">Angsuran</th>
                <th width=\"95px\">Pokok Akhir</th>
                <th width=\"135px\">Keterangan</th>
            </tr>";

        $no = 1;

        $t_pokok_awal  = 0;
        $t_pokok       = 0;
        $t_bunga       = 0;
        $t_angsuran    = 0;
        $t_pokok_akhir = 0;

        $data_pinjaman['tgl_pinjam'] = balik_tanggal($data_pinjaman['tgl_pinjam']);

        // exit(baca_array($data_pinjaman));

        if ($data_pinjaman['kd_pinjaman'] == "1") {
            $data_angsuran = $this->pinjaman_model->get_angsuran_reguler($data_pinjaman);
        } else if ($data_pinjaman['kd_pinjaman'] == "2") {
            $data_angsuran = $this->pinjaman_model->get_angsuran_kkb($data_pinjaman);
        } else if ($data_pinjaman['kd_pinjaman'] == "3") {
            $data_angsuran = $this->pinjaman_model->get_angsuran_pht($data_pinjaman);
        } else if ($data_pinjaman['kd_pinjaman'] == "4") {
            $data_angsuran = $this->pinjaman_model->get_angsuran_kpr($data_pinjaman);
        }

        // exit(baca_array($data_angsuran));

        // $data_saldo = $this->pinjaman_model->get_simulasi_angsuran($data_pinjaman['no_pinjam']);

        foreach ($data_angsuran as $key => $value) {
            $teks_nama_pot_bonus = ($data_pinjaman['kd_pinjaman'] == "4") ? "Pot. Gaji, " : "";

            $view_angsuran .= "
                <tr>
                    <td align=\"right\">" . $no . "</td>
                    <td>" . $value['blth_angsuran'] . "</td>
                    <td>" . $value['hari'] . "</td>
                    <td align=\"right\">" . number_format($value['pokok_awal'], 2) . "</td>
                    <td align=\"right\">" . number_format($value['pokok_per_bulan'], 2) . "</td>
                    <td align=\"right\">" . number_format($value['margin_per_bulan'], 2) . "</td>
                    <td align=\"right\">" . number_format($value['angsuran_per_bulan'], 2) . "</td>
                    <td align=\"right\">" . number_format($value['pokok_akhir'], 2) . "</td>
                    <td>" . $value['nm_pot_bonus'] . "</td>
                </tr>";

            $no++;
            $t_pokok_awal += $value['pokok_awal'];
            $t_pokok += $value['pokok_per_bulan'];
            $t_bunga += $value['margin_per_bulan'];
            $t_angsuran += $value['angsuran_per_bulan'];
            $t_pokok_akhir += $value['pokok_akhir'];
        }

        $view_angsuran .= "
                <tr>
                    <th colspan=\"3\">Total</th>
                    <th align=\"right\">" . number_format(0, 2) . "</th>
                    <th align=\"right\">" . number_format($t_pokok, 2) . "</th>
                    <th align=\"right\">" . number_format($t_bunga, 2) . "</th>
                    <th align=\"right\">" . number_format($t_angsuran, 2) . "</th>
                    <th align=\"right\">" . number_format(0, 2) . "</th>
                    <th></th>
                </tr>
            </table>";

        $pdf->SetFontSize('9');

        $pdf->writeHTML($view_angsuran, true, false, true, false, '');

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_pinjaman_sudah_realisasi($no_pinjam)
    {
        $this->load->library('mypdf');

        $pdf = new mypdf("P", "mm", array("350", "215"));

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_pinjaman_" . $no_pinjam . "_" . date("Y-m-d_H:i:s") . ".pdf";

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

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddPage();

        $cari['field'] = array("no_pinjam");
        $cari['value'] = $no_pinjam;

        $data_pinjaman = $this->pinjaman_model->get_pinjaman(0, $cari)->row_array(0);

        $cari['field'] = array("no_ang");
        $cari['value'] = $data_pinjaman['no_ang'];

        $data_anggota = $this->anggota_model->get_anggota(0, $cari)->row_array(0);

        $pdf->SetFontSize('15');
        $pdf->Cell("0", "5", "Bukti Pinjaman " . $data_pinjaman['nm_pinjaman'], 0, 1, 'C');

        $pdf->Ln();

        $pdf->SetFontSize('9');

        $view = "<table width=\"100%\">
                <tr>
                    <td width=\"100px\">Nama</td>
                    <td width=\"10px\">:</td>
                    <td width=\"270px\">" . $data_anggota['nm_ang'] . "</td>
                    <td width=\"80px\">Perusahaan</td>
                    <td width=\"10px\">:</td>
                    <td width=\"250px\">" . $data_anggota['nm_prsh'] . "</td>
                </tr>
                <tr>
                    <td>No. Anggota / Peg</td>
                    <td>:</td>
                    <td>" . $data_anggota['no_ang'] . " / " . $data_anggota['no_peg'] . "</td>
                    <td>Departemen</td>
                    <td>:</td>
                    <td>" . $data_anggota['nm_dep'] . "</td>
                </tr>
            </table><br><br>";

        /*<tr>
        <td>Plafon Pot. Gaji</td>
        <td>:</td>
        <td>" . number_format($data_anggota['plafon'], 2) . "</td>
        <td>Sisa Plafon</td>
        <td>:</td>
        <td>" . number_format(($data_anggota['plafon'] - $data_anggota['plafon_pakai']), 2) . "</td>
        </tr>*/

        $view .= "<table width=\"100%\">
                <tr>
                    <td width=\"100px\">Tgl. Pengajuan</td>
                    <td width=\"10px\">:</td>
                    <td width=\"120px\">" . balik_tanggal($data_pinjaman['tgl_simulasi']) . "</td>
                    <td width=\"100px\">Jumlah Pinjam</td>
                    <td width=\"10px\">:</td>
                    <td width=\"120px\" align=\"right\">" . number_format($data_pinjaman['jml_pinjam'], 2) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td width=\"80px\">Jangka</td>
                    <td width=\"10px\">:</td>
                    <td width=\"100px\">" . $data_pinjaman['tempo_bln'] . " Bulan</td>
                </tr>
                <tr>
                    <td width=\"100px\">Tgl. Realisasi</td>
                    <td width=\"10px\">:</td>
                    <td width=\"120px\">" . $data_pinjaman['tgl_pinjam'] . "</td>
                    <td>Jml. Margin</td>
                    <td>:</td>
                    <td align=\"right\">" . number_format($data_pinjaman['jml_margin'], 2) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Angsuran</td>
                    <td>:</td>
                    <td align=\"right\">" . number_format($data_pinjaman['angsuran'], 2) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>Margin</td>
                    <td>:</td>
                    <td>" . $data_pinjaman['margin'] . "%</td>
                    <td>Biaya Admin</td>
                    <td>:</td>
                    <td align=\"right\">" . number_format($data_pinjaman['jml_biaya_admin'], 2) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>Min. Angsuran</td>
                    <td>:</td>
                    <td align=\"right\">" . number_format($data_pinjaman['jml_min_angsuran'], 2) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>Tgl. Jt Tempo</td>
                    <td>:</td>
                    <td>" . balik_tanggal($data_pinjaman['tgl_jt']) . "</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Max. Angsuran</td>
                    <td>:</td>
                    <td align=\"right\">" . number_format($data_pinjaman['jml_max_angsuran'], 2) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
            </table><br><br>";

        $pdf->writeHTML($view, true, false, true, false, '');

        $view_angsuran = "<table border=\"1\" cellpadding=\"3px\">
            <tr>
                <th width=\"40px\">Ke</th>
                <th width=\"55px\">Periode</th>
                <th width=\"30px\">Hari</th>
                <th width=\"95px\">Posisi Awal</th>
                <th width=\"90px\">Pokok</th>
                <th width=\"90px\">Margin</th>
                <th width=\"90px\">Angsuran</th>
                <th width=\"95px\">Posisi Akhir</th>
                <th width=\"135px\">Keterangan</th>
            </tr>";

        $no = 1;

        $t_pokok_awal  = 0;
        $t_pokok       = 0;
        $t_bunga       = 0;
        $t_angsuran    = 0;
        $t_pokok_akhir = 0;

        $data_saldo = $this->pinjaman_model->get_angsuran($data_pinjaman['no_pinjam']);

        foreach ($data_saldo->result_array() as $key => $value) {
            // $teks_nama_pot_bonus = ($data_pinjaman['kd_pinjaman'] == "4") ? "Pot. Gaji, " : "";

            $view_angsuran .= "
                <tr>
                    <td align=\"right\">" . $value['angs_ke'] . "</td>
                    <td>" . date("m-Y", mktime(0, 0, 0, $value['bulan_angsuran'], 1, $value['tahun_angsuran'])) . "</td>
                    <td>" . $value['hari'] . "</td>
                    <td align=\"right\">" . number_format($value['pokok_awal'], 2) . "</td>
                    <td align=\"right\">" . number_format($value['pokok'], 2) . "</td>
                    <td align=\"right\">" . number_format($value['bunga'], 2) . "</td>
                    <td align=\"right\">" . number_format($value['angsuran'], 2) . "</td>
                    <td align=\"right\">" . number_format($value['pokok_akhir'], 2) . "</td>
                    <td>" . $value['nm_pot_bonus'] . "</td>
                </tr>";

            $no++;
            $t_pokok_awal += $value['pokok_awal'];
            $t_pokok += $value['pokok'];
            $t_bunga += $value['bunga'];
            $t_angsuran += $value['angsuran'];
            $t_pokok_akhir += $value['pokok_akhir'];
        }

        $view_angsuran .= "
                <tr>
                    <th colspan=\"3\">Total</th>
                    <th align=\"right\">" . number_format(0, 2) . "</th>
                    <th align=\"right\">" . number_format($t_pokok, 2) . "</th>
                    <th align=\"right\">" . number_format($t_bunga, 2) . "</th>
                    <th align=\"right\">" . number_format($t_angsuran, 2) . "</th>
                    <th align=\"right\">" . number_format(0, 2) . "</th>
                    <th></th>
                </tr>
            </table>";

        $pdf->SetFontSize('9');

        $pdf->writeHTML($view_angsuran, true, false, true, false, '');

        $pdf->Output($judul_file, 'I');
    }

    public function get_data_cetak_buku_ss1()
    {
        $get_request = get_request("post");

        $data_post = json_decode(base64_decode($get_request['data']), true);

        if ($data_post) {
            $tgl_awal  = isset($data_post['tgl_awal']) ? balik_tanggal($data_post['tgl_awal']) : "";
            $tgl_akhir = isset($data_post['tgl_akhir']) ? balik_tanggal($data_post['tgl_akhir']) : "";

            $is_cetak = "";

            if ($data_post['mode_data'] == "belumcetak") {
                $is_cetak = "0";
            }

            $data_simpanan = $this->simpanan_model->get_data_cetak_buku_ss1($data_post['no_ang'], $tgl_awal, $tgl_akhir, $is_cetak);

            // if ($data_simpanan->num_rows() > 0) {
            //     $tgl_awal = balik_tanggal($data_simpanan->row(0)->tgl_simpan);
            // } else {
            //     $tgl_awal = date("Y-m-d");
            // }

            $strtanggal = strtotime($tgl_awal);
            $hari_awal  = date("d", $strtanggal);
            $bulan      = date("m", $strtanggal);
            $tahun      = date("Y", $strtanggal);

            $saldo_awal_harian = $this->simpanan_model->get_saldo_awal_cetak_buku_ss1($data_post['no_ang'], $tahun, $bulan, $hari_awal);

            $saldo_akhir = $saldo_awal_harian;

            if (isset($data_post['mode_cetak']) and $data_post['mode_cetak'] == "cetaklangsung") {
                $data_terakhir = $this->db->where("no_ang", $data_post['no_ang'])
                    ->where("is_cetak", "1")
                    ->order_by("tgl_simpan desc, no_simpan desc")
                    ->limit("1")
                    ->get("t_simpanan_ang");

                $baris_ke = ($data_terakhir->num_rows() > 0) ? ($data_terakhir->row()->baris_cetak + 1) : 1;

                if ($baris_ke == 25) {
                    $baris_ke = 1;
                }
            } else {
                $baris_ke = $data_post['baris_ke'];
            }

            $hasil = "<table class=\"table table-condensed table-striped\" id=\"tabel_data_cetak\">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Bukti</th>
                            <th>Tanggal</th>
                            <th>Sandi</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                            <th>Saldo</th>
                            <th>Status Cetak</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($data_simpanan->result_array() as $key => $value) {
                // if ($key == 0 and $saldo_akhir == 0) {
                //     $strtanggal = strtotime($value['tgl_simpan']);
                //     $hari_awal  = date("d", $strtanggal);
                //     $bulan      = date("m", $strtanggal);
                //     $tahun      = date("Y", $strtanggal);

                //     $saldo_akhir = $this->simpanan_model->get_saldo_awal_cetak_buku_ss1($data_post['no_ang'], $tahun, $bulan, $hari_awal);
                // }

                $saldo_akhir += ($value['kredit_debet'] == "K") ? $value['kredit'] : (0 - $value['debet']);

                if ($value['is_cetak'] == "0") {
                    $status_cetak = "<i class=\"fa fa-times\"></i> Belum dicetak";
                } else {
                    $status_cetak = "<i class=\"fa fa-check\"></i> Sudah dicetak";
                }

                $hasil .= "
                    <tr>
                        <td>" . $baris_ke . "</td>
                        <td>" . $value['no_simpan'] . "</td>
                        <td>" . $value['tgl_simpan'] . "</td>
                        <td>" . $value['kd_jns_transaksi'] . "</td>
                        <td class=\"text-right\">" . number_format($value['debet'], 2) . "</td>
                        <td class=\"text-right\">" . number_format($value['kredit'], 2) . "</td>
                        <td class=\"text-right\">" . number_format($saldo_akhir, 2) . "</td>
                        <td>" . $status_cetak . "</td>
                    </tr>";

                $baris_ke++;

                if ($baris_ke == 25) {
                    $baris_ke = 1;
                }
            }

            $hasil .= "
                    </tbody>
                </table>";

            echo $hasil;
        }
    }

    public function cetak_buku_ss1()
    {
        set_time_limit(0);

        $get_request = get_request("get");

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $html = "<style>
                .border_atas {
                    border-top: 1px solid black;
                }
                .border_bawah {
                    border-bottom: 1px solid black;
                }
                .border_kanan {
                    border-right: 1px solid black;
                }
                .border_kiri {
                    border-left: 1px solid black;
                }
                .text_center {
                    text-align: center;
                }
                .text_right {
                    text-align: right;
                }
                table {
                    font-size: 8px;
                }
            </style>";

        $tgl_awal  = isset($data_req['tgl_awal']) ? balik_tanggal($data_req['tgl_awal']) : "";
        $tgl_akhir = isset($data_req['tgl_akhir']) ? balik_tanggal($data_req['tgl_akhir']) : "";

        $is_cetak = "";

        if ($data_req['mode_data'] == "belumcetak") {
            $is_cetak = "0";
        }

        $data_simpanan = $this->simpanan_model->get_data_cetak_buku_ss1($data_req['no_ang'], $tgl_awal, $tgl_akhir, $is_cetak);

        if ($data_simpanan->num_rows() > 0) {
            $tgl_awal = balik_tanggal($data_simpanan->row(0)->tgl_simpan);
        } else {
            $tgl_awal = date("Y-m-d");
        }

        $strtanggal = strtotime($tgl_awal);
        $hari_awal  = date("d", $strtanggal);
        $bulan      = date("m", $strtanggal);
        $tahun      = date("Y", $strtanggal);

        $saldo_awal_harian = $this->simpanan_model->get_saldo_awal_cetak_buku_ss1($data_req['no_ang'], $tahun, $bulan, $hari_awal);

        $saldo_akhir = $saldo_awal_harian;

        if (isset($data_req['mode_cetak']) and $data_req['mode_cetak'] == "cetaklangsung") {
            $data_terakhir = $this->db->where("no_ang", $data_req['no_ang'])
                ->where("is_cetak", "1")
                ->order_by("tgl_simpan desc, no_simpan desc")
                ->limit("1")
                ->get("t_simpanan_ang");

            $no = ($data_terakhir->num_rows() > 0) ? ($data_terakhir->row()->baris_cetak + 1) : 1;

            if ($no == 25) {
                $no = 1;
            }
        } else {
            $no = $data_req['baris_ke'];
        }

        $cwidth[0] = "15";
        $cwidth[1] = "52";
        $cwidth[2] = "30";
        $cwidth[3] = "65";
        $cwidth[4] = "72";
        $cwidth[5] = "75";
        $cwidth[6] = "20";
        $cheight   = "11";

        $html .= "<table border=\"0\">";

        if ($no > 1) {
            for ($i = 1; $i < $no; $i++) {
                $html .= "<tr>
                                <td colspan=\"7\" height=\"" . $cheight . "\">&nbsp;</td>
                            </tr>";

                if ($i == "12") {
                    /*baris 12 adalah baris terakhir pada halaman atas*/
                    for ($spasi = 1; $spasi <= 7; $spasi++) {
                        $html .= "<tr>
                                    <td colspan=\"7\" height=\"" . $cheight . "\">&nbsp;</td>
                                </tr>";
                    }
                }
            }
        }

        foreach ($data_simpanan->result_array() as $key => $value) {
            $saldo_akhir += ($value['kredit_debet'] == "K") ? $value['kredit'] : (0 - $value['debet']);

            $teks_pagebreak = "";

            if ($no > 24) {
                $teks_pagebreak = "pagebreak=\"true\"";
                $no             = "1";
            }

            $html .= "<tr " . $teks_pagebreak . ">
                    <td class=\"text_right\" width=\"" . $cwidth[0] . "\" height=\"" . $cheight . "\">" . $no . "</td>
                    <td width=\"" . $cwidth[1] . "\" height=\"" . $cheight . "\">" . $value['tgl_simpan'] . "</td>
                    <td width=\"" . $cwidth[2] . "\" height=\"" . $cheight . "\">" . $value['kd_jns_transaksi'] . "</td>
                    <td class=\"text_right\" width=\"" . $cwidth[3] . "\" height=\"" . $cheight . "\">" . number_format($value['debet'], 2) . "</td>
                    <td class=\"text_right\" width=\"" . $cwidth[4] . "\" height=\"" . $cheight . "\">" . number_format($value['kredit'], 2) . "</td>
                    <td class=\"text_right\" width=\"" . $cwidth[5] . "\" height=\"" . $cheight . "\">" . number_format($saldo_akhir, 2) . "</td>
                    <td width=\"" . $cwidth[6] . "\" height=\"" . $cheight . "\"></td>
                </tr>";

            $no++;

            if ($no == "13") {
                for ($spasi = 1; $spasi <= 7; $spasi++) {
                    $html .= "<tr>
                                <td colspan=\"7\" height=\"" . $cheight . "\">&nbsp;</td>
                            </tr>";
                }
            }
        }

        $html .= "</table>";

        $this->load->library('mypdf');

        $pdf = new Mypdf("P", PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('0', '31', '7');
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        $pdf->setFontSize("8");
		
		$js = 'print(true);';

		// set javascript
		$pdf->IncludeJS($js);

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output("Cetak_buku_sukarela_" . $data_req['no_ang'] . ".pdf", 'I');
    }

    public function set_status_cetak_ss1()
    {
        $data_post = get_request("post");

        if ($data_post) {
            $tgl_awal  = isset($data_post['tgl_awal']) ? balik_tanggal($data_post['tgl_awal']) : "";
            $tgl_akhir = isset($data_post['tgl_akhir']) ? balik_tanggal($data_post['tgl_akhir']) : "";

            $is_cetak = "";

            if ($data_post['mode_data'] == "belumcetak") {
                $is_cetak = "0";
            }

            $data_simpanan = $this->simpanan_model->get_data_cetak_buku_ss1($data_post['no_ang'], $tgl_awal, $tgl_akhir, $is_cetak);

            if ($data_simpanan->num_rows() > 0) {
                $tgl_awal = balik_tanggal($data_simpanan->row(0)->tgl_simpan);
            } else {
                $tgl_awal = date("Y-m-d");
            }

            if (isset($data_post['mode_cetak']) and $data_post['mode_cetak'] == "cetaklangsung") {
                $data_terakhir = $this->db->where("no_ang", $data_post['no_ang'])
                    ->where("is_cetak", "1")
                    ->order_by("tgl_simpan desc, no_simpan desc")
                    ->limit("1")
                    ->get("t_simpanan_ang");

                $baris_ke = ($data_terakhir->num_rows() > 0) ? ($data_terakhir->row()->baris_cetak + 1) : 1;

                if ($baris_ke == 25) {
                    $baris_ke = 1;
                }
            } else {
                $baris_ke = $data_post['baris_ke'];
            }

            foreach ($data_simpanan->result_array() as $key => $value) {
                $set_data = array(
                    "is_cetak"    => "1",
                    "baris_cetak" => $baris_ke,
                    "tgl_cetak"   => date("Y-m-d H:i:s"),
                );

                $this->db->set($set_data)->where("no_simpan", $value['no_simpan'])->update("t_simpanan_ang");

                $baris_ke++;

                if ($baris_ke == 25) {
                    $baris_ke = 1;
                }
            }

            return true;
        }
    }

    public function cetak_id_buku()
    {
        set_time_limit(0);

        $get_request = get_request("get");

        $data_req = json_decode(base64_decode($get_request['data']), true);

        $html = "<style>
                .border_atas {
                    border-top: 1px solid black;
                }
                .border_bawah {
                    border-bottom: 1px solid black;
                }
                .border_kanan {
                    border-right: 1px solid black;
                }
                .border_kiri {
                    border-left: 1px solid black;
                }
                .text_center {
                    text-align: center;
                }
                .text_right {
                    text-align: right;
                }
                table {
                    font-size: 8px;
                }
            </style>";

        $cwidth[0] = "15";
        $cwidth[1] = "52";
        $cwidth[2] = "35";
        $cwidth[3] = "56";
        $cwidth[4] = "72";
        $cwidth[5] = "75";
        $cwidth[6] = "20";
        $cheight   = "11";

        $html .= "<table border=\"0\">";

        for ($i = 0; $i < 27; $i++) {
            $html .= "<tr><td colspan=\"2\" height=\"" . $cheight . "\"></td></tr>";
        }
		
		$html .= "<tr><td colspan=\"2\" height=\"11\"></td></tr>";

        $data_nasabah = $this->anggota_model->get_nasabah(0, "", "", "", "1", "", $data_req['no_ang'])->row_array(0);

        $height_data = "15";

        $html .= "<tr>
            <td width=\"150\" height=\"" . $height_data . "\"></td> <td  height=\"" . $height_data . "\">".$data_nasabah['no_ang']."</td> 
        </tr>
        <tr><td></td><td height=\"17\">".$data_nasabah['nm_ang']."</td></tr>
        <tr><td></td><td height=\"16\">".$data_nasabah['no_peg']."</td></tr>
        <tr><td></td><td height=\"17\">".$data_nasabah['no_ang']."</td></tr>
        <tr><td></td><td height=\"" . $height_data . "\">".$data_nasabah['alm_rmh']."</td></tr>
        ";

        $html .= "</table>";

        $this->load->library('mypdf');

        $pdf = new Mypdf("P", PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins('0', '31', '7');
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        $pdf->setFontSize("8");

        $pdf->writeHTML($html, true, false, true, false, '');
		
		$js = 'print(true);';

		// set javascript
		$pdf->IncludeJS($js);

        $pdf->Output("Cetak_id_buku_" . $data_req['no_ang'] . ".pdf", 'I');
    }

}
