<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Simpanan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model("master_model");
    }

    public function get_tempo_bln_simpanan()
    {
        $tempo_bln = array(
            // "1"  => "1",
            "3"  => "3",
            "6"  => "6",
            "12" => "12",
            // "48"  => "(4 Tahun) 48",
            // "60"  => "(5 Tahun) 60",
            // "72"  => "(6 Tahun) 72",
            // "84"  => "(7 Tahun) 84",
            // "96"  => "(8 Tahun) 96",
            // "108" => "(9 Tahun) 108",
            // "120" => "(10 Tahun) 120",
        );

        return $tempo_bln;
    }

    public function get_simpanan($numrows = 0, $cari = "", $order = "", $offset = "0", $limit = "", $bulan = "", $tahun = "", $no_ang = "", $kd_jns_simpanan = "", $kredit_debet = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "(@rownum:=@rownum+1) nomor, no_simpan, tgl_simpan tgl_simpan1, date_format(tgl_simpan, '%d-%m-%Y') tgl_simpan, waktu_simpan, unit_adm, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan, kd_jns_transaksi, nm_jns_transaksi, kredit_debet, jumlah, if(kredit_debet = 'K', jumlah, 0) kredit, if(kredit_debet = 'D', jumlah, 0) debet, tempo_bln, tgl_jt, margin, jml_margin, no_ref_bukti, is_cetak, baris_cetak, tgl_cetak, no_ref_cetak, is_margin_ss1, is_margin_ss2, is_margin_syariah, user_input, tgl_insert, user_edit, tgl_update";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_simpan", "no_ang", "no_peg", "nm_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "tgl_simpan1 desc, no_simpan desc";

        $this->db->order_by($set_order);

        if ($offset == "") {
            $offset = 0;
        }

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($bulan != "" and $tahun != "") {
            $this->db->where("month(tgl_simpan)", $bulan)->where("year(tgl_simpan)", $tahun);
        }

        if ($no_ang != "") {
            $this->db->where("no_ang", $no_ang);
        }

        if ($kd_jns_simpanan != "") {
            if (is_array($kd_jns_simpanan)) {
                $this->db->where_in("kd_jns_simpanan", $kd_jns_simpanan);
            } else {
                $this->db->where("kd_jns_simpanan", $kd_jns_simpanan);
            }
        }

        if ($kredit_debet != "") {
            $this->db->where("kredit_debet", $kredit_debet);
        }

        return $this->db->get("t_simpanan_ang, (select @rownum:=" . $offset . ", 1 tgl_simpan1) as t");
    }

    public function get_no_simpan($tgl_simpan, $kode_bukti = "SS")
    {
        $strtime = strtotime($tgl_simpan);
        $tahun   = date("Y", $strtime);
        $bulan   = date("m", $strtime);

        $nomor_baru = $kode_bukti . $bulan . $tahun;

        $nomor = $this->db->select("ifnull(max(substr(no_simpan, -5)), 0) + 1 nomor")->like("no_simpan", $nomor_baru, "after")
            ->get("t_simpanan_ang")->row(0)->nomor;

        $nomor_baru .= str_pad($nomor, "5", "0", STR_PAD_LEFT);

        if ($this->db->set("no_simpan", $nomor_baru)->insert("t_simpanan_ang")) {
            return $nomor_baru;
        } else {
            return $this->get_no_simpan($tgl_simpan);
        }
    }

    public function insert_simpanan($data, $no_simpan_edit = "", $mode_edit = "")
    {
        if ($mode_edit == "1") {
            $no_simpan = $no_simpan_edit;
        } else {
            $no_simpan = $this->get_no_simpan($data['tgl_simpan']);
        }

        $kd_jns_transaksi = (isset($data['kd_jns_transaksi'])) ? $data['kd_jns_transaksi'] : "";
        $nm_jns_transaksi = (isset($data['nm_jns_transaksi'])) ? $data['nm_jns_transaksi'] : "";

        $set_data = array(
            "no_simpan"        => $no_simpan,
            "tgl_simpan"       => $data['tgl_simpan'],
            "waktu_simpan"     => date("H:i:s"),
            "no_ang"           => strtoupper($data['no_ang']),
            "no_peg"           => $data['no_peg'],
            "nm_ang"           => $data['nm_ang'],
            "kd_prsh"          => $data['kd_prsh'],
            "nm_prsh"          => $data['nm_prsh'],
            "kd_dep"           => $data['kd_dep'],
            "nm_dep"           => $data['nm_dep'],
            "kd_bagian"        => $data['kd_bagian'],
            "nm_bagian"        => $data['nm_bagian'],
            "kd_jns_simpanan"  => $data['kd_jns_simpanan'],
            "nm_jns_simpanan"  => $data['nm_jns_simpanan'],
            "kd_jns_transaksi" => $kd_jns_transaksi,
            "nm_jns_transaksi" => $nm_jns_transaksi,
            "kredit_debet"     => $data['kredit_debet'],
            "jumlah"           => hapus_koma($data['jumlah']),
            // "user_input"   => $this->session->userdata("username"),
            // "tgl_insert"   => date("Y-m-d"),
            // "user_edit"     => $this->session->userdata("username"),
            // "tgl_update"    => $data['tgl_update'],
        );

        if ($this->cekDataSimpananCetakSetelahnya($data['tgl_simpan'], $data['no_ang']) > 0) {
            $set_data['is_cetak'] = "1";
        }

        if ($mode_edit == "1") {
            $cari['field'] = array("no_simpan");
            $cari['value'] = $no_simpan;

            $data_lama               = $this->get_simpanan(0, $cari)->row_array(0);
            $data_lama['tgl_simpan'] = balik_tanggal($data_lama['tgl_simpan']);

            $set_data["user_edit"]  = $this->session->userdata("username");
            $set_data["tgl_update"] = date("Y-m-d H:i:s");

            $insert = $this->db->set($set_data)->where("no_simpan", $no_simpan)->update("t_simpanan_ang");

            $this->update_saldo_simpanan($data_lama);
        } else {
            $set_data["user_input"] = $this->session->userdata("username");
            $set_data["tgl_insert"] = date("Y-m-d H:i:s");

            $insert = $this->db->set($set_data)->where("no_simpan", $no_simpan)->update("t_simpanan_ang");
        }

        $this->update_saldo_simpanan($set_data);

        $exTglSimpan = explode("-", $data['tgl_simpan']);

        $tahun = $exTglSimpan[0];

        $this->updateSaldoSimpAwalTahun($tahun);

        return $insert;
    }

    public function delete_simpanan($data)
    {
        $delete = $this->db->where("no_simpan", $data['no_simpan'])->delete("t_simpanan_ang");

        $this->update_saldo_simpanan($data);

        $exTglSimpan = explode("-", $data['tgl_simpan']);

        $tahun = $exTglSimpan[0];

        $this->updateSaldoSimpAwalTahun($tahun);

        return $delete;
    }

    public function update_saldo_simpanan($data)
    {
        $strtime = strtotime($data['tgl_simpan']);
        $tahun   = date("Y", $strtime);
        $bulan   = date("m", $strtime);

        /*delete record*/
        if (isset($data['no_ang']) and ($data['no_ang'] != "" and $data['no_ang'] != null)) {
            $this->db->where("no_ang", $data['no_ang']);
        }

        if (isset($data['kd_jns_simpanan']) and ($data['kd_jns_simpanan'] != "" and $data['kd_jns_simpanan'] != null)) {
            $this->db->where("kd_jns_simpanan", $data['kd_jns_simpanan']);
        }

        // if (isset($data['kd_jns_transaksi']) and ($data['kd_jns_transaksi'] != "" and $data['kd_jns_transaksi'] != null)) {
        //     $this->db->where("kd_jns_transaksi", $data['kd_jns_transaksi']);
        // }

        $this->db->where("tahun", $tahun)->where("bulan", $bulan)->delete("t_saldo_simpanan");

        /*query new record*/
        if (isset($data['no_ang']) and ($data['no_ang'] != "" and $data['no_ang'] != null)) {
            $this->db->where("no_ang", $data['no_ang']);
        }

        if (isset($data['kd_jns_simpanan']) and ($data['kd_jns_simpanan'] != "" and $data['kd_jns_simpanan'] != null)) {
            $this->db->where("kd_jns_simpanan", $data['kd_jns_simpanan']);
        }

        // if (isset($data['kd_jns_transaksi']) and ($data['kd_jns_transaksi'] != "" and $data['kd_jns_transaksi'] != null)) {
        //     $this->db->where("kd_jns_transaksi", $data['kd_jns_transaksi']);
        // }

        $this->db->group_by("no_ang, kd_jns_simpanan");

        $xquery_saldo = $this->db->select("'" . $tahun . "-" . $bulan . "', '" . $tahun . "', '" . $bulan . "', no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan, kd_jns_transaksi, nm_jns_transaksi, ifnull(sum(if(kredit_debet = 'K', jumlah, 0)), 0) kredit, ifnull(sum(if(kredit_debet = 'D', jumlah, 0)), 0) debet, ifnull(sum(if(kredit_debet = 'K', jumlah, 0)), 0) - ifnull(sum(if(kredit_debet = 'D', jumlah, 0)), 0) saldo_akhir")
            ->where("year(tgl_simpan)", $tahun)->where("month(tgl_simpan)", $bulan)
            ->get_compiled_select("t_simpanan_ang");

        $query_saldo = str_replace("`", "", $xquery_saldo);

        $query_insert = "
            insert into t_saldo_simpanan
            (blth, tahun, bulan, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan, kd_jns_transaksi, nm_jns_transaksi, kredit, debet, saldo_akhir)
            " . $query_saldo;

        $this->db->query($query_insert);

        // $this->db->where("kredit", 0)->where("debet", 0)->where("saldo_akhir", 0)->delete("t_saldo_simpanan");
    }

    public function cek_saldo_simpanan($data)
    {
        $strtime = strtotime($data['tgl_simpan']);
        $tahun   = date("Y", $strtime);
        $bulan   = date("m", $strtime);

        $saldo = $this->db->select("ifnull(sum(saldo_akhir), 0) saldo_akhir")->where("tahun", $tahun)->where("bulan between 00 and " . $bulan)->where("no_ang", $data['no_ang'])->where("kd_jns_simpanan", $data['kd_jns_simpanan'])
            ->get("t_saldo_simpanan")->row_array();

        return $saldo['saldo_akhir'];
    }

    public function get_saldo_simpanan($tahun, $bulan, $no_ang = "", $group_by = "", $kd_jns_simpanan = "")
    {
        $this->db->select("tahun, bulan, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan,
            ifnull(sum(if(bulan < '" . $bulan . "', saldo_akhir, 0)), 0) saldo_awal,
            ifnull(sum(if(bulan = '" . $bulan . "', kredit, 0)), 0) jml_kredit,
            ifnull(sum(if(bulan = '" . $bulan . "', debet, 0)), 0) jml_debet,
            ifnull(sum(saldo_akhir), 0) saldo_akhir")
            ->where("tahun", $tahun)->where("bulan between 00 and " . $bulan);

        if ($no_ang) {
            $this->db->where("no_ang", $no_ang);
        }

        if ($group_by == "" or $group_by == null) {
            $group_by = "kd_jns_simpanan";
        }

        $this->db->group_by($group_by);

        if ($kd_jns_simpanan != "") {
            $this->db->where("kd_jns_simpanan", $kd_jns_simpanan)->group_by("kd_jns_simpanan");
        }

        $this->db->having("saldo_awal != 0 or jml_kredit != 0 or jml_debet != 0 or saldo_akhir != 0");

        return $this->db->get("t_saldo_simpanan");
    }

    public function get_simpanan_sukarela2($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $no_ang = "", $status_debet = "", $tgl_skrg = "", $mode_dipercepat = "")
    {
        if ($tgl_skrg == "") {
            $tgl_skrg = date("Y-m-d");
        }

        $select = ($numrows) ? "count(*) numrows" : "no_simpan, tgl_simpan, waktu_simpan, no_ss2, unit_adm, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan, kd_jns_transaksi, nm_jns_transaksi, jml_simpanan, tempo_bln, margin, jml_margin, jml_margin_bln, tgl_jt, is_debet, tgl_debet, jml_debet, jml_denda, is_dipercepat, is_diperpanjang, no_bukti_baru, ket, user_input, tgl_insert, user_edit, tgl_update, timestampdiff(MONTH, tgl_simpan, '" . $tgl_skrg . "') umur_bulan, timestampdiff(DAY, tgl_simpan, '" . $tgl_skrg . "') umur_hari, if(tgl_simpan <= date(now()) and date(now()) >= tgl_jt, 1, 0) is_aktif";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_simpan", "no_ang", "no_peg", "nm_ang", "no_ss2");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "tgl_simpan desc, no_simpan desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($no_ang != "") {
            $this->db->where("no_ang", $no_ang);
        }

        if ($status_debet != "") {
            $this->db->where("sts_debet", $status_debet);
        }

        if ($mode_dipercepat == "1") {
            $this->db->where("tgl_jt >", $tgl_skrg)->where("tgl_simpan <=", $tgl_skrg);
        }

        $this->db->where("((is_debet = '0') or (is_debet = '1' and is_diperpanjang = '0') or (is_debet = '1' and is_dipercepat = '1'))");

        return $this->db->get("t_simpanan_sukarela2");
    }

    public function cek_pokok_simpanan_sukarela2($data)
    {
        $query = $this->db->select("ifnull(sum(jml_simpanan), 0) saldo_akhir")
            ->where("no_ang", $data['no_ang'])->where("is_debet", "0")
            ->get("t_simpanan_sukarela2")->row_array();

        return $query['saldo_akhir'];
    }

    public function get_no_simpan_ss2($tgl_simpan, $kode_bukti = "SB")
    {
        $strtime = strtotime($tgl_simpan);
        $tahun   = date("Y", $strtime);
        $bulan   = date("m", $strtime);

        $nomor_baru = $kode_bukti . $bulan . $tahun;

        $nomor = $this->db->select("ifnull(max(substr(no_simpan, -5)), 0) + 1 nomor")->like("no_simpan", $nomor_baru, "after")
            ->get("t_simpanan_sukarela2")->row(0)->nomor;

        $nomor_baru .= str_pad($nomor, "5", "0", STR_PAD_LEFT);

        if ($this->db->set("no_simpan", $nomor_baru)->insert("t_simpanan_sukarela2")) {
            return $nomor_baru;
        } else {
            return $this->get_no_simpan_ss2($tgl_simpan);
        }
    }

    public function insert_simpanan_sukarela2($data)
    {
        $ada_no_ss2 = $this->db->where("no_ss2", $data['no_ss2'])->where("is_debet", "0")->get("t_simpanan_sukarela2")->num_rows();

        if ($ada_no_ss2 > 0) {
            $hasil['status'] = false;
            $hasil['msg']    = "No. SS2 sudah ada dan masih aktif";

            echo json_encode($hasil);
            exit();
        }

        $no_simpan = $this->get_no_simpan_ss2($data['tgl_simpan']);

        $xtgl = strtotime($data['tgl_simpan']);

        $xhari  = date("d", $xtgl);
        $xbulan = date("m", $xtgl);
        $xtahun = date("Y", $xtgl);

        $tgl_jt = date("Y-m-d", mktime(0, 0, 0, $xbulan + $data['tempo_bln'], $xhari, $xtahun));

        $margin = $data['margin'];

        $jml_margin_setahun = hapus_koma($data['jml_simpanan']) * ($margin / 100);

        $jml_margin_bln = round($jml_margin_setahun / 12);
        $jml_margin     = $jml_margin_bln * $data['tempo_bln'];

        $set_data = array(
            // "no_simpan"       => $no_simpan,
            "tgl_simpan"      => $data['tgl_simpan'],
            "no_ss2"          => $data['no_ss2'],
            "no_ang"          => strtoupper($data['no_ang']),
            "no_peg"          => $data['no_peg'],
            "nm_ang"          => $data['nm_ang'],
            "kd_prsh"         => $data['kd_prsh'],
            "nm_prsh"         => $data['nm_prsh'],
            "kd_dep"          => $data['kd_dep'],
            "nm_dep"          => $data['nm_dep'],
            "kd_bagian"       => $data['kd_bagian'],
            "nm_bagian"       => $data['nm_bagian'],
            "kd_jns_simpanan" => $data['kd_jns_simpanan'],
            "nm_jns_simpanan" => $data['nm_jns_simpanan'],
            "jml_simpanan"    => hapus_koma($data['jml_simpanan']),
            "tempo_bln"       => $data['tempo_bln'],
            "tgl_jt"          => $tgl_jt,
            "margin"          => $margin,
            "jml_margin"      => $jml_margin,
            "jml_margin_bln"  => $jml_margin_bln,
            "ket"             => "BARU",
            "user_input"      => $this->session->userdata("username"),
            "tgl_insert"      => date("Y-m-d H:i:s"),
        );

        $insert = $this->db->set($set_data)->where("no_simpan", $no_simpan)->update("t_simpanan_sukarela2");

        for ($i = 1; $i <= $data['tempo_bln']; $i++) {
            $xtgl_jt_det = mktime(0, 0, 0, $xbulan + 1, 1, $xtahun);
            $xtahun      = date("Y", $xtgl_jt_det);
            $xbulan      = date("m", $xtgl_jt_det);
            // $xhari       = date("d", $xtgl_jt_det);
            $tgl_jt_det = $xtahun . "-" . $xbulan . "-" . $xhari;

            if (!checkdate($xbulan, $xhari, $xtahun)) {
                $tgl_jt_det = date("Y-m-t", mktime(0, 0, 0, $xbulan, 1, $xtahun));
                // $xhari      = date("t", mktime(0, 0, 0, $xbulan, 1, $xtahun));
            }

            // $xtgl_jt_det = mktime(0, 0, 0, $xbulan + 1, $xhari, $xtahun);
            // $xtahun      = date("Y", $xtgl_jt_det);
            // $xbulan      = date("m", $xtgl_jt_det);
            // $xhari       = date("d", $xtgl_jt_det);
            // $tgl_jt_det  = $xtahun . "-" . $xbulan . "-" . $xhari;

            $set_data1 = array(
                "no_simpan_det"  => $no_simpan . str_pad($i, 2, "0", STR_PAD_LEFT),
                "no_simpan"      => $no_simpan,
                "tgl_jt"         => $tgl_jt_det,
                "blth"           => ($xtahun . "-" . $xbulan),
                "tahun"          => $xtahun,
                "bulan"          => $xbulan,
                "hari"           => $xhari,
                "margin_ke"      => $i,
                "tempo_bln"      => $data['tempo_bln'],
                "jml_margin_bln" => $jml_margin_bln,
            );

            $this->db->set($set_data1)->insert("t_simpanan_sukarela2_det");
        }

        return $insert;
    }

    public function delete_simpanan_sukarela2($data)
    {
        $delete1 = $this->db->where("no_simpan", $data['no_simpan'])->delete("t_simpanan_sukarela2");
        $delete2 = $this->db->where("no_simpan", $data['no_simpan'])->delete("t_simpanan_sukarela2_det");

        if ($delete1 and $delete2) {
            return true;
        } else {
            return false;
        }
    }

    public function perpanjang_ss2($data)
    {
        $cari['value'] = $data['no_simpan'];
        $cari['field'] = array("no_simpan");

        $data_sebelumnya = $this->get_simpanan_sukarela2(0, $cari)->row_array();

        if ($data_sebelumnya['is_debet'] == "0") {
            $hasil['status'] = false;
            $hasil['msg']    = "Simpanan Masih Aktif";
            exit(json_encode($hasil));
        }

        $no_simpan_baru = $this->get_no_simpan_ss2(date("Y-m-d"));

        $set_data = array(
            "is_diperpanjang" => "1",
            "no_bukti_baru"   => $no_simpan_baru,
        );

        $this->db->set($set_data)->where("no_simpan", $data['no_simpan'])->update("t_simpanan_sukarela2");

        $this->load->model("anggota_model");

        $cari_anggota['value'] = $data['no_ang'];
        $cari_anggota['field'] = array("no_ang");

        $data_anggota = $this->anggota_model->get_anggota(0, $cari_anggota)->row_array();

        $str_tanggal = strtotime($data_sebelumnya['tgl_jt']);

        $xhari  = date("d", $str_tanggal);
        $xbulan = date("m", $str_tanggal);
        $xtahun = date("Y", $str_tanggal);

        $tgl_jt = date("Y-m-d", mktime(0, 0, 0, $xbulan + $data_sebelumnya['tempo_bln'], $xhari, $xtahun));

        $data_margin = $this->master_model->get_margin_simpanan_berlaku($data_sebelumnya['kd_jns_simpanan'], $data_sebelumnya['tempo_bln'], date("Y-m-d"));

        $margin = ($data_margin->num_rows() > 0) ? $data_margin->row(0)->rate : 0;

        $jml_margin_setahun = hapus_koma($data_sebelumnya['jml_simpanan']) * ($margin / 100);

        $jml_margin_bln = round($jml_margin_setahun / 12);
        $jml_margin     = $jml_margin_bln * $data_sebelumnya['tempo_bln'];

        $data_baru = array(
            "tgl_simpan"      => $data_sebelumnya['tgl_jt'],
            "no_ss2"          => $data_sebelumnya['no_ss2'],
            "no_ang"          => $data_sebelumnya['no_ang'],
            "no_peg"          => $data_sebelumnya['no_peg'],
            "nm_ang"          => $data_sebelumnya['nm_ang'],
            "kd_prsh"         => $data_anggota['kd_prsh'],
            "nm_prsh"         => $data_anggota['nm_prsh'],
            "kd_dep"          => $data_anggota['kd_dep'],
            "nm_dep"          => $data_anggota['nm_dep'],
            "kd_bagian"       => $data_anggota['kd_bagian'],
            "nm_bagian"       => $data_anggota['nm_bagian'],
            "kd_jns_simpanan" => $data_sebelumnya['kd_jns_simpanan'],
            "nm_jns_simpanan" => $data_sebelumnya['nm_jns_simpanan'],
            "jml_simpanan"    => $data_sebelumnya['jml_simpanan'],
            "tempo_bln"       => $data_sebelumnya['tempo_bln'],
            "margin"          => $margin,
            "jml_margin"      => $jml_margin,
            "jml_margin_bln"  => $jml_margin_bln,
            "tgl_jt"          => $tgl_jt,
            "user_input"      => $this->session->userdata("username"),
            "tgl_insert"      => date("Y-m-d H:i:s"),
        );

        $insert = $this->db->set($data_baru)->where("no_simpan", $no_simpan_baru)->update("t_simpanan_sukarela2");

        for ($i = 1; $i <= $data['tempo_bln']; $i++) {
            $xtgl_jt_det = mktime(0, 0, 0, $xbulan + 1, 1, $xtahun);
            $xtahun      = date("Y", $xtgl_jt_det);
            $xbulan      = date("m", $xtgl_jt_det);
            // $xhari       = date("d", $xtgl_jt_det);
            $tgl_jt_det = $xtahun . "-" . $xbulan . "-" . $xhari;

            if (!checkdate($xbulan, $xhari, $xtahun)) {
                $tgl_jt_det = date("Y-m-t", mktime(0, 0, 0, $xbulan, 1, $xtahun));
                // $xhari      = date("t", mktime(0, 0, 0, $xbulan, 1, $xtahun));
            }

            // $xtgl_jt_det = mktime(0, 0, 0, $xbulan + 1, $xhari, $xtahun);
            // $xtahun      = date("Y", $xtgl_jt_det);
            // $xbulan      = date("m", $xtgl_jt_det);
            // $xhari       = date("d", $xtgl_jt_det);
            // $tgl_jt_det  = $xtahun . "-" . $xbulan . "-" . $xhari;

            $set_data1 = array(
                "no_simpan_det"  => $no_simpan_baru . str_pad($i, 2, "0", STR_PAD_LEFT),
                "no_simpan"      => $no_simpan_baru,
                "tgl_jt"         => $tgl_jt_det,
                "blth"           => ($xtahun . "-" . $xbulan),
                "tahun"          => $xtahun,
                "bulan"          => $xbulan,
                "hari"           => $xhari,
                "margin_ke"      => $i,
                "tempo_bln"      => $data_sebelumnya['tempo_bln'],
                "jml_margin_bln" => $jml_margin_bln,
            );

            $this->db->set($set_data1)->insert("t_simpanan_sukarela2_det");
        }

        return $insert;
    }

    public function get_saldo_awal_cetak_buku_ss1($no_ang, $tahun, $bulan, $tgl_awal, $mode_cetak)
    {
        $bulan_tahun_lalu = date('Y-m', mktime(0, 0, 0, $bulan - 1, 1, $tahun));
        $blth_lalu        = ($bulan == "01") ? $tahun . "-00" : $bulan_tahun_lalu;

        $query_saldo_awal_bulan = $this->db->select("ifnull(sum(saldo_akhir), 0) sum_saldo_akhir")
            ->where("kd_jns_simpanan", "3000")->where("no_ang", $no_ang)->where("blth between '" . $tahun . "-00' and '" . $blth_lalu . "'")
            ->get("t_saldo_simpanan");

        $saldo_awal_bulan = $query_saldo_awal_bulan->row(0)->sum_saldo_akhir;

        if ($mode_cetak == "belumcetak") {
            $query_saldo_harian = $this->db->select("ifnull(sum(if(kredit_debet = 'K', jumlah, jumlah * -1)), 0) saldo_harian")
                ->where("kd_jns_simpanan", "3000")
                ->where("no_ang", $no_ang)
                ->like("tgl_simpan", ($tahun . "-" . $bulan), "after")
                ->where("tgl_simpan <=", ($tahun . "-" . $bulan . "-" . $tgl_awal))
                ->where("is_cetak", "1")
                ->get("t_simpanan_ang");
            // ->where("tgl_simpan between '" . $tahun . "-" . $bulan . "-01' and '" . $tahun . "-" . $bulan . "-" . $tgl_kemarin . "'")

            $saldo_awal_harian = $query_saldo_harian->row(0)->saldo_harian;

            $saldo_awal = $saldo_awal_bulan + $saldo_awal_harian;
        } else {
            if ($tgl_awal == "01" or $tgl_awal == "1" or $tgl_awal == "") {
                $saldo_awal_harian = 0;
            } else {
                $tgl_kemarin = date('d', mktime(0, 0, 0, $bulan, $tgl_awal - 1, $tahun));

                $query_saldo_harian = $this->db->select("ifnull(sum(if(kredit_debet = 'K', jumlah, jumlah * -1)), 0) saldo_harian")
                    ->where("kd_jns_simpanan", "3000")
                    ->where("no_ang", $no_ang)
                    ->like("tgl_simpan", ($tahun . "-" . $bulan), "after")
                    ->where("tgl_simpan <=", ($tahun . "-" . $bulan . "-" . $tgl_kemarin))
                    ->get("t_simpanan_ang");

                $saldo_awal_harian = $query_saldo_harian->row(0)->saldo_harian;
            }

            $saldo_awal = $saldo_awal_bulan + $saldo_awal_harian;
        }

        return $saldo_awal;
    }

    public function get_data_cetak_buku_ss1($no_ang = "", $tgl_awal = "", $tgl_akhir = "", $is_cetak = "")
    {
        $select = "no_simpan, tgl_simpan tgl_simpan1, date_format(tgl_simpan, '%d-%m-%Y') tgl_simpan, waktu_simpan, unit_adm, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan, kd_jns_transaksi, nm_jns_transaksi, kredit_debet, jumlah, if(kredit_debet = 'K', jumlah, 0) kredit, if(kredit_debet = 'D', jumlah, 0) debet, tempo_bln, tgl_jt, margin, jml_margin, no_ref_bukti, is_cetak, tgl_cetak, no_ref_cetak, is_margin_ss1, is_margin_ss2, is_margin_syariah, user_input, tgl_insert, user_edit, tgl_update";

        $this->db->select($select)->order_by("tgl_simpan1, no_simpan")->where("kd_jns_simpanan", "3000");

        if ($no_ang != "") {
            $this->db->where("no_ang", $no_ang);
        }

        if ($tgl_awal and $tgl_akhir) {
            $this->db->where("tgl_simpan between '" . $tgl_awal . "' and '" . $tgl_akhir . "'");
        }

        if ($is_cetak != "") {
            $this->db->where("is_cetak", $is_cetak);

            if ($is_cetak == "1") {
                $this->db->order_by("tgl_simpan1 desc, no_simpan desc")->limit("100");

                $sub_query = str_replace("`", "", $this->db->get_compiled_select());

                return $this->db->order_by("tgl_simpan1, no_simpan")->get("(" . $sub_query . ") a");
            } else {
                return $this->db->get("t_simpanan_ang");
            }
        } else {
            return $this->db->get("t_simpanan_ang");
        }
    }

    public function proses_penarikan_dipercepat_ss2($data)
    {
        $set_data_det = array(
            "is_debet"  => "1",
            "tgl_debet" => $data['tgl_debet'],
        );

        $this->db->set($set_data_det)->where("no_simpan", $data['no_simpan'])->where("is_debet", "0")->update("t_simpanan_sukarela2_det");

        $jumlah_debet = $this->db->select("ifnull(sum(jml_debet), 0) jumlah_debet")->where("no_simpan", $data['no_simpan'])->get("t_simpanan_sukarela2_det")->row(0)->jumlah_debet;

        $set_data_ss2 = array(
            "is_debet"      => "1",
            "tgl_debet"     => $data['tgl_debet'],
            "jml_debet"     => $jumlah_debet,
            "jml_denda"     => hapus_koma($data['jml_denda']),
            // "jenis_bayar_denda" => $data['jenis_bayar_denda'],
            "is_dipercepat" => "1",
            "ket"           => "DITARIK",
        );

        $this->db->set($set_data_ss2)->where("no_simpan", $data['no_simpan'])->update("t_simpanan_sukarela2");

        return true;
    }

    public function simpan_no_ss2($data)
    {
        $data_noss2 = $this->db->where("no_ss2", $data['no_ss2'])->where("is_debet", "0")->get("t_simpanan_sukarela2");

        if ($data_noss2->num_rows() > 0) {
            $hasil['status'] = false;
            $hasil['msg']    = "No. Sertifikat SS2 sudah terdaftar";

            exit(json_encode($hasil));
        }

        return $this->db->set("no_ss2", $data['no_ss2'])->where("no_simpan", $data['no_simpan'])->update("t_simpanan_sukarela2");
    }

    public function proses_margin($jenis_simpanan, $data)
    {
        set_time_limit(0);

        $tahun = $data['tahun'];
        $bulan = $data['bulan'];

        if ($jenis_simpanan == "SS2") {
            $data_detail_ss2 = $this->db->like("tgl_jt", ($tahun . "-" . $bulan), "after")
                ->where("is_debet", "0")->where("tgl_jt <=", date("Y-m-d"))
                ->get("t_simpanan_sukarela2_det");

            $data_total = $data_detail_ss2->num_rows();

            if ($data_total > 0) {
                $data_now = 1;
                $berhasil = 0;

                foreach ($data_detail_ss2->result_array() as $key => $value) {
                    $data_header_ss2 = $this->db->where("no_simpan", $value['no_simpan'])->get("t_simpanan_sukarela2")->row_array(0);

                    $no_simpan_ss1 = $this->get_no_simpan($value['tgl_jt'], "SS");

                    $set_data = array(
                        // "no_simpan"        => $no_simpan,
                        "tgl_simpan"       => $value['tgl_jt'],
                        "waktu_simpan"     => "00:00:00",
                        "no_ang"           => strtoupper($data_header_ss2['no_ang']),
                        "no_peg"           => $data_header_ss2['no_peg'],
                        "nm_ang"           => $this->db->escape_str($data_header_ss2['nm_ang']),
                        "kd_prsh"          => $data_header_ss2['kd_prsh'],
                        "nm_prsh"          => $data_header_ss2['nm_prsh'],
                        "kd_dep"           => $data_header_ss2['kd_dep'],
                        "nm_dep"           => $data_header_ss2['nm_dep'],
                        "kd_bagian"        => $data_header_ss2['kd_bagian'],
                        "nm_bagian"        => $data_header_ss2['nm_bagian'],
                        "kd_jns_simpanan"  => "3000",
                        "nm_jns_simpanan"  => "SIMPANAN SUKARELA 1",
                        "kd_jns_transaksi" => "06",
                        "nm_jns_transaksi" => "BUNGA SIMPANAN SUKARELA 2",
                        "kredit_debet"     => "K",
                        "jumlah"           => hapus_koma($value['jml_margin_bln']),
                    );

                    // if ($this->cekDataSimpananCetakSetelahnya($value['tgl_jt'], $data_header_ss2['no_ang']) > 0) {
                    //     $set_data['is_cetak'] = "1";
                    // }

                    $this->db->set($set_data)->where("no_simpan", $no_simpan_ss1)->update("t_simpanan_ang");

                    $set_data1 = array(
                        "is_debet"  => "1",
                        "tgl_debet" => $value['tgl_jt'],
                        "jml_debet" => $value['jml_margin_bln'],
                    );

                    $this->db->set($set_data1)->where("no_simpan_det", $value['no_simpan_det'])->update("t_simpanan_sukarela2_det");

                    $jml_margin_debet_det = $this->db->select("ifnull(sum(jml_debet), 0) sum_jml_debet")->where("is_debet", "1")->where("no_simpan", $value['no_simpan'])->get("t_simpanan_sukarela2_det")->row(0)->sum_jml_debet;

                    $set_data2 = array(
                        "jml_debet" => $jml_margin_debet_det,
                    );

                    if ($value['margin_ke'] == $value['tempo_bln']) {
                        $no_simpan_ss2_baru = $this->get_no_simpan_ss2($value['tgl_jt']);

                        $set_data2['is_debet']        = "1";
                        $set_data2['tgl_debet']       = $value['tgl_jt'];
                        $set_data2['is_diperpanjang'] = "1";
                        $set_data2['no_bukti_baru']   = $no_simpan_ss2_baru;

                        $strtime = strtotime($value['tgl_jt']);

                        $xhari  = date("d", $strtime);
                        $xbulan = date("m", $strtime);
                        $xtahun = date("Y", $strtime);

                        $tgl_jt = date("Y-m-d", mktime(0, 0, 0, $xbulan + $data_header_ss2['tempo_bln'], $xhari, $xtahun));

                        $margin = $data_header_ss2['margin'];

                        $jml_margin_setahun = hapus_koma($data_header_ss2['jml_simpanan']) * ($margin / 100);

                        $jml_margin_bln = round($jml_margin_setahun / 12);
                        $jml_margin     = $jml_margin_bln * $data_header_ss2['tempo_bln'];

                        $set_data_ss2_diperpanjang = array(
                            // "no_simpan"       => $no_simpan,
                            "tgl_simpan"      => $value['tgl_jt'],
                            "no_ss2"          => $data_header_ss2['no_ss2'],
                            "no_ang"          => strtoupper($data_header_ss2['no_ang']),
                            "no_peg"          => $data_header_ss2['no_peg'],
                            "nm_ang"          => $this->db->escape_str($data_header_ss2['nm_ang']),
                            "kd_prsh"         => $data_header_ss2['kd_prsh'],
                            "nm_prsh"         => $data_header_ss2['nm_prsh'],
                            "kd_dep"          => $data_header_ss2['kd_dep'],
                            "nm_dep"          => $data_header_ss2['nm_dep'],
                            "kd_bagian"       => $data_header_ss2['kd_bagian'],
                            "nm_bagian"       => $data_header_ss2['nm_bagian'],
                            "kd_jns_simpanan" => $data_header_ss2['kd_jns_simpanan'],
                            "nm_jns_simpanan" => $data_header_ss2['nm_jns_simpanan'],
                            "jml_simpanan"    => hapus_koma($data_header_ss2['jml_simpanan']),
                            "tempo_bln"       => $data_header_ss2['tempo_bln'],
                            "tgl_jt"          => $tgl_jt,
                            "margin"          => $margin,
                            "jml_margin"      => $jml_margin,
                            "jml_margin_bln"  => $jml_margin_bln,
                            "ket"             => "DIPERPANJANG",
                            "user_input"      => "PROSES",
                            "tgl_insert"      => date("Y-m-d H:i:s"),
                        );

                        $insert = $this->db->set($set_data_ss2_diperpanjang)->where("no_simpan", $no_simpan_ss2_baru)->update("t_simpanan_sukarela2");

                        for ($i = 1; $i <= $data_header_ss2['tempo_bln']; $i++) {
                            $xtgl_jt_det = mktime(0, 0, 0, $xbulan + 1, 1, $xtahun);
                            $xtahun      = date("Y", $xtgl_jt_det);
                            $xbulan      = date("m", $xtgl_jt_det);
                            // $xhari       = date("d", $xtgl_jt_det);
                            $tgl_jt_det = $xtahun . "-" . $xbulan . "-" . $xhari;

                            if (!checkdate($xbulan, $xhari, $xtahun)) {
                                $tgl_jt_det = date("Y-m-t", mktime(0, 0, 0, $xbulan, 1, $xtahun));
                                // $xhari      = date("t", mktime(0, 0, 0, $xbulan, 1, $xtahun));
                            }

                            $set_data_ss2_diperpanjang_det = array(
                                "no_simpan_det"  => $no_simpan_ss2_baru . str_pad($i, 2, "0", STR_PAD_LEFT),
                                "no_simpan"      => $no_simpan_ss2_baru,
                                "tgl_jt"         => $tgl_jt_det,
                                "blth"           => ($xtahun . "-" . $xbulan),
                                "tahun"          => $xtahun,
                                "bulan"          => $xbulan,
                                "hari"           => $xhari,
                                "margin_ke"      => $i,
                                "tempo_bln"      => $data_header_ss2['tempo_bln'],
                                "jml_margin_bln" => $jml_margin_bln,
                            );

                            $this->db->set($set_data_ss2_diperpanjang_det)->insert("t_simpanan_sukarela2_det");
                        }
                    }

                    $this->db->set($set_data2)->where("no_simpan", $value['no_simpan'])->update("t_simpanan_sukarela2");

                    $persen = round($data_now / $data_total * 100);

                    if (!is_cli()) {
                        $this->cache->file->save("margin_ss2_" . session_id(), $persen . ";" . $data_now . ";" . $data_total);

                        session_write_close();
                    } else {
                        baca("(" . $data_now . " / " . $data_total . ")");
                    }

                    $data_now++;
                }

                $this->setStatusTercetakOtomatis($data['tahun'], $data['bulan']);

                $this->update_saldo_simpanan_per_bulan($data);

                $this->updateSaldoSimpAwalTahun($data['tahun']);

                echo "Margin SS2 selesai diproses";
            } else {
                echo "Tidak ada data SS2";
            }
        }

        if ($jenis_simpanan == "SS1") {
            $this->db->where("kd_jns_transaksi", "05")
                ->where("kd_jns_simpanan", "3000")
                ->where("month(tgl_simpan)", $data['bulan'])
                ->where("year(tgl_simpan)", $data['tahun'])
            // ->where("no_ang", '4116')
                ->delete("t_simpanan_ang");

            $this->update_saldo_simpanan_per_bulan($data);

            $data_saldo = $this->get_saldo_simpanan($data['tahun'], $data['bulan'], "", "no_ang", "3000");

            $data_total = $data_saldo->num_rows();

            if ($data_total > 0) {
                $data_now = 1;
                $berhasil = 0;

                $tanggal_akhir  = date('t', mktime(0, 0, 0, $data['bulan'], 1, $data['tahun']));
                $tanggal_simpan = date('Y-m-t', mktime(0, 0, 0, $data['bulan'], 1, $data['tahun']));
                $dua_bulan_lalu = date('Y-m-01', mktime(0, 0, 0, $data['bulan'] - 2, 1, $data['tahun']));

                $data_margin = $this->master_model->get_margin_simpanan_berlaku("3000", "", ($data['tahun'] . "-" . $data['bulan'] . "-01"));

                $margin_per_tahun = ($data_margin->num_rows() > 0) ? $data_margin->row(0)->rate : 0;
                $margin_per_hari  = ($margin_per_tahun / 100) / 365;

                foreach ($data_saldo->result_array() as $key => $value) {
                    $data_anggota = $this->db->select("count(*) ada_anggota_aktif")
                        ->where("no_ang", $value['no_ang'])->where("status_keluar", "0")
                        ->get("t_nasabah");

                    $is_aktif = $data_anggota->row(0)->ada_anggota_aktif;

                    if ($is_aktif > 0) {
                        $array_kode_transaksi = array("01", "02", "03", "04", "06", "08", "09", "10");

                        $ada_transaksi_ss1 = 0;

                        $ada_transaksi_ss1 = $this->db->select("ifnull(count(*), 0) numrows")
                            ->where_in("kd_jns_transaksi", $array_kode_transaksi)
                            ->where("(tgl_simpan between '" . $dua_bulan_lalu . "' and '" . $tanggal_simpan . "')")
                            ->where("no_ang", $value['no_ang'])
                            ->get("t_simpanan_ang")->row(0)->numrows;

                        $is_nasabah_tk = 0;

                        if (strlen($value['no_ang']) == 5 and substr($value['no_ang'], 0, 1) == "2") {
                            /*jika nasabah TK maka mendapatkan bunga SS1*/
                            $is_nasabah_tk = 1;
                        }

                        // baca($value['saldo_akhir']);
                        // baca($ada_transaksi_ss1);
                        // baca($is_nasabah_tk);
                        // baca($dua_bulan_lalu);

                        // exit();

                        if (($value['saldo_akhir'] >= 5000000) or ($ada_transaksi_ss1 > 0) or ($is_nasabah_tk > 0)) {
                            $total_margin = 0;

                            $saldo_sukarela1 = round($value['saldo_awal'], 2);

                            $data_sukarela = $this->db->select("day(tgl_simpan) tanggal, tgl_simpan, no_ang, no_peg, round(ifnull(sum(if(kredit_debet = 'K', jumlah, 0 - jumlah)), 0), 2) jumlah_sukarela")
                                ->where("no_ang", $value['no_ang'])
                                ->where("month(tgl_simpan)", $data['bulan'])
                                ->where("year(tgl_simpan)", $data['tahun'])
                                ->where("kd_jns_transaksi !=", "05")
                                ->group_by("tanggal")
                                ->get("t_simpanan_ang");

                            $tanggal_awal = 1;

                            if ($data_sukarela->num_rows() > 0) {
                                foreach ($data_sukarela->result_array() as $key1 => $value1) {
                                    for ($i = $tanggal_awal; $i < $value1['tanggal']; $i++) {
                                        $jml_margin = ($saldo_sukarela1 * $margin_per_hari);

                                        $total_margin += $jml_margin;
                                    }

                                    $saldo_sukarela1 += $value1['jumlah_sukarela'];

                                    $jml_margin = ($saldo_sukarela1 * $margin_per_hari);

                                    $total_margin += $jml_margin;

                                    $tanggal_awal = $value1['tanggal'] + 1;

                                    // for ($i = $tanggal_awal; $i <= $value1['tanggal']; $i++) {
                                    //     $jml_margin = ($saldo_sukarela1 * $margin_per_hari);

                                    //     $total_margin += $jml_margin;

                                    //     $tanggal = str_pad($i, 2, "0", STR_PAD_LEFT);

                                    //     if ($tanggal == $value1['tanggal']) {
                                    //         $saldo_sukarela1 += $value1['jumlah_sukarela'];
                                    //     }
                                    // }

                                    // $tanggal_awal = $value1['tanggal'] + 1;
                                }
                            }

                            for ($i = $tanggal_awal; $i <= $tanggal_akhir; $i++) {
                                $jml_margin = ($saldo_sukarela1 * $margin_per_hari);

                                $total_margin += $jml_margin;
                            }

                            // baca($total_margin);

                            if ($total_margin > 0) {
                                $no_simpan = $this->get_no_simpan($tanggal_simpan);

                                $set_data = array(
                                    // "no_simpan"       => $no_simpan,
                                    "nm_ang"           => $this->db->escape_str($value['nm_ang']),
                                    "tgl_simpan"       => $tanggal_simpan,
                                    "no_ang"           => $value['no_ang'],
                                    "no_peg"           => $value['no_peg'],
                                    "kd_prsh"          => $value['kd_prsh'],
                                    "nm_prsh"          => $value['nm_prsh'],
                                    "kd_dep"           => $value['kd_dep'],
                                    "nm_dep"           => $value['nm_dep'],
                                    "kd_bagian"        => $value['kd_bagian'],
                                    "nm_bagian"        => $value['nm_bagian'],
                                    // "no_ref_bukti" => $value['no_simpan'],
                                    "kd_jns_simpanan"  => "3000",
                                    "nm_jns_simpanan"  => "SIMPANAN SUKARELA 1",
                                    "kd_jns_transaksi" => "05",
                                    "nm_jns_transaksi" => "BUNGA SIMPANAN SUKARELA 1",
                                    "kredit_debet"     => "K",
                                    "jumlah"           => round($total_margin, 2),
                                    "user_input"       => "PROSES",
                                    "tgl_insert"       => date("Y-m-d"),
                                );

                                // if ($this->cekDataSimpananCetakSetelahnya($tanggal_simpan, $value['no_ang']) > 0) {
                                //     $set_data['is_cetak'] = "1";
                                // }

                                // baca_array($set_data);exit();

                                $this->db->set($set_data)->where("no_simpan", $no_simpan)->update("t_simpanan_ang");
                            }
                        }
                    }

                    $persen = round($data_now / $data_total * 100);

                    if (!is_cli()) {
                        $this->cache->file->save("margin_ss1_" . session_id(), $persen . ";" . $data_now . ";" . $data_total);

                        session_write_close();
                    } else {
                        baca("(" . $data_now . " / " . $data_total . ")");
                    }

                    $data_now++;
                }

                $this->setStatusTercetakOtomatis($data['tahun'], $data['bulan']);

                $this->update_saldo_simpanan_per_bulan($data);

                $this->updateSaldoSimpAwalTahun($data['tahun']);

                echo "Margin SS1 selesai diproses";
            } else {
                echo "Tidak ada data SS1";
            }

        }

        // jangan dihapus

        // if ($jenis_simpanan == "syariah") {
        //     $this->db->where("is_margin_syariah", "1")
        //         ->where("month(tgl_simpan)", $data['bulan'])
        //         ->where("year(tgl_simpan)", $data['tahun'])
        //         ->delete("t_simpanan_ang");

        //     $tanggal_simpan       = balik_tanggal($data['tanggal_simpan']);
        //     $tanggal_akhir        = date('t', mktime(0, 0, 0, $data['bulan'], 1, $data['tahun']));
        //     $tanggal_akhir_margin = date('Y-m-t', mktime(0, 0, 0, $data['bulan'], 1, $data['tahun']));

        //     $data_saldo_all = $this->get_saldo_simpanan($data['tahun'], $data['bulan'], "", "", "5000")->result_array(0);

        //     $saldo_syariah_all = isset($data_saldo_all['saldo_awal']) ? $data_saldo_all['saldo_awal'] : 0;

        //     for ($i = 1; $i <= $tanggal_akhir; $i++) {
        //         $tanggal = ($i < 10) ? "0" . $i : $i;

        //         $data_syariah = $this->db->select("day(tgl_simpan) tanggal, tgl_simpan, no_peg, round(ifnull(sum(if(kredit_debet = 'K', jumlah, 0 - jumlah)), 0), 2) jumlah_syariah")
        //         // ->where("no_ang", $value['no_ang'])
        //             ->where("kd_jns_simpanan", "5000")
        //             ->where("is_margin_syariah", "0")
        //             ->where("day(tgl_simpan)", $tanggal)
        //             ->where("month(tgl_simpan)", $data['bulan'])
        //             ->where("year(tgl_simpan)", $data['tahun'])
        //             ->get("t_simpanan_ang")->row_array(0);

        //         $saldo_syariah_all += $data_syariah['jumlah_syariah'];
        //     }

        //     $saldo_syariah_all_rata2 = $saldo_syariah_all / $tanggal_akhir;

        //     $bagi_hasil = ($saldo_syariah_all_rata2 / $data['total_modal']) * $data['laba_kotor15'];

        //     $data_saldo = $this->get_saldo_simpanan($data['tahun'], $data['bulan'], "", "no_ang", "5000");

        //     if ($data_saldo->num_rows() > 0) {
        //         $data_total = $data_saldo->num_rows();
        //         $data_now   = 1;
        //         $berhasil   = 0;

        //         foreach ($data_saldo->result_array() as $key => $value) {
        //             $saldo_syariah = isset($value['saldo_awal']) ? $value['saldo_awal'] : 0;

        //             $total_margin = 0;

        //             for ($i = 1; $i <= $tanggal_akhir; $i++) {
        //                 $tanggal = ($i < 10) ? "0" . $i : $i;

        //                 $data_syariah = $this->db->select("day(tgl_simpan) tanggal, tgl_simpan, no_peg, round(ifnull(sum(if(kredit_debet = 'K', jumlah, 0 - jumlah)), 0), 2) jumlah_syariah")
        //                     ->where("no_ang", $value['no_ang'])
        //                     ->where("kd_jns_simpanan", "5000")
        //                     ->where("is_margin_syariah", "0")
        //                     ->where("day(tgl_simpan)", $tanggal)
        //                     ->where("month(tgl_simpan)", $data['bulan'])
        //                     ->where("year(tgl_simpan)", $data['tahun'])
        //                     ->get("t_simpanan_ang")->row_array(0);

        //                 $saldo_syariah += $data_syariah['jumlah_syariah'];
        //             }

        //             $saldo_syariah_rata2 = $saldo_syariah / $tanggal_akhir;

        //             $total_margin = ($saldo_syariah_rata2 / $saldo_syariah_all_rata2) * $bagi_hasil;

        //             $no_simpan = $this->get_no_simpan($tanggal_simpan);

        //             $set_data = array(
        //                 "no_simpan"         => $no_simpan,
        //                 "nm_ang"            => $this->db->escape_str($value['nm_ang']),
        //                 "tgl_simpan"        => $tanggal_simpan,
        //                 "no_ang"            => $value['no_ang'],
        //                 "no_peg"            => $value['no_peg'],
        //                 "kd_prsh"           => $value['kd_prsh'],
        //                 "nm_prsh"           => $value['nm_prsh'],
        //                 "kd_dep"            => $value['kd_dep'],
        //                 "nm_dep"            => $value['nm_dep'],
        //                 "kd_bagian"         => $value['kd_bagian'],
        //                 "nm_bagian"         => $value['nm_bagian'],
        //                 // "no_ref_bukti" => $value['no_simpan'],
        //                 "kd_jns_simpanan"   => "5000",
        //                 "nm_jns_simpanan"   => "SIMPANAN SYARIAH",
        //                 "is_margin_syariah" => "1",
        //                 "kredit_debet"      => "K",
        //                 "jumlah"            => $total_margin,
        //                 "user_input"        => $this->session->userdata("username"),
        //                 "tgl_insert"        => date("Y-m-d"),
        //             );

        //             $this->db->set($set_data)->where("no_simpan", $no_simpan)->update("t_simpanan_ang");
        //             $this->update_saldo_simpanan($set_data);

        //             $persen = round($data_now / $data_total * 100);

        //             $this->cache->file->save("margin_syariah_" . session_id(), $persen . ";" . $data_now . ";" . $data_total);

        //             $data_now++;

        //             session_write_close();
        //             echo "Semua simpanan sudah diproses";
        //         }
        //     } else {
        //         $this->cache->file->save("margin_syariah_" . session_id(), "100;0;0");

        //         session_write_close();

        //         echo "Semua simpanan sudah diproses";
        //     }
        // }
    }

    public function proses_insert_potga_ss1($data)
    {
        set_time_limit(0);

        $this->db->where("kd_jns_transaksi", "03")
            ->where("kd_jns_simpanan", "3000")
            ->where("month(tgl_simpan)", $data['bulan'])
            ->where("year(tgl_simpan)", $data['tahun'])
            ->delete("t_simpanan_ang");

        $tgl_masuk_ss1 = $data['tahun'] . "-" . $data['bulan'] . "-01";

        // $xsub_query = $this->db->where("tgl_masuk_ss1 <=", $tgl_masuk_ss1)
        //     ->order_by("no_ang, tgl_masuk_ss1 desc")
        //     ->get_compiled_select("m_potga_ss1");

        // $sub_query = str_replace("`", "", $xsub_query);

        $query_potga_ss1 = "SELECT a.*
            FROM m_potga_ss1 a
            JOIN (
                SELECT no_ang, max(tgl_masuk_ss1) max_tgl_masuk_ss1 FROM m_potga_ss1
                WHERE substr(tgl_masuk_ss1, 1, 7) <= '" . $data['tahun'] . "-" . $data['bulan'] . "'
                GROUP BY no_ang
            ) b
            on a.no_ang=b.no_ang AND a.tgl_masuk_ss1 = b.max_tgl_masuk_ss1
            where (
                (a.status_keluar = '0' and a.is_pensiun = '0')
                or ((substr(a.tgl_keluar, 1, 7) >= '" . $data['tahun'] . "-" . $data['bulan'] . "' and substr(a.tgl_keluar, 1, 7) < substr(a.tgl_pensiun, 1, 7)) or (substr(a.tgl_pensiun, 1, 7) >= '" . $data['tahun'] . "-" . $data['bulan'] . "'))
            )
            order by a.no_ang";

        $data_potga_ss1 = $this->db->query($query_potga_ss1);

        $data_total = $data_potga_ss1->num_rows();

        if ($data_total > 0) {
            $data_now = 1;

            foreach ($data_potga_ss1->result_array() as $key => $value) {
                $data_anggota = $this->db->select("count(*) ada_anggota_aktif")
                    ->where("no_ang", $value['no_ang'])->where("status_keluar", "0")
                    ->get("t_anggota");

                $is_aktif = $data_anggota->row(0)->ada_anggota_aktif;

                if ($is_aktif > 0) {
                    $no_simpan_ss1 = $this->get_no_simpan($tgl_masuk_ss1, "SS");

                    $set_data = array(
                        // "no_simpan"        => $no_simpan,
                        "tgl_simpan"       => $tgl_masuk_ss1,
                        "waktu_simpan"     => "00:00:00",
                        "no_ang"           => strtoupper($value['no_ang']),
                        "no_peg"           => $value['no_peg'],
                        "nm_ang"           => $this->db->escape_str($value['nm_ang']),
                        "kd_prsh"          => $value['kd_prsh'],
                        "nm_prsh"          => $value['nm_prsh'],
                        "kd_dep"           => $value['kd_dep'],
                        "nm_dep"           => $value['nm_dep'],
                        "kd_bagian"        => $value['kd_bagian'],
                        "nm_bagian"        => $value['nm_bagian'],
                        "kd_jns_simpanan"  => "3000",
                        "nm_jns_simpanan"  => "SIMPANAN SUKARELA 1",
                        "kd_jns_transaksi" => "03",
                        "nm_jns_transaksi" => "SETORAN POTONGAN GAJI",
                        "kredit_debet"     => "K",
                        "jumlah"           => hapus_koma($value['jumlah']),
                        "user_input"       => "PROSES",
                        "tgl_insert"       => date("Y-m-d"),
                    );

                    // if ($this->cekDataSimpananCetakSetelahnya($tgl_masuk_ss1, $value['no_ang']) > 0) {
                    //     $set_data['is_cetak'] = "1";
                    // }

                    $this->db->set($set_data)->where("no_simpan", $no_simpan_ss1)->update("t_simpanan_ang");
                }

                $persen = round($data_now / $data_total * 100);

                if (!is_cli()) {
                    $this->cache->file->save("proses_potga_ss1_" . session_id(), $persen . ";" . $data_now . ";" . $data_total);

                    session_write_close();
                } else {
                    baca("(" . $data_now . " / " . $data_total . ")");
                }

                $data_now++;
            }

            $this->setStatusTercetakOtomatis($data['tahun'], $data['bulan']);

            $this->update_saldo_simpanan_per_bulan($data);

            $this->updateSaldoSimpAwalTahun($data['tahun']);
        }

        echo "Potga SS1 selesai diproses";
    }

    public function proses_pajak_ss1($data)
    {
        set_time_limit(0);

        $this->db->where("kd_jns_transaksi", "10")
            ->where("kd_jns_simpanan", "3000")
            ->where("month(tgl_simpan)", $data['bulan'])
            ->where("year(tgl_simpan)", $data['tahun'])
            ->delete("t_simpanan_ang");

        $bulan_sblmnya = date("m", mktime(0, 0, 0, $data['bulan'] - 1, 1, $data['tahun']));
        $tahun_sblmnya = date("Y", mktime(0, 0, 0, $data['bulan'] - 1, 1, $data['tahun']));

        $data_bunga_bulan_sblmnya = $this->db->select("no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, sum(jumlah) jumlah_bunga")
            ->where_in("kd_jns_transaksi", array("05", "06"))
            ->where("year(tgl_simpan)", $tahun_sblmnya)->where("month(tgl_simpan)", $bulan_sblmnya)
            ->where("kd_jns_simpanan", "3000")
            ->group_by("no_ang")
            ->get("t_simpanan_ang");

        $data_total = $data_bunga_bulan_sblmnya->num_rows();
        $data_now   = 1;

        $tgl_pajak = date("Y-m-t", mktime(0, 0, 0, $data['bulan'], 1, $data['tahun']));

        if ($data_total > 0) {
            foreach ($data_bunga_bulan_sblmnya->result_array() as $key => $value) {
                if ($value['jumlah_bunga'] >= 240000) {
                    $no_simpan_ss1 = $this->get_no_simpan($tgl_pajak, "SS");

                    $jml_pajak = $value['jumlah_bunga'] * 0.1;

                    $set_data = array(
                        // "no_simpan"        => $no_simpan,
                        "tgl_simpan"       => $tgl_pajak,
                        "waktu_simpan"     => "00:00:00",
                        "no_ang"           => strtoupper($value['no_ang']),
                        "no_peg"           => $value['no_peg'],
                        "nm_ang"           => $this->db->escape_str($value['nm_ang']),
                        "kd_prsh"          => $value['kd_prsh'],
                        "nm_prsh"          => $value['nm_prsh'],
                        "kd_dep"           => $value['kd_dep'],
                        "nm_dep"           => $value['nm_dep'],
                        "kd_bagian"        => $value['kd_bagian'],
                        "nm_bagian"        => $value['nm_bagian'],
                        "kd_jns_simpanan"  => "3000",
                        "nm_jns_simpanan"  => "SIMPANAN SUKARELA 1",
                        "kd_jns_transaksi" => "10",
                        "nm_jns_transaksi" => "PAJAK",
                        "kredit_debet"     => "D",
                        "jumlah"           => hapus_koma($jml_pajak),
                    );

                    // if ($this->cekDataSimpananCetakSetelahnya($tgl_pajak, $value['no_ang']) > 0) {
                    //     $set_data['is_cetak'] = "1";
                    // }

                    $this->db->set($set_data)->where("no_simpan", $no_simpan_ss1)->update("t_simpanan_ang");
                }

                $persen = round($data_now / $data_total * 100);

                if (!is_cli()) {
                    $this->cache->file->save("proses_pajak_ss1_" . session_id(), $persen . ";" . $data_now . ";" . $data_total);

                    session_write_close();
                } else {
                    baca("(" . $data_now . " / " . $data_total . ")");
                }

                $data_now++;
            }

            $this->setStatusTercetakOtomatis($data['tahun'], $data['bulan']);

            $this->update_saldo_simpanan_per_bulan($data);

            $this->updateSaldoSimpAwalTahun($data['tahun']);
        }

        echo "Pajak SS1 selesai diproses";
    }

    public function update_saldo_simpanan_tahunan($tahun)
    {
        set_time_limit(0);

        $this->db->where("blth", ($tahun . "-00"))->delete("t_saldo_simpanan");

        $tahun_lalu = $tahun - 1;

        $query_insert = "
            insert into t_saldo_simpanan
            (blth, tahun, bulan, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan, kd_jns_transaksi, nm_jns_transaksi, kredit, debet, saldo_akhir)
            select '" . $tahun . "-00', '" . $tahun . "', '00', no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan, kd_jns_transaksi, nm_jns_transaksi,
                sum(kredit), sum(debet), sum(saldo_akhir)
            from t_saldo_simpanan
            where blth like '" . $tahun_lalu . "%'
            group by no_ang, kd_jns_simpanan
        ";

        $this->db->query($query_insert);

        $this->db->where("kredit", 0)->where("debet", 0)->where("saldo_akhir", 0)->delete("t_saldo_simpanan");

        echo "Saldo Awal Tahun SS1 telah diproses";
    }

    public function update_saldo_simpanan_per_bulan($data)
    {
        set_time_limit(0);

        $this->db->where("blth", ($data['tahun'] . "-" . $data['bulan']))->delete("t_saldo_simpanan");

        $xquery_saldo = $this->db->select("'" . $data['tahun'] . "-" . $data['bulan'] . "', '" . $data['tahun'] . "', '" . $data['bulan'] . "', no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan, kd_jns_transaksi, nm_jns_transaksi, ifnull(sum(if(kredit_debet = 'K', jumlah, 0)), 0) kredit, ifnull(sum(if(kredit_debet = 'D', jumlah, 0)), 0) debet, ifnull(sum(if(kredit_debet = 'K', jumlah, 0)), 0) - ifnull(sum(if(kredit_debet = 'D', jumlah, 0)), 0) saldo_akhir")
            ->where("year(tgl_simpan)", $data['tahun'])->where("month(tgl_simpan)", $data['bulan'])
            ->group_by("no_ang")
            ->get_compiled_select("t_simpanan_ang");

        $query_saldo = str_replace("`", "", $xquery_saldo);

        $query_insert = "
            insert into t_saldo_simpanan
            (blth, tahun, bulan, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_jns_simpanan, nm_jns_simpanan, kd_jns_transaksi, nm_jns_transaksi, kredit, debet, saldo_akhir)
            " . $query_saldo;

        $this->db->query($query_insert);

        $this->updateSaldoSimpAwalTahun($data['tahun']);

        $this->db->where("kredit", 0)->where("debet", 0)->where("saldo_akhir", 0)->delete("t_saldo_simpanan");
    }

    public function cekDataSimpananCetakSetelahnya($tanggal, $no_ang)
    {
        $adaCetak = 0;

        $adaCetak = $this->db->select("ifnull(count(*), 0) numrows")
            ->where("tgl_simpan >=", $tanggal)
            ->where("no_ang", $no_ang)
            ->where("is_cetak", "1")
            ->get("t_simpanan_ang")->row(0)->numrows;

        return $adaCetak;
    }

    public function setStatusTercetakOtomatis($tahun, $bulan)
    {
        /*SELECT no_ang FROM k3pg_sp.t_simpanan_ang x
        WHERE
        SUBSTRING(tgl_simpan, 1, 7) > '2018-10' and is_cetak = '1'
        group by no_ang*/

        /*SELECT * FROM k3pg_sp.t_simpanan_ang
        WHERE SUBSTRING(tgl_simpan, 1, 7) <= '2018-10' and is_cetak = '0'
        AND
        no_ang IN*/

        $queryNoang = $this->db->select("no_ang")
            ->where("substr(tgl_simpan, 1, 7) > '" . ($tahun . "-" . $bulan) . "'")
            ->where("is_cetak", "1")
            ->group_by("no_ang")
            ->get("t_simpanan_ang");

        $dataNoAng = array();

        foreach ($queryNoang->result_array() as $key => $value) {
            $dataNoAng[] = $value['no_ang'];
        }

        $this->db->set("is_cetak", "1")
            ->where("substr(tgl_simpan, 1, 7) <= '" . ($tahun . "-" . $bulan) . "'")
            ->where("is_cetak", "0")
            ->where_in("no_ang", $dataNoAng)
            ->update("t_simpanan_ang");
    }

    public function updateSaldoSimpAwalTahun($tahun)
    {
        if ($tahun < date("Y")) {
            for ($i = ($tahun + 1); $i <= date("Y"); $i++) {
                $this->update_saldo_simpanan_tahunan($i);
            }
        }
    }

}
