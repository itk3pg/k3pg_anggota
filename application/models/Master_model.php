<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Master_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_perusahaan($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "kd_prsh, nm_prsh";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("kd_prsh", "nm_prsh");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "kd_prsh desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get("m_perusahaan");
    }

    public function get_kode_perusahaan()
    {
        $kode = "P";

        $nomor = $this->db->select("lpad(ifnull(max(substr(kd_prsh, 2)), 0)+1, 2, '0') nomor")
            ->like("kd_prsh", $kode, "after")
            ->get("m_perusahaan")->row(0)->nomor;

        $kode .= $nomor;

        return $kode;
    }

    public function insert_perusahaan($data)
    {
        $kd_prsh = $this->get_kode_perusahaan();

        $set_data = array(
            "kd_prsh" => strtoupper($kd_prsh),
            "nm_prsh" => strtoupper($data['nm_prsh']),
        );

        return $this->db->set($set_data)->insert("m_perusahaan");
    }

    public function update_perusahaan($data, $id)
    {
        $set_data = array(
            "kd_prsh" => strtoupper($data['kd_prsh']),
            "nm_prsh" => strtoupper($data['nm_prsh']),
        );

        $this->db->set($set_data)->where("kd_prsh", $id)->update("m_departemen");
        $this->db->set($set_data)->where("kd_prsh", $id)->update("m_bagian");
        $this->db->set($set_data)->where("kd_prsh", $id)->update("t_anggota");

        return $this->db->set($set_data)->where("kd_prsh", $id)->update("m_perusahaan");
    }

    public function delete_perusahaan($data)
    {
        return $this->db->where("kd_prsh", $data['kd_prsh'])->delete("m_perusahaan");
    }

    public function get_departemen($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $kd_prsh = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id_dep, kd_dep, substr(kd_dep, 2) kode_nomor, nm_dep, kd_prsh, nm_prsh";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("kd_prsh", "nm_prsh", "kd_dep", "nm_dep");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "kd_dep desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($kd_prsh != "") {
            $this->db->where("kd_prsh", $kd_prsh);
        }

        return $this->db->get("m_departemen");
    }

    public function get_kode_departemen($kd_prsh)
    {
        $kode  = "D";
        $query = $this->db->select("ifnull(max(substr(kd_dep, 2)), 0)+1 nomor")
            ->where("kd_prsh", $kd_prsh)
            ->like("kd_dep", $kode, "after")
            ->get("m_departemen");

        return $query->row(0)->nomor;
    }

    public function insert_departemen($data)
    {
        $data['kd_dep'] = "D" . str_pad($data['kode_nomor'], 4, "0", STR_PAD_LEFT);

        $set_data = array(
            "kd_dep"  => strtoupper($data['kd_dep']),
            "nm_dep"  => strtoupper($data['nm_dep']),
            "kd_prsh" => strtoupper($data['kd_prsh']),
            "nm_prsh" => strtoupper($data['nm_prsh']),
        );

        return $this->db->set($set_data)->insert("m_departemen");
    }

    public function update_departemen($data, $id)
    {
        $cari['field'] = array("id_dep");
        $cari['value'] = $id;

        $data_dep = $this->get_departemen(0, $cari)->row_array(0);

        $data['kd_dep'] = "D" . str_pad($data['kode_nomor'], 4, "0", STR_PAD_LEFT);

        $set_data = array(
            "kd_dep"  => strtoupper($data['kd_dep']),
            "nm_dep"  => strtoupper($data['nm_dep']),
            "kd_prsh" => strtoupper($data['kd_prsh']),
            "nm_prsh" => strtoupper($data['nm_prsh']),
        );

        $this->db->set($set_data)->where("kd_dep", $data_dep['kd_dep'])->where("kd_prsh", $data_dep['kd_prsh'])->update("m_bagian");
        $this->db->set($set_data)->where("kd_dep", $data_dep['kd_dep'])->where("kd_prsh", $data_dep['kd_prsh'])->update("t_anggota");

        return $this->db->set($set_data)->where("id_dep", $id)->update("m_departemen");
    }

    public function delete_departemen($data)
    {
        return $this->db->where("id_dep", $data['id_dep'])->delete("m_departemen");
    }

    public function get_bagian($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $kd_prsh = "", $kd_dep = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id_bagian, kd_bagian, substr(kd_bagian, 2) kode_nomor, nm_bagian, kd_dep, nm_dep, kd_prsh, nm_prsh";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("kd_bagian", "nm_bagian", "kd_dep", "nm_dep", "kd_prsh", "nm_prsh");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "kd_bagian desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($kd_prsh != "") {
            $this->db->where("kd_prsh", $kd_prsh);
        }

        if ($kd_dep != "") {
            $this->db->where("kd_dep", $kd_dep);
        }

        return $this->db->get("m_bagian");
    }

    public function get_kode_bagian($kd_prsh, $kd_dep)
    {
        $kode  = "B";
        $query = $this->db->select("ifnull(max(substr(kd_bagian, 2)), 0)+1 nomor")
            ->where("kd_prsh", $kd_prsh)
            ->where("kd_dep", $kd_dep)
            ->like("kd_bagian", $kode, "after")
            ->get("m_bagian");

        return $query->row(0)->nomor;
    }

    public function insert_bagian($data)
    {
        $data['kd_bagian'] = "B" . str_pad($data['kode_nomor'], 4, "0", STR_PAD_LEFT);

        $set_data = array(
            "kd_bagian" => strtoupper($data['kd_bagian']),
            "kd_dep"    => strtoupper($data['kd_dep']),
            "kd_prsh"   => strtoupper($data['kd_prsh']),
            "nm_bagian" => strtoupper($data['nm_bagian']),
            "nm_dep"    => strtoupper($data['nm_dep']),
            "nm_prsh"   => strtoupper($data['nm_prsh']),
        );

        return $this->db->set($set_data)->insert("m_bagian");
    }

    public function update_bagian($data, $id)
    {
        $cari['field'] = array("id_bagian");
        $cari['value'] = $id;

        $data_bagian = $this->get_bagian(0, $cari)->row_array(0);

        $data['kd_bagian'] = "B" . str_pad($data['kode_nomor'], 4, "0", STR_PAD_LEFT);

        $set_data = array(
            "kd_bagian" => strtoupper($data['kd_bagian']),
            "kd_dep"    => strtoupper($data['kd_dep']),
            "kd_prsh"   => strtoupper($data['kd_prsh']),
            "nm_bagian" => strtoupper($data['nm_bagian']),
            "nm_dep"    => strtoupper($data['nm_dep']),
            "nm_prsh"   => strtoupper($data['nm_prsh']),
        );

        $this->db->set($set_data)->where("kd_bagian", $data_bagian['kd_bagian'])->where("kd_dep", $data_bagian['kd_dep'])->where("kd_prsh", $data_bagian['kd_prsh'])->update("t_anggota");

        return $this->db->set($set_data)->where("id_bagian", $id)->update("m_bagian");
    }

    public function delete_bagian($data)
    {
        return $this->db->where("id_bagian", $data['id_bagian'])->delete("m_bagian");
    }

    public function get_kelompok($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id_klp, kd_klp";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("id_klp", "kd_klp");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "kd_klp desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get("m_kelompok");
    }

    public function insert_kelompok($data)
    {
        $set_data = array(
            "kd_klp" => $data['kd_klp'],
        );

        return $this->db->set($set_data)->insert("m_kelompok");
    }

    public function update_kelompok($data, $id)
    {
        $set_data = array(
            // "id_klp" => $data['id_klp'],
            "kd_klp" => $data['kd_klp'],
        );

        return $this->db->set($set_data)->where("id_klp", $id)->update("m_kelompok");
    }

    public function delete_kelompok($data)
    {
        return $this->db->where("id_klp", $data['id_klp'])->delete("m_kelompok");
    }

    public function get_potga_ss1($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, jumlah, tahun, bulan";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang", "no_peg", "nm_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "id desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get("m_potga_ss1");
    }

    public function insert_potga_ss1($data)
    {
        $tgl_masuk_ss1 = date("Y-m-01", mktime(0, 0, 0, $data['bulan'] + 1, 1, $data['tahun']));

        $set_data = array(
            // "id"        => $id,
            "no_ang"        => $data['no_ang'],
            "no_peg"        => $data['no_peg'],
            "nm_ang"        => $data['nm_ang'],
            "kd_prsh"       => $data['kd_prsh'],
            "nm_prsh"       => $data['nm_prsh'],
            "kd_dep"        => $data['kd_dep'],
            "nm_dep"        => $data['nm_dep'],
            "kd_bagian"     => $data['kd_bagian'],
            "nm_bagian"     => $data['nm_bagian'],
            "jumlah"        => hapus_koma($data['jumlah']),
            "tahun"         => $data['tahun'],
            "bulan"         => $data['bulan'],
            "tgl_masuk_ss1" => $tgl_masuk_ss1,
        );

        return $this->db->set($set_data)->insert("m_potga_ss1");
    }

    public function update_potga_ss1($data, $id)
    {
        $tgl_masuk_ss1 = date("Y-m-01", mktime(0, 0, 0, $data['bulan'] + 1, 1, $data['tahun']));

        $set_data = array(
            // "id"        => $id,
            "no_ang"        => $data['no_ang'],
            "no_peg"        => $data['no_peg'],
            "nm_ang"        => $data['nm_ang'],
            "kd_prsh"       => $data['kd_prsh'],
            "nm_prsh"       => $data['nm_prsh'],
            "kd_dep"        => $data['kd_dep'],
            "nm_dep"        => $data['nm_dep'],
            "kd_bagian"     => $data['kd_bagian'],
            "nm_bagian"     => $data['nm_bagian'],
            "jumlah"        => hapus_koma($data['jumlah']),
            "tahun"         => $data['tahun'],
            "bulan"         => $data['bulan'],
            "tgl_masuk_ss1" => $tgl_masuk_ss1,
        );

        return $this->db->set($set_data)->where("id", $id)->update("m_potga_ss1");
    }

    public function delete_potga_ss1($data)
    {
        return $this->db->where("id", $data['id'])->delete("m_potga_ss1");
    }

    public function get_pot_bonus_pg($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $is_jadwal_tetap = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id, kd_prsh, nm_prsh, tahun, bulan, nm_pot_bonus, banyak_min_angsuran, banyak_max_angsuran";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("kd_prsh", "nm_prsh", "tahun", "bulan", "nm_pot_bonus");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "kd_prsh, tahun desc, bulan";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        if ($is_jadwal_tetap != "") {
            $this->db->where("is_jadwal_tetap", $is_jadwal_tetap);
        }

        return $this->db->get("m_pot_bonus_pg");
    }

    public function insert_pot_bonus_pg($data)
    {
        $set_data = array(
            // "id"                  => $data['id'],
            "kd_prsh"             => $data['kd_prsh'],
            "nm_prsh"             => $data['nm_prsh'],
            "tahun"               => $data['tahun'],
            "bulan"               => $data['bulan'],
            "nm_pot_bonus"        => $this->db->escape_str(strtoupper($data['nm_pot_bonus'])),
            "banyak_min_angsuran" => $data['banyak_min_angsuran'],
            "banyak_max_angsuran" => $data['banyak_max_angsuran'],
            "is_jadwal_tetap"     => $data['is_jadwal_tetap'],
        );

        return $this->db->set($set_data)->insert("m_pot_bonus_pg");
    }

    public function update_pot_bonus_pg($data, $id)
    {
        $set_data = array(
            // "id"                  => $data['id'],
            "kd_prsh"             => $data['kd_prsh'],
            "nm_prsh"             => $data['nm_prsh'],
            "tahun"               => $data['tahun'],
            "bulan"               => $data['bulan'],
            "nm_pot_bonus"        => $this->db->escape_str(strtoupper($data['nm_pot_bonus'])),
            "banyak_min_angsuran" => $data['banyak_min_angsuran'],
            "banyak_max_angsuran" => $data['banyak_max_angsuran'],
            "is_jadwal_tetap"     => $data['is_jadwal_tetap'],
        );

        return $this->db->set($set_data)->where("id", $id)->update("m_pot_bonus_pg");
    }

    public function delete_pot_bonus_pg($data)
    {
        return $this->db->where("id", $data['id'])->delete("m_pot_bonus_pg");
    }

    public function get_pot_bonus_pg_berlaku($tahun, $bulan, $kd_prsh = "")
    {
        if ($kd_prsh) {
            $this->db->where("kd_prsh", $kd_prsh);
        }

        $this->db->select("kd_prsh, nm_prsh, nm_pot_bonus, tahun, bulan, sum(banyak_min_angsuran) banyak_min_angsuran, sum(banyak_max_angsuran) banyak_max_angsuran")
            ->where("bulan", $bulan)
            ->where("tahun", $tahun)
            ->where("is_jadwal_tetap", "0")
            ->group_by("bulan");

        return $this->db->get("m_pot_bonus_pg");
    }

    public function get_pokok_wajib($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id, jml_pokok, jml_wajib, tgl_berlaku tgl_berlaku1, date_format(tgl_berlaku, '%d-%m-%Y') tgl_berlaku";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("tgl_berlaku");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "id desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get("m_pokok_wajib");
    }

    public function insert_pokok_wajib($data)
    {
        $set_data = array(
            // "id"                  => $data['id'],
            // "jml_pokok"   => hapus_koma($data['jml_pokok']),
            "jml_wajib"   => hapus_koma($data['jml_wajib']),
            "tgl_berlaku" => $data['tgl_berlaku'],
        );

        return $this->db->set($set_data)->insert("m_pokok_wajib");
    }

    public function update_pokok_wajib($data, $id)
    {
        $set_data = array(
            // "jml_pokok"   => hapus_koma($data['jml_pokok']),
            "jml_wajib"   => hapus_koma($data['jml_wajib']),
            "tgl_berlaku" => $data['tgl_berlaku'],
        );

        return $this->db->set($set_data)->where("id", $id)->update("m_pokok_wajib");
    }

    public function delete_pokok_wajib($data)
    {
        return $this->db->where("id", $data['id'])->delete("m_pokok_wajib");
    }

    public function get_pokok_wajib_berlaku($data)
    {
        return $this->db->where("tgl_berlaku <=", $data['tgl_berlaku'])
            ->order_by("tgl_berlaku desc")
            ->limit("1")
            ->get("m_pokok_wajib");
    }

    public function get_margin_pinjaman_berlaku($kd_jns_pinjaman, $tempo_bln, $tgl_berlaku)
    {
        if ($kd_jns_pinjaman == "3") {
            $this->db->where("(bln_awal <= '" . $tempo_bln . "' and '" . $tempo_bln . "' <= bln_akhir)");
        } else {
            $this->db->where("tempo_bln", $tempo_bln);
        }

        $this->db->select("id_rate_pinjaman, kd_jns_pinjaman, nm_jns_pinjaman, tempo_bln, bln_awal, bln_akhir, jenis_rate, rate, tgl_berlaku, user_input, tgl_insert, user_edit, tgl_update")
            ->where("kd_jns_pinjaman", $kd_jns_pinjaman)->where("tgl_berlaku <=", $tgl_berlaku)
            ->order_by("tgl_berlaku", "desc")
            ->limit("1");

        return $this->db->get("m_rate_pinjaman");
    }

    public function get_margin_bp_berlaku($tempo_bln, $tgl_berlaku)
    {
        $this->db->where("tempo_bln", $tempo_bln);

        $this->db->select("id_margin, tempo_bln, margin, tgl_berlaku")
            ->where("tgl_berlaku <=", $tgl_berlaku)
            ->order_by("tgl_berlaku", "desc")
            ->limit("1");

        return $this->db->get("m_margin_bp");
    }

}
