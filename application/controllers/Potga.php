<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Potga extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("potga_model");
    }

    public function index($page)
    {
        $option_bulan = get_option_tag(array_bulan(), "BULAN");

        if ($page == "proses-potga") {
            $data['judul_menu'] = "Proses Potong Gaji";
            $data['bulan']      = $option_bulan;

            $this->template->view("potga/proses_potga", $data);
        }
    }

    public function get_potga()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];
        $tahun         = isset($data['tahun']) ? $data['tahun'] : "";
        $bulan         = isset($data['bulan']) ? $data['bulan'] : "";

        $data_numrows = $this->potga_model->get_potga(1, $cari, "", "", "", $tahun, $bulan)->row(0)->numrows;
        $data_item    = $this->potga_model->get_potga(0, $cari, "", $offset, $limit, $tahun, $bulan);

        // $data_set = array();

        // foreach ($data_item->result_array() as $value) {
        //     $offset++;
        //     $value['nomor'] = $offset;
        //     $data_set[]     = $value;
        // }

        $array['recordsTotal']    = $data_numrows;
        $array['recordsFiltered'] = $array['recordsTotal'];
        $array['data']            = $data_item->result_array();

        echo json_encode($array);
    }

    public function get_jadwal_potongan_bonus()
    {
        $data_request = get_request();

        $tabel = "<table class=\"table table-bordered table-condensed table-striped\">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Perusahaan</th>
                        <th>Periode</th>
                        <th>Nama Bonus/Insentif</th>
                        <th>Banyak Min. Angsuran</th>
                        <th>Banyak Max. Angsuran</th>
                    </tr>
                </thead>
                <tbody>";

        $no            = 1;
        $data_potongan = $this->db->where("tahun", $data_request['tahun'])->where("bulan", $data_request['bulan'])
            ->get("m_pot_bonus_pg");

        if ($data_potongan->num_rows() > 0) {
            foreach ($data_potongan->result_array() as $key => $value) {
                $tabel .= "
                    <tr>
                        <td>" . $no . "</td>
                        <td>" . $value['nm_prsh'] . "</td>
                        <td>" . $data_request['bulan'] . "-" . $data_request['tahun'] . "</td>
                        <td>
                            <input type=\"hidden\" name=\"id[]\" value=\"" . $value['id'] . "\">
                            <input type=\"hidden\" name=\"kd_prsh[]\" value=\"" . $value['kd_prsh'] . "\">
                            <input type=\"text\" name=\"nm_pot_bonus[]\" id=\"nm_pot_bonus\" class=\"form-control\" value=\"" . $value['nm_pot_bonus'] . "\">
                        </td>
                        <td>
                            <input type=\"text\" name=\"banyak_min_angsuran[]\" id=\"banyak_min_angsuran\" class=\"form-control\" size=\"2\" value=\"" . $value['banyak_min_angsuran'] . "\">
                        </td>
                        <td>
                            <input type=\"text\" name=\"banyak_max_angsuran[]\" id=\"banyak_max_angsuran\" class=\"form-control\" size=\"2\" value=\"" . $value['banyak_max_angsuran'] . "\">
                        </td>
                    </tr>";

                $no++;
            }

        } else {
            $tabel .= "<tr>
                <td colspan=\"6\" class=\"text-center\">Tidak ada Data Potongan</td>
            </tr>";

        }

        $tabel .= "
                </tbody>
            </table>";

        echo $tabel;
    }

    public function init_progress_potga()
    {
        $this->cache->file->save('proses_potga_' . session_id(), "0;0;0");
    }

    public function get_progress_potga()
    {
        $data_proses = explode(";", $this->cache->file->get('proses_potga_' . session_id()));

        $json['persen']     = $data_proses[0];
        $json['data_now']   = $data_proses[1];
        $json['data_total'] = $data_proses[2];

        echo json_encode($json);
    }

    public function jumlah_hari($tanggal1, $tanggal2)
    {
        $datediff = strtotime($tanggal2) - (strtotime($tanggal1));
        return round($datediff / (60 * 60 * 24));
    }

    public function proses_potga()
    {
        set_time_limit(0);

        $data_request = get_request();

        $this->load->model("master_model");

        $this->db->like("tgl_potga", ($data_request['tahun'] . "-" . $data_request['bulan']), "after")
            ->where("kd_potga !=", "11")
            ->delete("t_potga");

        $data_total = 5;

        $data_now = 1;

        /*potga simpanan wajib dan sukarela*/

        $persen = round(($data_now / $data_total) * 100);

        $this->cache->file->save('proses_potga_' . session_id(), "Proses Simpanan Pokok " . $persen . ";" . $data_now . ";" . $data_total);
        session_write_close();

        $query_potga_simpanan = "INSERT INTO t_potga
            (bukti_potga, tgl_potga, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_potga, nm_potga, jumlah, ket, is_pensiun)
            SELECT CONCAT('PG" . $data_request['bulan'] . $data_request['tahun'] . "', LPAD(@nomor:=@nomor+1, 5, '0')) bukti_potga, '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01' tgl_potga, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, '111', 'SIMPANAN POKOK', a.jml_simp_pokok, 'Simpanan Pokok',
                if(is_pensiun = '1' and (substr(tgl_pensiun, 1, 7) <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "' or tgl_pensiun is null), '1', '0') is_pensiun
            FROM t_anggota a,
            (
                SELECT @nomor:=ifnull(max(SUBSTRING(bukti_potga, -5)), 0) FROM t_potga WHERE bukti_potga LIKE 'PG" . $data_request['bulan'] . $data_request['tahun'] . "%'
            ) z
            WHERE tgl_potga_pokok like '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "%'";

        /*---*/
        $this->db->query($query_potga_simpanan);
        /*---*/

        $this->cache->file->save('proses_potga_' . session_id(), "Proses Simpanan Wajib & Sukarela " . $persen . ";" . $data_now . ";" . $data_total);
        session_write_close();

        $query_potga_simpanan = "INSERT INTO t_potga
            (bukti_potga, tgl_potga, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_potga, nm_potga, jml_wajib, jml_sukarela, jumlah, ket, is_pensiun)
            SELECT CONCAT('PG" . $data_request['bulan'] . $data_request['tahun'] . "', LPAD(@nomor:=@nomor+1, 5, '0')) bukti_potga, '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01' tgl_potga, a.no_ang, a.no_peg, a.nm_ang, a.kd_prsh, a.nm_prsh, a.kd_dep, a.nm_dep, a.kd_bagian, a.nm_bagian, '1', 'SIMPANAN', c.jml_wajib,
                ifnull(b.jumlah, 0) jumlah_ss1,
                (c.jml_wajib + ifnull(b.jumlah, 0)) jumlah, 'Simpanan',
                if(a.is_pensiun = '1' and (substr(a.tgl_pensiun, 1, 7) <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "' or a.tgl_pensiun is null), '1', '0') is_pensiun
            FROM (
                select * 
                from t_anggota a 
                where a.no_ang REGEXP \"^[0-9.]+$\" 
                    and (tgl_msk <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01' or tgl_msk is NULL)
                    and sts_instansi = '0'
                    and (a.status_keluar = '0' or (a.status_keluar = '1' and '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01' < a.tgl_keluar)) 
            ) a 
            LEFT JOIN
            (
                SELECT a.*
                FROM m_potga_ss1 a
                JOIN (
                    SELECT no_ang, max(tgl_masuk_ss1) max_tgl_masuk_ss1 FROM m_potga_ss1
                    WHERE CONCAT(tahun, '-', bulan) <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
                    GROUP BY no_ang
                ) b
                on a.no_ang=b.no_ang AND a.tgl_masuk_ss1 = b.max_tgl_masuk_ss1
            ) b
            ON a.no_ang=b.no_ang,
            (
                SELECT jml_wajib FROM k3pg_sp.m_pokok_wajib
                WHERE SUBSTRING(tgl_berlaku, 1, 7) <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
                ORDER by tgl_berlaku DESC
                LIMIT 1
            ) c,
            (
                SELECT @nomor:=ifnull(max(SUBSTRING(bukti_potga, -5)), 0)
                FROM t_potga WHERE bukti_potga LIKE 'PG" . $data_request['bulan'] . $data_request['tahun'] . "%'
            ) z
            ORDER BY a.no_ang";

        /*---*/
        $this->db->query($query_potga_simpanan);
        /*---*/

        $persen = round(($data_now / $data_total) * 100);

        // $this->cache->file->save('proses_potga_' . session_id(), "Proses Simpanan Wajib & Sukarela " . $persen . ";" . $data_now . ";" . $data_total);
        // session_write_close();

        /* potga pinjaman reguler dan pht */

        $this->cache->file->save('proses_potga_' . session_id(), "Proses Pinjaman Reguler dan PHT " . $persen . ";" . $data_now . ";" . $data_total);
        session_write_close();

        $query_potga_reg_pht = "INSERT INTO t_potga
            (bukti_potga, tgl_potga, tgl_rilis, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_potga, nm_potga, kd_piutang, kd_pinjaman, nm_pinjaman, no_ref_bukti, angs_ke, tempo_bln, jml_pokok, angsuran, jumlah, ket, saldo_akhir, is_pensiun)
            SELECT CONCAT('PG" . $data_request['bulan'] . $data_request['tahun'] . "', LPAD(@nomor:=@nomor+1, 5, '0')) bukti_potga, '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01' tgl_potga, a.tgl_pinjam, c.no_ang, c.no_peg, c.nm_ang, c.kd_prsh, c.nm_prsh, c.kd_dep, c.nm_dep, c.kd_bagian, c.nm_bagian, '2', 'PINJAMAN',
                if(a.kd_piutang is null or a.kd_piutang = '', 'U', a.kd_piutang) kd_piutang,
                a.kd_pinjaman, a.nm_pinjaman, a.no_pinjam, b.angs_ke, a.tempo_bln, a.jml_pinjam, a.angsuran, a.angsuran, CONCAT('Pinjaman Uang ', a.nm_pinjaman), b.pokok_akhir,
                if(c.is_pensiun = '1' and (substr(c.tgl_pensiun, 1, 7) <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "' or c.tgl_pensiun is null), '1', '0') is_pensiun
            FROM t_pinjaman_ang a JOIN t_pinjaman_ang_det b
            ON a.no_pinjam = b.no_pinjam
            left join t_anggota c on a.no_ang=c.no_ang,
            (SELECT @nomor:=ifnull(max(SUBSTRING(bukti_potga, -5)), 0) FROM t_potga WHERE bukti_potga LIKE 'PG" . $data_request['bulan'] . $data_request['tahun'] . "%') z
            WHERE b.sts_lunas = '0' AND b.blth_angsuran = '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
            and c.sts_instansi = '0'
            AND a.kd_pinjaman in ('1', '3')";

        /*---*/
        $this->db->query($query_potga_reg_pht);
        /*---*/

        $data_now++;
        $persen = round(($data_now / $data_total) * 100);

        // $this->cache->file->save('proses_potga_' . session_id(), "Proses Pinjaman Reguler dan PHT " . $persen . ";" . $data_now . ";" . $data_total);
        // session_write_close();

        $this->cache->file->save('proses_potga_' . session_id(), "Proses Pinjaman KKB dan KPR " . $persen . ";" . $data_now . ";" . $data_total);
        session_write_close();

        $array_potongan = array();

        if (isset($data_request['id']) and sizeof($data_request['id']) > 0) {
            foreach ($data_request['id'] as $key => $value) {
                $set_data = array(
                    "nm_pot_bonus"        => $data_request['nm_pot_bonus'][$key],
                    "banyak_min_angsuran" => $data_request['banyak_min_angsuran'][$key],
                    "banyak_max_angsuran" => $data_request['banyak_max_angsuran'][$key],
                );

                $this->db->set($set_data)->where("id", $value)->update("m_pot_bonus_pg");

                $array_potongan[$data_request['kd_prsh'][$key]] = $set_data;
            }
        }

        $query_hapus_detil_angsuran = "DELETE FROM t_pinjaman_ang_det
            WHERE
            no_pinjam in
            (
                SELECT no_pinjam FROM t_pinjaman_ang WHERE kd_pinjaman in ('2', '4') AND substr(tgl_pinjam, 1, 7) <
                '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
            )
            AND blth_angsuran >= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
            AND angs_ke > 1";

        $this->db->query($query_hapus_detil_angsuran);

        $blth_lalu      = date("Y-m", mktime(0, 0, 0, $data_request['bulan'] - 1, 1, $data_request['tahun']));
        $tgl_akhir_lalu = date("Y-m-t", mktime(0, 0, 0, $data_request['bulan'] - 1, 1, $data_request['tahun']));
        $tgl_akhir_skrg = date("Y-m-t", mktime(0, 0, 0, $data_request['bulan'], 1, $data_request['tahun']));

        $data_kkbkpr = $this->db->select("b.*, timestampdiff(day, '" . $tgl_akhir_lalu . "', '" . $tgl_akhir_skrg . "') hari, a.tempo_bln, a.margin, a.kd_prsh, a.jml_min_angsuran, a.jml_max_angsuran,  a.angsuran angs_potga")
            ->from("t_pinjaman_ang a")
            ->join("t_pinjaman_ang_det b", "a.no_pinjam=b.no_pinjam")
            ->where("b.blth_angsuran", $blth_lalu)
            ->where_in("a.kd_pinjaman", array("2", "4"))
            ->where("b.pokok_akhir >", 0)
            ->get();

        $data_kkbkpr_now   = 0;
        $data_kkbkpr_total = $data_kkbkpr->num_rows();

        foreach ($data_kkbkpr->result_array() as $key => $value) {
            $kecuali = array("XKPR17113050", "XKPR18063093", "XKPR18093130", "XKBPG16042636", "XKPR18063091", "XKPR17113049", "XKPR18073109", "XKPR17123052", "XKPR18073107", "XKPR18073110", "XKPR17123054", "XKPR18063092", "XKPR18073108");

            // if (in_array($value['no_pinjam'], $kecuali)) {
            //     continue;
            // }

            $persen_kkbkpr = round(($data_kkbkpr_now / $data_kkbkpr_total) * 100);

            $this->cache->file->save('proses_potga_' . session_id(), "Proses Pinjaman KKB dan KPR " . $persen_kkbkpr . ";" . $data_kkbkpr_now . ";" . $data_kkbkpr_total . " " . $value['no_pinjam']);
            session_write_close();

            $hari        = $value['hari'];
            $margin      = $value['margin'];
            $pokok_awal  = $value['pokok_akhir'];
            $pokok_akhir = $value['pokok_akhir'];
            $angs_ke     = $value['angs_ke'] + 1;

            $blth_angsuran = substr($tgl_akhir_skrg, 0, 7);
            $tahun         = date("Y", strtotime($tgl_akhir_skrg));
            $bulan         = date("m", strtotime($tgl_akhir_skrg));

            $tgl_awal = $tgl_akhir_skrg;
            $tgl_angs = $tgl_akhir_skrg;

            $tidak_ada_jadwal = 0;

            while (($pokok_akhir > 0)) {
                $blth_angsuran = substr($tgl_angs, 0, 7);
                $tahun         = date("Y", strtotime($tgl_angs));
                $bulan         = date("m", strtotime($tgl_angs));

                $banyak_min_angsuran = 0;
                $banyak_max_angsuran = 0;
                $nm_pot_bonus        = null;

                $data_pot_bonus = $this->master_model->get_pot_bonus_pg_berlaku($tahun, $bulan, $value['kd_prsh']);

                if ($data_pot_bonus->num_rows() > 0) {
                    $banyak_min_angsuran = $data_pot_bonus->row(0)->banyak_min_angsuran;
                    $banyak_max_angsuran = $data_pot_bonus->row(0)->banyak_max_angsuran;
                    $nm_pot_bonus        = $data_pot_bonus->row(0)->nm_pot_bonus;

                    $tidak_ada_jadwal = 0;
                } else {
                    $tidak_ada_jadwal++;
                }

                if ($value['angs_potga'] > 0) {
                    $nm_pot_bonus = "Potong Gaji, " . $nm_pot_bonus;
                }

                $jml_potga        = $value['angs_potga'];
                $jml_min_angsuran = ($banyak_min_angsuran * $value['jml_min_angsuran']);
                $jml_max_angsuran = ($banyak_max_angsuran * $value['jml_max_angsuran']);

                $angsuran_per_bulan = $value['angs_potga'] + ($banyak_min_angsuran * $value['jml_min_angsuran']) + ($banyak_max_angsuran * $value['jml_max_angsuran']);
                $margin_per_bulan   = $pokok_awal * ($margin / 100) * ($hari / 365);
                $pokok_akhir        = $pokok_awal + $margin_per_bulan - $angsuran_per_bulan;

                if ($pokok_akhir <= 0) {
                    $pokok_akhir        = 0;
                    $angsuran_per_bulan = $pokok_awal + $margin_per_bulan;

                    $xangsuran_per_bulan = $angsuran_per_bulan;

                    if (($xangsuran_per_bulan - $jml_potga) > 0) {
                        $xangsuran_per_bulan -= $jml_potga;

                        if (($xangsuran_per_bulan - $jml_min_angsuran) > 0) {
                            $xangsuran_per_bulan -= $jml_min_angsuran;

                            if (($xangsuran_per_bulan - $jml_max_angsuran) <= 0) {
                                $jml_max_angsuran = $xangsuran_per_bulan;
                            }

                        } else {
                            $jml_min_angsuran = $xangsuran_per_bulan;
                            $jml_max_angsuran = 0;
                        }
                    } else {
                        $jml_potga        = $xangsuran_per_bulan;
                        $jml_min_angsuran = 0;
                        $jml_max_angsuran = 0;
                    }
                }

                $set_data = array(
                    "no_pinjam_det"    => $value['no_pinjam'] . str_pad($angs_ke, 4, "0", STR_PAD_LEFT),
                    "no_pinjam"        => $value['no_pinjam'],
                    "hari"             => $hari,
                    "blth_angsuran"    => $blth_angsuran,
                    "bulan_angsuran"   => $bulan,
                    "tahun_angsuran"   => $tahun,
                    "angs_ke"          => $angs_ke,
                    "tempo_bln"        => $value['tempo_bln'],
                    "pokok_awal"       => $pokok_awal,
                    // "pokok"          => $value['pokok'],
                    "bunga"            => $margin_per_bulan,
                    "angsuran"         => $angsuran_per_bulan,
                    "jml_potga"        => $jml_potga,
                    "jml_min_angsuran" => $jml_min_angsuran,
                    "jml_max_angsuran" => $jml_max_angsuran,
                    "pokok_akhir"      => $pokok_akhir,
                    "nm_pot_bonus"     => $nm_pot_bonus,
                );

                $this->db->set($set_data)->insert("t_pinjaman_ang_det");

                $angs_ke++;

                if (($tidak_ada_jadwal >= 5) and ($angs_ke > $value['tempo_bln'])) {
                    break;
                }

                $tgl_awal = $tgl_angs;
                $tgl_angs = date("Y-m-t", mktime(0, 0, 0, $bulan + 1, 1, $tahun));
                $hari     = $this->jumlah_hari($tgl_awal, $tgl_angs);

                $pokok_awal = $pokok_akhir;
            }

            $data_kkbkpr_now++;

            $persen_kkbkpr = round(($data_kkbkpr_now / $data_kkbkpr_total) * 100);

            $this->cache->file->save('proses_potga_' . session_id(), "Proses Pinjaman KKB dan KPR " . $persen_kkbkpr . ";" . $data_kkbkpr_now . ";" . $data_kkbkpr_total . " " . $value['no_pinjam']);
            session_write_close();

            /* ---- */
        }

        $query_potga_kkb_kpr = "
            INSERT INTO t_potga
            (bukti_potga, tgl_potga, tgl_rilis, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_potga, nm_potga, kd_piutang, kd_pinjaman, nm_pinjaman, no_ref_bukti, angs_ke, tempo_bln, jml_pokok, angsuran, jumlah, ket, saldo_akhir, is_pensiun)
            SELECT CONCAT('PG" . $data_request['bulan'] . $data_request['tahun'] . "', LPAD(@nomor:=@nomor+1, 5, '0')) bukti_potga, '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01' tgl_potga, a.tgl_pinjam, c.no_ang, c.no_peg, c.nm_ang, c.kd_prsh, c.nm_prsh, c.kd_dep, c.nm_dep, c.kd_bagian, c.nm_bagian, '2', 'PINJAMAN',
                if(a.kd_piutang is null or a.kd_piutang = '', 'U', a.kd_piutang) kd_piutang,
                a.kd_pinjaman, a.nm_pinjaman, a.no_pinjam, b.angs_ke, a.tempo_bln, a.jml_pinjam, b.jml_potga, b.jml_potga, CONCAT('Pinjaman Uang ', a.nm_pinjaman), b.pokok_akhir,
                if(c.is_pensiun = '1' and (substr(c.tgl_pensiun, 1, 7) <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "' or c.tgl_pensiun is null), '1', '0') is_pensiun
            FROM t_pinjaman_ang a JOIN t_pinjaman_ang_det b
            ON a.no_pinjam = b.no_pinjam
            left join t_anggota c on a.no_ang=c.no_ang,
            (SELECT @nomor:=ifnull(max(SUBSTRING(bukti_potga, -5)), 0) FROM t_potga WHERE bukti_potga LIKE 'PG" . $data_request['bulan'] . $data_request['tahun'] . "%') z
            WHERE b.sts_lunas = '0' AND b.blth_angsuran = '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
            AND a.kd_pinjaman in ('2', '4')
            AND b.jml_potga > 0
        ";

        /*---*/
        $this->db->query($query_potga_kkb_kpr);
        /*---*/

        $query_potga_kkb_kpr_bonus = "
            INSERT INTO t_potga
            (bukti_potga, tgl_potga, tgl_rilis, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_potga, nm_potga, kd_piutang, kd_pinjaman, nm_pinjaman, no_ref_bukti, angs_ke, tempo_bln, jml_pokok, jml_min_angsuran, jml_max_angsuran, jumlah, ket, saldo_akhir, is_pot_bonus, is_pensiun)
            SELECT CONCAT('PG" . $data_request['bulan'] . $data_request['tahun'] . "', LPAD(@nomor:=@nomor+1, 5, '0')) bukti_potga, '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01' tgl_potga, a.tgl_pinjam, a.no_ang, c.no_peg, c.nm_ang, c.kd_prsh, c.nm_prsh, c.kd_dep, c.nm_dep, c.kd_bagian, c.nm_bagian, '2', 'PINJAMAN',
                if(a.kd_piutang is null or a.kd_piutang = '', 'U', a.kd_piutang) kd_piutang,
                a.kd_pinjaman, a.nm_pinjaman, a.no_pinjam, b.angs_ke, a.tempo_bln, a.jml_pinjam, b.jml_min_angsuran, b.jml_max_angsuran, (b.jml_min_angsuran+b.jml_max_angsuran), CONCAT('Pinjaman Uang ', a.nm_pinjaman), b.pokok_akhir, '1',
                if(c.is_pensiun = '1' and (substr(c.tgl_pensiun, 1, 7) <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "' or c.tgl_pensiun is null), '1', '0') is_pensiun
            FROM t_pinjaman_ang a JOIN t_pinjaman_ang_det b
            ON a.no_pinjam = b.no_pinjam
            left join t_anggota c on a.no_ang=c.no_ang,
            (SELECT @nomor:=ifnull(max(SUBSTRING(bukti_potga, -5)), 0) FROM t_potga WHERE bukti_potga LIKE 'PG" . $data_request['bulan'] . $data_request['tahun'] . "%') z
            WHERE b.sts_lunas = '0' AND b.blth_angsuran = '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
            AND a.kd_pinjaman in ('2', '4')
            AND (b.jml_min_angsuran > 0 or b.jml_max_angsuran > 0)
        ";

        $this->db->query($query_potga_kkb_kpr_bonus);

        $data_now++;
        $persen = round(($data_now / $data_total) * 100);

        // $this->cache->file->save('proses_potga_' . session_id(), "Proses Pinjaman KKB dan KPR " . $persen . ";" . $data_now . ";" . $data_total);
        // session_write_close();

        /* potga bridging plafon */

        $this->cache->file->save('proses_potga_' . session_id(), "Proses Bridging Plafon " . $persen . ";" . $data_now . ";" . $data_total);
        session_write_close();

        $query_potga_bp = "INSERT INTO t_potga
            (bukti_potga, tgl_potga, tgl_rilis, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_potga, nm_potga, kd_piutang, no_ref_bukti, angs_ke, tempo_bln, jml_pokok, angsuran, jumlah, ket, is_pensiun)
            SELECT CONCAT('PG" . $data_request['bulan'] . $data_request['tahun'] . "', LPAD(@nomor:=@nomor+1, 5, '0')) bukti_potga, '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01' tgl_potga, a.tgl_trans, c.no_ang, c.no_peg, c.nm_ang, c.kd_prsh, c.nm_prsh, c.kd_dep, c.nm_dep, c.kd_bagian, c.nm_bagian, '3', 'BP',
                if(a.kd_piutang is null or a.kd_piutang = '', 'E', a.kd_piutang) kd_piutang,
                a.no_trans, b.angs_ke, a.tempo_bln, a.jml_trans, a.angsuran, a.angsuran, a.ket,
                if(c.is_pensiun = '1' and (substr(c.tgl_pensiun, 1, 7) <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "' or c.tgl_pensiun is null), '1', '0') is_pensiun
            FROM t_bridging_plafon a JOIN t_bridging_plafon_det b
            ON a.no_trans = b.no_trans
            join t_anggota c on a.no_ang=c.no_ang,
            (SELECT @nomor:=ifnull(max(SUBSTRING(bukti_potga, -5)), 0) FROM t_potga WHERE bukti_potga LIKE 'PG" . $data_request['bulan'] . $data_request['tahun'] . "%') z
            WHERE b.sts_lunas = '0' AND b.blth_angsuran = '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "' and c.sts_instansi = '0'";

        /*---*/
        $this->db->query($query_potga_bp);
        /*---*/

        $data_now++;
        $persen = round(($data_now / $data_total) * 100);

        // $this->cache->file->save('proses_potga_' . session_id(), "Proses Bridging Plafon " . $persen . ";" . $data_now . ";" . $data_total);
        // session_write_close();

        /* potga toko swalayan */

        $this->cache->file->save('proses_potga_' . session_id(), "Proses Data Toko " . $persen . ";" . $data_now . ";" . $data_total);
        session_write_close();

        $query_potga_toko = "INSERT INTO t_potga
            (bukti_potga, tgl_potga, tgl_rilis, no_ang, no_peg, nm_ang, kd_prsh, nm_prsh, kd_dep, nm_dep, kd_bagian, nm_bagian, kd_potga, nm_potga, kd_piutang, no_ref_bukti, angs_ke, tempo_bln, jml_pokok, angsuran, jumlah, ket, is_pensiun)
            SELECT CONCAT('PG" . $data_request['bulan'] . $data_request['tahun'] . "', LPAD(@nomor:=@nomor+1, 5, '0')) bukti_potga, '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01' tgl_potga, b.tgl, a.no_ang, a.no_peg, a.nm_ang, a.kd_prsh, a.nm_prsh, a.kd_dep, a.nm_dep, a.kd_bagian, a.nm_bagian, b.kd_potga, b.nm_potga, 'B' kd_piutang, b.bukti, b.angs_ke, b.tempo_bln, b.jml_pokok, b.jumlah, b.jumlah, b.ket,
                if(a.is_pensiun = '1' and (substr(a.tgl_pensiun, 1, 7) <= '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "' or a.tgl_pensiun is null), '1', '0') is_pensiun
            FROM t_anggota a
            JOIN
            (
                SELECT CONCAT(ref_penjualan, toko_kode) bukti, date(tanggal) tgl, pelanggan_kode nak, 1 tempo_bln, 1 angs_ke, jatuh_tempo, jumlah jml_pokok, jumlah, 'Belanja Kredit Buku' ket, '31' kd_potga, 'BELANJAKREDIT' nm_potga
                FROM db_wecode_smart.piutang
                WHERE SUBSTRING(date(tanggal), 1, 7) = '" . $blth_lalu . "' and is_entri_lunas = '0'
            UNION
                SELECT CONCAT(ref_penjualan, toko_kode) bukti, date(tanggal) tgl, pelanggan_kode nak, 1 tempo_bln, 1 angs_ke, jatuh_tempo, jumlah jml_pokok, jumlah, 'Belanja Kredit Buku' ket, '31' kd_potga, 'BELANJAKREDIT' nm_potga
                FROM db_bengkel.piutang
                WHERE SUBSTRING(date(tanggal), 1, 7) = '" . $blth_lalu . "' and is_entri_lunas = '0'
            UNION
                SELECT CONCAT(ref_penjualan, toko_kode) bukti, date(tanggal) tgl, pelanggan_kode nak, 1 tempo_bln, 1 angs_ke, jatuh_tempo, jumlah jml_pokok, jumlah, 'Belanja Kredit Buku' ket, '31' kd_potga, 'BELANJAKREDIT' nm_potga
                FROM db_pbb.piutang
                WHERE SUBSTRING(date(tanggal), 1, 7) = '" . $blth_lalu . "' and is_entri_lunas = '0'
            UNION
                SELECT CONCAT(ref_bukti_bo, kd_toko), tanggal, noang, ang_bulan, timestampdiff(month, CONCAT(SUBSTRING(tanggal, 1, 7), '-01'), '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01') angs_ke, tgl_jth_tempo, pokok_kredit, a.angs_perbulan, d.nama_barang, '32' kd_potga, 'BELANJAANGSURAN' nm_potga
                FROM db_wecode_smart.t_kredit_anggota a
                JOIN db_wecode_smart.t_kredit_anggota_det b on a.ref_bukti_bo=b.no_pinjam
                join db_wecode_smart.rst_fc_trans_detail c on a.ref_bukti_bo = c.fcode AND a.kd_toko=c.flokasi
                join db_wecode_smart.barang d on c.fitemkey = d.kode
                WHERE SUBSTRING(tanggal, 1, 7) < '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
                    and c.fstatuskey = '1'
                    AND CONCAT(b.tahun_angsuran,'-',LPAD(b.bulan_angsuran, 2, '0')) = '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
                    AND b.sts_lunas = 0 
            UNION
                SELECT CONCAT(ref_bukti_bo, kd_toko), tanggal, noang, ang_bulan, timestampdiff(month, CONCAT(SUBSTRING(tanggal, 1, 7), '-01'), '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01') angs_ke, tgl_jth_tempo, pokok_kredit, a.angs_perbulan, d.nama_barang, '32' kd_potga, 'BELANJAANGSURAN' nm_potga
                FROM db_bengkel.t_kredit_anggota a
                JOIN db_bengkel.t_kredit_anggota_det b on a.ref_bukti_bo=b.no_pinjam
                join db_bengkel.rst_fc_trans_detail c on a.ref_bukti_bo = c.fcode AND a.kd_toko=c.flokasi
                join db_bengkel.barang d on c.fitemkey = d.kode
                WHERE SUBSTRING(tanggal, 1, 7) < '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
                    and c.fstatuskey = '1'
                    AND CONCAT(b.tahun_angsuran,'-',LPAD(b.bulan_angsuran, 2, '0')) = '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
                    AND b.sts_lunas = 0 
            UNION
                SELECT CONCAT(ref_bukti_bo, kd_toko), tanggal, noang, ang_bulan, timestampdiff(month, CONCAT(SUBSTRING(tanggal, 1, 7), '-01'), '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "-01') angs_ke, tgl_jth_tempo, pokok_kredit, a.angs_perbulan, d.nama_barang, '32' kd_potga, 'BELANJAANGSURAN' nm_potga
                FROM db_pbb.t_kredit_anggota a
                JOIN db_pbb.t_kredit_anggota_det b on a.ref_bukti_bo=b.no_pinjam
                join db_pbb.rst_fc_trans_detail c on a.ref_bukti_bo = c.fcode AND a.kd_toko=c.flokasi
                join db_pbb.barang d on c.fitemkey = d.kode
                WHERE SUBSTRING(tanggal, 1, 7) < '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
                    and c.fstatuskey = '1'
                    AND CONCAT(b.tahun_angsuran,'-',LPAD(b.bulan_angsuran, 2, '0')) = '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
                    AND b.sts_lunas = 0
            ) b 
            ON a.no_ang = b.nak,
            (SELECT @nomor:=ifnull(max(SUBSTRING(bukti_potga, -5)), 0) FROM t_potga WHERE bukti_potga LIKE 'PG" . $data_request['bulan'] . $data_request['tahun'] . "%') z
            where a.sts_instansi = '0'";

        // baca($query_potga_toko);

        /*---*/
        $this->db->query($query_potga_toko);
        /*---*/

        $data_now++;
        $persen = round(($data_now / $data_total) * 100);

        $this->cache->file->save('proses_potga_' . session_id(), "Proses Data Toko " . $persen . ";" . $data_now . ";" . $data_total);
        session_write_close();

        /*hapus data yang sudah dilunasi pada periode potga yang dipilih*/

        $query_hapus = "
            delete from t_potga
            where no_ref_bukti in
                (SELECT CONCAT(no_ref_bukti, ifnull(kd_toko, '')) bukti
                FROM k3pg_sp.t_pelunasan
                WHERE blth_angsuran = '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "')
                and substr(tgl_potga, 1, 7) = '" . $data_request['tahun'] . "-" . $data_request['bulan'] . "'
            ";

        $this->db->query($query_hapus);

        echo "<h5>Tagihan Potga Selesai Diproses</h5>";
    }

    public function add_data_potga()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $data_post['tgl_potga'] = balik_tanggal($data_post['tgl_potga']);

            $insert = $this->potga_model->insert_data_potga($data_post);

            if ($insert) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Ditambah";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Ditambah";
            }

            echo json_encode($hasil);
        }
    }

    public function del_data_potga()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $delete = $this->db->where("bukti_potga", $data_post['bukti_potga'])->delete("t_potga");

            if ($delete) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Dihapus";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Dihapus";
            }

            echo json_encode($hasil);
        }

    }

    public function get_jadwal_kkbkpr()
    {
        $data_post = $this->input->post();

        if ($data_post) {
            $query_jadwal = "REPLACE INTO m_pot_bonus_pg
                (id, kd_prsh, nm_prsh, tahun, bulan, nm_pot_bonus, banyak_min_angsuran, banyak_max_angsuran)
                SELECT b.id, a.kd_prsh, a.nm_prsh, '" . $data_post['tahun'] . "' tahun, a.bulan,
                    if(b.id is null or b.id = '', a.nm_pot_bonus, b.nm_pot_bonus) nm_pot_bonus,
                    if(b.id is null or b.id = '', a.banyak_min_angsuran, b.banyak_min_angsuran) banyak_min_angsuran,
                    if(b.id is null or b.id = '', a.banyak_max_angsuran, b.banyak_max_angsuran) banyak_max_angsuran
                FROM
                (
                    SELECT id, kd_prsh, nm_prsh, tahun, bulan, nm_pot_bonus, banyak_min_angsuran, banyak_max_angsuran, is_jadwal_tetap
                    FROM k3pg_sp.m_pot_bonus_pg
                    WHERE is_jadwal_tetap = '1'
                    AND bulan = '" . $data_post['bulan'] . "'
                ) a
                left JOIN
                (
                    SELECT id, kd_prsh, nm_prsh, tahun, bulan, nm_pot_bonus, banyak_min_angsuran, banyak_max_angsuran, is_jadwal_tetap
                    FROM k3pg_sp.m_pot_bonus_pg
                    WHERE is_jadwal_tetap = '0'
                    AND bulan = '" . $data_post['bulan'] . "'
                    AND tahun = '" . $data_post['tahun'] . "'
                ) b
                ON a.kd_prsh = b.kd_prsh
            ";

            $this->db->query($query_jadwal);
        }
    }

    public function get_jadwal_potong_kkbkpr($tahun, $bulan, $kd_prsh)
    {
        $query_jadwal = "SELECT b.id, a.kd_prsh, a.nm_prsh, '" . $tahun . "' tahun, a.bulan,
                if(b.id is null or b.id = '', a.nm_pot_bonus, b.nm_pot_bonus) nm_pot_bonus,
                if(b.id is null or b.id = '', a.banyak_min_angsuran, b.banyak_min_angsuran) banyak_min_angsuran,
                if(b.id is null or b.id = '', a.banyak_max_angsuran, b.banyak_max_angsuran) banyak_max_angsuran
            FROM
            (
                SELECT id, kd_prsh, nm_prsh, tahun, bulan, nm_pot_bonus, banyak_min_angsuran, banyak_max_angsuran, is_jadwal_tetap
                FROM k3pg_sp.m_pot_bonus_pg
                WHERE is_jadwal_tetap = '1'
                AND bulan = '" . $bulan . "'
                AND kd_prsh = '" . $kd_prsh . "'
            ) a
            left JOIN
            (
                SELECT id, kd_prsh, nm_prsh, tahun, bulan, nm_pot_bonus, banyak_min_angsuran, banyak_max_angsuran, is_jadwal_tetap
                FROM k3pg_sp.m_pot_bonus_pg
                WHERE is_jadwal_tetap = '0'
                AND bulan = '" . $bulan . "'
                AND tahun = '" . $tahun . "'
            ) b
            ON a.kd_prsh = b.kd_prsh";

        return $this->db->query($query_jadwal);
    }
}
