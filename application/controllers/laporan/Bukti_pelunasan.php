<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bukti_pelunasan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("pinjaman_model");
        $this->load->model("master_model");
    }

    public function cetak_perhitungan()
    {
        $data_req = get_request();

        $data = json_decode(base64_decode($data_req['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = "A4";

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_perhitungan_" . $data['no_ang'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

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

        $pdf->SetMargins("10", "5", "5");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddPage();

        $pdf->SetFontSize("10");

        $data_ttd = $this->db->limit(1)->get("s_laporan")->row_array(0);

        $data_anggota = $this->db->where("no_ang", $data['no_ang'])->get("t_anggota")->row_array(0);

        $html = "";

        if ($data['kd_pinjaman'] != "") {
            $html .= "
                <h3 style=\"text-align: center\">PERHITUNGAN PELUNASAN
                    <br>PINJAMAN UANG " . $data['nm_pinjaman'] . "
                </h3>
                <br>
                <table border=\"0\">
                    <tr>
                        <th style=\"width: 3cm\">NAMA</th>
                        <th style=\"width: 16cm\">: " . $data_anggota['nm_ang'] . "</th>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <th>: " . $data_anggota['no_peg'] . "</th>
                    </tr>
                    <tr>
                        <th>NAK</th>
                        <th>: " . $data_anggota['no_ang'] . "</th>
                    </tr>
                    <tr>
                        <th>DEPT/BAGIAN</th>
                        <th>: " . $data_anggota['nm_dep'] . " / " . $data_anggota['nm_bagian'] . "</th>
                    </tr>
                </table>
                <br>
                <br>";
        }

        if ($data['kd_pinjaman'] == "1" or $data['is_sparepart'] == "1") {
            $data_tempo_bln = $data['tempo_bln'];

            foreach ($this->pinjaman_model->get_array_tempo_bln(1) as $key => $value) {
                if ($data_post['tempo_bln'] <= $value) {
                    $data_tempo_bln = $value;
                }
            }

            $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, date("Y-m-d"));

            if ($data_margin_pinjaman->num_rows() > 0) {
                $data['bunga'] = $data_margin_pinjaman->row(0)->rate;
            }
        }

        $data['no_pinjam'] = $data['no_ref_bukti'];

        $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data);

        if ($data['kd_pinjaman'] == "1" and $perhitungan['perhitungan_selisih'] >= 0) {
            $cari['value'] = $data['no_ref_bukti'];
            $cari['field'] = array("no_pinjam");

            $data_pinjaman = $this->pinjaman_model->get_pinjaman(0, $cari)->row_array(0);

            if ($data['sisa_bln'] < $data['tempo_bln']) {
                $html .= "
                    <table border=\"0\">
                        <tr>
                            <td width=\"4cm\">Tanggal Pelunasan</td>
                            <td>: " . balik_tanggal($data['tgl_lunas']) . "</td>
                        </tr>
                        <tr>
                            <td>Tanggal Realisasi</td>
                            <td>: " . $data_pinjaman['tgl_pinjam'] . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Pokok</td>
                            <td>: " . number_format($data_pinjaman['jml_pinjam'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Biaya Admin</td>
                            <td>: " . number_format($data_pinjaman['jml_biaya_admin'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Tahun (%)</td>
                            <td>: " . $data['bunga'] . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Bulan (%)</td>
                            <td>: " . number_format($perhitungan['bunga_bln'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jangka (Bulan)</td>
                            <td>: " . $data_pinjaman['tempo_bln'] . "</td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td>: " . $perhitungan['sudah_diangsur'] . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Angsuran/Bulan</td>
                            <td>: " . number_format($data_pinjaman['angsuran'], 2) . "</td>
                        </tr>
                    </table>
                    <br> <br>
                    <table border=\"0\">
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Lama</u></th>
                        </tr>
                        <tr>
                            <td style=\"width: 5.2cm\">Pokok Pinjaman</td>
                            <td style=\"text-align: right; width: 7cm\">Rp " . number_format($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right; width: 4cm\">" . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Bunga</td>
                            <td style=\"text-align: right\">Rp " . number_format($data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_margin'] / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td style=\"text-align: right\">Rp " . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) + $data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp  </td>
                            <td style=\"text-align: right\">" . number_format((($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) + $data_pinjaman['jml_margin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: left\"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td style=\"text-align: right\">" . $perhitungan['sudah_diangsur'] . " x " . number_format($data_pinjaman['angsuran'], 2) . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['sudah_diangsur'] * $data_pinjaman['angsuran'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Sisa Angsuran</td>
                            <td style=\"text-align: right\">" . $data['sisa_bln'] . " x " . number_format($data_pinjaman['angsuran'], 2) . " = Rp </td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Baru</u></th>
                        </tr>
                        <tr>
                            <td>Sisa Pokok Pinjaman</td>
                            <td style=\"text-align: right\">" . $data['sisa_bln'] . " x " . number_format($perhitungan['jml_pokok_bln'], 2) . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sisa Angsuran yg harus dibayar</td>
                            <td style=\"text-align: right\">( 1 + " . number_format($perhitungan['bunga_bln'], 2) . "% )<sup>" . $perhitungan['sudah_diangsur'] . "</sup> x " . number_format($perhitungan['sisa_pokok'], 2) . " = Rp </td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td><strong>Koreksi Bunga</strong></td>
                            <td>Perhitungan Lama</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Perhitungan Baru</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Selisih</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_selisih'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                    </table>
                    ";
            } else {
                $html .= "
                    <table>
                        <tr>
                            <td width=\"4cm\">Tanggal Pelunasan</td>
                            <td>: " . balik_tanggal($data['tgl_lunas']) . "</td>
                        </tr>
                        <tr>
                            <td>Tanggal Realisasi</td>
                            <td>: " . $data_pinjaman['tgl_pinjam'] . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Pokok</td>
                            <td>: " . number_format($data_pinjaman['jml_pinjam'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Biaya Admin</td>
                            <td>: " . number_format($data_pinjaman['jml_biaya_admin'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Tahun (%)</td>
                            <td>: " . $data['bunga'] . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Bulan (%)</td>
                            <td>: " . number_format($perhitungan['bunga_bln'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jangka (Bulan)</td>
                            <td>: " . $data_pinjaman['tempo_bln'] . "</td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur (Bulan)</td>
                            <td>: " . $perhitungan['sudah_diangsur'] . "</td>
                        </tr>
                        <tr>
                            <td>Masa</td>
                            <td>: " . $perhitungan['jml_hari'] . "</td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <table border=\"0\">
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Lama</u></th>
                        </tr>
                        <tr>
                            <td style=\"4cm\">Pokok Pinjaman</td>
                            <td style=\"text-align: right; width: 6cm\">Rp " . number_format($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right; width: 4cm\">" . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Bunga</td>
                            <td style=\"text-align: right\">Rp " . number_format($data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_margin'] / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td style=\"text-align: right\">Rp " . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) + $data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp  </td>
                            <td style=\"text-align: right\">" . number_format((($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) + $data_pinjaman['jml_margin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: left\"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Baru</u></th>
                        </tr>
                        <tr>
                            <td>Pokok Pinjaman</td>
                            <td style=\"text-align: right\">= Rp</td>
                            <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_pinjam'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Biaya Admin</td>
                            <td style=\"text-align: right\">= Rp </td>
                            <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_biaya_admin'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Bunga Harian</td>
                            <td style=\"text-align: right;\"><u>( " . number_format($data_pinjaman['jml_pinjam'], 2) . " x " . $data_pinjaman['margin'] . "% ) x " . $perhitungan['jml_hari'] . "</u> = Rp </td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['jml_bunga_harian'], 2) . "</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style=\"text-align: center\">360</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Angsuran yang harus dibayar</td>
                            <td style=\"text-align: right\">= Rp</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td><strong>Koreksi Bunga</strong></td>
                            <td>Perhitungan Lama</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Perhitungan Baru</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Selisih</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_selisih'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                    </table>
                ";
            }

            $html .= "
                <br>
                <br>
                <br>
                <br>
                <table border=\"0\">
                    <tr>
                        <td style=\"width: 6.5cm\"></td>
                        <td style=\"width: 6.5cm\"></td>
                        <td style=\"width: 6.5cm\">Gresik, " . balik_tanggal($data['tgl_lunas']) . "</td>
                    </tr>
                    <tr>
                        <td>Menyetujui,</td>
                        <td>Mengetahui,</td>
                        <td>Pembuat,</td>
                    </tr>
                    <tr>
                        <td>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><strong>" . $data_ttd['manager_adm_keuangan'] . "</strong></td>
                        <td><strong>" . $data_ttd['kabid_keuangan'] . "</strong></td>
                        <td><strong>" . $data_ttd['kaunit_potga'] . "</strong></td>
                    </tr>
                    <tr>
                        <td>(Mgr. Adm & Keuangan)</td>
                        <td>(Kabid Keuangan)</td>
                        <td>(Ka. Unit Potga)</td>
                    </tr>
                </table>";

        } else if ($data['kd_pinjaman'] == "3") {

        } else if (in_array($data['kd_pinjaman'], array("2", "4"))) {

        } else if ($data['is_sparepart'] == "1" and $perhitungan['perhitungan_selisih'] >= 0) {
            $html .= "
                <h3 style=\"text-align: center\">PERHITUNGAN PELUNASAN SPAREPART</h3>
                <br>
                <table border=\"0\">
                    <tr>
                        <th style=\"width: 3cm\">NAMA</th>
                        <th style=\"width: 16cm\">: " . $data_anggota['nm_ang'] . "</th>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <th>: " . $data_anggota['no_peg'] . "</th>
                    </tr>
                    <tr>
                        <th>NAK</th>
                        <th>: " . $data_anggota['no_ang'] . "</th>
                    </tr>
                    <tr>
                        <th>DEPT/BAGIAN</th>
                        <th>: " . $data_anggota['nm_dep'] . " / " . $data_anggota['nm_bagian'] . "</th>
                    </tr>
                </table>
                <br>
                <br>";

            if ($data['sisa_bln'] < $data['tempo_bln']) {
                $html .= "
                    <table>
                        <tr>
                            <td width=\"4cm\">Tanggal Pelunasan</td>
                            <td>: " . balik_tanggal($data['tgl_lunas']) . "</td>
                        </tr>
                        <tr>
                            <td>Tanggal Realisasi</td>
                            <td>: " . $data['tgl_ref_bukti'] . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Pokok</td>
                            <td>: " . number_format($data['jml_pokok'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Biaya Admin</td>
                            <td>: " . number_format($data['jml_admin'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Tahun (%)</td>
                            <td>: " . $data['bunga'] . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Bulan (%)</td>
                            <td>: " . number_format($perhitungan['bunga_bln'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jangka (Bulan)</td>
                            <td>: " . $data['tempo_bln'] . "</td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td>: " . $perhitungan['sudah_diangsur'] . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Angsuran/Bulan</td>
                            <td>: " . number_format($data['angsuran'], 2) . "</td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Lama</u></th>
                        </tr>
                        <tr>
                            <td width=\"5.2cm\">Pokok Pinjaman</td>
                            <td style=\"text-align: right; width: 7cm\">Rp " . number_format($data['jml_pokok'] + $data['jml_admin'], 2) . " / " . $data['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right; width: 4cm\">" . number_format(($data['jml_pokok'] + $data['jml_admin']) / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Bunga</td>
                            <td style=\"text-align: right\">Rp " . number_format($data['jml_bunga'], 2) . " / " . $data['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($data['jml_bunga'] / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td style=\"text-align: right\">Rp " . number_format(($data['jml_pokok'] + $data['jml_admin']) + $data['jml_bunga'], 2) . " / " . $data['tempo_bln'] . " = Rp  </td>
                            <td style=\"text-align: right\">" . number_format((($data['jml_pokok'] + $data['jml_admin']) + $data['jml_bunga']) / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: left\"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td style=\"text-align: right\">" . $perhitungan['sudah_diangsur'] . " x " . number_format($data['angsuran'], 2) . " = Rp</td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['sudah_diangsur'] * $data['angsuran'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sisa Angsuran</td>
                            <td style=\"text-align: right\">" . $data['sisa_bln'] . " x " . number_format($data['angsuran'], 2) . " = Rp</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Baru</u></th>
                        </tr>
                        <tr>
                            <td>Sisa Pokok Pinjaman</td>
                            <td style=\"text-align: right\">" . $data['sisa_bln'] . " x " . number_format($perhitungan['jml_pokok_bln'], 2) . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sisa Angsuran yg harus dibayar</td>
                            <td style=\"text-align: right\">( 1 + " . number_format($perhitungan['bunga_bln'], 2) . "% )<sup>" . $perhitungan['sudah_diangsur'] . "</sup> x " . number_format($perhitungan['sisa_pokok'], 2) . " = Rp</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td><strong>Koreksi Bunga</strong></td>
                            <td>Perhitungan Lama</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Perhitungan Baru</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Selisih</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_selisih'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                    </table>
                ";

            } else {
                $html .= "
                    <table>
                        <tr>
                            <td width=\"4cm\">Tanggal Pelunasan</td>
                            <td>: " . balik_tanggal($data['tgl_lunas']) . "</td>
                        </tr>
                        <tr>
                            <td>Tanggal Realisasi</td>
                            <td>: " . balik_tanggal($data['tgl_ref_bukti']) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Pokok</td>
                            <td>: " . number_format($data['jml_pokok'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Biaya Admin</td>
                            <td>: " . number_format($data['jml_admin'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Tahun (%)</td>
                            <td>: " . $data['bunga'] . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Bulan (%)</td>
                            <td>: " . number_format($perhitungan['bunga_bln'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jangka (Bulan)</td>
                            <td>: " . $data['tempo_bln'] . "</td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td>: " . $perhitungan['sudah_diangsur'] . "</td>
                        </tr>
                        <tr>
                            <td>Masa</td>
                            <td>: " . $perhitungan['jml_hari'] . "</td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <table border=\"0\">
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Lama</u></th>
                        </tr>
                        <tr>
                            <td style=\"4cm\">Pokok Pinjaman</td>
                            <td style=\"text-align: right; width: 6cm\">Rp " . number_format($data['jml_pokok'] + $data['jml_admin'], 2) . " / " . $data['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right; width: 4cm\">" . number_format(($data['jml_pokok'] + $data['jml_admin']) / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Bunga</td>
                            <td style=\"text-align: right\">Rp " . number_format($data['jml_bunga'], 2) . " / " . $data['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($data['jml_bunga'] / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td style=\"text-align: right\">Rp " . number_format(($data['jml_pokok'] + $data['jml_admin']) + $data['jml_bunga'], 2) . " / " . $data['tempo_bln'] . " = Rp  </td>
                            <td style=\"text-align: right\">" . number_format((($data['jml_pokok'] + $data['jml_admin']) + $data['jml_bunga']) / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: left\"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Baru</u></th>
                        </tr>
                        <tr>
                            <td>Pokok Pinjaman</td>
                            <td style=\"text-align: right\">= Rp </td>
                            <td style=\"text-align: right\">" . number_format($data['jml_pokok'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Biaya Admin</td>
                            <td style=\"text-align: right\">= Rp </td>
                            <td style=\"text-align: right\">" . number_format($data['jml_admin'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Bunga Harian</td>
                            <td style=\"text-align: right;\"><u>( " . number_format($data['jml_pokok'], 2) . " x " . $data['bunga'] . "% ) x " . $perhitungan['jml_hari'] . "</u> = Rp</td>
                            <td style=\"text-align: right;\">" . number_format($perhitungan['jml_bunga_harian'], 2) . "</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style=\"text-align: center\" >360</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Angsuran yang harus dibayar</td>
                            <td style=\"text-align: right\">= Rp</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td><strong>Koreksi Bunga</strong></td>
                            <td>Perhitungan Lama</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Perhitungan Baru</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Selisih</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_selisih'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                    </table> ";
            }

            $html .= "
                <br>
                <br>
                <br>
                <br>
                <table border=\"0\">
                    <tr>
                        <td style=\"width: 6.5cm\"></td>
                        <td style=\"width: 6.5cm\"></td>
                        <td style=\"width: 6.5cm\">Gresik, " . balik_tanggal($data['tgl_lunas']) . "</td>
                    </tr>
                    <tr>
                        <td>Menyetujui,</td>
                        <td>Mengetahui,</td>
                        <td>Pembuat,</td>
                    </tr>
                    <tr>
                        <td>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><strong>" . $data_ttd['manager_adm_keuangan'] . "</strong></td>
                        <td><strong>" . $data_ttd['kabid_keuangan'] . "</strong></td>
                        <td><strong>" . $data_ttd['kaunit_potga'] . "</strong></td>
                    </tr>
                    <tr>
                        <td>(Mgr. Adm & Keuangan)</td>
                        <td>(Kabid Keuangan)</td>
                        <td>(Ka. Unit Potga)</td>
                    </tr>
                </table>";
        }

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_slip1()
    {
        set_time_limit(0);

        $get_request = get_request();

        $data = json_decode(base64_decode($get_request['data']), true);

        if ($data['kd_pinjaman'] == "1" or $data['is_sparepart'] == "1") {
            $data_tempo_bln = $data['tempo_bln'];

            foreach ($this->pinjaman_model->get_array_tempo_bln(1) as $key => $value) {
                if ($data_post['tempo_bln'] <= $value) {
                    $data_tempo_bln = $value;
                }
            }

            $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, date("Y-m-d"));

            if ($data_margin_pinjaman->num_rows() > 0) {
                $data['bunga'] = $data_margin_pinjaman->row(0)->rate;
            }
        }

        $data['no_pinjam'] = $data['no_ref_bukti'];

        $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data);

        $this->load->library('mypdf');

        $ukuran_kertas = array(215, 215);

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_slip1_" . $data['no_ang'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

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

        $pdf->AddPage();

        $pdf->SetFontSize("9");

        $pdf->SetY(20);

        $pdf->Cell(70, 0, "");
        $pdf->Cell(50, 0, $data['nm_ang'] . " / " . $data['no_peg'] . " / " . $data['no_ang'], 0, 0, "L", 0, 0, 1);

        $pdf->Ln(10);

        $pdf->Cell(90, 0, "");
        $pdf->Cell(30, 0, number_format($perhitungan['jml_bayar'], 2), 0, 0, "R");

        $pdf->Ln(10);

        $terbilang = ucwords(terbilang($perhitungan['jml_bayar']));

        $pdf->Cell(70, 0, "");
        $pdf->MultiCell(120, 0, $terbilang, 0, "L");

        $pdf->SetY(60);

        // $array_bulan = array_bulan();
        // $bulan       = $array_bulan[$data['bulan']];

        $angs_ke = $data['angsur_bln'] + 1;

        $pdf->Cell(70, 0, "");
        $pdf->Cell(0, 0, "Pembayaran Pelunasan Angsuran Ke " . $angs_ke . " s.d. " . $data['tempo_bln']);
        $pdf->Ln();

        if ($perhitungan['perhitungan_selisih'] >= 0) {
            $pdf->Ln();
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "1141");
            $pdf->Cell(60, 0, "PIUTANG ANGGOTA");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan['sisa_angs_lama'], 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "3310");
            $pdf->Cell(60, 0, "PENDAPATAN YANG DITANGGUHKAN");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan['jml_bayar'], 2), 0, 0, "R");
            $pdf->Ln();
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "");
            $pdf->Cell(60, 0, "Koreksi Bunga");
            $pdf->Cell(10, 0, "");
            $pdf->Cell(40, 0, "", 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "");
            $pdf->Cell(60, 0, "Perhitungan Lama");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan['sisa_angs_lama'], 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "");
            $pdf->Cell(60, 0, "Perhitungan Baru");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan['jml_bayar'], 2), "B", 0, "R");
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "");
            $pdf->Cell(60, 0, "Selisih");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan['perhitungan_selisih'], 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(40, 0, "");
            $pdf->Cell(30, 0, "");
            $pdf->Cell(60, 0, "Bukti Terlampir");
            $pdf->Cell(10, 0, "");
            $pdf->Cell(40, 0, "", 0, 0, "R");
            $pdf->Ln();
        }

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_slip2()
    {
        set_time_limit(0);

        $get_request = get_request();

        $data = json_decode(base64_decode($get_request['data']), true);

        if ($data['kd_pinjaman'] == "1" or $data['is_sparepart'] == "1") {
            $data_tempo_bln = $data['tempo_bln'];

            foreach ($this->pinjaman_model->get_array_tempo_bln(1) as $key => $value) {
                if ($data_post['tempo_bln'] <= $value) {
                    $data_tempo_bln = $value;
                }
            }

            $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, date("Y-m-d"));

            if ($data_margin_pinjaman->num_rows() > 0) {
                $data['bunga'] = $data_margin_pinjaman->row(0)->rate;
            }
        }

        $data['no_pinjam'] = $data['no_ref_bukti'];

        $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data);

        $this->load->library('mypdf');

        $ukuran_kertas = "A4";

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_slip2_" . $data['no_ang'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

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

        $pdf->AddPage();

        $pdf->SetFontSize("9");

        $pdf->SetY(30);

        $array_bulan  = array_bulan();
        $ex_tgl_lunas = explode("-", $data['tgl_lunas']);
        $tahun_lunas  = $ex_tgl_lunas[0];
        $bulan_lunas  = $array_bulan[$ex_tgl_lunas[1]];
        $hari_lunas   = $ex_tgl_lunas[2];

        $pdf->Cell(130, 0, "");
        $pdf->Cell(0, 0, $hari_lunas . " " . $bulan_lunas . " " . $tahun_lunas);
        $pdf->Ln(10);

        $pdf->Cell(50, 0, "");
        $pdf->Cell(0, 0, $data['nm_ang'] . " / " . $data['no_peg'] . " / " . $data['no_ang'] . " / " . $data['nm_dep'], 0, 0, "L", 0, 0, 1);

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        $pdf->Cell(120, 0, "");
        $pdf->Cell(0, 0, "Angsuran/Bulan : " . number_format($data['angsuran'], 2));
        $pdf->Ln();

        $angs_ke = $data['angsur_bln'] + 1;

        $pdf->Cell(80, 0, number_format($perhitungan['jml_bayar'], 2), 0, 0, "R");
        $pdf->Cell(0, 0, "Pembayaran Angsuran Ke " . $angs_ke . " s.d " . $data['tempo_bln'] . " LUNAS");

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        $pdf->Cell(80, 0, number_format($perhitungan['jml_bayar'], 2), 0, 0, "R");

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_perhitungan_blm_lunas_baru()
    {
        $data_req = get_request();

        $rows_data = json_decode(base64_decode($data_req['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = "A4";

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_perhitungan_" . $rows_data[0]['no_ang'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

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

        $pdf->SetMargins("10", "5", "5");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->SetFontSize("10");

        $data_ttd = $this->db->limit(1)->get("s_laporan")->row_array(0);

        $data_anggota = $this->db->where("no_ang", $rows_data[0]['no_ang'])->get("t_anggota")->row_array(0);

        foreach ($rows_data as $key => $data) {
            if ($data['kd_pinjaman'] == "1" or $data['is_sparepart'] == "1") {
                $data_tempo_bln = $data['tempo_bln'];

                foreach ($this->pinjaman_model->get_array_tempo_bln(1) as $key => $value) {
                    if ($data['tempo_bln'] <= $value) {
                        $data_tempo_bln = $value;
                    }
                }

                $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, balik_tanggal($data['tgl_lunas']));

                if ($data_margin_pinjaman->num_rows() > 0) {
                    $data['bunga'] = $data_margin_pinjaman->row(0)->rate;
                }
            }

            $data['jml_pokok']     = $data['jml_pinjam'];
            $data['jml_admin']     = $data['jml_biaya_admin'];
            $data['jml_bunga']     = $data['jml_margin'];
            $data['tgl_ref_bukti'] = $data['tgl_pinjam'];

            $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data);

            if (($data['kd_pinjaman'] == "1" or $data['is_sparepart'] == "1") and ($perhitungan['perhitungan_selisih'] >= 0)) {
                $pdf->AddPage();
            } else {
                continue;
            }

            $html = "";

            if ($data['kd_pinjaman'] != "") {
                $html .= "
                <h3 style=\"text-align: center\">PERHITUNGAN PELUNASAN
                    <br>PINJAMAN UANG " . $data['nm_pinjaman'] . "
                </h3>
                <br>
                <table border=\"0\">
                    <tr>
                        <th style=\"width: 3cm\">NAMA</th>
                        <th style=\"width: 16cm\">: " . $data_anggota['nm_ang'] . "</th>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <th>: " . $data_anggota['no_peg'] . "</th>
                    </tr>
                    <tr>
                        <th>NAK</th>
                        <th>: " . $data_anggota['no_ang'] . "</th>
                    </tr>
                    <tr>
                        <th>DEPT/BAGIAN</th>
                        <th>: " . $data_anggota['nm_dep'] . " / " . $data_anggota['nm_bagian'] . "</th>
                    </tr>
                </table>
                <br>
                <br>";
            }

            if ($data['kd_pinjaman'] == "1") {
                $cari['value'] = $data['no_pinjam'];
                $cari['field'] = array("no_pinjam");

                $data_pinjaman = $this->pinjaman_model->get_pinjaman(0, $cari)->row_array(0);

                if ($data['sisa_bln'] < $data['tempo_bln']) {
                    $html .= "
                    <table border=\"0\">
                        <tr>
                            <td width=\"4cm\">Tanggal Pelunasan</td>
                            <td>: " . $data['tgl_lunas'] . "</td>
                        </tr>
                        <tr>
                            <td>Tanggal Realisasi</td>
                            <td>: " . $data_pinjaman['tgl_pinjam'] . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Pokok</td>
                            <td>: " . number_format($data_pinjaman['jml_pinjam'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Biaya Admin</td>
                            <td>: " . number_format($data_pinjaman['jml_biaya_admin'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Tahun (%)</td>
                            <td>: " . $data['bunga'] . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Bulan (%)</td>
                            <td>: " . number_format($perhitungan['bunga_bln'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jangka (Bulan)</td>
                            <td>: " . $data_pinjaman['tempo_bln'] . "</td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td>: " . $perhitungan['sudah_diangsur'] . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Angsuran/Bulan</td>
                            <td>: " . number_format($data_pinjaman['angsuran'], 2) . "</td>
                        </tr>
                    </table>
                    <br> <br>
                    <table border=\"0\">
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Lama</u></th>
                        </tr>
                        <tr>
                            <td style=\"width: 5.2cm\">Pokok Pinjaman</td>
                            <td style=\"text-align: right; width: 7cm\">Rp " . number_format($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right; width: 4cm\">" . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Bunga</td>
                            <td style=\"text-align: right\">Rp " . number_format($data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_margin'] / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td style=\"text-align: right\">Rp " . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) + $data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp  </td>
                            <td style=\"text-align: right\">" . number_format((($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) + $data_pinjaman['jml_margin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: left\"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td style=\"text-align: right\">" . $perhitungan['sudah_diangsur'] . " x " . number_format($data_pinjaman['angsuran'], 2) . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['sudah_diangsur'] * $data_pinjaman['angsuran'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Sisa Angsuran</td>
                            <td style=\"text-align: right\">" . $data['sisa_bln'] . " x " . number_format($data_pinjaman['angsuran'], 2) . " = Rp </td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Baru</u></th>
                        </tr>
                        <tr>
                            <td>Sisa Pokok Pinjaman</td>
                            <td style=\"text-align: right\">" . $data['sisa_bln'] . " x " . number_format($perhitungan['jml_pokok_bln'], 2) . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sisa Angsuran yg harus dibayar</td>
                            <td style=\"text-align: right\">( 1 + " . number_format($perhitungan['bunga_bln'], 2) . "% )<sup>" . $perhitungan['sudah_diangsur'] . "</sup> x " . number_format($perhitungan['sisa_pokok'], 2) . " = Rp </td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td><strong>Koreksi Bunga</strong></td>
                            <td>Perhitungan Lama</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Perhitungan Baru</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Selisih</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_selisih'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                    </table>
                    ";
                } else {
                    $html .= "
                    <table>
                        <tr>
                            <td width=\"4cm\">Tanggal Pelunasan</td>
                            <td>: " . $data['tgl_lunas'] . "</td>
                        </tr>
                        <tr>
                            <td>Tanggal Realisasi</td>
                            <td>: " . $data_pinjaman['tgl_pinjam'] . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Pokok</td>
                            <td>: " . number_format($data_pinjaman['jml_pinjam'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Biaya Admin</td>
                            <td>: " . number_format($data_pinjaman['jml_biaya_admin'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Tahun (%)</td>
                            <td>: " . $data['bunga'] . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Bulan (%)</td>
                            <td>: " . number_format($perhitungan['bunga_bln'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jangka (Bulan)</td>
                            <td>: " . $data_pinjaman['tempo_bln'] . "</td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur (Bulan)</td>
                            <td>: " . $perhitungan['sudah_diangsur'] . "</td>
                        </tr>
                        <tr>
                            <td>Masa</td>
                            <td>: " . $perhitungan['jml_hari'] . "</td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <table border=\"0\">
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Lama</u></th>
                        </tr>
                        <tr>
                            <td style=\"4cm\">Pokok Pinjaman</td>
                            <td style=\"text-align: right; width: 6cm\">Rp " . number_format($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right; width: 4cm\">" . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Bunga</td>
                            <td style=\"text-align: right\">Rp " . number_format($data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_margin'] / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td style=\"text-align: right\">Rp " . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) + $data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp  </td>
                            <td style=\"text-align: right\">" . number_format((($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) + $data_pinjaman['jml_margin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: left\"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Baru</u></th>
                        </tr>
                        <tr>
                            <td>Pokok Pinjaman</td>
                            <td style=\"text-align: right\">= Rp</td>
                            <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_pinjam'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Biaya Admin</td>
                            <td style=\"text-align: right\">= Rp </td>
                            <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_biaya_admin'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Bunga Harian</td>
                            <td style=\"text-align: right;\"><u>( " . number_format($data_pinjaman['jml_pinjam'], 2) . " x " . $data_pinjaman['margin'] . "% ) x " . $perhitungan['jml_hari'] . "</u> = Rp </td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['jml_bunga_harian'], 2) . "</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style=\"text-align: center\">360</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Angsuran yang harus dibayar</td>
                            <td style=\"text-align: right\">= Rp</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td><strong>Koreksi Bunga</strong></td>
                            <td>Perhitungan Lama</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Perhitungan Baru</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Selisih</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_selisih'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                    </table> ";
                }

                $html .= "
                    <br>
                    <br>
                    <br>
                    <br>
                    <table border=\"0\">
                        <tr>
                            <td style=\"width: 6.5cm\"></td>
                            <td style=\"width: 6.5cm\"></td>
                            <td style=\"width: 6.5cm\">Gresik, " . $data['tgl_lunas'] . "</td>
                        </tr>
                        <tr>
                            <td>Menyetujui,</td>
                            <td>Mengetahui,</td>
                            <td>Pembuat,</td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>" . $data_ttd['manager_adm_keuangan'] . "</strong></td>
                            <td><strong>" . $data_ttd['kabid_keuangan'] . "</strong></td>
                            <td><strong>" . $data_ttd['kaunit_potga'] . "</strong></td>
                        </tr>
                        <tr>
                            <td>(Mgr. Adm & Keuangan)</td>
                            <td>(Kabid Keuangan)</td>
                            <td>(Ka. Unit Potga)</td>
                        </tr>
                    </table>";

            } else if ($data['kd_pinjaman'] == "3") {

            } else if (in_array($data['kd_pinjaman'], array("2", "4"))) {

            } else if ($data['is_sparepart'] == "1") {
                $html .= "
                <h3 style=\"text-align: center\">PERHITUNGAN PELUNASAN SPAREPART</h3>
                <br>
                <table border=\"0\">
                    <tr>
                        <th style=\"width: 3cm\">NAMA</th>
                        <th style=\"width: 16cm\">: " . $data_anggota['nm_ang'] . "</th>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <th>: " . $data_anggota['no_peg'] . "</th>
                    </tr>
                    <tr>
                        <th>NAK</th>
                        <th>: " . $data_anggota['no_ang'] . "</th>
                    </tr>
                    <tr>
                        <th>DEPT/BAGIAN</th>
                        <th>: " . $data_anggota['nm_dep'] . " / " . $data_anggota['nm_bagian'] . "</th>
                    </tr>
                </table>
                <br>
                <br>";

                if ($data['sisa_bln'] < $data['tempo_bln']) {
                    $html .= "
                    <table>
                        <tr>
                            <td width=\"4cm\">Tanggal Pelunasan</td>
                            <td>: " . $data['tgl_lunas'] . "</td>
                        </tr>
                        <tr>
                            <td>Tanggal Realisasi</td>
                            <td>: " . balik_tanggal($data['tgl_pinjam']) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Pokok</td>
                            <td>: " . number_format($data['jml_pinjam'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Biaya Admin</td>
                            <td>: " . number_format($data['jml_biaya_admin'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Tahun (%)</td>
                            <td>: " . $data['bunga'] . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Bulan (%)</td>
                            <td>: " . number_format($perhitungan['bunga_bln'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jangka (Bulan)</td>
                            <td>: " . $data['tempo_bln'] . "</td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td>: " . $perhitungan['sudah_diangsur'] . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Angsuran/Bulan</td>
                            <td>: " . number_format($data['angsuran'], 2) . "</td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <table>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Lama</u></th>
                        </tr>
                        <tr>
                            <td width=\"5.2cm\">Pokok Pinjaman</td>
                            <td style=\"text-align: right; width: 7cm\">Rp " . number_format($data['jml_pinjam'] + $data['jml_biaya_admin'], 2) . " / " . $data['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right; width: 4cm\">" . number_format(($data['jml_pinjam'] + $data['jml_biaya_admin']) / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Bunga</td>
                            <td style=\"text-align: right\">Rp " . number_format($data['jml_margin'], 2) . " / " . $data['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($data['jml_margin'] / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td style=\"text-align: right\">Rp " . number_format(($data['jml_pinjam'] + $data['jml_biaya_admin']) + $data['jml_margin'], 2) . " / " . $data['tempo_bln'] . " = Rp  </td>
                            <td style=\"text-align: right\">" . number_format((($data['jml_pinjam'] + $data['jml_biaya_admin']) + $data['jml_margin']) / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: left\"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td style=\"text-align: right\">" . $perhitungan['sudah_diangsur'] . " x " . number_format($data['angsuran'], 2) . " = Rp</td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['sudah_diangsur'] * $data['angsuran'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sisa Angsuran</td>
                            <td style=\"text-align: right\">" . $data['sisa_bln'] . " x " . number_format($data['angsuran'], 2) . " = Rp</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Baru</u></th>
                        </tr>
                        <tr>
                            <td>Sisa Pokok Pinjaman</td>
                            <td style=\"text-align: right\">" . $data['sisa_bln'] . " x " . number_format($perhitungan['jml_pokok_bln'], 2) . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sisa Angsuran yg harus dibayar</td>
                            <td style=\"text-align: right\">( 1 + " . number_format($perhitungan['bunga_bln'], 2) . "% )<sup>" . $perhitungan['sudah_diangsur'] . "</sup> x " . number_format($perhitungan['sisa_pokok'], 2) . " = Rp</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td><strong>Koreksi Bunga</strong></td>
                            <td>Perhitungan Lama</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Perhitungan Baru</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Selisih</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_selisih'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                    </table>";

                } else {
                    $html .= "
                    <table>
                        <tr>
                            <td width=\"4cm\">Tanggal Pelunasan</td>
                            <td>: " . $data['tgl_lunas'] . "</td>
                        </tr>
                        <tr>
                            <td>Tanggal Realisasi</td>
                            <td>: " . balik_tanggal($data['tgl_pinjam']) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Pokok</td>
                            <td>: " . number_format($data['jml_pinjam'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jml. Biaya Admin</td>
                            <td>: " . number_format($data['jml_biaya_admin'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Tahun (%)</td>
                            <td>: " . $data['bunga'] . "</td>
                        </tr>
                        <tr>
                            <td>Bunga/Bulan (%)</td>
                            <td>: " . number_format($perhitungan['bunga_bln'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Jangka (Bulan)</td>
                            <td>: " . $data['tempo_bln'] . "</td>
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td>: " . $perhitungan['sudah_diangsur'] . "</td>
                        </tr>
                        <tr>
                            <td>Masa</td>
                            <td>: " . $perhitungan['jml_hari'] . "</td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <table border=\"0\">
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Lama</u></th>
                        </tr>
                        <tr>
                            <td style=\"4cm\">Pokok Pinjaman</td>
                            <td style=\"text-align: right; width: 6cm\">Rp " . number_format($data['jml_pinjam'] + $data['jml_biaya_admin'], 2) . " / " . $data['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right; width: 4cm\">" . number_format(($data['jml_pinjam'] + $data['jml_biaya_admin']) / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Bunga</td>
                            <td style=\"text-align: right\">Rp " . number_format($data['jml_margin'], 2) . " / " . $data['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($data['jml_margin'] / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td style=\"text-align: right\">Rp " . number_format(($data['jml_pinjam'] + $data['jml_biaya_admin']) + $data['jml_margin'], 2) . " / " . $data['tempo_bln'] . " = Rp  </td>
                            <td style=\"text-align: right\">" . number_format((($data['jml_pinjam'] + $data['jml_biaya_admin']) + $data['jml_margin']) / $data['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: left\"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan=\"4\"><u>Perhitungan Baru</u></th>
                        </tr>
                        <tr>
                            <td>Pokok Pinjaman</td>
                            <td style=\"text-align: right\">= Rp </td>
                            <td style=\"text-align: right\">" . number_format($data['jml_pinjam'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Biaya Admin</td>
                            <td style=\"text-align: right\">= Rp </td>
                            <td style=\"text-align: right\">" . number_format($data['jml_biaya_admin'], 2) . "</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Bunga Harian</td>
                            <td style=\"text-align: right;\"><u>( " . number_format($data['jml_pinjam'], 2) . " x " . $data['bunga'] . "% ) x " . $perhitungan['jml_hari'] . "</u> = Rp</td>
                            <td style=\"text-align: right;\">" . number_format($perhitungan['jml_bunga_harian'], 2) . "</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style=\"text-align: center\" >360</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Angsuran yang harus dibayar</td>
                            <td style=\"text-align: right\">= Rp</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td><strong>Koreksi Bunga</strong></td>
                            <td>Perhitungan Lama</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_lama'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Perhitungan Baru</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_baru'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Selisih</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_selisih'], 2) . "</strong></td>
                            <td></td>
                        </tr>
                    </table>
                ";
                }

                $html .= "
                    <br>
                    <br>
                    <br>
                    <br>
                    <table border=\"0\">
                        <tr>
                            <td style=\"width: 6.5cm\"></td>
                            <td style=\"width: 6.5cm\"></td>
                            <td style=\"width: 6.5cm\">Gresik, " . $data['tgl_lunas'] . "</td>
                        </tr>
                        <tr>
                            <td>Menyetujui,</td>
                            <td>Mengetahui,</td>
                            <td>Pembuat,</td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>" . $data_ttd['manager_adm_keuangan'] . "</strong></td>
                            <td><strong>" . $data_ttd['kabid_keuangan'] . "</strong></td>
                            <td><strong>" . $data_ttd['kaunit_potga'] . "</strong></td>
                        </tr>
                        <tr>
                            <td>(Mgr. Adm & Keuangan)</td>
                            <td>(Kabid Keuangan)</td>
                            <td>(Ka. Unit Potga)</td>
                        </tr>
                    </table>";
            }

            $pdf->writeHTML($html, true, false, true, false, '');
        }

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_slip1_blm_lunas_baru()
    {
        set_time_limit(0);

        $get_request = get_request();

        $rows_data = json_decode(base64_decode($get_request['data']), true);

        // baca_array($rows_data);exit();

        $this->load->library('mypdf');

        $ukuran_kertas = "letter";

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_slip1_" . $rows_data[0]['no_ang'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

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

        $pdf->SetMargins("0", "30", "0");
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->AddPage();

        $pdf->SetFontSize("9");

        $pdf->SetY(22);

        $pdf->Cell(87, 0, "");
        $pdf->Cell(50, 0, $rows_data[0]['nm_ang'] . " / " . $rows_data[0]['no_peg'] . " / " . $rows_data[0]['no_ang'], 0, 0, "L", 0, 0, 1);

        $pdf->Ln(7);

        $jml_bayar = 0;

        foreach ($rows_data as $key => $data) {
            if ($data['kd_pinjaman'] == "1" or $data['is_sparepart'] == "1") {
                $data_tempo_bln = $data['tempo_bln'];

                foreach ($this->pinjaman_model->get_array_tempo_bln(1) as $key => $value) {
                    if ($data['tempo_bln'] <= $value) {
                        $data_tempo_bln = $value;
                    }
                }

                $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, date("Y-m-d"));

                if ($data_margin_pinjaman->num_rows() > 0) {
                    $data['bunga'] = $data_margin_pinjaman->row(0)->rate;
                }
            }

            $data['jml_pokok']     = $data['jml_pinjam'];
            $data['jml_bunga']     = $data['jml_margin'];
            $data['jml_admin']     = $data['jml_biaya_admin'];
            $data['tgl_ref_bukti'] = $data['tgl_pinjam'];

            $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data);

            $jml_bayar += $perhitungan['jml_bayar'];
        }

        $pdf->Cell(110, 0, "");
        $pdf->Cell(30, 0, number_format($jml_bayar, 2), 0, 0, "R");

        $pdf->Ln(10);

        $terbilang = ucwords(terbilang($jml_bayar));

        $pdf->Cell(91, 0, "");
        $pdf->MultiCell(0, 0, $terbilang, 0, "L");

        $pdf->SetY(57);

        $ada_perhitungan     = 0;
        $perhitungan_lama    = 0;
        $perhitungan_baru    = 0;
        $perhitungan_selisih = 0;

        foreach ($rows_data as $key => $data) {
            if ($data['kd_pinjaman'] == "1" or $data['is_sparepart'] == "1") {
                $data_tempo_bln = $data['tempo_bln'];

                foreach ($this->pinjaman_model->get_array_tempo_bln(1) as $key => $value) {
                    if ($data['tempo_bln'] <= $value) {
                        $data_tempo_bln = $value;
                    }
                }

                $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, date("Y-m-d"));

                if ($data_margin_pinjaman->num_rows() > 0) {
                    $data['bunga'] = $data_margin_pinjaman->row(0)->rate;
                }
            }

            $data['jml_pokok']     = $data['jml_pinjam'];
            $data['jml_bunga']     = $data['jml_margin'];
            $data['jml_admin']     = $data['jml_biaya_admin'];
            $data['tgl_ref_bukti'] = $data['tgl_pinjam'];

            $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data);

            $sudah_diangsur = $data['tempo_bln'] - $data['sisa_bln'];

            $ada_perhitungan += $perhitungan['ada_perhitungan'];

            if ($perhitungan['ada_perhitungan'] > 0 and $perhitungan['perhitungan_selisih'] >= 0) {
                $sudah_diangsur = $perhitungan['sudah_diangsur'];

                $perhitungan_lama += $perhitungan['sisa_angs_lama'];
                $perhitungan_baru += $perhitungan['jml_bayar'];
                $perhitungan_selisih += $perhitungan['perhitungan_selisih'];
            }

            $angs_ke = $sudah_diangsur + 1;

            $pdf->Cell(75, 0, "");
            $pdf->Cell(80, 0, $data['ket'] . " Angs Ke " . $angs_ke . " s.d. " . $data['tempo_bln']);
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(30, 0, number_format($perhitungan['jml_bayar'], 2), "", "", "R");
            $pdf->Ln();
        }

        if ($ada_perhitungan > 0 and $perhitungan_lama > 0) {
            $pdf->Ln();
            // $pdf->Ln();

            $pdf->Cell(50, 0, "");
            $pdf->Cell(25, 0, "1141");
            $pdf->Cell(60, 0, "PIUTANG ANGGOTA");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan_lama, 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(50, 0, "");
            $pdf->Cell(25, 0, "3310");
            $pdf->Cell(60, 0, "PENDAPATAN YANG DITANGGUHKAN");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan_baru, 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(50, 0, "");
            $pdf->Cell(25, 0, "");
            $pdf->Cell(60, 0, "Koreksi Bunga");
            $pdf->Cell(10, 0, "");
            $pdf->Cell(40, 0, "", 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(50, 0, "");
            $pdf->Cell(25, 0, "");
            $pdf->Cell(60, 0, "Perhitungan Lama");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan_lama, 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(50, 0, "");
            $pdf->Cell(25, 0, "");
            $pdf->Cell(60, 0, "Perhitungan Baru");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan_baru, 2), "B", 0, "R");
            $pdf->Ln();

            $pdf->Cell(50, 0, "");
            $pdf->Cell(25, 0, "");
            $pdf->Cell(60, 0, "Selisih");
            $pdf->Cell(10, 0, "Rp");
            $pdf->Cell(40, 0, number_format($perhitungan_selisih, 2), 0, 0, "R");
            $pdf->Ln();

            $pdf->Cell(50, 0, "");
            $pdf->Cell(25, 0, "");
            $pdf->Cell(60, 0, "Bukti Terlampir");
            $pdf->Cell(10, 0, "");
            $pdf->Cell(40, 0, "", 0, 0, "R");
            $pdf->Ln();
        }

        $pdf->Output($judul_file, 'I');
    }

    public function cetak_slip2_blm_lunas_baru()
    {
        set_time_limit(0);

        $get_request = get_request();

        $rows_data = json_decode(base64_decode($get_request['data']), true);

        $this->load->library('mypdf');

        $ukuran_kertas = "letter";

        $pdf = new mypdf("P", "mm", $ukuran_kertas);

        $kreator      = "MBagusRD";
        $judul_file   = "Cetak PDF";
        $judul_header = "Koperasi Karyawan Keluarga Besar Petrokimia Gresik";
        $teks_header  = NAMA_PHP;
        $judul_file   = "cetak_slip2_" . $rows_data[0]['no_ang'] . "_" . date("Y-m-d_H-i-s") . ".pdf";

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

        $pdf->AddPage();

        $pdf->SetFontSize("10");

        $pdf->SetY(30);

        $array_bulan  = array_bulan();
        $ex_tgl_lunas = explode("-", $rows_data[0]['tgl_lunas']);
        $hari_lunas   = $ex_tgl_lunas[0];
        $bulan_lunas  = $array_bulan[$ex_tgl_lunas[1]];
        $tahun_lunas  = $ex_tgl_lunas[2];

        $pdf->Cell(130, 0, "");
        $pdf->Cell(0, 0, $hari_lunas . " " . $bulan_lunas . " " . $tahun_lunas);
        $pdf->Ln(10);

        $this->load->model("anggota_model");

        $cari['value'] = $rows_data[0]['no_ang'];
        $cari['field'] = array("no_ang");

        $data_anggota = $this->anggota_model->get_anggota(0, $cari)->row_array(0);

        $pdf->Cell(50, 0, "");
        $pdf->Cell(0, 0, $rows_data[0]['nm_ang'] . " / " . $rows_data[0]['no_peg'] . " / " . $rows_data[0]['no_ang'] . " / " . $data_anggota['nm_dep'], 0, 0, "L", 0, 0, 1);

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        $jml_bayar = 0;

        foreach ($rows_data as $key => $data) {
            if ($data['kd_pinjaman'] == "1" or $data['is_sparepart'] == "1") {
                $data_tempo_bln = $data['tempo_bln'];

                foreach ($this->pinjaman_model->get_array_tempo_bln(1) as $key => $value) {
                    if ($data['tempo_bln'] <= $value) {
                        $data_tempo_bln = $value;
                    }
                }

                $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, date("Y-m-d"));

                if ($data_margin_pinjaman->num_rows() > 0) {
                    $data['bunga'] = $data_margin_pinjaman->row(0)->rate;
                }
            }

            $data['jml_pokok']     = $data['jml_pinjam'];
            $data['jml_bunga']     = $data['jml_margin'];
            $data['jml_admin']     = $data['jml_biaya_admin'];
            $data['tgl_ref_bukti'] = $data['tgl_pinjam'];

            $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data);

            $pdf->Cell(120, 0, "");
            $pdf->Cell(0, 0, "Angsuran/Bulan : " . number_format($data['angsuran'], 2));
            $pdf->Ln();

            $angs_ke = $perhitungan['sudah_diangsur'] + 1;

            $pdf->Cell(60, 0, number_format($perhitungan['jml_bayar'], 2), 0, 0, "R");
            $pdf->Cell(0, 0, "Pembayaran Angsuran Ke " . $angs_ke . " s.d " . $data['tempo_bln'] . " LUNAS");
            $pdf->Ln();

            $jml_bayar += $perhitungan['jml_bayar'];
        }

        $pdf->SetY(140);

        $pdf->Cell(60, 0, number_format($jml_bayar, 2), 0, 0, "R");

        $pdf->Output($judul_file, 'I');
    }
}
