<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Laporan_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_ang_masuk($tahun, $bulan)
    {
        $blth = $tahun . "-" . $bulan;

        return $this->db->select("id_ang, no_ang, no_peg, no_peglm, sts_instansi, nm_ang, jns_kel, kt_lhr, tgl_lhr tgl_lhr1, date_format(tgl_lhr, '%d-%m-%Y') tgl_lhr, nm_ibukdg, nm_psg, no_ktp, no_npwp, alm_rmh, tlp_hp, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, tlp_kntr, sts_pindah, ket_pindah, kd_prsh_pindah, nm_prsh_pindah, kd_dep_pindah, nm_dep_pindah, kd_bagian_pindah, nm_bagian_pindah, kd_klp_pindah, tgl_msk tgl_msk1, date_format(tgl_msk, '%d-%m-%Y') tgl_msk, id_klp, kd_klp, sts_ketua, gaji, plafon_persen, plafon, plafon_pakai, (plafon - plafon_pakai) sisa_plafon, status_keluar, tgl_keluar tgl_keluar1, date_format(tgl_keluar, '%d-%m-%Y') tgl_keluar, id_alasan_keluar, alasan_keluar, ket_keluar")
            ->like("tgl_msk", $blth, "after")
            ->order_by("tgl_msk1")
            ->get("t_anggota_masuk");
    }

    public function get_ang_pindah($tahun, $bulan)
    {
        $blth = $tahun . "-" . $bulan;

        return $this->db->select("id_pindah, no_ang, no_peg, nm_ang, kd_prsh_lama, nm_prsh_lama, kd_dep_lama, nm_dep_lama, kd_bagian_lama, nm_bagian_lama, kd_prsh_baru, nm_prsh_baru, kd_dep_baru, nm_dep_baru, kd_bagian_baru, nm_bagian_baru, tgl_pindah, sts_pindah, ket_pindah, tgl_update")
            ->like("tgl_pindah", $blth, "after")
            ->order_by("id_pindah")
            ->get("t_anggota_pindah");
    }

    public function get_ang_keluar($tahun, $bulan)
    {
        $blth = $tahun . "-" . $bulan;

        return $this->db->select("id_keluar, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, tgl_keluar, kd_alasan, nm_alasan, ket_keluar, jml_hak, tgl_update")
            ->like("tgl_keluar", $blth, "after")
            ->order_by("id_keluar")
            ->get("t_anggota_keluar");
    }

    public function get_mutasi_anggota($tahun, $bulan)
    {
        $blth = $tahun . "-" . $bulan;

        return $this->db->select("kd_prsh, nm_prsh, sum(IF(blth < '" . $blth . "', saldo_akhir, 0)) saldo_awal, sum(IF(blth = '" . $blth . "', masuk, 0)) masuk, sum(IF(blth = '" . $blth . "', keluar, 0)) keluar, sum(saldo_akhir) saldo_akhir")
            ->like("blth", $tahun, "after")->where("blth <=", $blth)
            ->group_by("kd_prsh")
            ->having("saldo_awal != 0 or masuk != 0 or keluar != 0 or saldo_akhir != 0")
            ->get("t_mut_anggota");
    }

    public function get_ang_per_prsh($tahun, $bulan, $kd_prsh = "")
    {
        $blth = $tahun . "-" . $bulan;

        if ($kd_prsh) {
            $this->db->where("kd_prsh", $kd_prsh);
        }

        $this->db->select("id_ang, no_ang, no_peg, no_peglm, sts_instansi, nm_ang, jns_kel, kt_lhr, tgl_lhr tgl_lhr1, date_format(tgl_lhr, '%d-%m-%Y') tgl_lhr, nm_ibukdg, nm_psg, no_ktp, no_npwp, alm_rmh, tlp_hp, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, tlp_kntr, sts_pindah, ket_pindah, kd_prsh_pindah, nm_prsh_pindah, kd_dep_pindah, nm_dep_pindah, kd_bagian_pindah, nm_bagian_pindah, kd_klp_pindah, tgl_msk tgl_msk1, date_format(tgl_msk, '%d-%m-%Y') tgl_msk, id_klp, kd_klp, sts_ketua, gaji, plafon_persen, plafon, plafon_pakai, (plafon - plafon_pakai) sisa_plafon, status_keluar, tgl_keluar tgl_keluar1, date_format(tgl_keluar, '%d-%m-%Y') tgl_keluar, id_alasan_keluar, alasan_keluar, ket_keluar")
            ->where("substr(tgl_msk, 1, 7) <=", $blth)->where("(tgl_keluar is null or substr(tgl_keluar, 1, 7) > '" . $blth . "')")
            ->order_by("tgl_msk1");

        return $this->db->get("t_anggota");
    }

    public function cek_nomor_lap_potga($data)
    {
        $result = array(
            'no_sp'       => 0,
            'no_kuitansi' => 0,
            'ada_data'    => 0,
        );

        $data_nomor = $this->db->where("tahun", $data['tahun'])
            ->where("bulan", $data['bulan'])
            ->where("kd_prsh", $data['kd_prsh'])
            ->get("t_lap_potga");

        if ($data_nomor->num_rows() > 0) {
            $result['no_sp']       = $data_nomor->row(0)->no_sp;
            $result['no_kuitansi'] = $data_nomor->row(0)->no_kuitansi;
            $result['ada_data']    = 1;
        } else {
            $data_nomor = $this->db->select("ifnull(max(no_sp), 0) + 1 no_sp, ifnull(max(no_kuitansi), 0) + 1 no_kuitansi")
                ->where("tahun", $data['tahun'])
                ->where("bulan", $data['bulan'])
                ->get("t_lap_potga");

            $result['no_sp']       = $data_nomor->row(0)->no_sp;
            $result['no_kuitansi'] = $data_nomor->row(0)->no_kuitansi;
        }

        return $result;
    }

    public function simpan_nomor_lap_potga($data)
    {
        $set_data = array(
            "tahun"       => $data['tahun'],
            "bulan"       => $data['bulan'],
            "kd_prsh"     => $data['kd_prsh'],
            "no_sp"       => $data['no_sp'],
            "no_kuitansi" => $data['no_kuitansi'],
        );

        $cek_data = $this->db->where("tahun", $data['tahun'])
            ->where("bulan", $data['bulan'])
            ->where("kd_prsh", $data['kd_prsh'])
            ->get("t_lap_potga");

        if ($cek_data->num_rows() > 0) {
            $id = $cek_data->row(0)->id;

            $this->db->set($set_data)->where("id", $id)->update("t_lap_potga");
        } else {
            $this->db->set($set_data)->insert("t_lap_potga");
        }
    }

}
