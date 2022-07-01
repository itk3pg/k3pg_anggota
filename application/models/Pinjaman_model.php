<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pinjaman_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_tempo_bln()
    {
        $tempo_bln = array();

        for ($i = 1; $i <= 20; $i++) {
            $bln             = $i * 12;
            $tempo_bln[$bln] = "(" . $i . " Tahun) " . $bln;
        }

        return $tempo_bln;
    }

    public function get_array_tempo_bln($mode_dibalik = "")
    {
        $tempo_bln = array();

        if ($mode_dibalik) {
            for ($i = 20; $i >= 1; $i--) {
                $bln         = $i * 12;
                $tempo_bln[] = $bln;
                // $tempo_bln[$bln] = "(" . $i . " Tahun) " . $bln;
            }

        } else {
            for ($i = 1; $i <= 20; $i++) {
                $bln         = $i * 12;
                $tempo_bln[] = $bln;
                // $tempo_bln[$bln] = "(" . $i . " Tahun) " . $bln;
            }
        }

        return $tempo_bln;
    }

    public function get_tempo_bln_reguler()
    {
        $tempo_bln = array();

        $data_tempo = $this->db->where("kd_jns_pinjaman", "1")->group_by("tempo_bln")->get("m_rate_pinjaman");

        foreach ($data_tempo->result_array() as $key => $value) {
            $tahun                          = $value['tempo_bln'] / 12;
            $tempo_bln[$value['tempo_bln']] = "(" . $tahun . " Tahun) " . $value['tempo_bln'];
        }

        return $tempo_bln;
    }

    public function get_tempo_bln_kkb()
    {
        $tempo_bln = array();

        $data_tempo = $this->db->where("kd_jns_pinjaman", "2")->group_by("tempo_bln")->get("m_rate_pinjaman");

        foreach ($data_tempo->result_array() as $key => $value) {
            $tahun                          = $value['tempo_bln'] / 12;
            $tempo_bln[$value['tempo_bln']] = "(" . $tahun . " Tahun) " . $value['tempo_bln'];
        }

        return $tempo_bln;
    }

    public function get_tempo_bln_kpr()
    {
        $tempo_bln = array();

        $data_tempo = $this->db->where("kd_jns_pinjaman", "4")->group_by("tempo_bln")->get("m_rate_pinjaman");

        foreach ($data_tempo->result_array() as $key => $value) {
            $tahun                          = $value['tempo_bln'] / 12;
            $tempo_bln[$value['tempo_bln']] = "(" . $tahun . " Tahun) " . $value['tempo_bln'];
        }

        return $tempo_bln;
    }

    public function get_pinjaman($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $kd_pinjaman = "")
    {
        $select = ($numrows) ? "count(*) numrows, 1 tgl_pinjam1" : "no_pinjam, tgl_pinjam tgl_pinjam1, date_format(tgl_pinjam, '%d-%m-%Y') tgl_pinjam, no_simulasi, tgl_simulasi, is_aprove, tgl_aprove, user_aprove, is_realisasi, tgl_realisasi, user_realisasi, unit_adm, no_ang, no_peg, nm_ang, nm_ibukdg, tgl_lhr, alamat_ang, no_hp, no_npwp, no_ktp, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_pinjaman, nm_pinjaman, tgl_angs, tgl_jt, jml_pinjam, tempo_bln, jns_jangka, jenis_margin, margin, jml_margin, gaji, plafon, sisa_plafon, plafon_bonus, sisa_plafon_bonus, min_angsuran, jml_min_angsuran, max_angsuran, jml_max_angsuran, persen_angsuran, angsuran, saldo_angsuran, saldo_pinjaman, jml_biaya_admin, jml_provisi_bln, jml_simp_agunan, jml_pot_bunga, jml_potong, jml_diterima, jns_potong_admin, jns_potong_bunga, jns_bayar, no_rek_cek, kd_bank_dana, nm_bank_dana, kd_bank_ke, nm_bank_ke, kd_cb, nm_cb, sts_lunas, blth_lunas, tgl_update";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang", "nm_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "tgl_pinjam1 desc, no_pinjam desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($kd_pinjaman) {
            if (is_array($kd_pinjaman)) {
                $this->db->where_in("kd_pinjaman", $kd_pinjaman);
            } else {
                $this->db->where("kd_pinjaman", $kd_pinjaman);
            }
        }

        return $this->db->get("t_pinjaman_ang");
    }

    public function get_simulasi_pinjaman($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $kd_pinjaman = "", $is_aprove = "", $is_realisasi = "")
    {
        $select = ($numrows) ? "count(*) numrows, 1 tgl_pinjam1" : "no_pinjam, tgl_pinjam tgl_pinjam1, date_format(tgl_pinjam, '%d-%m-%Y') tgl_pinjam, is_aprove, tgl_aprove, user_aprove, is_realisasi, tgl_realisasi, user_realisasi, unit_adm, no_ang, no_peg, nm_ang, nm_ibukdg, tgl_lhr, alamat_ang, no_hp, no_npwp, no_ktp, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_pinjaman, nm_pinjaman, tgl_angs, tgl_jt, jml_pinjam, tempo_bln, jns_jangka, jenis_margin, margin, jml_margin, gaji, plafon, sisa_plafon, plafon_bonus, sisa_plafon_bonus, min_angsuran, jml_min_angsuran, max_angsuran, jml_max_angsuran, persen_angsuran, angsuran, saldo_angsuran, saldo_pinjaman, jml_biaya_admin, jml_provisi_bln, jml_simp_agunan, jml_pot_bunga, jml_potong, jml_diterima, jns_potong_admin, jns_potong_bunga, tgl_update";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_pinjam", "no_ang", "no_peg", "nm_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "tgl_pinjam1 desc, no_pinjam desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($kd_pinjaman) {
            $this->db->where("kd_pinjaman", $kd_pinjaman);
        }

        if ($is_aprove != "") {
            $this->db->where("is_aprove", $is_aprove);
        }

        if ($is_realisasi != "") {
            $this->db->where("is_realisasi", $is_realisasi);
        }

        return $this->db->get("t_simulasi_pinjaman_ang");
    }

    public function get_simulasi_angsuran($no_pinjam)
    {
        $this->db->select("no_pinjam_det, no_pinjam, tgl_pinjam, hari, blth_angsuran, bulan_angsuran, tahun_angsuran, angs_ke, tempo_bln, pokok_awal, pokok, bunga, angsuran, pokok_akhir, sts_lunas, sts_potga, blth_bayar, bukti_pelunasan, bukti_tagihan, nm_pot_bonus, tgl_update")
            ->where("no_pinjam", $no_pinjam)
            ->order_by("angs_ke");

        return $this->db->get("t_simulasi_pinjaman_ang_det");
    }

    public function get_angsuran($no_pinjam)
    {
        $this->db->select("no_pinjam_det, no_pinjam, tgl_pinjam, hari, blth_angsuran, bulan_angsuran, tahun_angsuran, angs_ke, tempo_bln, pokok_awal, pokok, bunga, angsuran, pokok_akhir, sts_lunas, sts_potga, blth_bayar, bukti_pelunasan, bukti_tagihan, nm_pot_bonus")
            ->where("no_pinjam", $no_pinjam)
            ->order_by("angs_ke");

        return $this->db->get("t_pinjaman_ang_det");
    }

    public function jumlah_hari($tanggal1, $tanggal2)
    {
        $datediff = strtotime($tanggal2) - (strtotime($tanggal1));
        return round($datediff / (60 * 60 * 24));
    }

    public function get_angsuran_reguler($data)
    {
        $tgl_pinjam = (isset($data['mode']) and $data['mode'] == "realisasi") ? $data['tgl_realisasi'] : $data['tgl_pinjam'];

        $xtgl           = strtotime($tgl_pinjam);
        $tahun          = date("Y", $xtgl);
        $bulan          = date("m", $xtgl);
        $hari_realisasi = date("d", $xtgl);

        $tgl_angs = date('Y-m-t', mktime(0, 0, 0, $bulan + 1, 1, $tahun));
        $tgl_awal = $tgl_pinjam;

        $data_margin = $this->master_model->get_margin_pinjaman_berlaku("1", $data['tempo_bln'], $data['tgl_pinjam']);

        $margin = ($data_margin->num_rows() > 0) ? $data_margin->row(0)->rate : 0;

        $pokok_awal = hapus_koma($data['jml_pinjam']) + hapus_koma($data['jml_biaya_admin']);

        $data_angsuran = array();

        for ($i = 0; $i < $data['tempo_bln']; $i++) {
            $blth_angsuran = substr($tgl_angs, 0, 7);
            $tahun         = date("Y", strtotime($tgl_angs));
            $bulan         = date("m", strtotime($tgl_angs));

            $pokok_per_bulan    = (hapus_koma($data['jml_pinjam']) + hapus_koma($data['jml_biaya_admin'])) / $data['tempo_bln'];
            $margin_per_bulan   = hapus_koma($data['jml_pinjam']) * (($margin / 100) / 12);
            $angsuran_per_bulan = $pokok_per_bulan + $margin_per_bulan;

            $pokok_akhir = $pokok_awal - $pokok_per_bulan;

            $item = array(
                "blth_angsuran"      => $blth_angsuran,
                "tahun"              => $tahun,
                "bulan"              => $bulan,
                "hari"               => "",
                "pokok_awal"         => $pokok_awal,
                "pokok_per_bulan"    => $pokok_per_bulan,
                "margin_per_bulan"   => $margin_per_bulan,
                "angsuran_per_bulan" => $angsuran_per_bulan,
                "pokok_akhir"        => $pokok_akhir,
                "nm_pot_bonus"       => "",
            );

            $data_angsuran[] = $item;

            $tgl_angs = date("Y-m-t", mktime(0, 0, 0, $bulan + 1, 1, $tahun));

            $pokok_awal = $pokok_akhir;
        }

        return $data_angsuran;
    }

    public function get_angsuran_kkb($data)
    {
        $tgl_pinjam = (isset($data['mode']) and $data['mode'] == "realisasi") ? $data['tgl_realisasi'] : $data['tgl_pinjam'];

        $xtgl           = strtotime($tgl_pinjam);
        $tahun          = date("Y", $xtgl);
        $bulan          = date("m", $xtgl);
        $hari_realisasi = date("d", $xtgl);

        $tgl_awal = $tgl_pinjam;
        $tgl_angs = date('Y-m-t', mktime(0, 0, 0, $bulan, 1, $tahun));

        $angsuran         = hapus_koma($data['gaji']) * ($data['persen_angsuran'] / 100);
        $jml_min_angsuran = hapus_koma($data['gaji']) * ($data['min_angsuran'] / 100);
        $jml_max_angsuran = hapus_koma($data['gaji']) * ($data['max_angsuran'] / 100);

        $data_margin = $this->master_model->get_margin_pinjaman_berlaku("2", $data['tempo_bln'], $data['tgl_pinjam']);

        $margin = ($data_margin->num_rows() > 0) ? $data_margin->row(0)->rate : 0;

        $pokok_awal  = hapus_koma($data['jml_pinjam']);
        $pokok_akhir = hapus_koma($data['jml_pinjam']);

        $data_angsuran = array();

        for ($i = 0; $i < $data['tempo_bln']; $i++) {
            $blth_angsuran = substr($tgl_angs, 0, 7);
            $tahun         = date("Y", strtotime($tgl_angs));
            $bulan         = date("m", strtotime($tgl_angs));
            $hari          = $this->jumlah_hari($tgl_awal, $tgl_angs);

            $data_pot_bonus = $this->master_model->get_pot_bonus_pg_berlaku($tahun, $bulan);

            $banyak_min_angsuran = ($data_pot_bonus->num_rows() > 0) ? $data_pot_bonus->row(0)->banyak_min_angsuran : 0;
            $banyak_max_angsuran = ($data_pot_bonus->num_rows() > 0) ? $data_pot_bonus->row(0)->banyak_max_angsuran : 0;
            $nm_pot_bonus        = ($data_pot_bonus->num_rows() > 0) ? $data_pot_bonus->row(0)->nm_pot_bonus : "";

            $angsuran = hapus_koma($data['gaji']) * ($data['persen_angsuran'] / 100);

            if ($i == 0) {
                $banyak_min_angsuran = 0;
                $banyak_max_angsuran = 0;
                $angsuran            = 0;
                $nm_pot_bonus        = "";
            }

            if ($angsuran > 0) {
                $nm_pot_bonus = "Potong Gaji; " . $nm_pot_bonus;
            }

            $angsuran_per_bulan = $angsuran + ($banyak_min_angsuran * $jml_min_angsuran) + ($banyak_max_angsuran * $jml_max_angsuran);
            $pokok_per_bulan    = ($i == 0) ? hapus_koma($data['jml_pinjam']) : 0;
            $margin_per_bulan   = $pokok_akhir * ($margin / 100) * ($hari / 365);

            $pokok_akhir = $pokok_awal + $margin_per_bulan - $angsuran_per_bulan;

            if ($pokok_akhir <= 0) {
                $angsuran_per_bulan = $pokok_awal + $margin_per_bulan;
                $pokok_akhir        = 0;
            }

            $item = array(
                "blth_angsuran"      => $blth_angsuran,
                "tahun"              => $tahun,
                "bulan"              => $bulan,
                "hari"               => $hari,
                "pokok_awal"         => $pokok_awal,
                "pokok_per_bulan"    => $pokok_per_bulan,
                "margin_per_bulan"   => $margin_per_bulan,
                "angsuran_per_bulan" => $angsuran_per_bulan,
                "pokok_akhir"        => $pokok_akhir,
                "nm_pot_bonus"       => $nm_pot_bonus,
            );

            $data_angsuran[] = $item;

            if ($pokok_akhir == 0) {
                break;
            }

            $tgl_awal = $tgl_angs;
            $tgl_angs = date("Y-m-t", mktime(0, 0, 0, $bulan + 1, 1, $tahun));

            $pokok_awal = $pokok_akhir;
        }

        return $data_angsuran;
    }

    public function get_angsuran_pht($data)
    {
        $tgl_pinjam = (isset($data['mode']) and $data['mode'] == "realisasi") ? $data['tgl_realisasi'] : $data['tgl_pinjam'];

        $xtgl           = strtotime($tgl_pinjam);
        $tahun          = date("Y", $xtgl);
        $bulan          = date("m", $xtgl);
        $hari_realisasi = date("d", $xtgl);

        $tgl_angs = date('Y-m-t', mktime(0, 0, 0, $bulan + 1, 1, $tahun));
        $tgl_awal = $tgl_pinjam;

        $data_margin = $this->master_model->get_margin_pinjaman_berlaku("3", $data['tempo_bln'], $data['tgl_pinjam']);

        $margin = ($data_margin->num_rows() > 0) ? $data_margin->row(0)->rate : 0;

        $pokok_awal = hapus_koma($data['jml_pinjam']);

        $data_angsuran = array();

        for ($i = 0; $i < $data['tempo_bln']; $i++) {
            $blth_angsuran = substr($tgl_angs, 0, 7);
            $tahun         = date("Y", strtotime($tgl_angs));
            $bulan         = date("m", strtotime($tgl_angs));

            if ($data['jns_potong_bunga'] == "POTONG") {
                if ($i == ($data['tempo_bln'] - 1)) {
                    $pokok_per_bulan    = ($i < ($data['tempo_bln'] - 1)) ? 0 : hapus_koma($data['jml_pinjam']);
                    $margin_per_bulan   = 0;
                    $angsuran_per_bulan = ($pokok_per_bulan + $margin_per_bulan);
                    $pokok_akhir        = (hapus_koma($data['jml_pinjam']) - $pokok_per_bulan);

                    $item = array(
                        "blth_angsuran"      => $blth_angsuran,
                        "tahun"              => $tahun,
                        "bulan"              => $bulan,
                        "hari"               => "",
                        "pokok_awal"         => $pokok_awal,
                        "pokok_per_bulan"    => $pokok_per_bulan,
                        "margin_per_bulan"   => $margin_per_bulan,
                        "angsuran_per_bulan" => $angsuran_per_bulan,
                        "pokok_akhir"        => $pokok_akhir,
                        "nm_pot_bonus"       => "",
                    );

                    $data_angsuran[] = $item;
                }
            } else {
                $pokok_per_bulan    = ($i < ($data['tempo_bln'] - 1)) ? 0 : hapus_koma($data['jml_pinjam']);
                $margin_per_bulan   = hapus_koma($data['jml_margin']) / $data['tempo_bln'];
                $angsuran_per_bulan = ($pokok_per_bulan + $margin_per_bulan);
                $pokok_akhir        = (hapus_koma($data['jml_pinjam']) - $pokok_per_bulan);

                $item = array(
                    "blth_angsuran"      => $blth_angsuran,
                    "tahun"              => $tahun,
                    "bulan"              => $bulan,
                    "hari"               => "",
                    "pokok_awal"         => $pokok_awal,
                    "pokok_per_bulan"    => $pokok_per_bulan,
                    "margin_per_bulan"   => $margin_per_bulan,
                    "angsuran_per_bulan" => $angsuran_per_bulan,
                    "pokok_akhir"        => $pokok_akhir,
                    "nm_pot_bonus"       => "",
                );

                $data_angsuran[] = $item;
            }

            $tgl_angs = date("Y-m-t", mktime(0, 0, 0, $bulan + 1, 1, $tahun));
        }

        return $data_angsuran;
    }

    public function get_angsuran_kpr($data)
    {
        $tgl_pinjam = (isset($data['mode']) and $data['mode'] == "realisasi") ? $data['tgl_realisasi'] : $data['tgl_pinjam'];

        $xtgl           = strtotime($tgl_pinjam);
        $tahun          = date("Y", $xtgl);
        $bulan          = date("m", $xtgl);
        $hari_realisasi = date("d", $xtgl);

        $tgl_awal = $tgl_pinjam;
        $tgl_angs = date('Y-m-t', mktime(0, 0, 0, $bulan, 1, $tahun));

        $angsuran         = hapus_koma($data['gaji']) * ($data['persen_angsuran'] / 100);
        $jml_min_angsuran = hapus_koma($data['gaji']) * ($data['min_angsuran'] / 100);
        $jml_max_angsuran = hapus_koma($data['gaji']) * ($data['max_angsuran'] / 100);

        $data_margin = $this->master_model->get_margin_pinjaman_berlaku("4", $data['tempo_bln'], $data['tgl_pinjam']);

        $margin = ($data_margin->num_rows() > 0) ? $data_margin->row(0)->rate : 0;

        $pokok_awal  = hapus_koma($data['jml_pinjam']);
        $pokok_akhir = hapus_koma($data['jml_pinjam']);

        $data_angsuran = array();

        for ($i = 0; $i < $data['tempo_bln']; $i++) {
            $blth_angsuran = substr($tgl_angs, 0, 7);
            $tahun         = date("Y", strtotime($tgl_angs));
            $bulan         = date("m", strtotime($tgl_angs));
            $hari          = $this->jumlah_hari($tgl_awal, $tgl_angs);

            $data_pot_bonus = $this->master_model->get_pot_bonus_pg_berlaku($tahun, $bulan);

            $banyak_min_angsuran = ($data_pot_bonus->num_rows() > 0) ? $data_pot_bonus->row(0)->banyak_min_angsuran : 0;
            $banyak_max_angsuran = ($data_pot_bonus->num_rows() > 0) ? $data_pot_bonus->row(0)->banyak_max_angsuran : 0;
            $nm_pot_bonus        = ($data_pot_bonus->num_rows() > 0) ? $data_pot_bonus->row(0)->nm_pot_bonus : "";

            $angsuran = hapus_koma($data['gaji']) * ($data['persen_angsuran'] / 100);

            if ($i == 0) {
                $banyak_min_angsuran = 0;
                $banyak_max_angsuran = 0;
                $angsuran            = 0;
                $nm_pot_bonus        = "";
            }

            if ($angsuran > 0) {
                $nm_pot_bonus = "Potong Gaji; " . $nm_pot_bonus;
            }

            $angsuran_per_bulan = $angsuran + ($banyak_min_angsuran * $jml_min_angsuran) + ($banyak_max_angsuran * $jml_max_angsuran);
            $pokok_per_bulan    = ($i == 0) ? hapus_koma($data['jml_pinjam']) : 0;
            $margin_per_bulan   = $pokok_akhir * ($margin / 100) * ($hari / 365);

            $pokok_akhir = $pokok_awal + $margin_per_bulan - $angsuran_per_bulan;

            if ($pokok_akhir <= 0) {
                $angsuran_per_bulan = $pokok_awal + $margin_per_bulan;
                $pokok_akhir        = 0;
            }

            $item = array(
                "blth_angsuran"      => $blth_angsuran,
                "tahun"              => $tahun,
                "bulan"              => $bulan,
                "hari"               => $hari,
                "pokok_awal"         => $pokok_awal,
                "pokok_per_bulan"    => $pokok_per_bulan,
                "margin_per_bulan"   => $margin_per_bulan,
                "angsuran_per_bulan" => $angsuran_per_bulan,
                "pokok_akhir"        => $pokok_akhir,
                "nm_pot_bonus"       => $nm_pot_bonus,
            );

            $data_angsuran[] = $item;

            if ($pokok_akhir == 0) {
                break;
            }

            $tgl_awal = $tgl_angs;
            $tgl_angs = date("Y-m-t", mktime(0, 0, 0, $bulan + 1, 1, $tahun));

            $pokok_awal = $pokok_akhir;
        }

        return $data_angsuran;
    }

    public function get_bukti_pelunasan($tgl_pelunasan, $kode = "PL")
    {
        $strtime = strtotime($tgl_pelunasan);
        $tahun   = date("Y", $strtime);
        $bulan   = date("m", $strtime);

        $nomor_baru = $kode . $bulan . $tahun;

        $nomor = $this->db->select("ifnull(max(substr(bukti_lunas, -5)), 0) + 1 nomor")->like("bukti_lunas", $nomor_baru, "after")
            ->get("t_pelunasan")->row(0)->nomor;

        $nomor_baru .= str_pad($nomor, "5", "0", STR_PAD_LEFT);

        if ($this->db->set("bukti_lunas", $nomor_baru)->insert("t_pelunasan")) {
            return $nomor_baru;
        } else {
            return $this->get_bukti_pelunasan($tgl_pelunasan, $kode);
        }
    }

    public function get_pinjaman_lunas($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $jenis_pelunasan = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "bukti_lunas, tgl_lunas, no_ref_bukti, tgl_ref_bukti, kd_toko, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_pinjaman, nm_pinjaman, kd_piutang, jns_pelunasan, is_sparepart, blth_angsuran, tempo_bln, angsur_bln, sisa_bln, bunga, bunga_bln, bunga_harian, angsuran, jml_pokok, jml_pokok_bln, jml_admin, jml_bunga, jml_bunga_bln, jml_angsuran, jml_sisa_pokok, jml_sisa_bunga, jml_sisa_angsuran, jml_sisa_angsuran_lama, jml_sisa_angsuran_baru, jml_selisih_sisa_angsuran, jml_pot_bunga, persen_denda, jml_denda, persen_asuransi, jml_asuransi, jml_bayar, jml_dibayar, ket, is_proses_plafon, rilis, tgl_update";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang", "no_peg", "nm_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "tgl_lunas desc, bukti_lunas desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($jenis_pelunasan != "") {
            if ($jenis_pelunasan == "NONPINJAMAN") {
                $this->db->where("jns_pelunasan !=", "PINJAMAN");
            } else {
                $this->db->where("jns_pelunasan", $jenis_pelunasan)->where("kd_pinjaman is not null");
            }
        }

        return $this->db->get("t_pelunasan");
    }

    public function get_pinjaman_belum_lunas1($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $no_ang = "", $tahun = "", $bulan = "")
    {
        if (!$bulan) {
            $bulan = date('m');
        }

        if (!$tahun) {
            $tahun = date('Y');
        }

        $blth_lalu = date("Y-m", mktime(0, 0, 0, $bulan - 1, 1, $tahun));

        $blth_skrg = $tahun . "-" . $bulan;

        $sub_pinjaman_det = "SELECT * FROM t_pinjaman_ang_det WHERE blth_angsuran <= '" . $blth_skrg . "' order by blth_angsuran desc, no_pinjam desc";

        $this->db->select("b.tempo_bln - (count(*)-1) sisa_bln, b.blth_angsuran, b.bunga, b.angs_ke,
            IF(a.kd_pinjaman in('2', '4'), sum(if(blth_angsuran = '" . $blth_lalu . "', b.pokok_akhir, 0)), (b.tempo_bln - (count(*)-1)) * b.angsuran) posisi_akhir,
            a.no_pinjam, a.tgl_pinjam, a.no_ang, a.no_peg, a.kd_prsh, a.nm_prsh, a.kd_dep, a.nm_dep, a.kd_bagian, a.nm_bagian, a.kd_pinjaman, a.nm_pinjaman, a.tempo_bln, a.jml_pinjam, a.angsuran")
            ->from("t_pinjaman_ang a")->join("(" . $sub_pinjaman_det . ") b", "a.no_pinjam=b.no_pinjam")
            ->where("a.sts_lunas", "0")
            ->group_by("a.no_pinjam");

        if ($no_ang != "") {
            $this->db->where("a.no_ang", $no_ang);
        }

        $dataset = $this->db->get_compiled_select();

        $this->db->from("(" . $dataset . ") as a");

        $select = ($numrows) ? "count(*) numrows" : "no_pinjam, tgl_pinjam, kd_pinjaman, nm_pinjaman, tempo_bln, jml_pinjam, blth_angsuran, angsuran, bunga, posisi_akhir, sisa_bln";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang", "no_peg", "nm_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "tgl_pinjam, no_pinjam";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $query = str_replace("`", "", $this->db->get_compiled_select());

        return $this->db->query($query);
    }

    public function get_pinjaman_belum_lunas($no_ang = "", $tahun = "", $bulan = "", $mode = "")
    {
        if (!$bulan) {
            $bulan = date('m');
        }

        if (!$tahun) {
            $tahun = date('Y');
        }

        $blth_lalu = date("Y-m", mktime(0, 0, 0, $bulan - 1, 1, $tahun));

        $blth_skrg = $tahun . "-" . $bulan;

        $whereKodePinjaman = ($mode == "cetak") ? " and (a.angsuran != 0) " : "";

        /*(a.tempo_bln - a.angs_ke + 1)*/
        /*if((max_tempo - a.angs_ke + 1) < 0, concat('+', abs((max_tempo - a.angs_ke + 1))), (max_tempo - a.angs_ke + 1)) sisa_bln*/

        $query_kredit = "
            select (@nomor:=@nomor+1) nomor, x.*, (angs_ke - 1) sudah_diangsur, date_format(tgl_pinjam, '%d-%m-%Y') tgl_pinjam1
            from (
                select no_pinjam, tgl_pinjam, '' kd_toko, no_ang, no_peg, nm_ang, jml_pinjam, margin, jml_margin, tempo_bln, sisa_bln, angs_ke, blth_angsuran, angsuran, posisi_akhir, jml_biaya_admin, 'PINJAMAN UANG' ket, 'PINJAMAN' asal_data, '0' is_sparepart, bunga, kd_pinjaman, nm_pinjaman, sts_lunas
                from (
                    SELECT
                        a.no_pinjam, tgl_pinjam, '' kd_toko, no_ang, no_peg, nm_ang, a.jml_pinjam, margin, jml_margin, max_tempo tempo_bln, (max_tempo - a.angs_ke + 1) sisa_bln, angs_ke, a.blth_angsuran, angsuran,
                        if(a.kd_pinjaman in ('2', '4'), if(a.angsuran != 0, (max_tempo - a.angs_ke + 1) * a.angsuran, a.pokok_awal), (max_tempo - a.angs_ke + 1) * a.angsuran)  posisi_akhir, jml_biaya_admin, 'PINJAMAN UANG' ket, 'PINJAMAN' asal_data, '0' is_sparepart, bunga, kd_pinjaman, nm_pinjaman, sts_lunas
                    FROM
                    (
                        SELECT a.*, b.blth_angsuran, b.angs_ke, b.pokok, b.bunga, b.pokok_awal
                        FROM k3pg_sp.t_pinjaman_ang a
                        JOIN k3pg_sp.t_pinjaman_ang_det b
                        ON a.no_pinjam = b.no_pinjam
                        WHERE no_ang = '" . $no_ang . "'
                    ) a
                    join (
                        SELECT a.no_pinjam, a.jml_pinjam, min(b.blth_angsuran) blth_angsuran, max(angs_ke) max_tempo
                        FROM k3pg_sp.t_pinjaman_ang a
                        JOIN k3pg_sp.t_pinjaman_ang_det b
                        ON a.no_pinjam = b.no_pinjam
                        WHERE no_ang = '" . $no_ang . "'
                        AND '" . $blth_skrg . "' <= b.blth_angsuran
                        " . $whereKodePinjaman . "
                        GROUP BY a.no_pinjam
                    ) b
                    ON a.no_pinjam=b.no_pinjam and a.blth_angsuran=b.blth_angsuran
                    where substr(a.tgl_pinjam, 1, 7) <= '" . $blth_skrg . "'
                    and a.sts_lunas = '0'
                    and a.is_entri_lunas = '0'
                ) data_pinjaman
                UNION
                SELECT a.no_trans, a.tgl_trans, '' kd_toko, a.no_ang, a.no_peg, a.nm_ang, a.jml_trans,
                    if(a.tgl_trans >= '2018-09-01',
                        if(a.tempo_bln = '12' or a.tempo_bln = '24' or a.tempo_bln = '36', a.margin, a.margin * 12)
                    , a.margin) margin
                    , a.jml_margin, a.tempo_bln, COUNT(*) sisa_bln, b.angs_ke, b.blth_angsuran, a.angsuran, sum(b.angsuran) sisa_angsuran, 0 jml_biaya_admin, ket, 'BP' asal_data, if(a.ket LIKE '%spare%' or a.unit_adm = 'BENGKEL', 1, 0) is_sparepart, a.margin bunga, '' kd_pinjaman, '' nm_pinjaman, a.sts_bayar
                FROM t_bridging_plafon a
                JOIN t_bridging_plafon_det b
                ON a.no_trans=b.no_trans
                WHERE no_ang = '" . $no_ang . "'
                    and substr(a.tgl_trans, 1, 7) <= '" . $blth_skrg . "'
                    AND b.blth_angsuran >= '" . $blth_skrg . "'
                    AND (a.sts_bayar = '0' or (a.sts_bayar = '1' and '" . $blth_skrg . "' <= a.blth_bayar))
                    AND a.is_entri_lunas = '0'
                GROUP BY a.no_trans
                UNION
                SELECT b.ref_bukti_bo, b.tanggal, b.kd_toko, a.no_ang, a.no_peg, a.nm_ang, b.pokok_kredit, b.suku_bunga, b.nilai_bunga, b.ang_bulan, b.sisa_bln, b.angs_ke, b.blth_angsuran, b.angs_perbulan, b.sisa_angsuran, 0 jml_biaya_admin, ifnull(b.nama_barang, '[TOKO] BELANJA KREDIT ANGSURAN') ket, 'TOKO_KR_ANG' asal_data, '0' is_sparepart, 0 bunga, '' kd_pinjaman, '' nm_pinjaman, b.is_lunas
                FROM t_anggota a JOIN
                (
                    SELECT
                    a.ref_bukti_bo, a.kd_toko, date(tanggal) tanggal, noang, pokok_kredit, a.suku_bunga, nilai_bunga, ang_bulan, count(*) sisa_bln, angs_perbulan, CONCAT(b.tahun_angsuran, '-', lpad(b.bulan_angsuran, 2, '0')) blth_angsuran, (count(*) * angs_perbulan) sisa_angsuran, min(b.angs_ke) angs_ke, d.nama_barang, a.is_lunas
                    FROM db_wecode_smart.t_kredit_anggota a
                    JOIN db_wecode_smart.t_kredit_anggota_det b
                    ON a.ref_bukti_bo = b.no_pinjam AND a.kd_toko = b.flokasi
                    join db_wecode_smart.rst_fc_trans_detail c 
                    on a.ref_bukti_bo = c.fcode and a.kd_toko = c.flokasi
                    join db_wecode_smart.barang d on c.fitemkey = d.kode
                    WHERE a.noang = '" . $no_ang . "'
                        and substr(date(tanggal), 1, 7) <= '" . $blth_skrg . "'
                        AND CONCAT(b.tahun_angsuran, '-', lpad(b.bulan_angsuran, 2, '0')) >= '" . $blth_skrg . "'
                        AND (a.is_lunas = '0' or (a.is_lunas = '1' and '" . $blth_skrg . "' <= substr(a.tgl_lunas, 1, 7)))
                        AND a.is_entri_lunas = '0'
                    GROUP BY ref_bukti_bo
                ) b
                ON a.no_ang=b.noang 
                UNION
                SELECT b.ref_bukti_bo, b.tanggal, b.kd_toko, a.no_ang, a.no_peg, a.nm_ang, b.pokok_kredit, b.suku_bunga, b.nilai_bunga, b.ang_bulan, b.sisa_bln, b.angs_ke, b.blth_angsuran, b.angs_perbulan, b.sisa_angsuran, 0 jml_biaya_admin, ifnull(b.nama_barang, '[TOKO] BELANJA KREDIT ANGSURAN') ket, 'TOKO_KR_ANG' asal_data, '0' is_sparepart, 0 bunga, '' kd_pinjaman, '' nm_pinjaman, b.is_lunas
                FROM t_anggota a JOIN
                (
                    SELECT
                    a.ref_bukti_bo, a.kd_toko, date(tanggal) tanggal, noang, pokok_kredit, a.suku_bunga, nilai_bunga, ang_bulan, count(*) sisa_bln, angs_perbulan, CONCAT(b.tahun_angsuran, '-', lpad(b.bulan_angsuran, 2, '0')) blth_angsuran, (count(*) * angs_perbulan) sisa_angsuran, min(b.angs_ke) angs_ke, d.nama_barang, a.is_lunas
                    FROM db_bengkel.t_kredit_anggota a
                    JOIN db_bengkel.t_kredit_anggota_det b
                    ON a.ref_bukti_bo = b.no_pinjam AND a.kd_toko = b.flokasi
                    join db_bengkel.rst_fc_trans_detail c 
                    on a.ref_bukti_bo = c.fcode and a.kd_toko = c.flokasi
                    join db_bengkel.barang d on c.fitemkey = d.kode
                    WHERE a.noang = '" . $no_ang . "'
                        and substr(date(tanggal), 1, 7) <= '" . $blth_skrg . "'
                        AND CONCAT(b.tahun_angsuran, '-', lpad(b.bulan_angsuran, 2, '0')) >= '" . $blth_skrg . "'
                        AND (a.is_lunas = '0' or (a.is_lunas = '1' and '" . $blth_skrg . "' <= substr(a.tgl_lunas, 1, 7)))
                        AND a.is_entri_lunas = '0'
                    GROUP BY ref_bukti_bo
                ) b
                ON a.no_ang=b.noang 
                UNION
                SELECT b.ref_bukti_bo, b.tanggal, b.kd_toko, a.no_ang, a.no_peg, a.nm_ang, b.pokok_kredit, b.suku_bunga, b.nilai_bunga, b.ang_bulan, b.sisa_bln, b.angs_ke, b.blth_angsuran, b.angs_perbulan, b.sisa_angsuran, 0 jml_biaya_admin, ifnull(b.nama_barang, '[TOKO] BELANJA KREDIT ANGSURAN') ket, 'TOKO_KR_ANG' asal_data, '0' is_sparepart, 0 bunga, '' kd_pinjaman, '' nm_pinjaman, b.is_lunas
                FROM t_anggota a JOIN
                (
                    SELECT
                    a.ref_bukti_bo, a.kd_toko, date(tanggal) tanggal, noang, pokok_kredit, a.suku_bunga, nilai_bunga, ang_bulan, count(*) sisa_bln, angs_perbulan, CONCAT(b.tahun_angsuran, '-', lpad(b.bulan_angsuran, 2, '0')) blth_angsuran, (count(*) * angs_perbulan) sisa_angsuran, min(b.angs_ke) angs_ke, d.nama_barang, a.is_lunas
                    FROM db_pbb.t_kredit_anggota a
                    JOIN db_pbb.t_kredit_anggota_det b
                    ON a.ref_bukti_bo = b.no_pinjam AND a.kd_toko = b.flokasi
                    join db_pbb.rst_fc_trans_detail c 
                    on a.ref_bukti_bo = c.fcode and a.kd_toko = c.flokasi
                    join db_pbb.barang d on c.fitemkey = d.kode
                    WHERE a.noang = '" . $no_ang . "'
                        and substr(date(tanggal), 1, 7) <= '" . $blth_skrg . "'
                        AND CONCAT(b.tahun_angsuran, '-', lpad(b.bulan_angsuran, 2, '0')) >= '" . $blth_skrg . "'
                        AND (a.is_lunas = '0' or (a.is_lunas = '1' and '" . $blth_skrg . "' <= substr(a.tgl_lunas, 1, 7)))
                        AND a.is_entri_lunas = '0'
                    GROUP BY ref_bukti_bo
                ) b
                ON a.no_ang=b.noang
                UNION 
                SELECT b.ref_penjualan, b.tanggal, b.toko_kode, a.no_ang, a.no_peg, a.nm_ang, b.jumlah, b.suku_bunga, b.nilai_bunga, b.ang_bulan, b.sisa_bln, '1' angs_ke, '" . $blth_skrg . "' blth_angsuran, jumlah angs_perbulan, b.sisa_angsuran, 0 jml_biaya_admin, '[TOKO] BELANJA KREDIT' ket, 'TOKO_PIUTANG' asal_data, '0' is_sparepart, 0 bunga, '' kd_pinjaman, '' nm_pinjaman, is_lunas
                FROM t_anggota a JOIN
                (
                    SELECT ref_penjualan, toko_kode, pelanggan_kode, date(tanggal) tanggal, jumlah, 0 suku_bunga, 0 nilai_bunga, '1' ang_bulan, '1' sisa_bln, jumlah sisa_angsuran, is_lunas
                    FROM db_wecode_smart.piutang
                    WHERE pelanggan_kode = '" . $no_ang . "'
                        AND substr(date(tanggal), 1, 7) <= '" . $blth_skrg . "'
                        AND (is_lunas = '0' or (is_lunas = '1' and '" . $blth_skrg . "' <= substr(tgl_lunas, 1, 7)))
                        AND is_entri_lunas = '0'
                ) b
                ON a.no_ang=b.pelanggan_kode 
                UNION 
                SELECT b.ref_penjualan, b.tanggal, b.toko_kode, a.no_ang, a.no_peg, a.nm_ang, b.jumlah, b.suku_bunga, b.nilai_bunga, b.ang_bulan, b.sisa_bln, '1' angs_ke, '" . $blth_skrg . "' blth_angsuran, jumlah angs_perbulan, b.sisa_angsuran, 0 jml_biaya_admin, '[TOKO] BELANJA KREDIT' ket, 'TOKO_PIUTANG' asal_data, '0' is_sparepart, 0 bunga, '' kd_pinjaman, '' nm_pinjaman, is_lunas
                FROM t_anggota a JOIN
                (
                    SELECT ref_penjualan, toko_kode, pelanggan_kode, date(tanggal) tanggal, jumlah, 0 suku_bunga, 0 nilai_bunga, '1' ang_bulan, '1' sisa_bln, jumlah sisa_angsuran, is_lunas
                    FROM db_bengkel.piutang
                    WHERE pelanggan_kode = '" . $no_ang . "'
                        AND substr(date(tanggal), 1, 7) <= '" . $blth_skrg . "'
                        AND (is_lunas = '0' or (is_lunas = '1' and '" . $blth_skrg . "' <= substr(tgl_lunas, 1, 7)))
                        AND is_entri_lunas = '0'
                ) b
                ON a.no_ang=b.pelanggan_kode 
                UNION 
                SELECT b.ref_penjualan, b.tanggal, b.toko_kode, a.no_ang, a.no_peg, a.nm_ang, b.jumlah, b.suku_bunga, b.nilai_bunga, b.ang_bulan, b.sisa_bln, '1' angs_ke, '" . $blth_skrg . "' blth_angsuran, jumlah angs_perbulan, b.sisa_angsuran, 0 jml_biaya_admin, '[TOKO] BELANJA KREDIT' ket, 'TOKO_PIUTANG' asal_data, '0' is_sparepart, 0 bunga, '' kd_pinjaman, '' nm_pinjaman, is_lunas
                FROM t_anggota a JOIN
                (
                    SELECT ref_penjualan, toko_kode, pelanggan_kode, date(tanggal) tanggal, jumlah, 0 suku_bunga, 0 nilai_bunga, '1' ang_bulan, '1' sisa_bln, jumlah sisa_angsuran, is_lunas
                    FROM db_pbb.piutang
                    WHERE pelanggan_kode = '" . $no_ang . "'
                        AND substr(date(tanggal), 1, 7) <= '" . $blth_skrg . "'
                        AND (is_lunas = '0' or (is_lunas = '1' and '" . $blth_skrg . "' <= substr(tgl_lunas, 1, 7)))
                        AND is_entri_lunas = '0'
                ) b
                ON a.no_ang=b.pelanggan_kode
            ) x, (select @nomor:=0) z
            order by tgl_pinjam
        ";

        // exit($query_kredit);

        return $this->db->query($query_kredit);
    }

    public function pelunasan_dipercepat_ganda($data)
    {
        $this->load->model("anggota_model");
        $this->load->model("master_model");

        $dataAnggota = $this->anggota_model->get_anggota(0, "", "", "", "", "", $data[0]['no_ang']);

        $kd_prsh   = $dataAnggota->num_rows() > 0 ? $dataAnggota->row(0)->kd_prsh : "";
        $nm_prsh   = $dataAnggota->num_rows() > 0 ? $dataAnggota->row(0)->nm_prsh : "";
        $kd_dep    = $dataAnggota->num_rows() > 0 ? $dataAnggota->row(0)->kd_dep : "";
        $nm_dep    = $dataAnggota->num_rows() > 0 ? $dataAnggota->row(0)->nm_dep : "";
        $kd_bagian = $dataAnggota->num_rows() > 0 ? $dataAnggota->row(0)->kd_bagian : "";
        $nm_bagian = $dataAnggota->num_rows() > 0 ? $dataAnggota->row(0)->nm_bagian : "";

        // baca_array($data);

        foreach ($data as $key => $value) {
            $is_proses_plafon = $value['sts_lunas'] == 0 ? 1 : 0;

            $set_data = array(
                // "bukti_lunas"     => $bukti_lunas,
                "tgl_lunas"        => balik_tanggal($value['tgl_lunas']),
                // "bukti_potga"     => $bukti_potga,
                "no_ref_bukti"     => $value['no_pinjam'],
                "tgl_ref_bukti"    => $value['tgl_pinjam'],
                "kd_toko"          => $value['kd_toko'],
                // "no_pinjam_baru"  => $no_pinjam_baru,
                // "status_anggota"  => $status_anggota,
                "no_ang"           => $value['no_ang'],
                "no_peg"           => $value['no_peg'],
                "nm_ang"           => $value['nm_ang'],
                "kd_prsh"          => $kd_prsh,
                "nm_prsh"          => $nm_prsh,
                "kd_dep"           => $kd_dep,
                "nm_dep"           => $nm_dep,
                "kd_bagian"        => $kd_bagian,
                "nm_bagian"        => $nm_bagian,
                "jns_pelunasan"    => $value['asal_data'],
                "kd_pinjaman"      => $value['kd_pinjaman'],
                "nm_pinjaman"      => $value['nm_pinjaman'],
                "tempo_bln"        => $value['tempo_bln'],
                "blth_angsuran"    => $value['blth_angsuran'],
                "ket"              => $value['ket'],
                "angsuran"         => $value['angsuran'],
                "sisa_bln"         => $value['sisa_bln'],
                "angsur_bln"       => $value['sudah_diangsur'],
                // "jml_angsuran"    => $jml_angsuran,
                // "jml_pokok"       => $jml_pokok,
                // "jml_pokok_pdk"   => $jml_pokok_pdk,
                // "jml_pokok_pjg"   => $jml_pokok_pjg,
                // "jml_bunga"       => $jml_bunga,
                // "jml_pot_bunga"   => $jml_pot_bunga,
                // "persen_denda"    => $persen_denda,
                // "jml_denda"       => $jml_denda,
                // "persen_asuransi" => $persen_asuransi,
                // "jml_asuransi"    => $jml_asuransi,
                // "jml_bayar"        => hapus_koma($value['jml_bayar']),
                // "jml_dibayar"     => $jml_dibayar,
                "is_sparepart"     => $value['is_sparepart'],
                "is_proses_plafon" => $is_proses_plafon,
                // "rilis"           => $rilis,
                // "tgl_update"      => $tgl_update,
            );

            $jml_sisa_angsuran = $value['angsuran'] * $value['sisa_bln'];

            $data_tempo_bln = $value['tempo_bln'];

            foreach ($this->get_array_tempo_bln() as $key1 => $value1) {
                if ($value['tempo_bln'] <= $value1) {
                    $data_tempo_bln = $value1;
                } else {
                    break;
                }
            }

            // baca($value['tgl_lunas']); exit();

            $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, balik_tanggal($value['tgl_lunas']));

            $bunga = ($data_margin_pinjaman->num_rows() > 0) ? $data_margin_pinjaman->row(0)->rate : 0;

            if ($value['kd_pinjaman'] == "1" or $value['is_sparepart'] == "1") {
                $dataPerhitungan['bunga'] = $bunga;

                $dataPerhitungan['tempo_bln']     = $value['tempo_bln'];
                $dataPerhitungan['no_pinjam']     = $value['no_pinjam'];
                $dataPerhitungan['sisa_bln']      = $value['sisa_bln'];
                $dataPerhitungan['angsuran']      = $value['angsuran'];
                $dataPerhitungan['kd_pinjaman']   = $value['kd_pinjaman'];
                $dataPerhitungan['is_sparepart']  = $value['is_sparepart'];
                $dataPerhitungan['jml_pokok']     = $value['jml_pinjam'];
                $dataPerhitungan['jml_admin']     = $value['jml_biaya_admin'];
                $dataPerhitungan['jml_bunga']     = $value['jml_margin'];
                $dataPerhitungan['tgl_ref_bukti'] = $value['tgl_pinjam'];
                $dataPerhitungan['tgl_lunas']     = balik_tanggal($value['tgl_lunas']);

                // baca_array($dataPerhitungan);

                $perhitungan = $this->perhitungan_pelunasan($dataPerhitungan);

                // baca_array($perhitungan);

                if ($perhitungan['perhitungan_selisih'] >= 0) {
                    $set_data["bunga"]                     = $bunga;
                    $set_data["bunga_bln"]                 = $perhitungan['bunga_bln'];
                    $set_data["sisa_bln"]                  = $value['sisa_bln'];
                    $set_data["jml_pokok"]                 = $value['jml_pinjam'];
                    $set_data["jml_admin"]                 = $value['jml_biaya_admin'];
                    $set_data["jml_bunga"]                 = $value['jml_margin'];
                    $set_data["jml_bunga_bln"]             = $perhitungan['jml_bunga_bln'];
                    $set_data['jml_sisa_angsuran_lama']    = $perhitungan['sisa_angs_lama'];
                    $set_data['jml_sisa_angsuran_baru']    = $perhitungan['sisa_angs_baru'];
                    $set_data['jml_perhitungan_lama']      = $perhitungan['perhitungan_lama'];
                    $set_data['jml_perhitungan_baru']      = $perhitungan['perhitungan_baru'];
                    $set_data['jml_selisih_sisa_angsuran'] = $perhitungan['perhitungan_selisih'];
                    $set_data['jml_sisa_angsuran']         = $jml_sisa_angsuran;
                    $set_data['jml_bayar']                 = $perhitungan['jml_bayar'];

                    if ($value['sisa_bln'] < $value['tempo_bln']) {
                        $set_data["jml_pokok_bln"]  = $perhitungan['jml_pokok_bln'];
                        $set_data["jml_sisa_pokok"] = $perhitungan['sisa_pokok'];
                    } else {
                        $set_data['jml_hari']         = $perhitungan['jml_hari'];
                        $set_data['jml_bunga_harian'] = $perhitungan['jml_bunga_harian'];
                    }
                } else {
                    $set_data['jml_sisa_angsuran'] = $jml_sisa_angsuran;
                    $set_data['jml_bayar']         = $jml_sisa_angsuran;
                }
            } else if ($value['kd_pinjaman'] == "2" or $value['kd_pinjaman'] == "4") {
                $set_data['jml_sisa_angsuran'] = hapus_koma($value['jml_sisa_angsuran']);
                $set_data['persen_denda']      = $value['persen_denda'];
                $set_data['jml_denda']         = hapus_koma($value['jml_denda']);
                $set_data['persen_asuransi']   = $value['persen_asuransi'];
                $set_data['jml_asuransi']      = hapus_koma($value['jml_asuransi']);

                // $set_data['jml_sisa_angsuran'] = $jml_sisa_angsuran;
                $set_data['jml_bayar']         = hapus_koma($value['jml_bayar']);
            } else if ($value['kd_pinjaman'] == "3") {
                $set_data['jml_pokok']      = hapus_koma($value['jml_pokok']);
                $set_data['persen_denda']   = $value['persen_denda'];
                $set_data['jml_denda']      = hapus_koma($value['jml_denda']);
                $set_data['jml_sisa_bunga'] = hapus_koma($value['jml_bunga_1bulan']);

                $set_data['jml_sisa_angsuran'] = $jml_sisa_angsuran;
                $set_data['jml_bayar']         = hapus_koma($value['jml_bayar']);
            } else {
                $set_data['jml_sisa_angsuran'] = $jml_sisa_angsuran;
                $set_data['jml_bayar']         = $jml_sisa_angsuran;
            }

            // baca_array($set_data); exit();

            $bukti_lunas = $this->get_bukti_pelunasan(balik_tanggal($value['tgl_lunas']), "PL");

            $this->db->set($set_data)->where("bukti_lunas", $bukti_lunas)->update("t_pelunasan");

            if ($value['asal_data'] == "PINJAMAN") {
                $set_pinjaman_lunas = array(
                    "is_entri_lunas" => "1",
                    "sts_lunas"      => "1",
                    "blth_lunas"     => substr(balik_tanggal($value['tgl_lunas']), 0, 7),
                );

                $this->db->set($set_pinjaman_lunas)
                    ->where("no_pinjam", $value['no_pinjam'])
                    ->update("t_pinjaman_ang");

                $set_data_detail = array(
                    "sts_lunas"       => "1",
                    "blth_bayar"      => substr(balik_tanggal($value['tgl_lunas']), 0, 7),
                    "bukti_pelunasan" => $bukti_lunas,
                );

                $this->db->set($set_data_detail)
                    ->where("sts_lunas", "0")
                    ->where("no_pinjam", $value['no_pinjam'])
                    ->where("blth_angsuran >=", $value['blth_angsuran'])
                    ->update("t_pinjaman_ang_det");

            } else if ($value['asal_data'] == "BP") {
                $set_data_pelunasan = array(
                    "is_entri_lunas" => "1",
                    "sts_bayar"      => "1",
                    "blth_bayar"     => substr(balik_tanggal($value['tgl_lunas']), 0, 7),
                );

                $this->db->set($set_data_pelunasan)
                    ->where("no_trans", $value['no_pinjam'])
                    ->update("t_bridging_plafon");

                $set_data_pelunasan = array(
                    "sts_lunas"       => "1",
                    "blth_bayar"      => substr(balik_tanggal($value['tgl_lunas']), 0, 7),
                    "bukti_pelunasan" => $bukti_lunas,
                );

                $this->db->set($set_data_pelunasan)
                    ->where("no_trans", $value['no_pinjam'])
                    ->where("blth_angsuran >=", $value['blth_angsuran'])
                    ->update("t_bridging_plafon_det");

            } else if ($value['asal_data'] == "TOKO_KR_ANG") {
                $set_data_pelunasan = array(
                    "is_entri_lunas" => "1",
                    "is_lunas"       => "1",
                    "nobukti_lunas"  => $bukti_lunas,
                    "tgl_lunas"      => balik_tanggal($value['tgl_lunas']),
                );

                $this->db->set($set_data_pelunasan)
                    ->where("ref_bukti_bo", $value['no_pinjam'])
                    ->where("kd_toko", $value['kd_toko'])
                    ->update("db_wecode_smart.t_kredit_anggota");

                $this->db->set($set_data_pelunasan)
                    ->where("ref_bukti_bo", $value['no_pinjam'])
                    ->where("kd_toko", $value['kd_toko'])
                    ->update("db_bengkel.t_kredit_anggota");

                $this->db->set($set_data_pelunasan)
                    ->where("ref_bukti_bo", $value['no_pinjam'])
                    ->where("kd_toko", $value['kd_toko'])
                    ->update("db_pbb.t_kredit_anggota");

                $set_data_pelunasan = array(
                    "sts_lunas"       => "1",
                    "blth_bayar"      => substr(balik_tanggal($value['tgl_lunas']), 0, 7),
                    "bukti_pelunasan" => $bukti_lunas,
                );

                $this->db->set($set_data_pelunasan)
                    ->where("no_pinjam", $value['no_pinjam'])
                    ->where("flokasi", $value['kd_toko'])
                    ->where("CONCAT(tahun_angsuran, '-', lpad(bulan_angsuran, 2, '0')) >=", $value['blth_angsuran'])
                    ->update("db_wecode_smart.t_kredit_anggota_det");
                
                $this->db->set($set_data_pelunasan)
                    ->where("no_pinjam", $value['no_pinjam'])
                    ->where("flokasi", $value['kd_toko'])
                    ->where("CONCAT(tahun_angsuran, '-', lpad(bulan_angsuran, 2, '0')) >=", $value['blth_angsuran'])
                    ->update("db_bengkel.t_kredit_anggota_det");

                $this->db->set($set_data_pelunasan)
                    ->where("no_pinjam", $value['no_pinjam'])
                    ->where("flokasi", $value['kd_toko'])
                    ->where("CONCAT(tahun_angsuran, '-', lpad(bulan_angsuran, 2, '0')) >=", $value['blth_angsuran'])
                    ->update("db_pbb.t_kredit_anggota_det");

            } else if ($value['asal_data'] == "TOKO_PIUTANG") {
                $set_data_pelunasan = array(
                    "is_entri_lunas" => "1",
                    "is_lunas"       => "1",
                    "nobukti_lunas"  => $bukti_lunas,
                    "tgl_lunas"      => balik_tanggal($value['tgl_lunas']),
                );

                $this->db->set($set_data_pelunasan)
                    ->where("ref_penjualan", $value['no_pinjam'])
                    ->where("toko_kode", $value['kd_toko'])
                    ->update("db_wecode_smart.piutang");
                
                $this->db->set($set_data_pelunasan)
                    ->where("ref_penjualan", $value['no_pinjam'])
                    ->where("toko_kode", $value['kd_toko'])
                    ->update("db_bengkel.piutang");

                $this->db->set($set_data_pelunasan)
                    ->where("ref_penjualan", $value['no_pinjam'])
                    ->where("toko_kode", $value['kd_toko'])
                    ->update("db_pbb.piutang");
            }

            /*hapus data potga yang dilunasi sesuai awal periode pelunasannya*/

            $this->db->like("tgl_potga", $value['blth_angsuran'], "after")->where("no_ref_bukti", ($value['no_pinjam'] . $value['kd_toko']))->delete("t_potga");

            if ($value['sts_lunas'] == "0") {
                $set_debet_plafon = array(
                    "no_ang"      => $value['no_ang'],
                    "no_peg"      => $value['no_peg'],
                    "nm_ang"      => $value['nm_ang'],
                    "kd_prsh"     => $kd_prsh,
                    "nm_prsh"     => $nm_prsh,
                    "kd_dep"      => $kd_dep,
                    "nm_dep"      => $nm_dep,
                    "kd_bagian"   => $kd_bagian,
                    "nm_bagian"   => $nm_bagian,
                    "jenis_debet" => $value['asal_data'],
                    "noref_penj"  => $bukti_lunas,
                    "tgl_penj"    => substr(balik_tanggal($value['tgl_lunas']), 0, 7),
                    "jml_debet"   => (0 - $value['angsuran']),
                    // "status"      => $status,
                );

                // baca_array($set_debet_plafon);

                $this->db->set($set_debet_plafon)->insert("t_plafon_debet");

                $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $value['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

                $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $value['no_ang'])->update("t_anggota");
            }
        }

        return true;
    }

    public function hapus_pelunasan_dipercepat($data)
    {
        if ($data['jns_pelunasan'] == 'PINJAMAN') {
            $set_pinjaman_lunas = array(
                "is_entri_lunas" => "0",
                "sts_lunas"      => "0",
                "blth_lunas"     => null,
            );

            $this->db->set($set_pinjaman_lunas)
                ->where("no_pinjam", $data['no_ref_bukti'])
                ->update("t_pinjaman_ang");

            $set_data = array(
                "sts_lunas"       => "0",
                "blth_bayar"      => null,
                "bukti_pelunasan" => null,
            );

            $this->db->set($set_data)
                ->where("bukti_pelunasan", $data['bukti_lunas'])
                ->update("t_pinjaman_ang_det");

        } else if ($data['jns_pelunasan'] == 'BP') {
            $set_data_pelunasan = array(
                "is_entri_lunas" => "0",
                "sts_bayar"      => "0",
                "blth_bayar"     => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("no_trans", $data['no_ref_bukti'])
                ->update("t_bridging_plafon");

            $set_data_pelunasan = array(
                "sts_lunas"       => "0",
                "blth_bayar"      => null,
                "bukti_pelunasan" => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("no_trans", $data['no_ref_bukti'])
                ->where("bukti_pelunasan", $data['bukti_lunas'])
                ->update("t_bridging_plafon_det");

        } else if ($data['jns_pelunasan'] == "TOKO_KR_ANG") {
            $set_data_pelunasan = array(
                "is_entri_lunas" => "0",
                "is_lunas"       => "0",
                "nobukti_lunas"  => null,
                "tgl_lunas"      => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("ref_bukti_bo", $data['no_ref_bukti'])
                ->where("kd_toko", $data['kd_toko'])
                ->update("db_wecode_smart.t_kredit_anggota");

            $this->db->set($set_data_pelunasan)
                ->where("ref_bukti_bo", $data['no_ref_bukti'])
                ->where("kd_toko", $data['kd_toko'])
                ->update("db_bengkel.t_kredit_anggota");

            $this->db->set($set_data_pelunasan)
                ->where("ref_bukti_bo", $data['no_ref_bukti'])
                ->where("kd_toko", $data['kd_toko'])
                ->update("db_pbb.t_kredit_anggota");

            $set_data_pelunasan = array(
                "sts_lunas"       => "0",
                "blth_bayar"      => null,
                "bukti_pelunasan" => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("no_pinjam", $data['no_ref_bukti'])
                ->where("flokasi", $data['kd_toko'])
                ->where("bukti_pelunasan", $data['bukti_lunas'])
                ->update("db_wecode_smart.t_kredit_anggota_det");

            $this->db->set($set_data_pelunasan)
                ->where("no_pinjam", $data['no_ref_bukti'])
                ->where("flokasi", $data['kd_toko'])
                ->where("bukti_pelunasan", $data['bukti_lunas'])
                ->update("db_bengkel.t_kredit_anggota_det");

            $this->db->set($set_data_pelunasan)
                ->where("no_pinjam", $data['no_ref_bukti'])
                ->where("flokasi", $data['kd_toko'])
                ->where("bukti_pelunasan", $data['bukti_lunas'])
                ->update("db_pbb.t_kredit_anggota_det");

        } else if ($data['jns_pelunasan'] == "TOKO_PIUTANG") {
            $set_data_pelunasan = array(
                "is_entri_lunas" => "0",
                "is_lunas"       => "0",
                "nobukti_lunas"  => null,
                "tgl_lunas"      => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("ref_penjualan", $data['no_ref_bukti'])
                ->where("toko_kode", $data['kd_toko'])
                ->update("db_wecode_smart.piutang");

            $this->db->set($set_data_pelunasan)
                ->where("ref_penjualan", $data['no_ref_bukti'])
                ->where("toko_kode", $data['kd_toko'])
                ->update("db_bengkel.piutang");

            $this->db->set($set_data_pelunasan)
                ->where("ref_penjualan", $data['no_ref_bukti'])
                ->where("toko_kode", $data['kd_toko'])
                ->update("db_pbb.piutang");
        }

        $this->db->where("bukti_lunas", $data['bukti_lunas'])->delete("t_pelunasan");

        $this->db->where("noref_penj", $data['bukti_lunas'])->delete("t_plafon_debet");

        $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

        $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data['no_ang'])->update("t_anggota");

        return true;
    }

    public function proses_pelunasan_pinjaman_dipercepat($data)
    {
        $bukti_lunas = $this->get_bukti_pelunasan($data['tgl_lunas'], $data['kode_bukti']);

        $set_data = array(
            // "bukti_lunas"     => $bukti_lunas,
            "tgl_lunas"        => $data['tgl_lunas'],
            // "bukti_potga"     => $bukti_potga,
            "no_ref_bukti"     => $data['no_pinjam'],
            "tgl_ref_bukti"    => $data['tgl_pinjam'],
            // "no_pinjam_baru"  => $no_pinjam_baru,
            // "status_anggota"  => $status_anggota,
            "no_ang"           => $data['no_ang'],
            "no_peg"           => $data['no_peg'],
            "nm_ang"           => $data['nm_ang'],
            "kd_prsh"          => $data['kd_prsh'],
            "nm_prsh"          => $data['nm_prsh'],
            "kd_dep"           => $data['kd_dep'],
            "nm_dep"           => $data['nm_dep'],
            "kd_bagian"        => $data['kd_bagian'],
            "nm_bagian"        => $data['nm_bagian'],
            "jns_pelunasan"    => $data['jns_pelunasan'],
            "kd_pinjaman"      => $data['kd_pinjaman'],
            "nm_pinjaman"      => $data['nm_pinjaman'],
            "tempo_bln"        => $data['tempo_bln'],
            "blth_angsuran"    => $data['blth_angsuran'],
            "ket"              => $data['ket'],
            "angsuran"         => $data['angsuran'],
            "sisa_bln"         => $data['sisa_bln'],
            "angsur_bln"       => $data['sudah_diangsur'],
            // "angsuran"        => $angsuran,
            // "sisa_bln"        => $sisa_bln,
            // "jml_angsuran"    => $jml_angsuran,
            // "jml_pokok"       => $jml_pokok,
            // "jml_pokok_pdk"   => $jml_pokok_pdk,
            // "jml_pokok_pjg"   => $jml_pokok_pjg,
            // "jml_bunga"       => $jml_bunga,
            // "jml_pot_bunga"   => $jml_pot_bunga,
            // "persen_denda"    => $persen_denda,
            // "jml_denda"       => $jml_denda,
            // "persen_asuransi" => $persen_asuransi,
            // "jml_asuransi"    => $jml_asuransi,
            "jml_bayar"        => hapus_koma($data['jml_bayar']),
            // "jml_dibayar"     => $jml_dibayar,
            "is_proses_plafon" => "1",
            // "rilis"           => $rilis,
            // "tgl_update"      => $tgl_update,
        );

        if ($data['kd_pinjaman'] == "1") {
            $set_data["bunga"]                     = $data['bunga'];
            $set_data["bunga_bln"]                 = $data['bunga_bln'];
            $set_data["jml_pokok"]                 = $data['jml_pokok'];
            $set_data["jml_admin"]                 = $data['jml_admin'];
            $set_data["jml_bunga"]                 = $data['jml_bunga'];
            $set_data["jml_bunga_bln"]             = $data['jml_bunga_bln'];
            $set_data["jml_sisa_angsuran_lama"]    = $data['jml_sisa_angsuran_lama'];
            $set_data["jml_sisa_angsuran_baru"]    = $data['jml_sisa_angsuran_baru'];
            $set_data["jml_selisih_sisa_angsuran"] = $data['jml_selisih_sisa_angsuran'];

            if ($data['sisa_bln'] < $data['tempo_bln']) {
                $set_data["jml_pokok_bln"]  = $data['jml_pokok_bln'];
                $set_data["jml_sisa_pokok"] = $data['jml_sisa_pokok'];
                // $set_data["jml_bayar"]      = $data['jml_sisa_angsuran_baru'];
            } else {
                $set_data['jml_bunga_harian'] = $data['jml_bunga_harian'];
                $set_data['jml_hari']         = $data['jml_hari'];
                // $set_data['jml_bayar']        = $data['jml_bayar'];
            }
        } else if ($data['kd_pinjaman'] == "3") {
            $set_data['jml_pokok']    = hapus_koma($data['jml_pokok']);
            $set_data['persen_denda'] = $data['persen_denda'];
            $set_data['jml_denda']    = hapus_koma($data['jml_denda']);
            $set_data['jml_bunga']    = hapus_koma($data['jml_bunga']);
        } else if (in_array($data['kd_pinjaman'], array("2", "4"))) {
            $set_data['jml_sisa_angsuran'] = hapus_koma($data['jml_sisa_angsuran']);
            $set_data['persen_denda']      = $data['persen_denda'];
            $set_data['jml_denda']         = hapus_koma($data['jml_denda']);
            $set_data['persen_asuransi']   = $data['persen_asuransi'];
            $set_data['jml_asuransi']      = hapus_koma($data['jml_asuransi']);
        }

        $this->db->set($set_data)->where("bukti_lunas", $bukti_lunas)->update("t_pelunasan");

        $set_data_detail = array(
            "sts_lunas"       => "1",
            "blth_bayar"      => substr($data['tgl_lunas'], 0, 7),
            "bukti_pelunasan" => $bukti_lunas,
        );

        $this->db->set($set_data_detail)
            ->where("no_pinjam", $data['no_pinjam'])->where("sts_lunas", "0")
            ->where("blth_angsuran >=", $data['blth_angsuran'])
            ->update("t_pinjaman_ang_det");

        $set_pinjaman_lunas = array(
            "is_entri_lunas" => "1",
            "sts_lunas"      => "1",
            "blth_lunas"     => substr($data['tgl_lunas'], 0, 7),
        );

        $this->db->set($set_pinjaman_lunas)->where("no_pinjam", $data['no_pinjam'])->update("t_pinjaman_ang");

        $this->db->like("tgl_potga", $data['blth_angsuran'], "after")->where("no_ref_bukti", $data['no_pinjam'])->delete("t_potga");

        if ($data['sts_lunas'] == "0") {
            $set_debet_plafon = array(
                "no_ang"      => $data['no_ang'],
                "no_peg"      => $data['no_peg'],
                "nm_ang"      => $data['nm_ang'],
                // "kd_prsh"     => $data['kd_prsh'],
                // "nm_prsh"     => $data['nm_prsh'],
                // "kd_dep"      => $data['kd_dep'],
                // "nm_dep"      => $data['nm_dep'],
                // "kd_bagian"   => $data['kd_bagian'],
                // "nm_bagian"   => $data['nm_bagian'],
                "jenis_debet" => "PINJAMAN",
                "noref_penj"  => $bukti_lunas,
                "tgl_penj"    => substr($data['tgl_lunas'], 0, 7),
                "jml_debet"   => (0 - $data['angsuran']),
                // "status"      => $status,
            );

            $this->db->set($set_debet_plafon)->insert("t_plafon_debet");

            $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

            $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data['no_ang'])->update("t_anggota");
        }

        return true;
    }

    public function hapus_pelunasan_pinjaman_dipercepat($data)
    {
        $set_data = array(
            "sts_lunas"       => "0",
            "blth_bayar"      => null,
            "bukti_pelunasan" => null,
        );

        $this->db->set($set_data)
            ->where("bukti_pelunasan", $data['bukti_lunas'])
            ->update("t_pinjaman_ang_det");

        $set_pinjaman_lunas = array(
            "is_entri_lunas" => "0",
            "sts_lunas"      => "0",
            "blth_lunas"     => null,
        );

        $this->db->set($set_pinjaman_lunas)
            ->where("no_pinjam", $data['no_ref_bukti'])
            ->update("t_pinjaman_ang");

        $this->db->where("bukti_lunas", $data['bukti_lunas'])->delete("t_pelunasan");

        if ($data['is_proses_plafon'] == "1") {
            $this->db->where("noref_penj", $data['bukti_lunas'])->delete("t_plafon_debet");

            $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

            $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data['no_ang'])->update("t_anggota");
        }

        return true;
    }

    public function proses_pelunasan_kredit_non_pinjaman($data)
    {
        $bukti_lunas = $this->get_bukti_pelunasan($data['tgl_lunas'], $data['kode_bukti']);

        $set_data = array(
            // "bukti_lunas"     => $bukti_lunas,
            "tgl_lunas"        => $data['tgl_lunas'],
            // "bukti_potga"     => $bukti_potga,
            "no_ref_bukti"     => $data['no_trans'],
            "tgl_ref_bukti"    => $data['tgl_trans'],
            "kd_toko"          => $data['kd_toko'],
            // "no_pinjam_baru"  => $no_pinjam_baru,
            // "status_anggota"  => $status_anggota,
            "no_ang"           => $data['no_ang'],
            "no_peg"           => $data['no_peg'],
            "nm_ang"           => $data['nm_ang'],
            "kd_prsh"          => $data['kd_prsh'],
            "nm_prsh"          => $data['nm_prsh'],
            "kd_dep"           => $data['kd_dep'],
            "nm_dep"           => $data['nm_dep'],
            "kd_bagian"        => $data['kd_bagian'],
            "nm_bagian"        => $data['nm_bagian'],
            "jns_pelunasan"    => $data['jns_pelunasan'],
            // "kd_pinjaman"      => $data['kd_pinjaman'],
            // "nm_pinjaman"      => $data['nm_pinjaman'],
            "tempo_bln"        => $data['tempo_bln'],
            "blth_angsuran"    => $data['blth_angsuran'],
            // "angsuran"        => $angsuran,
            // "sisa_bln"        => $sisa_bln,
            // "jml_angsuran"    => $jml_angsuran,
            // "jml_pokok"       => $jml_pokok,
            // "jml_pokok_pdk"   => $jml_pokok_pdk,
            // "jml_pokok_pjg"   => $jml_pokok_pjg,
            // "jml_bunga"       => $jml_bunga,
            // "jml_pot_bunga"   => $jml_pot_bunga,
            // "persen_denda"    => $persen_denda,
            // "jml_denda"       => $jml_denda,
            // "persen_asuransi" => $persen_asuransi,
            // "jml_asuransi"    => $jml_asuransi,
            "jml_bayar"        => hapus_koma($data['jml_bayar']),
            // "jml_dibayar"     => $jml_dibayar,
            "is_proses_plafon" => "1",
            "ket"              => $data['ket'],
            // "rilis"           => $rilis,
            // "tgl_update"      => $tgl_update,
        );

        if ($data['is_sparepart'] == 1) {
            $set_data["angsur_bln"]                = $data['angsur_bln'];
            $set_data["angsuran"]                  = $data['angsuran'];
            $set_data["bunga"]                     = $data['bunga'];
            $set_data["bunga_bln"]                 = $data['bunga_bln'];
            $set_data["sisa_bln"]                  = $data['sisa_bln'];
            $set_data["jml_pokok"]                 = $data['jml_pokok'];
            $set_data["jml_admin"]                 = $data['jml_admin'];
            $set_data["jml_bunga"]                 = $data['jml_bunga'];
            $set_data["jml_bunga_bln"]             = $data['jml_bunga_bln'];
            $set_data["jml_sisa_angsuran_lama"]    = $data['jml_sisa_angsuran_lama'];
            $set_data["jml_sisa_angsuran_baru"]    = $data['jml_sisa_angsuran_baru'];
            $set_data["jml_selisih_sisa_angsuran"] = $data['jml_selisih_sisa_angsuran'];
            $set_data['is_sparepart']              = "1";

            if ($data['sisa_bln'] < $data['tempo_bln']) {
                $set_data["jml_pokok_bln"]  = $data['jml_pokok_bln'];
                $set_data["jml_sisa_pokok"] = $data['jml_sisa_pokok'];
            } else {
                $set_data['jml_bunga_harian'] = $data['jml_bunga_harian'];
                $set_data['jml_hari']         = $data['jml_hari'];
            }
        } else {
            $set_data["angsur_bln"]        = $data['angsur_bln'];
            $set_data["sisa_bln"]          = $data['sisa_bln'];
            $set_data["jml_pokok"]         = $data['jml_pokok'];
            $set_data["angsuran"]          = $data['angsuran'];
            $set_data["jml_sisa_angsuran"] = $data['jml_sisa_angsuran'];
        }

        $this->db->set($set_data)->where("bukti_lunas", $bukti_lunas)->update("t_pelunasan");

        $no_ref_bukti_potga = $data['no_trans'];

        if ($data['jns_pelunasan'] == 'BP') {
            $set_data_pelunasan = array(
                "is_entri_lunas" => "1",
                "sts_bayar"      => "1",
                "blth_bayar"     => substr($data['tgl_lunas'], 0, 7),
            );

            $this->db->set($set_data_pelunasan)
                ->where("no_trans", $data['no_trans'])
                ->update("t_bridging_plafon");

            $set_data_pelunasan = array(
                "sts_lunas"       => "1",
                "blth_bayar"      => $data['blth_angsuran'],
                "bukti_pelunasan" => $bukti_lunas,
            );

            $this->db->set($set_data_pelunasan)
                ->where("no_trans", $data['no_trans'])
                ->where("blth_angsuran >=", $data['blth_angsuran'])
                ->update("t_bridging_plafon_det");

        } else if ($data['jns_pelunasan'] == "TOKO_KR_ANG") {
            $set_data_pelunasan = array(
                "is_entri_lunas" => "1",
                "is_lunas"       => "1",
                "nobukti_lunas"  => $bukti_lunas,
                "tgl_lunas"      => $data['tgl_lunas'],
            );

            $this->db->set($set_data_pelunasan)
                ->where("ref_bukti_bo", $data['no_trans'])
                ->where("kd_toko", $data['kd_toko'])
                ->update("db_wecode_smart.t_kredit_anggota");

            $this->db->set($set_data_pelunasan)
                ->where("ref_bukti_bo", $data['no_trans'])
                ->where("kd_toko", $data['kd_toko'])
                ->update("db_bengkel.t_kredit_anggota");

            $this->db->set($set_data_pelunasan)
                ->where("ref_bukti_bo", $data['no_trans'])
                ->where("kd_toko", $data['kd_toko'])
                ->update("db_pbb.t_kredit_anggota");

            $set_data_pelunasan = array(
                "sts_lunas"       => "1",
                "blth_bayar"      => $data['blth_angsuran'],
                "bukti_pelunasan" => $bukti_lunas,
            );

            $this->db->set($set_data_pelunasan)
                ->where("no_pinjam", $data['no_trans'])
                ->where("flokasi", $data['kd_toko'])
                ->where("CONCAT(tahun_angsuran, '-', lpad(bulan_angsuran, 2, '0')) >=", $data['blth_angsuran'])
                ->update("db_wecode_smart.t_kredit_anggota_det");

            $this->db->set($set_data_pelunasan)
                ->where("no_pinjam", $data['no_trans'])
                ->where("flokasi", $data['kd_toko'])
                ->where("CONCAT(tahun_angsuran, '-', lpad(bulan_angsuran, 2, '0')) >=", $data['blth_angsuran'])
                ->update("db_bengkel.t_kredit_anggota_det");

            $this->db->set($set_data_pelunasan)
                ->where("no_pinjam", $data['no_trans'])
                ->where("flokasi", $data['kd_toko'])
                ->where("CONCAT(tahun_angsuran, '-', lpad(bulan_angsuran, 2, '0')) >=", $data['blth_angsuran'])
                ->update("db_pbb.t_kredit_anggota_det");

            $no_ref_bukti_potga .= $data['kd_toko'];

        } else if ($data['jns_pelunasan'] == "TOKO_PIUTANG") {
            $set_data_pelunasan = array(
                "is_entri_lunas" => "1",
                "is_lunas"       => "1",
                "nobukti_lunas"  => $bukti_lunas,
                "tgl_lunas"      => $data['tgl_lunas'],
            );

            $this->db->set($set_data_pelunasan)
                ->where("ref_penjualan", $data['no_trans'])
                ->where("toko_kode", $data['kd_toko'])
                ->update("db_wecode_smart.piutang");

            $this->db->set($set_data_pelunasan)
                ->where("ref_penjualan", $data['no_trans'])
                ->where("toko_kode", $data['kd_toko'])
                ->update("db_bengkel.piutang");

            $this->db->set($set_data_pelunasan)
                ->where("ref_penjualan", $data['no_trans'])
                ->where("toko_kode", $data['kd_toko'])
                ->update("db_pbb.piutang");

            $no_ref_bukti_potga .= $data['kd_toko'];
        }

        $this->db->like("tgl_potga", $data['blth_angsuran'], "after")->where("no_ref_bukti", $no_ref_bukti_potga)->delete("t_potga");

        if ($data['sts_lunas'] == "0") {
            $set_debet_plafon = array(
                "no_ang"      => $data['no_ang'],
                "no_peg"      => $data['no_peg'],
                "nm_ang"      => $data['nm_ang'],
                // "kd_prsh"     => $data['kd_prsh'],
                // "nm_prsh"     => $data['nm_prsh'],
                // "kd_dep"      => $data['kd_dep'],
                // "nm_dep"      => $data['nm_dep'],
                // "kd_bagian"   => $data['kd_bagian'],
                // "nm_bagian"   => $data['nm_bagian'],
                "jenis_debet" => $data['jns_pelunasan'],
                "noref_penj"  => $bukti_lunas,
                "tgl_penj"    => substr($data['tgl_lunas'], 0, 7),
                "jml_debet"   => (0 - $data['angsuran']),
                // "status"      => $status,
            );

            $this->db->set($set_debet_plafon)->insert("t_plafon_debet");

            $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

            $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data['no_ang'])->update("t_anggota");
        }

        return true;
    }

    public function hapus_pelunasan_kredit_non_pinjaman($data)
    {
        if ($data['jns_pelunasan'] == 'BP') {
            $set_data_pelunasan = array(
                "is_entri_lunas" => "0",
                "sts_bayar"      => "0",
                "blth_bayar"     => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("no_trans", $data['no_ref_bukti'])
                ->update("t_bridging_plafon");

            $set_data_pelunasan = array(
                "sts_lunas"       => "0",
                "blth_bayar"      => null,
                "bukti_pelunasan" => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("no_trans", $data['no_ref_bukti'])
                ->where("bukti_pelunasan", $data['bukti_lunas'])
                ->update("t_bridging_plafon_det");

        } else if ($data['jns_pelunasan'] == "TOKO_KR_ANG") {
            $set_data_pelunasan = array(
                "is_entri_lunas" => "0",
                "is_lunas"       => "0",
                "nobukti_lunas"  => null,
                "tgl_lunas"      => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("ref_bukti_bo", $data['no_ref_bukti'])
                ->where("kd_toko", $data['kd_toko'])
                ->update("db_wecode_smart.t_kredit_anggota");

            $this->db->set($set_data_pelunasan)
                ->where("ref_bukti_bo", $data['no_ref_bukti'])
                ->where("kd_toko", $data['kd_toko'])
                ->update("db_bengkel.t_kredit_anggota");

            $this->db->set($set_data_pelunasan)
                ->where("ref_bukti_bo", $data['no_ref_bukti'])
                ->where("kd_toko", $data['kd_toko'])
                ->update("db_pbb.t_kredit_anggota");

            $set_data_pelunasan = array(
                "sts_lunas"       => "0",
                "blth_bayar"      => null,
                "bukti_pelunasan" => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("no_pinjam", $data['no_ref_bukti'])
                ->where("flokasi", $data['kd_toko'])
                ->where("bukti_pelunasan", $data['bukti_lunas'])
                ->update("db_wecode_smart.t_kredit_anggota_det");

            $this->db->set($set_data_pelunasan)
                ->where("no_pinjam", $data['no_ref_bukti'])
                ->where("flokasi", $data['kd_toko'])
                ->where("bukti_pelunasan", $data['bukti_lunas'])
                ->update("db_bengkel.t_kredit_anggota_det");

            $this->db->set($set_data_pelunasan)
                ->where("no_pinjam", $data['no_ref_bukti'])
                ->where("flokasi", $data['kd_toko'])
                ->where("bukti_pelunasan", $data['bukti_lunas'])
                ->update("db_pbb.t_kredit_anggota_det");

        } else if ($data['jns_pelunasan'] == "TOKO_PIUTANG") {
            $set_data_pelunasan = array(
                "is_entri_lunas" => "0",
                "is_lunas"       => "0",
                "nobukti_lunas"  => null,
                "tgl_lunas"      => null,
            );

            $this->db->set($set_data_pelunasan)
                ->where("ref_penjualan", $data['no_ref_bukti'])
                ->where("toko_kode", $data['kd_toko'])
                ->update("db_wecode_smart.piutang");

            $this->db->set($set_data_pelunasan)
                ->where("ref_penjualan", $data['no_ref_bukti'])
                ->where("toko_kode", $data['kd_toko'])
                ->update("db_bengkel.piutang");

            $this->db->set($set_data_pelunasan)
                ->where("ref_penjualan", $data['no_ref_bukti'])
                ->where("toko_kode", $data['kd_toko'])
                ->update("db_pbb.piutang");
        }

        $this->db->where("bukti_lunas", $data['bukti_lunas'])->delete("t_pelunasan");

        if ($data['is_proses_plafon'] == "1") {
            $this->db->where("noref_penj", $data['bukti_lunas'])->delete("t_plafon_debet");

            $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

            $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data['no_ang'])->update("t_anggota");
        }

        return true;
    }

    public function perhitungan_pelunasan($data)
    {
        $sudah_diangsur = $data['tempo_bln'] - $data['sisa_bln'];
        $jml_pokok_bln  = ($data['jml_pokok'] + $data['jml_admin']) / $data['tempo_bln'];
        $bunga_bln      = $data['bunga'] / 12;
        $jml_bunga_bln  = $data['jml_bunga'] / $data['tempo_bln'];
        $sisa_pokok     = $data['sisa_bln'] * round($jml_pokok_bln, 2);
        $sisa_angs      = $data['sisa_bln'] * $data['angsuran'];
        $sisa_angs_lama = $data['tempo_bln'] * $data['angsuran'];
        $jml_bayar      = $sisa_angs;
        // $sisa_angs_baru_hitung = pow(((100 + $bunga_bln) / 100), $sudah_diangsur) * round($sisa_pokok, 2);
        // $sisa_angs_baru        = $sisa_angs_baru_hitung + ($sudah_diangsur * $data['angsuran']);
        // $selisih_sisa_angs     = $sisa_angs_lama - $sisa_angs_baru;

        $json['ada_perhitungan']     = 0;
        $json['perhitungan_selisih'] = 0;

        if ($data['kd_pinjaman'] == "1" or $data['is_sparepart'] == "1") {
            if ($data['kd_pinjaman'] == "1") {
                $json['ada_perhitungan'] = 1;

                $cari['value'] = $data['no_pinjam'];
                $cari['field'] = array("no_pinjam");

                $data_pinjaman = $this->pinjaman_model->get_pinjaman(0, $cari)->row_array(0);

                $sudah_diangsur        = $data_pinjaman['tempo_bln'] - $data['sisa_bln'];
                $jml_pokok_bln         = ($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) / $data_pinjaman['tempo_bln'];
                $bunga_bln             = $data['bunga'] / 12;
                $jml_bunga_bln         = $data_pinjaman['jml_margin'] / $data_pinjaman['tempo_bln'];
                $sisa_pokok            = $data['sisa_bln'] * round($jml_pokok_bln, 2);
                $sisa_angs             = round($data['sisa_bln'] * $data['angsuran']); /*dibulatkan*/
                $sisa_angs_lama        = round($data['tempo_bln'] * $data_pinjaman['angsuran']); /*dibulatkan*/
                $sisa_angs_baru_hitung = round(pow(((100 + $bunga_bln) / 100), $sudah_diangsur) * $sisa_pokok); /*dibulatkan*/
                $sisa_angs_baru        = round($sisa_angs_baru_hitung + ($sudah_diangsur * $data_pinjaman['angsuran'])); /*dibulatkan*/
                $selisih_sisa_angs     = $sisa_angs_lama - $sisa_angs_baru;

                if ($data['sisa_bln'] < $data['tempo_bln']) {
                    $jml_bayar = $sisa_angs_baru_hitung;
                } else {
                    $jml_hari = $this->pinjaman_model->jumlah_hari($data_pinjaman['tgl_pinjam'], $data['tgl_lunas']);

                    $jml_bunga_harian = ($data_pinjaman['jml_pinjam'] * ($data_pinjaman['margin'] / 100) * $jml_hari) / 360;

                    $sisa_angs_baru = round($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin'] + $jml_bunga_harian); /*dibulatkan*/

                    $selisih_sisa_angs = $sisa_angs_lama - $sisa_angs_baru;

                    $jml_bayar = $sisa_angs_baru;

                    $json['jml_hari']         = $jml_hari;
                    $json['jml_bunga_harian'] = $jml_bunga_harian;
                }
            } else if ($data['is_sparepart'] == "1") {
                $json['ada_perhitungan'] = 1;

                $sudah_diangsur        = $data['tempo_bln'] - $data['sisa_bln'];
                $jml_pokok_bln         = ($data['jml_pokok'] + $data['jml_admin']) / $data['tempo_bln'];
                $bunga_bln             = $data['bunga'] / 12;
                $jml_bunga_bln         = $data['jml_bunga'] / $data['tempo_bln'];
                $sisa_pokok            = $data['sisa_bln'] * round($jml_pokok_bln, 2);
                $sisa_angs             = round($data['sisa_bln'] * $data['angsuran']); /*dibulatkan*/
                $sisa_angs_lama        = round($data['tempo_bln'] * $data['angsuran']); /*dibulatkan*/
                $sisa_angs_baru_hitung = round(pow(((100 + $bunga_bln) / 100), $sudah_diangsur) * round($sisa_pokok, 2)); /*dibulatkan*/
                $sisa_angs_baru        = round($sisa_angs_baru_hitung + ($sudah_diangsur * $data['angsuran'])); /*dibulatkan*/
                $selisih_sisa_angs     = $sisa_angs_lama - $sisa_angs_baru;

                if ($data['sisa_bln'] < $data['tempo_bln']) {
                    $jml_bayar = $sisa_angs_baru_hitung;
                } else {
                    $jml_hari = $this->pinjaman_model->jumlah_hari($data['tgl_ref_bukti'], $data['tgl_lunas']);

                    $jml_bunga_harian = ($data['jml_pokok'] * ($data['bunga'] / 100) * $jml_hari) / 360;

                    $sisa_angs_baru = round($data['jml_pokok'] + $data['jml_admin'] + $jml_bunga_harian); /*dibulatkan*/

                    $selisih_sisa_angs = $sisa_angs_lama - $sisa_angs_baru;

                    $jml_bayar = $sisa_angs_baru;

                    $json['jml_hari']         = $jml_hari;
                    $json['jml_bunga_harian'] = $jml_bunga_harian;
                }
            }

            $json['perhitungan_lama']    = $sisa_angs_lama;
            $json['perhitungan_baru']    = $sisa_angs_baru;
            $json['perhitungan_selisih'] = $selisih_sisa_angs;

            if ($selisih_sisa_angs < 0) {
                $jml_bayar = $sisa_angs;
            }
        }

        $json['sudah_diangsur'] = $sudah_diangsur;
        $json['jml_pokok_bln']  = $jml_pokok_bln;
        $json['bunga_bln']      = $bunga_bln;
        $json['jml_bunga_bln']  = $jml_bunga_bln;
        $json['sisa_pokok']     = $sisa_pokok;
        $json['sisa_angs_lama'] = $sisa_angs;
        $json['sisa_angs_baru'] = $jml_bayar;
        $json['jml_bayar']      = $jml_bayar;

        return $json;
    }

}
