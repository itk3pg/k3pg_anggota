<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Anggota_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_anggota($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $status_keluar = "", $no_ang = "", $anggota_only = "", $is_pensiun = "", $is_pensiun_aktif = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id_ang, no_ang, no_peg, no_peglm, sts_instansi, nm_ang, jns_kel, kt_lhr, tgl_lhr tgl_lhr1, date_format(tgl_lhr, '%d-%m-%Y') tgl_lhr, day(tgl_lhr) hari_lahir, lpad(month(tgl_lhr), 2, '0') bulan_lahir, year(tgl_lhr) tahun_lahir, nm_ibukdg, nm_psg, no_ktp, no_npwp, alm_rmh, tlp_hp, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, tlp_kntr, sts_pindah, ket_pindah, kd_prsh_pindah, nm_prsh_pindah, kd_dep_pindah, nm_dep_pindah, kd_bagian_pindah, nm_bagian_pindah, kd_klp_pindah, tgl_msk tgl_msk1, date_format(tgl_msk, '%d-%m-%Y') tgl_msk, id_klp, kd_klp, sts_ketua, gaji, plafon_persen, plafon, plafon_pakai, (plafon - plafon_pakai) sisa_plafon, status_keluar, tgl_keluar tgl_keluar1, date_format(tgl_keluar, '%d-%m-%Y') tgl_keluar, id_alasan_keluar, alasan_keluar, ket_keluar, is_pensiun, date_format(tgl_pensiun, '%d-%m-%Y') tgl_pensiun, tgl_pensiun tgl_pensiun1, is_pensiun_aktif, date_format(tgl_pensiun_aktif, '%d-%m-%Y') tgl_pensiun_aktif, tgl_pensiun_aktif tgl_pensiun_aktif1, jml_simp_pokok, date_format(tgl_potga_pokok, '%d-%m-%Y') tgl_potga_pokok, tgl_potga_pokok tgl_potga_pokok1, is_blokir_plafon, ket_blokir_plafon, ket_buku_hilang, is_meninggal, user_edit, tgl_update";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang", "nm_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        if ($status_keluar != "") {
            $this->db->where("status_keluar", $status_keluar);
        }

        if ($no_ang != "") {
            $this->db->where("no_ang", $no_ang);
        }

        if ($anggota_only == "1") {
            $this->db->where("sts_instansi", "0");
        }

        if ($is_pensiun != "") {
            $this->db->where("is_pensiun", $is_pensiun);
        }

        if ($is_pensiun_aktif != "") {
            $this->db->where("is_pensiun_aktif", $is_pensiun_aktif);
        }

        $set_order = ($order) ? $order : "id_ang desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get("t_anggota");
    }

    public function insert_anggota_masuk($data)
    {
        $id_ang = get_maxid("t_anggota", "id_ang");

        if (!isset($data['kd_dep'])) {
            $data['kd_dep'] = "";
        }

        if (!isset($data['kd_bagian'])) {
            $data['kd_bagian'] = "";
        }

        $set_data = array(
            "id_ang"          => $id_ang,
            "no_ang"          => strtoupper($data['no_ang']),
            "no_peg"          => strtoupper($data['no_peg']),
            "nm_ang"          => $this->db->escape_str(strtoupper($data['nm_ang'])),
            "jns_kel"         => $data['jns_kel'],
            "kt_lhr"          => $data['kt_lhr'],
            "tgl_lhr"         => ($data['tahun_lahir'] . "-" . $data['bulan_lahir'] . "-" . $data['hari_lahir']),
            "nm_ibukdg"       => $this->db->escape_str(strtoupper($data['nm_ibukdg'])),
            "nm_psg"          => $this->db->escape_str(strtoupper($data['nm_psg'])),
            "no_ktp"          => $data['no_ktp'],
            "alm_rmh"         => $this->db->escape_str(strtoupper($data['alm_rmh'])),
            "tlp_hp"          => $data['tlp_hp'],
            "kd_prsh"         => $data['kd_prsh'],
            "nm_prsh"         => $data['nm_prsh'],
            "kd_dep"          => $data['kd_dep'],
            "nm_dep"          => strtoupper($data['nm_dep']),
            "kd_bagian"       => $data['kd_bagian'],
            "nm_bagian"       => strtoupper($data['nm_bagian']),
            "tgl_msk"         => $data['tgl_msk'],
            // "kd_klp"    => $data['kd_klp'],
            "gaji"            => hapus_koma($data['gaji']),
            "plafon"          => hapus_koma($data['plafon']),
            "jml_simp_pokok"  => hapus_koma($data['jml_simp_pokok']),
            "tgl_potga_pokok" => $data['tgl_potga_pokok'],
        );

        $query_insert = $this->db->set($set_data)->insert("t_anggota");
        $query_insert = $this->db->set($set_data)->insert("t_anggota_masuk");

        $xtgl  = explode("-", $data['tgl_msk']);
        $tahun = $xtgl[0];
        $bulan = $xtgl[1];

        $this->update_mutasi_anggota($tahun, $bulan, $data['kd_prsh']);

        return $query_insert;
    }

    public function insert_anggota_pindah($data)
    {
        $set_data = array(
            "kd_prsh"   => $data['kd_prsh_baru'],
            "nm_prsh"   => $data['nm_prsh_baru'],
            "kd_dep"    => $data['kd_dep_baru'],
            "nm_dep"    => $data['nm_dep_baru'],
            "kd_bagian" => $data['kd_bagian_baru'],
            "nm_bagian" => $data['nm_bagian_baru'],
        );

        $this->db->set($set_data)->where("no_ang", $data['no_ang'])->update("t_anggota");

        $set_data1 = array(
            // "id_ang"         => $data['id_ang']
            "no_ang"         => $data['no_ang'],
            "no_peg"         => $data['no_peg'],
            "nm_ang"         => $data['nm_ang'],
            "kd_prsh_lama"   => $data['kd_prsh'],
            "nm_prsh_lama"   => $data['nm_prsh'],
            "kd_dep_lama"    => $data['kd_dep'],
            "nm_dep_lama"    => $data['nm_dep'],
            "kd_bagian_lama" => $data['kd_bagian'],
            "nm_bagian_lama" => $data['nm_bagian'],
            "kd_prsh_baru"   => $data['kd_prsh_baru'],
            "nm_prsh_baru"   => $data['nm_prsh_baru'],
            "kd_dep_baru"    => $data['kd_dep_baru'],
            "nm_dep_baru"    => $data['nm_dep_baru'],
            "kd_bagian_baru" => $data['kd_bagian_baru'],
            "nm_bagian_baru" => $data['nm_bagian_baru'],
            "tgl_pindah"     => $data['tgl_pindah'],
            "sts_pindah"     => "1",
            "ket_pindah"     => strtoupper($data['ket_pindah']),
        );

        $query = $this->db->set($set_data1)->insert("t_anggota_pindah");

        $xtgl  = explode("-", $data['tgl_pindah']);
        $tahun = $xtgl[0];
        $bulan = $xtgl[1];

        $this->update_mutasi_anggota($tahun, $bulan, $data['kd_prsh']);
        $this->update_mutasi_anggota($tahun, $bulan, $data['kd_prsh_baru']);

        return $query;
    }

    public function get_anggota_pindah($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id_pindah, no_ang, no_peg, nm_ang, kd_prsh_lama, nm_prsh_lama, kd_dep_lama, nm_dep_lama, kd_bagian_lama, nm_bagian_lama, kd_prsh_baru, nm_prsh_baru, kd_dep_baru, nm_dep_baru, kd_bagian_baru, nm_bagian_baru, tgl_pindah, sts_pindah, ket_pindah, tgl_update";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang", "no_peg", "nm_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "id_pindah desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get("t_anggota_pindah");
    }

    public function hapus_anggota_pindah($data)
    {
        $cari['field'][] = "id_pindah";
        $cari['value']   = $data['id_pindah'];

        $data_sebelumnya = $this->get_anggota_pindah(0, $cari)->row_array();

        $set_data = array(
            "kd_prsh"   => $data['kd_prsh_lama'],
            "nm_prsh"   => $data['nm_prsh_lama'],
            "kd_dep"    => $data['kd_dep_lama'],
            "nm_dep"    => $data['nm_dep_lama'],
            "kd_bagian" => $data['kd_bagian_lama'],
            "nm_bagian" => $data['nm_bagian_lama'],
        );

        $this->db->set($set_data)->where("no_ang", $data_sebelumnya['no_ang'])->update("t_anggota");

        $query = $this->db->where("id_pindah", $data['id_pindah'])->delete("t_anggota_pindah");

        $xtgl  = explode("-", $data['tgl_pindah']);
        $tahun = $xtgl[0];
        $bulan = $xtgl[1];

        $this->update_mutasi_anggota($tahun, $bulan, $data['kd_prsh_lama']);
        $this->update_mutasi_anggota($tahun, $bulan, $data['kd_prsh_baru']);

        return $query;
    }

    public function insert_anggota_keluar($data)
    {
        $set_data = array(
            "status_keluar" => "1",
            "tgl_keluar"    => $data['tgl_keluar'],
            // "id_alasan_keluar" => $data['id_alasan_keluar'],
            // "alasan_keluar"    => $data['alasan_keluar'],
            "ket_keluar"    => $this->db->escape_str(strtoupper($data['ket_keluar'])),
        );

        $this->db->set($set_data)->where("no_ang", $data['no_ang'])->update("t_anggota");

        $set_data1 = array(
            // "id_ang"     => $data['id_ang'],
            "no_ang"     => $data['no_ang'],
            "no_peg"     => $data['no_peg'],
            "nm_ang"     => $data['nm_ang'],
            "kd_prsh"    => $data['kd_prsh'],
            "nm_prsh"    => $data['nm_prsh'],
            "kd_dep"     => $data['kd_dep'],
            "nm_dep"     => $data['nm_dep'],
            "kd_bagian"  => $data['kd_bagian'],
            "nm_bagian"  => $data['nm_bagian'],
            "tgl_keluar" => $data['tgl_keluar'],
            "jml_hak"    => hapus_koma($data['jml_hak']),
            // "kd_alasan"  => $data['kd_alasan'],
            // "nm_alasan"  => $data['nm_alasan'],
            "ket_keluar" => $this->db->escape_str(strtoupper($data['ket_keluar'])),
        );

        $query = $this->db->set($set_data1)->insert("t_anggota_keluar");

        $xtgl  = explode("-", $data['tgl_keluar']);
        $tahun = $xtgl[0];
        $bulan = $xtgl[1];
        $hari  = $xtgl[2];

        $this->update_mutasi_anggota($tahun, $bulan, $data['kd_prsh']);

        /*update master potga ss1*/

        $cekDataPotgaSS1 = $this->db->where("no_ang", $data['no_ang'])
            ->where("jumlah !=", 0)
            ->get("m_potga_ss1")
            ->num_rows();

        if ($cekDataPotgaSS1 > 0) {
            // if ($hari > 12) {
            //     $strtime_bulan_depan = mktime(0, 0, 0, $bulan + 1, 1, $tahun);

            //     $tahun = date("Y", $strtime_bulan_depan);
            //     $bulan = date("m", $strtime_bulan_depan);
            //     $hari  = date("d", $strtime_bulan_depan);
            // } else {
            /*hapus data potga anggota yang keluar pada tanggal 1-12*/
            // $this->db->like("tgl_potga", ($tahun . "-" . $bulan), "after")
            //     ->where("no_ang", $data['no_ang'])
            //     ->delete("t_potga");
            // }

            $tgl_masuk_ss1 = date("Y-m-01", mktime(0, 0, 0, $bulan + 1, 1, $tahun));

            $set_data_potga_ss1 = array(
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
                "jumlah"        => 0,
                "tahun"         => $tahun,
                "bulan"         => $bulan,
                "tgl_masuk_ss1" => $tgl_masuk_ss1,
                "status_keluar" => "1",
                "tgl_keluar"    => $data['tgl_keluar'],
            );

            $data_potga_ss1 = $this->db->where("no_ang", $data['no_ang'])->where("status_keluar", "1")->where("jumlah", 0)
                ->get("m_potga_ss1");

            if ($data_potga_ss1->num_rows() > 0) {
                $id_potga_ss1 = $data_potga_ss1->row(0)->id;

                $this->db->where("id", $id_potga_ss1)->set($set_data_potga_ss1)->update("m_potga_ss1");
            } else {
                $this->db->set($set_data_potga_ss1)->insert("m_potga_ss1");
            }
        }

        /*update status keluar pada anggota/nasabah yang keluar beserta turunannya*/
        $this->db->set("status_keluar", "1")->set("tgl_keluar", $data['tgl_keluar'])
            ->like("no_ang", $data['no_ang'], "after")
            ->update("t_nasabah");

        return $query;
    }

    public function get_anggota_keluar($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id_keluar, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, tgl_keluar, kd_alasan, nm_alasan, ket_keluar, jml_hak, tgl_update";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang", "no_peg", "nm_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "id_keluar desc";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get("t_anggota_keluar");
    }

    public function hapus_anggota_keluar($data)
    {
        $set_data = array(
            "status_keluar" => "0",
            "tgl_keluar"    => null,
            "ket_keluar"    => null,
        );

        $this->db->set($set_data)->where("no_ang", $data['no_ang'])->update("t_anggota");
        $this->db->set($set_data)->like("no_ang", $data['no_ang'], "after")->update("t_nasabah");

        $query = $this->db->where("id_keluar", $data['id_keluar'])->delete("t_anggota_keluar");

        $xtgl  = explode("-", $data['tgl_keluar']);
        $tahun = $xtgl[0];
        $bulan = $xtgl[1];

        $this->update_mutasi_anggota($tahun, $bulan, $data['kd_prsh']);

        $this->db->where("no_ang", $data['no_ang'])->where("status_keluar", "1")->where("jumlah", 0)->delete("m_potga_ss1");

        return $query;
    }

    public function update_data_anggota($data, $id)
    {
        if (!isset($data['kd_dep'])) {
            $data['kd_dep'] = "";
        }

        if (!isset($data['kd_bagian'])) {
            $data['kd_bagian'] = "";
        }

        if (!isset($data['hari_lahir'])) {
            $data['hari_lahir'] = "";
        }

        if (!isset($data['bulan_lahir'])) {
            $data['bulan_lahir'] = "";
        }

        $data['tgl_msk'] = ($data['tgl_msk'] != "") ? balik_tanggal($data['tgl_msk']) : null;

        if ($data['tgl_pensiun'] != "") {
            $data['tgl_pensiun'] = balik_tanggal($data['tgl_pensiun']);

            $cekDataPotgaSS1 = $this->db->where("no_ang", $data['no_ang'])
                ->where("jumlah !=", 0)
                ->get("m_potga_ss1")
                ->num_rows();

            if ($cekDataPotgaSS1 > 0) {
                $ex_tgl_pensiun = explode("-", $data['tgl_pensiun']);
                $tahun_pensiun  = $ex_tgl_pensiun[0];
                $bulan_pensiun  = $ex_tgl_pensiun[1];

                $tgl_masuk_ss1 = date("Y-m-01", mktime(0, 0, 0, $bulan_pensiun + 1, 1, $tahun_pensiun));

                $set_data_potga_ss1 = array(
                    "no_ang"        => $data['no_ang'],
                    "no_peg"        => $data['no_peg'],
                    "nm_ang"        => $data['nm_ang'],
                    "kd_prsh"       => $data['kd_prsh'],
                    "nm_prsh"       => $data['nm_prsh'],
                    "kd_dep"        => $data['kd_dep'],
                    "nm_dep"        => $data['nm_dep'],
                    "kd_bagian"     => $data['kd_bagian'],
                    "nm_bagian"     => $data['nm_bagian'],
                    "jumlah"        => 0,
                    "tahun"         => $tahun_pensiun,
                    "bulan"         => $bulan_pensiun,
                    "tgl_masuk_ss1" => $tgl_masuk_ss1,
                    "is_pensiun"    => "1",
                    "tgl_pensiun"   => $data['tgl_pensiun'],
                );

                $data_potga_ss1 = $this->db->where("no_ang", $data['no_ang'])->where("is_pensiun", "1")->where("jumlah", 0)
                    ->get("m_potga_ss1");

                if ($data_potga_ss1->num_rows() > 0) {
                    $id_potga_ss1 = $data_potga_ss1->row(0)->id;

                    $this->db->set($set_data_potga_ss1)->where("id", $id_potga_ss1)->update("m_potga_ss1");
                } else {
                    $this->db->set($set_data_potga_ss1)->insert("m_potga_ss1");
                }
            }
        } else {
            $data['tgl_pensiun'] = null;

            $this->db->where("no_ang", $data['no_ang'])->where("is_pensiun", "1")->where("jumlah", 0)->delete("m_potga_ss1");
        }

        $data['tgl_pensiun_aktif'] = ($data['tgl_pensiun_aktif'] != "") ? balik_tanggal($data['tgl_pensiun_aktif']) : null;

        $data['tgl_potga_pokok'] = ($data['tgl_potga_pokok'] != "") ? balik_tanggal($data['tgl_potga_pokok']) : null;

        $set_data = array(
            // "id_ang"    => $id_ang,
            // "no_ang"            => strtoupper($data['no_ang']),
            "no_peg"            => strtoupper($data['no_peg']),
            "nm_ang"            => strtoupper($data['nm_ang']),
            // "jns_kel"   => $data['jns_kel'],
            "kt_lhr"            => $data['kt_lhr'],
            "tgl_lhr"           => ($data['tahun_lahir'] . "-" . $data['bulan_lahir'] . "-" . $data['hari_lahir']),
            "nm_ibukdg"         => strtoupper($data['nm_ibukdg']),
            "nm_psg"            => strtoupper($data['nm_psg']),
            "no_ktp"            => $data['no_ktp'],
            "alm_rmh"           => strtoupper($data['alm_rmh']),
            "tlp_hp"            => $data['tlp_hp'],
            "kd_prsh"           => $data['kd_prsh'],
            "nm_prsh"           => $data['nm_prsh'],
            "kd_dep"            => $data['kd_dep'],
            "nm_dep"            => $data['nm_dep'],
            "kd_bagian"         => $data['kd_bagian'],
            "nm_bagian"         => $data['nm_bagian'],
            "tgl_msk"           => $data['tgl_msk'],
            // "kd_klp"    => $data['kd_klp'],
            "gaji"              => hapus_koma($data['gaji']),
            "plafon"            => hapus_koma($data['plafon']),
            "is_pensiun"        => $data['is_pensiun'],
            "tgl_pensiun"       => $data['tgl_pensiun'],
            "is_pensiun_aktif"  => $data['is_pensiun_aktif'],
            "tgl_pensiun_aktif" => $data['tgl_pensiun_aktif'],
            "jml_simp_pokok"    => hapus_koma($data['jml_simp_pokok']),
            "tgl_potga_pokok"   => $data['tgl_potga_pokok'],
            "is_blokir_plafon"  => $data['is_blokir_plafon'],
            "ket_blokir_plafon" => $data['ket_blokir_plafon'],
            "ket_buku_hilang"   => $data['ket_buku_hilang'],
            "is_meninggal"      => $data['is_meninggal'],
            "user_edit"         => $this->session->userdata("username"),
        );

        if (isset($data['jns_kel'])) {
            $set_data['jns_kel'] = $data['jns_kel'];
        }

        $this->db->set($set_data)->where("no_ang", $data['no_ang'])->update("t_anggota_masuk");

        return $this->db->set($set_data)->where("id_ang", $id)->update("t_anggota");
    }

    public function delete_data_anggota($data)
    {
        $this->db->where("no_ang", $data['no_ang'])->delete("t_anggota");
        $this->db->where("no_ang", $data['no_ang'])->delete("t_anggota_masuk");

        return true;
    }

    public function update_mutasi_anggota($tahun, $bulan, $kd_prsh)
    {
        $this->load->model("master_model");

        $cr['field'][] = "kd_prsh";
        $cr['value']   = $kd_prsh;

        $data_prsh = $this->master_model->get_perusahaan(0, $cr)->row_array(0);

        $nm_prsh = $data_prsh['nm_prsh'];

        $blth = $tahun . "-" . $bulan;

        $this->db->where("blth", $blth)->where("kd_prsh", $kd_prsh)->delete("t_mut_anggota");

        $from_union = "
            select '" . $blth . "' blth, \"" . $kd_prsh . "\" kd_prsh, \"" . $nm_prsh . "\" nm_prsh, sum(masuk), sum(keluar), sum(masuk-keluar)
            from
            (
                SELECT kd_prsh, nm_prsh, COUNT(*) masuk, 0 keluar
                FROM t_anggota
                WHERE tgl_msk LIKE '" . $tahun . "-" . $bulan . "%' and kd_prsh = '" . $kd_prsh . "'
                UNION
                SELECT kd_prsh_lama, nm_prsh_lama, 0 masuk, count(*) keluar
                FROM t_anggota_pindah
                WHERE tgl_pindah LIKE '" . $tahun . "-" . $bulan . "%' and kd_prsh_lama = '" . $kd_prsh . "'
                UNION
                SELECT kd_prsh_lama, nm_prsh_lama, count(*) masuk, 0 keluar
                FROM t_anggota_pindah
                WHERE tgl_pindah LIKE '" . $tahun . "-" . $bulan . "%' and kd_prsh_lama = '" . $kd_prsh . "'
                UNION
                SELECT kd_prsh, nm_prsh, 0, COUNT(*)
                FROM t_anggota_keluar
                WHERE tgl_keluar LIKE '" . $tahun . "-" . $bulan . "%' AND kd_prsh = '" . $kd_prsh . "'
            ) tabel
            group by kd_prsh";

        /*UNION
        SELECT kd_prsh_baru, nm_prsh_baru, count(*), 0 keluar
        FROM t_anggota_pindah
        WHERE tgl_pindah LIKE '" . $tahun . "-" . $bulan . "%' and kd_prsh_baru = '" . $kd_prsh . "'*/

        $query_insert = "insert into t_mut_anggota
            (blth, kd_prsh, nm_prsh, masuk, keluar, saldo_akhir)
            " . $from_union;

        $this->db->query($query_insert);

        $this->db->where("masuk", 0)->where("keluar", 0)->where("saldo_akhir", 0)->delete("t_mut_anggota");
    }

    public function upload_data_anggota_pkg($file_upload)
    {
        $this->load->library("php_excel");

        if (isset($file_upload) and $file_upload['file_upload']['size'] > 0) {
            $inputFileName = $file_upload['file_upload']['tmp_name'];
            $objPHPExcel   = PHPExcel_IOFactory::load($inputFileName);

            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

            if (sizeof($sheetData) > 0) {
                $data_total = sizeof($sheetData);
                $data_now   = 1;
                $berhasil   = 0;
                $gagal      = 0;

                $upload_status    = "";
                $daftar_nik_gagal = "";

                foreach ($sheetData as $key => $value) {
                    if ($key == 1) {
                        continue;
                    }

                    $no_peg = $value['B'];

                    $no_peg_angka = filter_var($no_peg, FILTER_SANITIZE_NUMBER_INT);

                    $data_anggota = $this->db->like("no_peg", $no_peg_angka, "both")->get("t_anggota");

                    if ($data_anggota->num_rows() > 0) {
                        $no_ang = $data_anggota->row(0)->no_ang;

                        $set_data = array(
                            "plafon"    => $value['D'],
                            "nm_dep"    => $value['E'],
                            "nm_bagian" => $value['F'],
                            "user_edit" => "UPLOADEXCEL",
                        );

                        $is_pensiun       = $data_anggota->row(0)->is_pensiun;
                        $is_pensiun_aktif = $data_anggota->row(0)->is_pensiun_aktif;
                        $is_blokir_plafon = $data_anggota->row(0)->is_blokir_plafon;
                        $status_keluar    = $data_anggota->row(0)->status_keluar;

                        if ($is_pensiun == '1' or $is_pensiun_aktif == '1' or $is_blokir_plafon == '1' or $status_keluar == '1') {
                            // unset($set_data['plafon']);
                        } else {
                            $this->db->set($set_data)->where("no_ang", $no_ang)->update("t_anggota");
                        }

                    } else {
                        $gagal++;

                        $daftar_nik_gagal .= "Data <strong>[" . $no_peg . "] " . $value['C'] . "</strong> | tidak ada di master Anggota<br>";
                    }

                    $persen = round($data_now / $data_total * 100);

                    if (!is_cli()) {
                        $this->cache->file->save("upload_anggota_pkg_" . session_id(), $persen . ";" . $data_now . ";" . $data_total);

                        session_write_close();
                    } else {
                        baca("(" . $data_now . " / " . $data_total . ")");
                    }

                    $data_now++;
                }

                if ($gagal > 0) {
                    $upload_status .= $gagal . " data tidak dapat diupdate<br><br>" . $daftar_nik_gagal;
                } else {
                    $upload_status .= "Data selesai diproses";
                }

                echo $upload_status;
            }
        }
    }

    public function get_koreksi_plafon($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "", $no_ang = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "(@nomor:=@nomor+1) nomor, id, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, jenis_debet, if(jenis_debet = 'TAMBAHPLAFON', 'TAMBAH', 'KURANG') jenis_debet1, noref_penj, tgl_penj, abs(jml_debet) jml_debet, status";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("no_ang");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "id desc";

        $this->db->order_by($set_order);

        if (!$offset) {
            $offset = 0;
        }

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        $this->db->where_in("jenis_debet", array("TAMBAHPLAFON", "KURANGPLAFON"));

        if ($no_ang) {
            $this->db->where("no_ang", $no_ang);
        }

        return $this->db->get("t_plafon_debet a, (select @nomor:=" . $offset . ") z");
    }

    public function proses_sisa_plafon($data)
    {
        $strtime_bulan_lalu = mktime(0, 0, 0, $data['bulan'] - 1, 1, $data['tahun']);

        $tahun_lalu = date("Y", $strtime_bulan_lalu);
        $bulan_lalu = date("m", $strtime_bulan_lalu);

        $data_total = 3;
        $data_now   = 1;

        $array_bukti_proses_tambah = array();

        $data_proses_tambah = $this->db->where("jenis_debet", "PROSESTAMBAH")->like("tgl_penj", ($data['tahun'] . "-" . $data['bulan']), "after")->get("t_plafon_debet")->result_array();

        foreach ($data_proses_tambah as $key => $value) {
            $array_bukti_proses_tambah[] = $value['noref_penj'];
        }

        if (sizeof($array_bukti_proses_tambah) > 0) {
            $strBukti = "('" . implode("', '", $array_bukti_proses_tambah) . "')";

            $query1 = "update t_pinjaman_ang set sts_lunas='0' where no_pinjam in " . $strBukti;

            $this->db->query($query1);

            $query1 = "update t_bridging_plafon set sts_bayar='0' where no_trans in " . $strBukti;

            $this->db->query($query1);

            $query1 = "update db_wecode_smart.piutang set is_lunas='0' where CONCAT(ref_penjualan, toko_kode) in " . $strBukti;

            $this->db->query($query1);

            $query1 = "update db_bengkel.piutang set is_lunas='0' where CONCAT(ref_penjualan, toko_kode) in " . $strBukti;

            $this->db->query($query1);

            $query1 = "update db_pbb.piutang set is_lunas='0' where CONCAT(ref_penjualan, toko_kode) in " . $strBukti;

            $this->db->query($query1);

            $query1 = "update db_wecode_smart.t_kredit_anggota set is_lunas='0' where CONCAT(ref_bukti_bo, kd_toko) in " . $strBukti;

            $this->db->query($query1);

            $query1 = "update db_bengkel.t_kredit_anggota set is_lunas='0' where CONCAT(ref_bukti_bo, kd_toko) in " . $strBukti;

            $this->db->query($query1);

            $query1 = "update db_pbb.t_kredit_anggota set is_lunas='0' where CONCAT(ref_bukti_bo, kd_toko) in " . $strBukti;

            $this->db->query($query1);

            // $this->db->set("sts_lunas", "0")->where("no_pinjam in " . $strBukti)->update("t_pinjaman_ang");
            // $this->db->set("sts_bayar", "0")->where("no_trans in " . $strBukti)->update("t_bridging_plafon");
            // $this->db->set("is_lunas", "0")->where("CONCAT(ref_penjualan, toko_kode) in " . $strBukti)->update("db_wecode_smart.piutang");
            // $this->db->set("is_lunas", "0")->where("CONCAT(ref_bukti_bo, kd_toko) in " . $strBukti)->update("db_wecode_smart.t_kredit_anggota");
        }

        $this->db->where("jenis_debet", "PROSESTAMBAH")
            ->like("tgl_penj", ($data['tahun'] . "-" . $data['bulan']), "after")
            ->delete("t_plafon_debet");

        $persen = round($data_now / $data_total * 100);

        if (!is_cli()) {
            $this->cache->file->save("proses_sisa_plafon_" . session_id(), $persen . ";" . $data_now . ";" . $data_total);

            session_write_close();
        } else {
            baca("(" . $data_now . " / " . $data_total . ")");
        }

        $data_now++;

        $insert_tambah_plafon = "INSERT INTO t_plafon_debet
                (no_ang, jenis_debet, noref_penj, tgl_penj, jml_debet)
                    SELECT a.no_ang, 'PROSESTAMBAH', a.no_trans, '" . $data['tahun'] . "-" . $data['bulan'] . "-01', (0- a.angsuran)
                    FROM t_bridging_plafon a
                    JOIN t_bridging_plafon_det b
                    ON a.no_trans=b.no_trans
                    join t_anggota c
                    on a.no_ang=c.no_ang
                    WHERE (
                        (b.blth_angsuran = '" . $tahun_lalu . "-" . $bulan_lalu . "'
                        AND b.angs_ke=b.tempo_bln)
                        or (a.tempo_bln = '1'
                        AND b.blth_angsuran = '" . $data['tahun'] . "-" . $data['bulan'] . "')
                    )
                    AND a.sts_bayar = '0'
                UNION
                    SELECT a.no_ang, 'PROSESTAMBAH', a.no_pinjam, '" . $data['tahun'] . "-" . $data['bulan'] . "-01', (0- a.angsuran)
                    FROM t_pinjaman_ang a
                    JOIN t_pinjaman_ang_det b
                    ON a.no_pinjam=b.no_pinjam
                    join t_anggota c
                    on a.no_ang=c.no_ang
                    WHERE b.blth_angsuran = '" . $tahun_lalu . "-" . $bulan_lalu . "'
                    AND b.angs_ke=b.tempo_bln
                    AND a.angsuran > 0
                    AND b.pokok_akhir <=1
                    AND a.sts_lunas = '0'
                UNION
                    SELECT pelanggan_kode, 'PROSESTAMBAH', CONCAT(ref_penjualan, toko_kode), '" . $data['tahun'] . "-" . $data['bulan'] . "-01', (0- jumlah)
                    FROM db_wecode_smart.piutang a
                    left join k3pg_sp.t_anggota c
                    on a.pelanggan_kode=c.no_ang
                    WHERE SUBSTRING(tanggal, 1, 7) = '" . $tahun_lalu . "-" . $bulan_lalu . "'
                    AND is_lunas = '0' 
                UNION
                    SELECT pelanggan_kode, 'PROSESTAMBAH', CONCAT(ref_penjualan, toko_kode), '" . $data['tahun'] . "-" . $data['bulan'] . "-01', (0- jumlah)
                    FROM db_bengkel.piutang a
                    left join k3pg_sp.t_anggota c
                    on a.pelanggan_kode=c.no_ang
                    WHERE SUBSTRING(tanggal, 1, 7) = '" . $tahun_lalu . "-" . $bulan_lalu . "'
                    AND is_lunas = '0' 
                UNION
                    SELECT pelanggan_kode, 'PROSESTAMBAH', CONCAT(ref_penjualan, toko_kode), '" . $data['tahun'] . "-" . $data['bulan'] . "-01', (0- jumlah)
                    FROM db_pbb.piutang a
                    left join k3pg_sp.t_anggota c
                    on a.pelanggan_kode=c.no_ang
                    WHERE SUBSTRING(tanggal, 1, 7) = '" . $tahun_lalu . "-" . $bulan_lalu . "'
                    AND is_lunas = '0'
                UNION
                    SELECT a.noang, 'PROSESTAMBAH', CONCAT(a.ref_bukti_bo, a.kd_toko), '" . $data['tahun'] . "-" . $data['bulan'] . "-01', (0- a.angs_perbulan)
                    FROM db_wecode_smart.t_kredit_anggota a
                    JOIN db_wecode_smart.t_kredit_anggota_det b
                    ON a.ref_bukti_bo = b.no_pinjam AND a.kd_toko = b.flokasi
                    left join k3pg_sp.t_anggota c
                    on a.noang=c.no_ang
                    WHERE b.tahun_angsuran = '" . $tahun_lalu . "'
                    AND LPAD(b.bulan_angsuran, 2, '0') = '" . $bulan_lalu . "'
                    AND b.angs_ke=b.tempo_bln
                    AND a.is_lunas = '0' 
                UNION
                    SELECT a.noang, 'PROSESTAMBAH', CONCAT(a.ref_bukti_bo, a.kd_toko), '" . $data['tahun'] . "-" . $data['bulan'] . "-01', (0- a.angs_perbulan)
                    FROM db_bengkel.t_kredit_anggota a
                    JOIN db_bengkel.t_kredit_anggota_det b
                    ON a.ref_bukti_bo = b.no_pinjam AND a.kd_toko = b.flokasi
                    left join k3pg_sp.t_anggota c
                    on a.noang=c.no_ang
                    WHERE b.tahun_angsuran = '" . $tahun_lalu . "'
                    AND LPAD(b.bulan_angsuran, 2, '0') = '" . $bulan_lalu . "'
                    AND b.angs_ke=b.tempo_bln
                    AND a.is_lunas = '0' 
                UNION
                    SELECT a.noang, 'PROSESTAMBAH', CONCAT(a.ref_bukti_bo, a.kd_toko), '" . $data['tahun'] . "-" . $data['bulan'] . "-01', (0- a.angs_perbulan)
                    FROM db_pbb.t_kredit_anggota a
                    JOIN db_pbb.t_kredit_anggota_det b
                    ON a.ref_bukti_bo = b.no_pinjam AND a.kd_toko = b.flokasi
                    left join k3pg_sp.t_anggota c
                    on a.noang=c.no_ang
                    WHERE b.tahun_angsuran = '" . $tahun_lalu . "'
                    AND LPAD(b.bulan_angsuran, 2, '0') = '" . $bulan_lalu . "'
                    AND b.angs_ke=b.tempo_bln
                    AND a.is_lunas = '0'";

        $this->db->query($insert_tambah_plafon);

        $persen = round($data_now / $data_total * 100);

        if (!is_cli()) {
            $this->cache->file->save("proses_sisa_plafon_" . session_id(), $persen . ";" . $data_now . ";" . $data_total);

            session_write_close();
        } else {
            baca("(" . $data_now . " / " . $data_total . ")");
        }

        $query_update_plafon = "UPDATE t_anggota a
                JOIN
                (
                    SELECT no_ang, SUM(jml_debet) jumlah
                    FROM t_plafon_debet
                    GROUP BY no_ang
                ) b
                ON a.no_ang=b.no_ang
                SET a.plafon_pakai = b.jumlah";

        $this->db->query($query_update_plafon);

        $array_bukti_proses_tambah = array();

        $data_proses_tambah = $this->db->where("jenis_debet", "PROSESTAMBAH")->like("tgl_penj", ($data['tahun'] . "-" . $data['bulan']), "after")->get("t_plafon_debet")->result_array();

        foreach ($data_proses_tambah as $key => $value) {
            $array_bukti_proses_tambah[] = $value['noref_penj'];
        }

        $blth_lunas = $data['tahun'] . "-" . $data['bulan'];

        $strBukti = "('" . implode("', '", $array_bukti_proses_tambah) . "')";

        $query1 = "update t_pinjaman_ang set sts_lunas='1', blth_lunas = '" . $blth_lunas . "' where no_pinjam in " . $strBukti;

        $this->db->query($query1);

        $query1 = "update t_bridging_plafon set sts_bayar='1', blth_bayar = '" . $blth_lunas . "' where no_trans in " . $strBukti;

        $this->db->query($query1);

        $query1 = "update db_wecode_smart.piutang set is_lunas='1', tgl_lunas = '" . $blth_lunas . "-01' where CONCAT(ref_penjualan, toko_kode) in " . $strBukti;

        $this->db->query($query1);

        $query1 = "update db_bengkel.piutang set is_lunas='1', tgl_lunas = '" . $blth_lunas . "-01' where CONCAT(ref_penjualan, toko_kode) in " . $strBukti;

        $this->db->query($query1);

        $query1 = "update db_pbb.piutang set is_lunas='1', tgl_lunas = '" . $blth_lunas . "-01' where CONCAT(ref_penjualan, toko_kode) in " . $strBukti;

        $this->db->query($query1);

        $query1 = "update db_wecode_smart.t_kredit_anggota set is_lunas='1', tgl_lunas = '" . $blth_lunas . "-01' where CONCAT(ref_bukti_bo, kd_toko) in " . $strBukti;

        $this->db->query($query1);

        $query1 = "update db_bengkel.t_kredit_anggota set is_lunas='1', tgl_lunas = '" . $blth_lunas . "-01' where CONCAT(ref_bukti_bo, kd_toko) in " . $strBukti;

        $this->db->query($query1);

        $query1 = "update db_pbb.t_kredit_anggota set is_lunas='1', tgl_lunas = '" . $blth_lunas . "-01' where CONCAT(ref_bukti_bo, kd_toko) in " . $strBukti;

        $this->db->query($query1);

        // $this->db->set("sts_lunas", "1")->set("blth_lunas", $blth_lunas)
        //     ->where("no_pinjam in " . $strBukti)
        //     ->update("t_pinjaman_ang");

        // $this->db->set("sts_bayar", "1")->set("blth_bayar", $blth_lunas)
        //     ->where("no_trans in " . $strBukti)
        //     ->update("t_bridging_plafon");

        // $this->db->set("is_lunas", "1")->set("tgl_lunas", $blth_lunas . "-01")
        //     ->where("CONCAT(ref_penjualan, toko_kode) in " . $strBukti)
        //     ->update("db_wecode_smart.piutang");

        // $this->db->set("is_lunas", "1")->set("tgl_lunas", $blth_lunas . "-01")
        //     ->where("CONCAT(ref_bukti_bo, kd_toko) in " . $strBukti)
        //     ->update("db_wecode_smart.t_kredit_anggota");

        $data_now++;

        $persen = round($data_now / $data_total * 100);

        if (!is_cli()) {
            $this->cache->file->save("proses_sisa_plafon_" . session_id(), $persen . ";" . $data_now . ";" . $data_total);

            session_write_close();
        } else {
            baca("(" . $data_now . " / " . $data_total . ")");
        }

        baca("Sisa Plafon berhasil diproses");
    }
}
