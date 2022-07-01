<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Potga_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_potga($numrows = 0, $cari = "", $order = "", $offset = "0", $limit = "", $tahun = "", $bulan = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "(@rownum:=@rownum+1) nomor, bukti_potga, tgl_potga, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_potga, nm_potga, kd_piutang, no_ref_bukti, jml_pokok, angs_ke, tempo_bln, jml_wajib, jml_sukarela, angsuran, jml_min_angsuran, jml_max_angsuran, jumlah, ket, user_input, tgl_insert, user_edit, tgl_update";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang", "nm_ang", "ket");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        if ($tahun != "" and $bulan != "") {
            $this->db->like("tgl_potga", ($tahun . "-" . $bulan), "after");
        }

        $set_order = ($order) ? $order : "tgl_potga desc, kd_potga desc, bukti_potga desc";

        $this->db->order_by($set_order);

        if (!$offset) {
            $offset = 0;
        }

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $this->db->where("is_pensiun", "0");

        return $this->db->get("t_potga, (select @rownum:=" . $offset . ") as x");
    }

    public function get_bukti_potga($tgl_potga)
    {
        $strtime = strtotime($tgl_potga);
        $tahun   = date("Y", $strtime);
        $bulan   = date("m", $strtime);

        $nomor_baru = "PG" . $bulan . $tahun;

        $nomor = $this->db->select("ifnull(max(substr(bukti_potga, -5)), 0) + 1 nomor")->like("bukti_potga", $nomor_baru, "after")
            ->get("t_potga")->row(0)->nomor;

        $nomor_baru .= str_pad($nomor, "5", "0", STR_PAD_LEFT);

        if ($this->db->set("bukti_potga", $nomor_baru)->insert("t_potga")) {
            return $nomor_baru;
        } else {
            return $this->get_bukti_potga($tgl_potga);
        }
    }

    public function insert_data_potga($data)
    {
        $bukti_potga = $this->get_bukti_potga($data['tgl_potga']);

        $nm_potga = "";
        $ket      = "";

        if ($data['kd_potga'] == "11") {
            $nm_potga = "SIMPANAN POKOK";
            $ket      = "SIMPANAN POKOK";
        }

        $set_data = array(
            "bukti_potga" => $bukti_potga,
            "tgl_potga"   => $data['tgl_potga'],
            "no_ang"      => $data['no_ang'],
            "no_peg"      => $data['no_peg'],
            "nm_ang"      => $data['nm_ang'],
            "kd_prsh"     => $data['kd_prsh'],
            "nm_prsh"     => $data['nm_prsh'],
            "kd_dep"      => $data['kd_dep'],
            "nm_dep"      => $data['nm_dep'],
            "kd_bagian"   => $data['kd_bagian'],
            "nm_bagian"   => $data['nm_bagian'],
            "kd_potga"    => $data['kd_potga'],
            "nm_potga"    => $nm_potga,
            "is_pensiun"  => $data['is_pensiun'],
            "jumlah"      => hapus_koma($data['jumlah']),
            "ket"         => $ket,
        );

        $insert = $this->db->set($set_data)->replace("t_potga");

        return $insert;
    }

    public function get_potga_pensiun($numrows = 0, $cari = "", $order = "", $offset = "0", $limit = "", $tahun = "", $bulan = "", $no_ang = "", $status_bayar = "", $kd_potga = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "(@rownum:=@rownum+1) nomor, bukti_potga, tgl_potga, date_format(tgl_potga, '%Y-%m') blth_angsuran, tgl_rilis, date_format(tgl_rilis, '%Y-%m') blth_rilis, tgl_jt, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_potga, nm_potga, kd_piutang, kd_pinjaman, nm_pinjaman, no_ref_bukti, angs_ke, tempo_bln, jml_pokok, angsuran, jml_min_angsuran, jml_max_angsuran, jml_wajib, jml_sukarela, is_pot_bonus, is_pensiun, jumlah, ket, saldo_akhir, is_entri, is_bayar, bukti_bayar, tgl_bayar, mode_bayar, jml_bayar, user_input, tgl_insert, user_edit, tgl_update";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang", "nm_ang", "ket");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        if ($tahun != "" and $bulan != "") {
            $this->db->like("tgl_potga", ($tahun . "-" . $bulan), "after");
        }

        $set_order = ($order) ? $order : "tgl_potga desc, kd_potga desc, bukti_potga desc";

        $this->db->order_by($set_order);

        if (!$offset) {
            $offset = 0;
        }

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($no_ang != "") {
            $this->db->where("no_ang", $no_ang);
        }

        if ($kd_potga != "") {
            $this->db->where("kd_potga", $kd_potga);
        }

        if ($status_bayar == "BELUM") {
            $this->db->where("(bukti_bayar is null or bukti_bayar = '')");
        } else if ($status_bayar == "SUDAH") {
            $this->db->where("(bukti_bayar is not null)");
        }

        return $this->db->get("t_potga_pensiun, (select @rownum:=" . $offset . ") as x");
    }

    public function getBuktiPotgaPurnatugas($tahun, $bulan, $kode_bukti = "PP")
    {
        // $strtime = strtotime($tgl_potga);
        // $tahun   = date("Y", $strtime);
        // $bulan   = date("m", $strtime);

        $nomor_baru = $kode_bukti . $bulan . $tahun;

        $nomor = $this->db->select("ifnull(max(substr(bukti_potga, -5)), 0) + 1 nomor")->like("bukti_potga", $nomor_baru, "after")
            ->get("t_potga_pensiun")->row(0)->nomor;

        $nomor_baru .= str_pad($nomor, "5", "0", STR_PAD_LEFT);

        if ($this->db->set("bukti_potga", $nomor_baru)->insert("t_potga_pensiun")) {
            return $nomor_baru;
        } else {
            return $this->getBuktiPotgaPurnatugas($tahun, $bulan_angsuran, $kode_bukti);
        }
    }

    public function simpanTagihanSimpWajib($data)
    {
        $tgl_potga = $data['tahun_angsuran'] . "-" . $data['bulan_angsuran'] . "-01";
        $tgl_rilis = $data['tahun_tagihan'] . "-" . $data['bulan_tagihan'] . "-01";

        $bukti_potga = $this->getBuktiPotgaPurnatugas($data['tahun_angsuran'], $data['bulan_angsuran'], "WP");

        $set_data = array(
            // "bukti_potga" => $bukti_potga,
            "tgl_potga" => $tgl_potga,
            "tgl_rilis" => $tgl_rilis,
            "no_ang"    => $data['no_ang'],
            "no_peg"    => $data['no_peg'],
            "nm_ang"    => $data['nm_ang'],
            "kd_prsh"   => $data['kd_prsh'],
            "nm_prsh"   => $data['nm_prsh'],
            "kd_dep"    => $data['kd_dep'],
            "nm_dep"    => $data['nm_dep'],
            "kd_bagian" => $data['kd_bagian'],
            "nm_bagian" => $data['nm_bagian'],
            "kd_potga"  => '1',
            "nm_potga"  => 'SIMPANAN',
            "tempo_bln" => $data['tempo_bln'],
            "jml_wajib" => hapus_koma($data['jumlah']),
            "jumlah"    => hapus_koma($data['jumlah']),
            "is_entri"  => '1',
        );

        return $this->db->set($set_data)->where("bukti_potga", $bukti_potga)->update("t_potga_pensiun");
    }

    public function hapusTagihanSimpWajib($data)
    {
        return $this->db->where("bukti_potga", $data['bukti_potga'])->delete("t_potga_pensiun");
    }

    public function get_transaksi($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $no_ang = "", $tahun = "", $bulan = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "(@nomor:=@nomor+1) nomor, no_trans, tgl_trans, unit_adm, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_trans, nm_trans, kd_piutang, jml_awal_trans, jml_uang_muka, jml_trans, persen_biaya_admin, jml_biaya_admin, tempo_bln, margin, jml_margin, angsuran, tgl_angs, tgl_jt, ket, sts_bayar, blth_bayar, jml_bayar, user_input, tgl_insert, user_edit, tgl_update ";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_trans", "no_ang", "no_peg", "nm_ang", "ket");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "tgl_trans desc, no_trans desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($no_ang) {
            $this->db->where("no_ang", $no_ang);
        }

        if ($tahun and $bulan) {
            $this->db->like("tgl_trans", ($tahun . "-" . $bulan), "after");
        }

        $this->db->group_start();
        $this->db->where("unit_adm", "POTGA");
        $this->db->or_group_start();
        $this->db->where("unit_adm", "POTGA");
        $this->db->like("no_trans", 'x', 'after');
        $this->db->group_end();
        $this->db->group_end();

        $this->db->from("t_bridging_plafon, (select @nomor:=0) x");

        return $this->db->get();
    }

    public function get_margin_trans_berlaku($tempo_bln, $tgl_berlaku)
    {
        $margin = 0;

        if ($tempo_bln > 1) {
            $data_margin = $this->db->where("tempo_bln", $tempo_bln)
                ->where("tgl_berlaku <=", $tgl_berlaku)
                ->order_by("tgl_berlaku desc")
                ->limit("1")
                ->get("m_margin_bp");

            if ($data_margin->num_rows() > 0) {
                $margin = $data_margin->row(0)->margin;
            }
        }

        return $margin;
    }

    public function get_no_trans($tgl_trans, $kode_bukti = "BP")
    {
        $strtime = strtotime($tgl_trans);
        $tahun   = date("Y", $strtime);
        $bulan   = date("m", $strtime);

        $nomor_baru = $kode_bukti . $bulan . $tahun;

        $nomor = $this->db->select("ifnull(max(substr(no_trans, -5)), 0) + 1 nomor")->like("no_trans", $nomor_baru, "after")
            ->get("t_bridging_plafon")->row(0)->nomor;

        $nomor_baru .= str_pad($nomor, "5", "0", STR_PAD_LEFT);

        if ($this->db->set("no_trans", $nomor_baru)->insert("t_bridging_plafon")) {
            return $nomor_baru;
        } else {
            return $this->get_no_trans($tgl_trans);
        }
    }

    public function insert_bridging_plafon($data)
    {
        $no_trans = $this->get_no_trans($data['tgl_trans'], "BP");

        $strtime = strtotime($data['tgl_trans']);

        $xhari  = date("d", $strtime);
        $xbulan = date("m", $strtime);
        $xtahun = date("Y", $strtime);

        $tgl_angs = date("Y-m-d", mktime(0, 0, 0, $xbulan + 1, $xhari, $xtahun));
        $tgl_jt   = date("Y-m-d", mktime(0, 0, 0, $xbulan + $data['tempo_bln'], $xhari, $xtahun));

        $set_data = array(
            // "no_trans"           => $data['no_trans'],
            "tgl_trans"       => $data['tgl_trans'],
            "unit_adm"        => "POTGA",
            "no_ang"          => $data['no_ang'],
            "no_peg"          => $data['no_peg'],
            "nm_ang"          => $data['nm_ang'],
            "kd_prsh"         => $data['kd_prsh'],
            "nm_prsh"         => $data['nm_prsh'],
            "kd_dep"          => $data['kd_dep'],
            "nm_dep"          => $data['nm_dep'],
            "kd_bagian"       => $data['kd_bagian'],
            "nm_bagian"       => $data['nm_bagian'],
            "kd_piutang"      => $data['kd_piutang'],
            "jml_awal_trans"  => hapus_koma($data['jml_awal_trans']),
            // "jml_uang_muka"   => hapus_koma($data['jml_uang_muka']),
            "jml_trans"       => hapus_koma($data['jml_trans']),
            "tempo_bln"       => $data['tempo_bln'],
            "margin"          => $data['margin'],
            "jml_margin"      => $data['jml_margin'],
            "angsuran"        => hapus_koma($data['angsuran']),
            "jml_biaya_admin" => hapus_koma($data['jml_biaya_admin']),
            "tgl_angs"        => $tgl_angs,
            "tgl_jt"          => $tgl_jt,
            "ket"             => strtoupper($data['ket']),
            "user_input"      => $this->session->userdata("username"),
            "tgl_insert"      => date("Y-m-d H:i:s"),
        );

        $insert = $this->db->set($set_data)->where("no_trans", $no_trans)->update("t_bridging_plafon");

        /*update plafon*/
        // if (strtotime($data['tgl_trans']) >= strtotime('2018-10-01')) {
        $set_debet_plafon = array(
            "no_ang"      => $data['no_ang'],
            "no_peg"      => $data['no_peg'],
            "nm_ang"      => $data['nm_ang'],
            "kd_prsh"     => $data['kd_prsh'],
            "nm_prsh"     => $data['nm_prsh'],
            "kd_dep"      => $data['kd_dep'],
            "nm_dep"      => $data['nm_dep'],
            "kd_bagian"   => $data['kd_bagian'],
            "nm_bagian"   => $data['nm_bagian'],
            "jenis_debet" => "POTGA",
            "noref_penj"  => $no_trans,
            "tgl_penj"    => $data['tgl_trans'],
            "jml_debet"   => hapus_koma($data['angsuran']),
            // "status"      => $status,
        );

        $this->db->set($set_debet_plafon)->insert("t_plafon_debet");

        $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

        $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data['no_ang'])->update("t_anggota");

        $pokok_per_bulan = (hapus_koma($data['jml_trans']) + hapus_koma($data['jml_biaya_admin'])) / $data['tempo_bln'];

        $margin_per_bulan = hapus_koma($data['angsuran']) - $pokok_per_bulan;

        for ($i = 1; $i <= $data['tempo_bln']; $i++) {
            $xtgl_jt_det = mktime(0, 0, 0, $xbulan + 1, 1, $xtahun);
            $xtahun      = date("Y", $xtgl_jt_det);
            $xbulan      = date("m", $xtgl_jt_det);

            $set_data1 = array(
                "no_trans_det"   => $no_trans . str_pad($i, 2, "0", STR_PAD_LEFT),
                "no_trans"       => $no_trans,
                "tgl_trans"      => $data['tgl_trans'],
                "kd_piutang"     => $data['kd_piutang'],
                "blth_angsuran"  => ($xtahun . "-" . $xbulan),
                "bulan_angsuran" => $xbulan,
                "tahun_angsuran" => $xtahun,
                "angs_ke"        => $i,
                "tempo_bln"      => $data['tempo_bln'],
                // "pokok_awal"     => "pokok_awal",
                "pokok"          => $pokok_per_bulan,
                "bunga"          => $margin_per_bulan,
                "angsuran"       => hapus_koma($data['angsuran']),
                // "pokok_akhir"    => "pokok_akhir",
                // "sts_lunas"=>"sts_lunas",
                // "sts_potga"=>"sts_potga",
                // "blth_bayar"=>"blth_bayar",
                // "bukti_pelunasan"=>"bukti_pelunasan",
                // "bukti_tagihan"=>"bukti_tagihan",
                // "tgl_update"=>"tgl_update"
            );

            $this->db->set($set_data1)->insert("t_bridging_plafon_det");
        }

        return $insert;
    }

    public function delete_bridging_plafon($data)
    {
        $this->db->where("no_trans", $data['no_trans'])->delete("t_bridging_plafon");
        $this->db->where("no_trans", $data['no_trans'])->delete("t_bridging_plafon_det");

        if (strtotime($data['tgl_trans']) >= strtotime('2018-10-01')) {
            $this->db->where("noref_penj", $data['no_trans'])->delete("t_plafon_debet");

            $jml_plafon_pakai = $this->db->select("ifnull(sum(jml_debet), 0) jml_debet_plafon")->where("no_ang", $data['no_ang'])->get("t_plafon_debet")->row(0)->jml_debet_plafon;

            $this->db->set(array("plafon_pakai" => $jml_plafon_pakai))->where("no_ang", $data['no_ang'])->update("t_anggota");
        }

        return true;
    }

    public function proses_simp_wajib_purnatugas_baru()
    {
        $this->load->model("master_model");
        $this->load->model("anggota_model");

        $qTagihanSimpWajib = "SELECT a.*
            FROM t_potga_pensiun a
            JOIN (
                SELECT max(tgl_potga) max_tgl_potga, max(tgl_rilis), a.no_ang
                FROM t_potga_pensiun a
                group by no_ang
            ) b
            ON a.no_ang=b.no_ang and a.tgl_potga=b.max_tgl_potga
            WHERE b.max_tgl_potga < '" . date('Y-m-01') . "'";

        $dataTagihanSimpWajib = $this->db->query($qTagihanSimpWajib);

        if ($dataTagihanSimpWajib->num_rows() > 0) {
            $awalBulanIni = date("Y-m-01");

            foreach ($dataTagihanSimpWajib->result_array() as $key => $value) {
                $tglPotga = $value['tgl_potga'];
                $tglRilis = $value['tgl_rilis'];
                $tempoBln = $value['tempo_bln'];

                while (strtotime($awalBulanIni) > strtotime($tglPotga)) {
                    $xTglPotga  = explode("-", $tglPotga);
                    $bulanPotga = $xTglPotga[1];
                    $tahunPotga = $xTglPotga[0];

                    $xTglRilis  = explode("-", $tglRilis);
                    $bulanRilis = $xTglRilis[1];
                    $tahunRilis = $xTglRilis[0];

                    $mTglPotga      = mktime(0, 0, 0, $bulanPotga + $tempoBln, 1, $tahunPotga);
                    $bulanPotgaBaru = date('m', $mTglPotga);
                    $tahunPotgaBaru = date('Y', $mTglPotga);
                    $tglPotga       = date("Y-m-01", $mTglPotga);
                    // baca($tglPotga);

                    $mTglRilis = mktime(0, 0, 0, $bulanRilis + $tempoBln, 1, $tahunRilis);
                    $tglRilis  = date("Y-m-01", $mTglRilis);

                    $datamaster['tgl_berlaku'] = $tglRilis;
                    $dataJmlSimpWajib          = $this->master_model->get_pokok_wajib_berlaku($datamaster);

                    $jml_wajib = ($dataJmlSimpWajib->num_rows() > 0) ? $dataJmlSimpWajib->row(0)->jml_wajib : 0;

                    if ($jml_wajib == 0) {
                        $jml_wajib = 25000;
                    }

                    $total_wajib = $jml_wajib * $tempoBln;

                    $dataAnggota = $this->anggota_model->get_anggota(0, "", "", "", "", "", $value['no_ang']);
                    $adaAnggota  = $dataAnggota->num_rows();

                    $no_peg    = $adaAnggota > 0 ? $dataAnggota->row(0)->no_peg : "";
                    $nm_ang    = $adaAnggota > 0 ? $dataAnggota->row(0)->nm_ang : "";
                    $kd_prsh   = $adaAnggota > 0 ? $dataAnggota->row(0)->kd_prsh : "";
                    $nm_prsh   = $adaAnggota > 0 ? $dataAnggota->row(0)->nm_prsh : "";
                    $kd_dep    = $adaAnggota > 0 ? $dataAnggota->row(0)->kd_dep : "";
                    $nm_dep    = $adaAnggota > 0 ? $dataAnggota->row(0)->nm_dep : "";
                    $kd_bagian = $adaAnggota > 0 ? $dataAnggota->row(0)->kd_bagian : "";
                    $nm_bagian = $adaAnggota > 0 ? $dataAnggota->row(0)->nm_bagian : "";

                    $bukti_potga = $this->getBuktiPotgaPurnatugas($tahunPotgaBaru, $bulanPotgaBaru, "WP");

                    $set_data = array(
                        // "bukti_potga" => $bukti_potga,
                        "tgl_potga" => $tglPotga,
                        "tgl_rilis" => $tglRilis,
                        "no_ang"    => $value['no_ang'],
                        "no_peg"    => $no_peg,
                        "nm_ang"    => $nm_ang,
                        "kd_prsh"   => $kd_prsh,
                        "nm_prsh"   => $nm_prsh,
                        "kd_dep"    => $kd_dep,
                        "nm_dep"    => $nm_dep,
                        "kd_bagian" => $kd_bagian,
                        "nm_bagian" => $nm_bagian,
                        "kd_potga"  => '1',
                        "nm_potga"  => 'SIMPANAN',
                        "tempo_bln" => $tempoBln,
                        "jml_wajib" => hapus_koma($total_wajib),
                        "jumlah"    => hapus_koma($total_wajib),
                        "is_entri"  => '1',
                    );

                    // baca_array($set_data);

                    $this->db->set($set_data)->where("bukti_potga", $bukti_potga)->update("t_potga_pensiun");

                    // baca($tglRilis);

                }
            }
        }
    }

}
