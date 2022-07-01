<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rincian_kredit_anggota extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();
    }

    public function index()
    {
        $bulan = get_option_tag(array_bulan(), "BULAN");

        $data['judul_menu'] = "Rincian Kredit Anggota";
        $data['bulan']      = $bulan;

        $this->template->view("laporan/rincian_kredit_anggota", $data);
    }

    public function tampilkan()
    {
        $data_req = get_request();

        if ($data_req) {
            $this->load->model("laporan_model");

            if (@!$data_req['tahun'] or !isset($data_req['tahun'])) {
                $data_req['tahun'] = date("Y");
            }

            if (@!$data_req['bulan'] or !isset($data_req['bulan'])) {
                $data_req['bulan'] = date("m");
            }

            $data_anggota = $this->db->where("no_ang", $data_req['no_ang'])->get("t_anggota")->row_array(0);

            $total_plafon = $data_anggota['plafon'];
            $sisa_plafon  = ($data_anggota['plafon'] - $data_anggota['plafon_pakai']);

            $laporan = "<table class=\"table table-bordered table-condensed\" border=\"1\" style=\"white-space: nowrap;\">
                    <thead>
                        <tr>
                            <th colspan=\"6\" style=\"text-align: right\">Total Plafon</th>
                            <th style=\"text-align: right\">" . number_format($total_plafon, 2) . "</th>
                        </tr>
                        <tr>
                            <th colspan=\"7\"></th>
                        </tr>
                        <tr>
                            <th>No.</th>
                            <th>Tgl Rilis</th>
                            <th>Keterangan</th>
                            <th>Jml. Pokok</th>
                            <th>Jangka (bln)</th>
                            <th>Jatuh Tempo</th>
                            <th>Angsuran per bulan</th>
                        </tr>
                    </thead>
                    <tbody>";

            $no         = 1;
            $t_angsuran = 0;

            $query_kredit = "SELECT no_ang, no_peg, nm_ang, date_format(tgl_kredit, '%d-%m-%Y') tgl_kredit, ket, jml_pokok, tempo_bln, angs_ke, angsuran
                FROM
                (
                    SELECT no_ang, no_peg, nm_ang, a.tgl_pinjam tgl_kredit, CONCAT('PINJAMAN ', a.nm_pinjaman) ket, a.jml_pinjam jml_pokok, a.tempo_bln, b.angs_ke, a.angsuran
                    FROM t_pinjaman_ang a
                    JOIN t_pinjaman_ang_det b
                    ON a.no_pinjam=b.no_pinjam
                    WHERE no_ang = '" . $data_req['no_ang'] . "'
                    AND substr(a.tgl_pinjam, 1, 7) <= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                    AND (a.blth_lunas > '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' or a.blth_lunas is null)
                    AND a.angsuran != 0
                    group by a.no_pinjam
                ) a
                union
                SELECT pelanggan_kode, '' no_peg, '' nm_ang, date_format(tanggal, '%d-%m-%Y') tanggal, '[TOKO] Kredit Belanja' ket, jumlah, '1' tempo_bln, '1' angs_ke, jumlah
                FROM
                (
                    SELECT ref_penjualan, pelanggan_kode, toko_kode, no_kuitansi, keterangan_kuitansi, tanggal, jumlah, tempo, jatuh_tempo, fcharge_trans, is_lunas
                    FROM db_wecode_smart.piutang
                    WHERE pelanggan_kode = '" . $data_req['no_ang'] . "'
                    AND substr(tanggal, 1, 7) <= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                    AND (substr(tgl_lunas, 1, 7) > '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' or tgl_lunas is null)
                ) b
                union
                SELECT noang, '' no_peg, '' nm_ang, date_format(tanggal, '%d-%m-%Y') tanggal, '[TOKO] KREDIT ANGSURAN' ket, pokok_kredit, ang_bulan, '1' angs_ke, angs_perbulan
                FROM
                (
                    SELECT ref_bukti_bo, kd_toko, tanggal, noang, pokok_kredit, uang_muka, suku_bunga, ang_bulan, nilai_bunga, adm, nilai_adm, jml_kredit, angs_perbulan, tgl_jth_tempo, status_transaksi, status_hapus, tgl_update
                    FROM db_wecode_smart.t_kredit_anggota
                    WHERE noang = '" . $data_req['no_ang'] . "'
                    AND substr(tanggal, 1, 7) <= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                    AND (substr(tgl_lunas, 1, 7) > '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' or tgl_lunas is null)
                ) c
                union
                SELECT no_ang, no_peg, nm_ang, date_format(tgl_trans, '%d-%m-%Y') tgl_trans, ket, jml_trans, tempo_bln, '1' angs_ke, angsuran
                FROM
                (
                    SELECT a.no_trans, a.tgl_trans, unit_adm, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_trans, nm_trans, a.kd_piutang, jml_trans, jml_diterima, persen_biaya_admin, jml_biaya_admin, a.tempo_bln, margin, jml_margin, a.angsuran, tgl_angs, tgl_jt, ket
                    FROM k3pg_sp.t_bridging_plafon a
                    JOIN k3pg_sp.t_bridging_plafon_det b
                    ON a.no_trans=b.no_trans
                    WHERE no_ang = '" . $data_req['no_ang'] . "'
                    AND substr(a.tgl_trans, 1, 7) <= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                    AND (a.blth_bayar > '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' or a.blth_bayar is null)
                    group by a.no_trans
                ) d
            ";

            // AND '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' <= b.blth_angsuran

            // AND substr(jatuh_tempo, 1, 7) > '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'

            // AND substr(tgl_jth_tempo, 1, 7) >= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'

            // AND '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' <= b.blth_angsuran

            // baca($query_kredit);

            $data_kredit = $this->db->query($query_kredit);

            foreach ($data_kredit->result_array() as $key => $value) {
                $tglKredit = explode("-", $value['tgl_kredit']);
                $strTglJT  = mktime(0, 0, 0, $tglKredit[1] + $value['tempo_bln'], 1, $tglKredit[2]);
                $tglJT     = date("m Y", $strTglJT);

                $laporan .= "
                    <tr>
                        <td style=\"text-align: right\">" . $no . "</td>
                        <td>" . $value['tgl_kredit'] . "</td>
                        <td>" . $value['ket'] . "</td>
                        <td style=\"text-align: right\">" . number_format($value['jml_pokok'], 2) . "</td>
                        <td style=\"text-align: center\">" . $value['tempo_bln'] . "</td>
                        <td style=\"text-align: center\">" . $tglJT . "</td>
                        <td style=\"text-align: right\">" . number_format($value['angsuran'], 2) . "</td>
                    </tr>
                ";

                $no++;
                $t_angsuran += $value['angsuran'];
            }

            $laporan .= "
                <tr>
                    <th colspan=\"6\" style=\"text-align: right\">Total Angsuran</th>
                    <th style=\"text-align: right\">" . number_format($t_angsuran, 2) . "</th>
                </tr>
                <tr>
                    <th colspan=\"7\"></th>
                </tr>
                <tr>
                    <th colspan=\"6\" style=\"text-align: right\">Sisa Plafon</th>
					<th style=\"text-align: right\">" . number_format(($data_anggota['plafon']-$t_angsuran), 2) . "</th>
	            </tr>
            ";

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

    public function singkron_sisa_plafon()
    {
        $data_req = get_request();

        if ($data_req) {
            if (@!$data_req['tahun'] or !isset($data_req['tahun'])) {
                $data_req['tahun'] = date("Y");
            }

            if (@!$data_req['bulan'] or !isset($data_req['bulan'])) {
                $data_req['bulan'] = date("m");
            }

            $query_kredit = "
                SELECT ifnull(sum(angsuran), 0) total_angsuran
                FROM (
                    SELECT no_ang, no_peg, nm_ang, date_format(tgl_kredit, '%d-%m-%Y') tgl_kredit, ket, jml_pokok, tempo_bln, angs_ke, angsuran
                    FROM
                    (
                        SELECT no_ang, no_peg, nm_ang, a.tgl_pinjam tgl_kredit, CONCAT('PINJAMAN ', a.nm_pinjaman) ket, a.jml_pinjam jml_pokok, a.tempo_bln, b.angs_ke, a.angsuran
                        FROM t_pinjaman_ang a
                        JOIN t_pinjaman_ang_det b
                        ON a.no_pinjam=b.no_pinjam
                        WHERE no_ang = '" . $data_req['no_ang'] . "'
                        AND substr(a.tgl_pinjam, 1, 7) <= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                        AND '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' <= b.blth_angsuran
                        AND (a.blth_lunas > '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' or a.blth_lunas is null)
                        AND a.angsuran != 0
                        group by a.no_pinjam
                    ) a
                    union
                    SELECT pelanggan_kode, '' no_peg, '' nm_ang, date_format(tanggal, '%d-%m-%Y') tanggal, 'KREDIT BUKU TOKO' ket, jumlah, '1' tempo_bln, '1' angs_ke, jumlah
                    FROM
                   (
                        SELECT ref_penjualan, pelanggan_kode, toko_kode, no_kuitansi, keterangan_kuitansi, tanggal, jumlah, tempo, jatuh_tempo, fcharge_trans, is_lunas
                        FROM db_wecode_smart.piutang
                        WHERE pelanggan_kode = '" . $data_req['no_ang'] . "'
                        AND substr(tanggal, 1, 7) <= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                        AND (substr(tgl_lunas, 1, 7) > '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' or tgl_lunas is null)			
					) b
                    union
                    SELECT noang, '' no_peg, '' nm_ang, date_format(tanggal, '%d-%m-%Y') tanggal, 'KREDIT ANGSURAN TOKO' ket, pokok_kredit, ang_bulan, '1' angs_ke, angs_perbulan
                    FROM
                    (
                        SELECT ref_bukti_bo, kd_toko, tanggal, noang, pokok_kredit, uang_muka, suku_bunga, ang_bulan, nilai_bunga, adm, nilai_adm, jml_kredit, angs_perbulan, tgl_jth_tempo, status_transaksi, status_hapus, tgl_update
                        FROM db_wecode_smart.t_kredit_anggota
                        WHERE noang = '" . $data_req['no_ang'] . "'
                        AND substr(tanggal, 1, 7) <= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                        AND substr(tgl_jth_tempo, 1, 7) >= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                        AND (substr(tgl_lunas, 1, 7) > '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' or tgl_lunas is null)
                    ) c
                    union
                    SELECT no_ang, no_peg, nm_ang, date_format(tgl_trans, '%d-%m-%Y') tgl_trans, ket, jml_trans, tempo_bln, '1' angs_ke, angsuran
                    FROM
                    (
                        SELECT a.no_trans, a.tgl_trans, unit_adm, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_trans, nm_trans, a.kd_piutang, jml_trans, jml_diterima, persen_biaya_admin, jml_biaya_admin, a.tempo_bln, margin, jml_margin, a.angsuran, tgl_angs, tgl_jt, ket
                        FROM k3pg_sp.t_bridging_plafon a
                        JOIN k3pg_sp.t_bridging_plafon_det b
                        ON a.no_trans=b.no_trans
                        WHERE no_ang = '" . $data_req['no_ang'] . "'
                        AND substr(a.tgl_trans, 1, 7) <= '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "'
                        AND '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' <= b.blth_angsuran
                        AND (a.blth_bayar > '" . $data_req['tahun'] . "-" . $data_req['bulan'] . "' or a.blth_bayar is null)
                        group by a.no_trans
                    ) d
                ) zz
            ";

            $total_angsuran = $this->db->query($query_kredit)->row(0)->total_angsuran;

            $data_plafon_debet = $this->db->select("ifnull(sum(jml_debet), 0) total_debet")
                ->where("no_ang", $data_req['no_ang'])
                ->get("t_plafon_debet");

            $total_debet = $data_plafon_debet->row(0)->total_debet;

            $selisih_sisa_plafon = $total_angsuran - $total_debet;

            if ($selisih_sisa_plafon != 0) {
                $set_data = array(
                    "no_ang"      => $data_req['no_ang'],
                    "tgl_penj"    => date("Y-m-d"),
                    "jenis_debet" => "SINGKRON",
                    "jml_debet"   => $selisih_sisa_plafon,
                );

                $query = $this->db->set($set_data)->insert("t_plafon_debet");

                $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data_req['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

                $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data_req['no_ang'])->update("t_anggota");
            }

            $this->tampilkan();
        }
    }
}
