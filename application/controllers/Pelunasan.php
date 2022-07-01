<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pelunasan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->login_model->cek_login();

        $this->load->model("pinjaman_model");
        $this->load->model("master_model");
    }

    public function index($page)
    {
        $strtime_blnini = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));

        $bulan_depan = date("m", $strtime_blnini);
        $tahun_depan = date("Y", $strtime_blnini);

        $opt_bulan = "";

        foreach (array_bulan() as $key => $value) {
            $opt_bulan .= "<option value=\"" . $key . "\" ";
            if ($key == $bulan_depan) {
                $opt_bulan .= "selected";
            }
            $opt_bulan .= ">" . $value . "</option>";
        }

        $bulan = get_option_tag(array_bulan(), "BULAN");

        if ($page == "pelunasan-dipercepat") {
            $data['judul_menu']     = "Pelunasan Dipercepat";
            $data['bulan_angsuran'] = $opt_bulan;
            $data['tahun_angsuran'] = $tahun_depan;
            $data['bulan']          = $bulan;

            $this->template->view("pelunasan/pelunasan_dipercepat", $data);
        }
    }

    public function get_pinjaman_belum_lunas()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $no_ang = (isset($data['no_ang']) and $data['no_ang'] != "") ? $data['no_ang'] : "xxx";
        $tahun  = isset($data['tahun']) ? $data['tahun'] : date('Y');
        $bulan  = isset($data['bulan']) ? $data['bulan'] : date('m');

        $strtime_blnini    = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $strtime_blthinput = mktime(0, 0, 0, $bulan, 1, $tahun);
        $strtime_blndepan    = mktime(0, 0, 0, date("m")+1, 1, date("Y"));

        if ($strtime_blthinput < $strtime_blnini or $strtime_blthinput > $strtime_blndepan) {
            $array['recordsTotal']    = 0;
            $array['recordsFiltered'] = 0;
            $array['data']            = array();

            exit(json_encode($array));
        }

        $data_item = $this->pinjaman_model->get_pinjaman_belum_lunas($no_ang, $tahun, $bulan);

        $array['recordsTotal']    = $data_item->num_rows();
        $array['recordsFiltered'] = $array['recordsTotal'];
        $array['data']            = $data_item->result_array();

        echo json_encode($array);
    }

    public function get_pinjaman_belum_lunas_cetak()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $no_ang = (isset($data['no_ang']) and $data['no_ang'] != "") ? $data['no_ang'] : "xxx";
        $tahun  = isset($data['tahun']) ? $data['tahun'] : date('Y');
        $bulan  = isset($data['bulan']) ? $data['bulan'] : date('m');

        $strtime_blnini    = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $strtime_blthinput = mktime(0, 0, 0, $bulan, 1, $tahun);
        $strtime_blndepan    = mktime(0, 0, 0, date("m")+1, 1, date("Y"));

        if ($strtime_blthinput < $strtime_blnini or $strtime_blthinput > $strtime_blndepan) {
            $array['recordsTotal']    = 0;
            $array['recordsFiltered'] = 0;
            $array['data']            = array();

            exit(json_encode($array));
        }

        $data_item = $this->pinjaman_model->get_pinjaman_belum_lunas($no_ang, $tahun, $bulan, "cetak");

        $array['recordsTotal']    = $data_item->num_rows();
        $array['recordsFiltered'] = $array['recordsTotal'];
        $array['data']            = $data_item->result_array();

        echo json_encode($array);
    }

    public function pelunasan_dipercepat_ganda()
    {
        $data_post = get_request('post');

        if ($data_post) {
            // baca_array($data_post);exit();

            $query = $this->pinjaman_model->pelunasan_dipercepat_ganda($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diproses";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diproses";
            }

            echo json_encode($hasil);
        }
    }

    public function hapus_pelunasan_dipercepat()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $query = $this->pinjaman_model->hapus_pelunasan_dipercepat($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Dihapus";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Dihapus";
            }

            echo json_encode($hasil);
        }
    }

    public function get_var_pelunasan()
    {
        $data_post = get_request('post');

        $html = "";

        // baca_array($data_post);

        if ($data_post) {
            if ($data_post['kd_pinjaman'] == '1' or $data_post['is_sparepart'] == '1') {
                $data_post['tgl_ref_bukti']  = $data_post['tgl_pinjam'];
                $data_post['jml_margin_bln'] = $data_post['bunga'];
                $data_post['jml_pokok']      = $data_post['jml_pinjam'];
                $data_post['jml_admin']      = $data_post['jml_biaya_admin'];
                $data_post['jml_bunga']      = $data_post['jml_margin'];

                $data_tempo_bln = $data_post['tempo_bln'];

                foreach ($this->pinjaman_model->get_array_tempo_bln(1) as $key => $value) {
                    if ($data_post['tempo_bln'] <= $value) {
                        $data_tempo_bln = $value;
                    }
                }

                $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, date("Y-m-d"));

                if ($data_margin_pinjaman->num_rows() > 0) {
                    $data_post['bunga'] = $data_margin_pinjaman->row(0)->rate;
                }

                $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data_post);

                // baca_array($perhitungan);

                if ($perhitungan['perhitungan_selisih'] > 0) {
                    $judul_pelunasan = "";

                    if ($data_post['kd_pinjaman'] == '1') {
                        $judul_pelunasan = 'Pinjaman Uang Reguler';
                    }

                    if ($data_post['is_sparepart'] == '1') {
                        $judul_pelunasan = 'Sparepart';
                    }

                    $html .= "<h4>Pembayaran " . $judul_pelunasan . "</h4>
                        <table class=\"table table-condensed table-striped\">
                            <tr>
                                <td width=\"200px\">Tanggal</td>
                                <td>" . $data_post['tgl_pinjam'] . "</td>
                            </tr>
                            <tr>
                                <td>Jml. Pokok</td>
                                <td>" . number_format($data_post['jml_pinjam'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Jml. Biaya Admin</td>
                                <td>" . number_format($data_post['jml_biaya_admin'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Bunga/Tahun (%)</td>
                                <td>" . $data_post['bunga'] . "</td>
                            </tr>
                            <tr>
                                <td>Bunga/Bulan (%)</td>
                                <td>" . number_format($perhitungan['bunga_bln'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Jangka (Bulan)</td>
                                <td>" . $data_post['tempo_bln'] . "</td>
                            </tr>
                            <tr>
                                <td>Sudah Diangsur</td>
                                <td>" . $perhitungan['sudah_diangsur'] . "</td>
                            </tr>";

                    if ($data_post['sisa_bln'] < $data_post['tempo_bln']) {
                        $html .= "
                            <tr>
                                <td>Jml. Angsuran/Bulan</td>
                                <td>" . number_format($data_post['angsuran'], 2) . "</td>
                            </tr>";
                    } else {
                        $html .= "
                            <tr>
                                <td>Masa</td>
                                <td>" . $perhitungan['jml_hari'] . "</td>
                            </tr>";
                    }

                    $html .= "
                        </table>
                        <table class=\"table table-condensed table-striped\">
                            <tr>
                                <th colspan=\"4\">Perhitungan Lama</th>
                            </tr>
                            <tr>
                                <td>Pokok Pinjaman</td>
                                <td style=\"text-align: right\">Rp " . number_format($data_post['jml_pinjam'] + $data_post['jml_biaya_admin'], 2) . " / " . $data_post['tempo_bln'] . " = Rp </td>
                                <td style=\"text-align: right\">" . number_format(($data_post['jml_pinjam'] + $data_post['jml_biaya_admin']) / $data_post['tempo_bln'], 2) . "</td>
                                <td style=\"text-align: right\"></td>
                            </tr>
                            <tr>
                                <td>Bunga</td>
                                <td style=\"text-align: right\">Rp " . number_format($data_post['jml_margin'], 2) . " / " . $data_post['tempo_bln'] . " = Rp </td>
                                <td style=\"text-align: right\">" . number_format($data_post['jml_margin'] / $data_post['tempo_bln'], 2) . "</td>
                                <td style=\"text-align: right\"></td>
                            </tr>
                            <tr>
                                <td>Jumlah</td>
                                <td style=\"text-align: right\">Rp " . number_format(($data_post['jml_pinjam'] + $data_post['jml_biaya_admin']) + $data_post['jml_margin'], 2) . " / " . $data_post['tempo_bln'] . " = Rp  </td>
                                <td style=\"text-align: right\">" . number_format((($data_post['jml_pinjam'] + $data_post['jml_biaya_admin']) + $data_post['jml_margin']) / $data_post['tempo_bln'], 2) . "</td>
                                <td style=\"text-align: left\"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            ";

                    if ($data_post['sisa_bln'] < $data_post['tempo_bln']) {
                        $html .= "<tr>
                                <td>Sudah Diangsur</td>
                                <td style=\"text-align: center\">" . $perhitungan['sudah_diangsur'] . " x </td>
                                <td style=\"text-align: right\">" . number_format($data_post['angsuran'], 2) . "</td>
                                <td>= Rp " . number_format($perhitungan['sudah_diangsur'] * $data_post['angsuran'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Sisa Angsuran</td>
                                <td style=\"text-align: center\">" . $data_post['sisa_bln'] . " x </td>
                                <td style=\"text-align: right\">" . number_format($data_post['angsuran'], 2) . "</td>
                                <td>= <strong>Rp " . number_format($perhitungan['sisa_angs_lama'], 2) . "</strong></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan=\"4\">Perhitungan Baru</th>
                            </tr>
                            <tr>
                                <td>Sisa Pokok Pinjaman</td>
                                <td style=\"text-align: center\">" . $data_post['sisa_bln'] . " x </td>
                                <td style=\"text-align: right\">" . number_format($perhitungan['jml_pokok_bln'], 2) . "</td>

                                <td>= Rp " . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Sisa Angsuran yg harus dibayar</td>
                                <td style=\"text-align: center\">( 1 + " . number_format($perhitungan['bunga_bln'], 2) . "% )<sup>" . $perhitungan['sudah_diangsur'] . "</sup> x </td>
                                <td style=\"text-align: right\">" . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                                <td>= <strong>Rp " . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                            </tr>
                            <tr>
                                <td colspan=\"4\"></td>
                            </tr>
                            <tr>
                                <td colspan=\"4\"></td>
                            </tr>";
                    } else {
                        $html .= "<tr>
                                <th colspan=\"4\">Perhitungan Baru</th>
                            </tr>
                            <tr>
                                <td>Pokok Pinjaman</td>
                                <td style=\"text-align: center\"></td>
                                <td style=\"text-align: right\"></td>
                                <td>= Rp " . number_format($data_post['jml_pinjam'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Biaya Admin</td>
                                <td style=\"text-align: center\"></td>
                                <td style=\"text-align: right\"></td>
                                <td>= Rp " . number_format($data_post['jml_biaya_admin'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Bunga Harian</td>
                                <td style=\"text-align: center;border-bottom: 1px black;\" colspan=\"2\">( " . number_format($data_post['jml_pinjam'], 2) . " x " . $data_post['margin'] . "% ) x " . $perhitungan['jml_hari'] . " </td>
                                <td>= Rp " . number_format($perhitungan['jml_bunga_harian'], 2) . "</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style=\"text-align: center\" colspan=\"2\">360</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan=\"3\">Angsuran yang harus dibayar</td>
                                <td style=\"text-align: left\"><strong>Rp " . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                            </tr>
                            <tr>
                                <td colspan=\"4\"></td>
                            </tr>";
                    }

                    $html .= "<tr>
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
                    $html .= "<table class=\"table table-bordered table-condensed table-striped\">
                        <thead>
                            <tr>
                                <th>Jangka</th>
                                <th>Sisa Waktu</th>
                                <th>Angsuran per Bulan</th>
                                <th>Jml Sisa Angsuran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>" . $data_post['tempo_bln'] . "</th>
                                <th>" . $data_post['sudah_diangsur'] . "</th>
                                <th>" . number_format($data_post['angsuran'], 2) . "</th>
                                <th>" . number_format($data_post['posisi_akhir'], 2) . "</th>
                            </tr>
                        </tbody>
                    </table>";
                }

            } else if (in_array($data_post['kd_pinjaman'], array("2", "4"))) {
                $html .= "<h4>Pembayaran Pinjaman Uang KKB/KPR</h4>
                <table class=\"table table-bordered table-condensed table-striped form-inline\">
                    <tbody>
                        <tr>
                            <td>1.</td>
                            <td>Posisi Akhir</td>
                            <td>
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">Rp</div>
                                    <input type=\"text\" name=\"jml_sisa_angsuran\" id=\"jml_sisa_angsuran\" class=\"form-control number_format text-right\" readonly=\"\" value=\"" . number_format($data_post['posisi_akhir'], 2) . "\">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2.</td>
                            <td>Denda => Posisi Akhir x
                                <div class=\"input-group\">
                                    <input type=\"text\" name=\"persen_denda\" id=\"persen_denda\" class=\"form-control\" onchange=\"hitung_total()\" size=\"2\">
                                    <div class=\"input-group-addon\">%</div>
                                </div></td>
                            <td>
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">Rp</div>
                                    <input type=\"text\" name=\"jml_denda\" id=\"jml_denda\" class=\"form-control number_format text-right\" readonly=\"\" value=\"0\">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3.</td>
                            <td>Asuransi => Posisi Akhir x <input type=\"text\" name=\"sisa_bln\" id=\"sisa_bln\" class=\"form-control\" onchange=\"hitung_total()\" size=\"2\" value=\"" . $data_post['sisa_bln'] . "\" > Bulan x <div class=\"input-group\">
                                    <input type=\"text\" name=\"persen_asuransi\" id=\"persen_asuransi\" class=\"form-control\" onchange=\"hitung_total()\" size=\"2\" >
                                    <div class=\"input-group-addon\">%</div>
                                </div></td>
                            <td>
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">Rp</div>
                                    <input type=\"text\" name=\"jml_asuransi\" id=\"jml_asuransi\" class=\"form-control number_format text-right\" readonly=\"\" value=\"0\">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan=\"2\">Jumlah Bayar</td>
                            <td>
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">Rp</div>
                                    <input type=\"text\" name=\"jml_bayar\" id=\"jml_bayar\" class=\"form-control number_format text-right input-lg text-bold\" readonly=\"\" value=\"0\">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <script type=\"text/javascript\">
                $(\".number_format\").on(\"change\", function() { $(this).val(number_format($(this).val(), 2)); });

                $(\"#fm_pelunasan\").validate().destroy();

                $(\"#fm_pelunasan\").validate({
                    rules: {
                        persen_denda: {
                            required: true,
                            number: true
                        },
                        persen_asuransi: {
                            required: true,
                            number: true
                        }
                    }
                });

                function hitung_total() {
                    var_jml_angsuran = parseFloat(hapus_koma($(\"#jml_sisa_angsuran\").val()));
                    var_sisa_bln = parseFloat(hapus_koma($(\"#sisa_bln\").val()));
                    var_persen_denda = $(\"#persen_denda\").val();
                    var_jml_denda = var_jml_angsuran * var_persen_denda / 100;
                    var_persen_asuransi = $(\"#persen_asuransi\").val();
                    var_jml_asuransi = var_jml_angsuran * var_sisa_bln * (var_persen_asuransi/100)
                    var_jml_bayar = var_jml_angsuran + var_jml_denda + var_jml_asuransi;

                    $(\"#jml_denda\").val(var_jml_denda).trigger(\"change\");
                    $(\"#jml_asuransi\").val(var_jml_asuransi).trigger(\"change\");
                    $(\"#jml_bayar\").val(var_jml_bayar).trigger(\"change\");
                }
                </script>
                ";
            } else if ($data_post['kd_pinjaman'] == "3") {
                $html .= "<h4>Pembayaran Pinjaman Uang PHT</h4>
                    <table class=\"table table-bordered table-condensed table-striped form-inline\">
                        <tbody>
                            <tr>
                                <td>1.</td>
                                <td>Pokok Pinjaman</td>
                                <td>
                                    <div class=\"input-group\">
                                        <div class=\"input-group-addon\">Rp</div>
                                        <input type=\"text\" name=\"jml_pokok\" id=\"jml_pokok\" class=\"form-control number_format text-right\" readonly=\"\" value=\"" . number_format($data_post['jml_pinjam'], 2) . "\">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Denda => Pokok Pinjaman x
                                    <div class=\"input-group\">
                                        <input type=\"text\" name=\"persen_denda\" id=\"persen_denda\" class=\"form-control\" onchange=\"hitung_total()\" size=\"2\">
                                        <div class=\"input-group-addon\">%</div>
                                    </div></td>
                                <td>
                                    <div class=\"input-group\">
                                        <div class=\"input-group-addon\">Rp</div>
                                        <input type=\"text\" name=\"jml_denda\" id=\"jml_denda\" class=\"form-control number_format text-right\" readonly=\"\" value=\"0\">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Bunga 1 Bulan</td>
                                <td>
                                    <div class=\"input-group\">
                                        <div class=\"input-group-addon\">Rp</div>
                                        <input type=\"text\" name=\"jml_bunga_1bulan\" id=\"jml_bunga_1bulan\" class=\"form-control number_format text-right\" readonly=\"\" value=\"" . number_format($data_post['bunga'], 2) . "\">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=\"2\">Jumlah Bayar</td>
                                <td>
                                    <div class=\"input-group\">
                                        <div class=\"input-group-addon\">Rp</div>
                                        <input type=\"text\" name=\"jml_bayar\" id=\"jml_bayar\" class=\"form-control number_format text-right input-lg text-bold\" readonly=\"\">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <script type=\"text/javascript\">
                    $(\".number_format\").on(\"change\", function() { $(this).val(number_format($(this).val(), 2)); });

                    $(\"#fm_pelunasan\").validate().destroy();

                    $(\"#fm_pelunasan\").validate({
                        rules: {
                            persen_denda: {
                                required: true,
                                number: true
                            }
                        }
                    });

                    function hitung_total() {
                        var_jml_pokok = parseFloat(hapus_koma($(\"#jml_pokok\").val()));
                        var_jml_bunga = parseFloat(hapus_koma($(\"#jml_bunga_1bulan\").val()));
                        var_persen_denda = $(\"#persen_denda\").val();
                        var_jml_denda = var_jml_pokok * var_persen_denda / 100;
                        var_jml_bayar = var_jml_pokok + var_jml_bunga + var_jml_denda;

                        $(\"#jml_denda\").val(var_jml_denda).trigger(\"change\");
                        $(\"#jml_bayar\").val(var_jml_bayar).trigger(\"change\");
                    }
                    </script>
                ";
            } else {
                $html .= "<table class=\"table table-bordered table-condensed table-striped\">
                        <thead>
                            <tr>
                                <th>Jangka</th>
                                <th>Sisa Waktu</th>
                                <th>Angsuran per Bulan</th>
                                <th>Jml Sisa Angsuran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>" . $data_post['tempo_bln'] . "</th>
                                <th>" . $data_post['sudah_diangsur'] . "</th>
                                <th>" . number_format($data_post['angsuran'], 2) . "</th>
                                <th>" . number_format($data_post['posisi_akhir'], 2) . "</th>
                            </tr>
                        </tbody>
                    </table>";
            }
        }

        echo $html;
    }

    public function get_var_pelunasan_dipercepat()
    {
        $data_post = get_request('post');

        $html = "";

        if ($data_post) {
            if ($data_post['kd_pinjaman'] == "1" or $data_post['is_sparepart'] == "1") {
                $data_tempo_bln = $data_post['tempo_bln'];

                if ($data_post['tempo_bln'] <= 36) {
                    $data_tempo_bln = 36;
                }

                if ($data_post['tempo_bln'] <= 24) {
                    $data_tempo_bln = 24;
                }

                if ($data_post['tempo_bln'] <= 12) {
                    $data_tempo_bln = 12;
                }

                $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, date("Y-m-d"));

                if ($data_margin_pinjaman->num_rows() > 0) {
                    $data_post['bunga'] = $data_margin_pinjaman->row(0)->rate;
                }
            }

            if ($data_post['ubah_margin']) {
                $data_post['bunga'] = $data_post['ubah_margin'];
            }

            $data_post['jml_pokok'] = $data_post['jml_pinjam'];
            $data_post['jml_admin'] = $data_post['jml_biaya_admin'];
            $data_post['jml_bunga'] = $data_post['jml_margin'];

            $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data_post);

            if ($data_post['kd_pinjaman'] == "1") {
                $cari['value'] = $data_post['no_pinjam'];
                $cari['field'] = array("no_pinjam");

                $data_pinjaman = $this->pinjaman_model->get_pinjaman(0, $cari)->row_array(0);

                if ($data_post['ubah_margin']) {
                    $data_pinjaman['margin'] = $data_post['ubah_margin'];
                }

                if ($data_post['sisa_bln'] < $data_post['tempo_bln']) {
                    $html .= "<h4>Pembayaran Pinjaman Uang Reguler</h4>
                        <table class=\"table table-condensed table-striped\">
                            <tr>
                                <td width=\"200px\">Tanggal</td>
                                <td>" . $data_pinjaman['tgl_pinjam'] . "</td>
                                <input type=\"hidden\" id=\"no_ref_bukti\" name=\"no_ref_bukti\" value=\"" . $data_pinjaman['no_pinjam'] . "\">
                                <input type=\"hidden\" id=\"tgl_ref_bukti\" name=\"tgl_ref_bukti\" value=\"" . balik_tanggal($data_pinjaman['tgl_pinjam']) . "\">
                            </tr>
                            <tr>
                                <td>Jml. Pokok</td>
                                <td>" . number_format($data_pinjaman['jml_pinjam'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_pokok\" name=\"jml_pokok\" value=\"" . $data_pinjaman['jml_pinjam'] . "\">
                            </tr>
                            <tr>
                                <td>Jml. Biaya Admin</td>
                                <td>" . number_format($data_pinjaman['jml_biaya_admin'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_admin\" name=\"jml_admin\" value=\"" . $data_pinjaman['jml_biaya_admin'] . "\">
                            </tr>
                            <tr>
                                <td>Bunga/Tahun (%)</td>
                                <td>" . $data_post['bunga'] . "</td>
                                <input type=\"hidden\" id=\"bunga\" name=\"bunga\" value=\"" . $data_post['bunga'] . "\">
                            </tr>
                            <tr>
                                <td>Bunga/Bulan (%)</td>
                                <td>" . number_format($perhitungan['bunga_bln'], 2) . "</td>
                                <input type=\"hidden\" id=\"bunga_bln\" name=\"bunga_bln\" value=\"" . $perhitungan['bunga_bln'] . "\">
                            </tr>
                            <tr>
                                <td>Jangka (Bulan)</td>
                                <td>" . $data_pinjaman['tempo_bln'] . "</td>
                                <input type=\"hidden\" id=\"tempo_bln\" name=\"tempo_bln\" value=\"" . $data_pinjaman['margin'] . "\">
                            </tr>
                            <tr>
                                <td>Sudah Diangsur</td>
                                <td>" . $perhitungan['sudah_diangsur'] . "</td>
                                <input type=\"hidden\" id=\"angsur_bln\" name=\"angsur_bln\" value=\"" . $perhitungan['sudah_diangsur'] . "\">
                                <input type=\"hidden\" id=\"sisa_bln\" name=\"sisa_bln\" value=\"" . $data_post['sisa_bln'] . "\">
                            </tr>
                            <tr>
                                <td>Jml. Angsuran/Bulan</td>
                                <td>" . number_format($data_pinjaman['angsuran'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_angsuran\" name=\"jml_angsuran\" value=\"" . $data_pinjaman['angsuran'] . "\">
                            </tr>
                        </table>
                        <br>
                        <table class=\"table table-condensed table-striped\">
                            <tr>
                                <th colspan=\"4\">Perhitungan Lama</th>
                            </tr>
                            <tr>
                                <td>Pokok Pinjaman</td>
                                <td style=\"text-align: right\">Rp " . number_format($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                                <td style=\"text-align: right\">" . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                                <td style=\"text-align: right\"></td>
                            </tr>
                            <tr>
                                <td>Bunga</td>
                                <td style=\"text-align: right\">Rp " . number_format($data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                                <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_margin'] / $data_pinjaman['tempo_bln'], 2) . "</td>
                                <td style=\"text-align: right\"></td>
                                <input type=\"hidden\" id=\"jml_bunga\" name=\"jml_bunga\" value=\"" . $data_pinjaman['jml_margin'] . "\">
                                <input type=\"hidden\" id=\"jml_bunga_bln\" name=\"jml_bunga_bln\" value=\"" . $perhitungan['jml_bunga_bln'] . "\">
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
                                <td style=\"text-align: center\">" . $perhitungan['sudah_diangsur'] . " x </td>
                                <td style=\"text-align: right\">" . number_format($data_pinjaman['angsuran'], 2) . "</td>
                                <td>= Rp " . number_format($perhitungan['sudah_diangsur'] * $data_pinjaman['angsuran'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Sisa Angsuran</td>
                                <td style=\"text-align: center\">" . $data_post['sisa_bln'] . " x </td>
                                <td style=\"text-align: right\">" . number_format($data_pinjaman['angsuran'], 2) . "</td>
                                <td>= <strong>Rp " . number_format($perhitungan['sisa_angs_lama'], 2) . "</strong></td>
                                <input type=\"hidden\" id=\"jml_sisa_angsuran_lama\" name=\"jml_sisa_angsuran_lama\" value=\"" . $perhitungan['sisa_angs_lama'] . "\">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan=\"4\">Perhitungan Baru</th>
                            </tr>
                            <tr>
                                <td>Sisa Pokok Pinjaman</td>
                                <td style=\"text-align: center\">" . $data_post['sisa_bln'] . " x </td>
                                <td style=\"text-align: right\">" . number_format($perhitungan['jml_pokok_bln'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_pokok_bln\" name=\"jml_pokok_bln\" value=\"" . $perhitungan['jml_pokok_bln'] . "\">
                                <td>= Rp " . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_sisa_pokok\" name=\"jml_sisa_pokok\" value=\"" . $perhitungan['sisa_pokok'] . "\">
                            </tr>
                            <tr>
                                <td>Sisa Angsuran yg harus dibayar</td>
                                <td style=\"text-align: center\">( 1 + " . number_format($perhitungan['bunga_bln'], 2) . "% )<sup>" . $perhitungan['sudah_diangsur'] . "</sup> x </td>
                                <td style=\"text-align: right\">" . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                                <td>= <strong>Rp " . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                                <input type=\"hidden\" id=\"jml_sisa_angsuran_baru\" name=\"jml_sisa_angsuran_baru\" value=\"" . $perhitungan['sisa_angs_baru'] . "\">
                                <input type=\"hidden\" id=\"jml_bayar\" name=\"jml_bayar\" value=\"" . $perhitungan['sisa_angs_baru'] . "\">
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
                                <input type=\"hidden\" id=\"jml_selisih_sisa_angsuran\" name=\"jml_selisih_sisa_angsuran\" value=\"" . $perhitungan['perhitungan_selisih'] . "\">
                            </tr>
                        </table>
                    ";
                } else {
                    $html .= "<h4>Pembayaran Pinjaman Uang Reguler</h4>
                        <table class=\"table table-condensed table-striped\">
                            <tr>
                                <td width=\"200px\">Tanggal</td>
                                <td>" . $data_pinjaman['tgl_pinjam'] . "</td>
                                <input type=\"hidden\" id=\"no_ref_bukti\" name=\"no_ref_bukti\" value=\"" . $data_pinjaman['no_pinjam'] . "\">
                                <input type=\"hidden\" id=\"tgl_ref_bukti\" name=\"tgl_ref_bukti\" value=\"" . balik_tanggal($data_pinjaman['tgl_pinjam']) . "\">
                            </tr>
                            <tr>
                                <td>Jml. Pokok</td>
                                <td>" . number_format($data_pinjaman['jml_pinjam'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_pokok\" name=\"jml_pokok\" value=\"" . $data_pinjaman['jml_pinjam'] . "\">
                            </tr>
                            <tr>
                                <td>Jml. Biaya Admin</td>
                                <td>" . number_format($data_pinjaman['jml_biaya_admin'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_admin\" name=\"jml_admin\" value=\"" . $data_pinjaman['jml_biaya_admin'] . "\">
                            </tr>
                            <tr>
                                <td>Bunga/Tahun (%)</td>
                                <td>" . $data_pinjaman['margin'] . "</td>
                                <input type=\"hidden\" id=\"bunga\" name=\"bunga\" value=\"" . $data_pinjaman['margin'] . "\">
                            </tr>
                            <tr>
                                <td>Bunga/Bulan (%)</td>
                                <td>" . number_format($perhitungan['bunga_bln'], 2) . "</td>
                                <input type=\"hidden\" id=\"bunga_bln\" name=\"bunga_bln\" value=\"" . $perhitungan['bunga_bln'] . "\">
                            </tr>
                            <tr>
                                <td>Jangka (Bulan)</td>
                                <td>" . $data_pinjaman['tempo_bln'] . "</td>
                                <input type=\"hidden\" id=\"tempo_bln\" name=\"tempo_bln\" value=\"" . $data_pinjaman['margin'] . "\">
                            </tr>
                            <tr>
                                <td>Sudah Diangsur</td>
                                <td>" . $perhitungan['sudah_diangsur'] . "</td>
                                <input type=\"hidden\" id=\"angsur_bln\" name=\"angsur_bln\" value=\"" . $perhitungan['sudah_diangsur'] . "\">
                                <input type=\"hidden\" id=\"sisa_bln\" name=\"sisa_bln\" value=\"" . $data_post['sisa_bln'] . "\">
                            </tr>
                            <tr>
                                <td>Masa</td>
                                <td>" . $perhitungan['jml_hari'] . "</td>
                                <input type=\"hidden\" id=\"jml_hari\" name=\"jml_hari\" value=\"" . $perhitungan['jml_hari'] . "\">
                            </tr>
                        </table>
                        <br>
                        <table class=\"table table-condensed table-striped\">
                            <tr>
                                <th colspan=\"4\">Perhitungan Lama</th>
                            </tr>
                            <tr>
                                <td>Pokok Pinjaman</td>
                                <td style=\"text-align: right\">Rp " . number_format($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                                <td style=\"text-align: right\">" . number_format(($data_pinjaman['jml_pinjam'] + $data_pinjaman['jml_biaya_admin']) / $data_pinjaman['tempo_bln'], 2) . "</td>
                                <td style=\"text-align: right\"></td>
                            </tr>
                            <tr>
                                <td>Bunga</td>
                                <td style=\"text-align: right\">Rp " . number_format($data_pinjaman['jml_margin'], 2) . " / " . $data_pinjaman['tempo_bln'] . " = Rp </td>
                                <td style=\"text-align: right\">" . number_format($data_pinjaman['jml_margin'] / $data_pinjaman['tempo_bln'], 2) . "</td>
                                <td style=\"text-align: right\"></td>
                                <input type=\"hidden\" id=\"jml_bunga\" name=\"jml_bunga\" value=\"" . $data_pinjaman['jml_margin'] . "\">
                                <input type=\"hidden\" id=\"jml_bunga_bln\" name=\"jml_bunga_bln\" value=\"" . $perhitungan['jml_bunga_bln'] . "\">
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
                                <th colspan=\"4\">Perhitungan Baru</th>
                            </tr>
                            <tr>
                                <td>Pokok Pinjaman</td>
                                <td style=\"text-align: center\"></td>
                                <td style=\"text-align: right\"></td>
                                <td>= Rp " . number_format($data_pinjaman['jml_pinjam'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Biaya Admin</td>
                                <td style=\"text-align: center\"></td>
                                <td style=\"text-align: right\"></td>
                                <td>= Rp " . number_format($data_pinjaman['jml_biaya_admin'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Bunga Harian</td>
                                <td style=\"text-align: center;border-bottom: 1px black;\" colspan=\"2\">( " . number_format($data_pinjaman['jml_pinjam'], 2) . " x " . $data_pinjaman['margin'] . "% ) x " . $perhitungan['jml_hari'] . " </td>
                                <td>= Rp " . number_format($perhitungan['jml_bunga_harian'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_bunga_harian\" name=\"jml_bunga_harian\" value=\"" . $perhitungan['jml_bunga_harian'] . "\">
                            </tr>
                            <tr>
                                <td></td>
                                <td style=\"text-align: center\" colspan=\"2\">360</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan=\"3\">Angsuran yang harus dibayar</td>
                                <td style=\"text-align: left\"><strong>Rp " . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                                <input type=\"hidden\" id=\"jml_bayar\" name=\"jml_bayar\" value=\"" . $perhitungan['sisa_angs_baru'] . "\">
                            </tr>
                            <tr>
                                <td colspan=\"4\"></td>
                            </tr>
                            <tr>
                                <td><strong>Koreksi Bunga</strong></td>
                                <td>Perhitungan Lama</td>
                                <td style=\"text-align: right\"><strong>" . number_format($sisa_angs_lama, 2) . "</strong></td>
                                <td></td>
                                <input type=\"hidden\" id=\"jml_sisa_angsuran_lama\" name=\"jml_sisa_angsuran_lama\" value=\"" . $perhitungan['perhitungan_lama'] . "\">
                            </tr>
                            <tr>
                                <td></td>
                                <td>Perhitungan Baru</td>
                                <td style=\"text-align: right\"><strong>" . number_format($sisa_angs_baru, 2) . "</strong></td>
                                <td></td>
                                <input type=\"hidden\" id=\"jml_sisa_angsuran_baru\" name=\"jml_sisa_angsuran_baru\" value=\"" . $perhitungan['perhitungan_baru'] . "\">
                            </tr>
                            <tr>
                                <td></td>
                                <td>Selisih</td>
                                <td style=\"text-align: right\"><strong>" . number_format($selisih_sisa_angs, 2) . "</strong></td>
                                <td></td>
                                <input type=\"hidden\" id=\"jml_selisih_sisa_angsuran\" name=\"jml_selisih_sisa_angsuran\" value=\"" . $perhitungan['perhitungan_selisih'] . "\">
                            </tr>
                        </table>
                    ";
                }
            } else if ($data_post['kd_pinjaman'] == "3") {
                $html .= "<h4>Pembayaran Pinjaman Uang PHT</h4>
                    <table class=\"table table-bordered table-condensed table-striped form-inline\">
                        <tbody>
                            <tr>
                                <td>1.</td>
                                <td>Pokok Pinjaman</td>
                                <td>
                                    <div class=\"input-group\">
                                        <div class=\"input-group-addon\">Rp</div>
                                        <input type=\"text\" name=\"jml_pokok\" id=\"jml_pokok\" class=\"form-control number_format text-right\" readonly=\"\" value=\"" . number_format($data_post['jml_pinjam'], 2) . "\">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Denda => Pokok Pinjaman x
                                    <div class=\"input-group\">
                                        <input type=\"text\" name=\"persen_denda\" id=\"persen_denda\" class=\"form-control\" onchange=\"hitung_total()\" size=\"2\">
                                        <div class=\"input-group-addon\">%</div>
                                    </div></td>
                                <td>
                                    <div class=\"input-group\">
                                        <div class=\"input-group-addon\">Rp</div>
                                        <input type=\"text\" name=\"jml_denda\" id=\"jml_denda\" class=\"form-control number_format text-right\" readonly=\"\" value=\"0\">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Bunga 1 Bulan</td>
                                <td>
                                    <div class=\"input-group\">
                                        <div class=\"input-group-addon\">Rp</div>
                                        <input type=\"text\" name=\"jml_bunga\" id=\"jml_bunga\" class=\"form-control number_format text-right\" readonly=\"\" value=\"" . number_format($data_post['bunga'], 2) . "\">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=\"2\">Jumlah Bayar</td>
                                <td>
                                    <div class=\"input-group\">
                                        <div class=\"input-group-addon\">Rp</div>
                                        <input type=\"text\" name=\"jml_bayar\" id=\"jml_bayar\" class=\"form-control number_format text-right input-lg text-bold\" readonly=\"\">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <script type=\"text/javascript\">
                    $(\".number_format\").on(\"change\", function() { $(this).val(number_format($(this).val(), 2)); });

                    $(\"#fm_pelunasan\").validate().destroy();

                    $(\"#fm_pelunasan\").validate({
                        rules: {
                            persen_denda: {
                                required: true,
                                number: true
                            }
                        }
                    });

                    function hitung_total() {
                        var_jml_pokok = parseFloat(hapus_koma($(\"#jml_pokok\").val()));
                        var_jml_bunga = parseFloat(hapus_koma($(\"#jml_bunga\").val()));
                        var_persen_denda = $(\"#persen_denda\").val();
                        var_jml_denda = var_jml_pokok * var_persen_denda / 100;
                        var_jml_bayar = var_jml_pokok + var_jml_bunga + var_jml_denda;

                        $(\"#jml_denda\").val(var_jml_denda).trigger(\"change\");
                        $(\"#jml_bayar\").val(var_jml_bayar).trigger(\"change\");
                    }
                    </script>
                ";

            } else if (in_array($data_post['kd_pinjaman'], array("2", "4"))) {
                $html .= "<h4>Pembayaran Pinjaman Uang KKB/KPR</h4>
                <table class=\"table table-bordered table-condensed table-striped form-inline\">
                    <tbody>
                        <tr>
                            <td>1.</td>
                            <td>Posisi Akhir</td>
                            <td>
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">Rp</div>
                                    <input type=\"text\" name=\"jml_sisa_angsuran\" id=\"jml_sisa_angsuran\" class=\"form-control number_format text-right\" readonly=\"\" value=\"" . number_format($data_post['posisi_akhir'], 2) . "\">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2.</td>
                            <td>Denda => Posisi Akhir x
                                <div class=\"input-group\">
                                    <input type=\"text\" name=\"persen_denda\" id=\"persen_denda\" class=\"form-control\" onchange=\"hitung_total()\" size=\"2\">
                                    <div class=\"input-group-addon\">%</div>
                                </div></td>
                            <td>
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">Rp</div>
                                    <input type=\"text\" name=\"jml_denda\" id=\"jml_denda\" class=\"form-control number_format text-right\" readonly=\"\" value=\"0\">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3.</td>
                            <td>Asuransi => Posisi Akhir x <input type=\"text\" name=\"sisa_bln\" id=\"sisa_bln\" class=\"form-control\" onchange=\"hitung_total()\" size=\"2\" value=\"" . $data_post['sisa_bln'] . "\" > Bulan x <div class=\"input-group\">
                                    <input type=\"text\" name=\"persen_asuransi\" id=\"persen_asuransi\" class=\"form-control\" onchange=\"hitung_total()\" size=\"2\" >
                                    <div class=\"input-group-addon\">%</div>
                                </div></td>
                            <td>
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">Rp</div>
                                    <input type=\"text\" name=\"jml_asuransi\" id=\"jml_asuransi\" class=\"form-control number_format text-right\" readonly=\"\" value=\"0\">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan=\"2\">Jumlah Bayar</td>
                            <td>
                                <div class=\"input-group\">
                                    <div class=\"input-group-addon\">Rp</div>
                                    <input type=\"text\" name=\"jml_bayar\" id=\"jml_bayar\" class=\"form-control number_format text-right input-lg text-bold\" readonly=\"\" value=\"0\">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <script type=\"text/javascript\">
                $(\".number_format\").on(\"change\", function() { $(this).val(number_format($(this).val(), 2)); });

                $(\"#fm_pelunasan\").validate().destroy();

                $(\"#fm_pelunasan\").validate({
                    rules: {
                        persen_denda: {
                            required: true,
                            number: true
                        },
                        persen_asuransi: {
                            required: true,
                            number: true
                        }
                    }
                });

                function hitung_total() {
                    var_jml_angsuran = parseFloat(hapus_koma($(\"#jml_sisa_angsuran\").val()));
                    var_sisa_bln = parseFloat(hapus_koma($(\"#sisa_bln\").val()));
                    var_persen_denda = $(\"#persen_denda\").val();
                    var_jml_denda = var_jml_angsuran * var_persen_denda / 100;
                    var_persen_asuransi = $(\"#persen_asuransi\").val();
                    var_jml_asuransi = var_jml_angsuran * var_sisa_bln * (var_persen_asuransi/100)
                    var_jml_bayar = var_jml_angsuran + var_jml_denda + var_jml_asuransi;

                    $(\"#jml_denda\").val(var_jml_denda).trigger(\"change\");
                    $(\"#jml_asuransi\").val(var_jml_asuransi).trigger(\"change\");
                    $(\"#jml_bayar\").val(var_jml_bayar).trigger(\"change\");
                }
                </script>
                ";
            }
        }

        echo $html;
    }

    public function proses_pelunasan_pinjaman_dipercepat()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $data_post['jns_pelunasan'] = "PINJAMAN";
            $data_post['kode_bukti']    = "PL";
            $data_post['tgl_lunas']     = balik_tanggal($data_post['tgl_lunas']);

            // baca_array($data_post); exit();

            $query = $this->pinjaman_model->proses_pelunasan_pinjaman_dipercepat($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diproses";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diproses";
            }

            echo json_encode($hasil);
        }
    }

    public function get_pinjaman_lunas()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];
        // $bulan         = $data['bulan'];
        // $tahun         = $data['tahun'];

        $jns_pelunasan = isset($data['jns_pelunasan']) ? $data['jns_pelunasan'] : "";

        $data_numrows = $this->pinjaman_model->get_pinjaman_lunas(1, $cari, "", "", "")->row(0)->numrows;
        $data_item    = $this->pinjaman_model->get_pinjaman_lunas(0, $cari, "", $offset, $limit);

        $data_set = array();

        foreach ($data_item->result_array() as $value) {
            $offset++;
            $value['nomor'] = $offset;
            $data_set[]     = $value;
        }

        $array['recordsTotal']    = $data_numrows;
        $array['recordsFiltered'] = $array['recordsTotal'];
        $array['data']            = $data_set;

        echo json_encode($array);
    }

    public function hapus_pelunasan_pinjaman_dipercepat()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $query = $this->pinjaman_model->hapus_pelunasan_pinjaman_dipercepat($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Dihapus";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Dihapus";
            }

            echo json_encode($hasil);
        }
    }

    public function get_kredit_non_pinjaman_belum_lunas()
    {
        $data = get_request();

        $cari['value'] = $data['search']['value'];
        $offset        = $data['start'];
        $limit         = $data['length'];

        $no_ang = (isset($data['no_ang']) and $data['no_ang'] != "") ? $data['no_ang'] : "xxx";
        $tahun  = isset($data['tahun']) ? $data['tahun'] : date('Y');
        $bulan  = isset($data['bulan']) ? $data['bulan'] : date('m');

        // $data_numrows = $this->pinjaman_model->get_pinjaman_belum_lunas(1, $cari, "", "", "", $no_ang)->row(0)->numrows;
        $data_item = $this->pinjaman_model->get_kredit_non_pinjaman_belum_lunas($no_ang, $tahun, $bulan);

        $data_set = array();

        foreach ($data_item->result_array() as $value) {
            $offset++;
            $value['nomor'] = $offset;
            $data_set[]     = $value;
        }

        $array['recordsTotal']    = $data_item->num_rows();
        $array['recordsFiltered'] = $array['recordsTotal'];
        $array['data']            = $data_set;

        echo json_encode($array);
    }

    public function get_var_pelunasan_kredit_non_pinjaman()
    {
        $data_post = get_request('post');

        $html = "";

        if ($data_post) {
            $data_post['no_trans']      = isset($data_post['no_trans']) ? $data_post['no_trans'] : $data_post['no_pinjam'];
            $data_post['tgl_trans']     = isset($data_post['tgl_trans']) ? $data_post['tgl_trans'] : $data_post['tgl_pinjam'];
            $data_post['jml_trans']     = isset($data_post['jml_trans']) ? $data_post['jml_trans'] : $data_post['jml_pinjam'];
            $data_post['sisa_angsuran'] = isset($data_post['sisa_angsuran']) ? $data_post['sisa_angsuran'] : $data_post['posisi_akhir'];

            if ($data_post['is_sparepart'] == "1") {
                if ($data_post['kd_pinjaman'] == "1" or $data_post['is_sparepart'] == "1") {
                    $data_tempo_bln = $data_post['tempo_bln'];

                    if ($data_post['tempo_bln'] <= 36) {
                        $data_tempo_bln = 36;
                    }

                    if ($data_post['tempo_bln'] <= 24) {
                        $data_tempo_bln = 24;
                    }

                    if ($data_post['tempo_bln'] <= 12) {
                        $data_tempo_bln = 12;
                    }

                    $data_margin_pinjaman = $this->master_model->get_margin_pinjaman_berlaku("1", $data_tempo_bln, date("Y-m-d"));

                    if ($data_margin_pinjaman->num_rows() > 0) {
                        $data_post['bunga'] = $data_margin_pinjaman->row(0)->rate;
                    }
                }

                if ($data_post['ubah_margin']) {
                    $data_post['margin'] = $data_post['ubah_margin'];
                }

                $data_post['jml_pokok']     = $data_post['jml_pinjam'];
                $data_post['jml_admin']     = $data_post['jml_biaya_admin'];
                $data_post['jml_bunga']     = $data_post['jml_margin'];
                $data_post['tgl_ref_bukti'] = $data_post['tgl_pinjam'];

                $perhitungan = $this->pinjaman_model->perhitungan_pelunasan($data_post);

                if ($data_post['sisa_bln'] < $data_post['tempo_bln']) {
                    $html .= "<h4>Pelunasan Spare Part</h4>
                        <table class=\"table table-condensed table-striped\">
                            <tr>
                                <td width=\"200px\">Tanggal</td>
                                <td>" . $data_post['tgl_trans'] . "</td>
                                <input type=\"hidden\" id=\"no_ref_bukti\" name=\"no_ref_bukti\" value=\"" . $data_post['no_trans'] . "\">
                                <input type=\"hidden\" id=\"tgl_ref_bukti\" name=\"tgl_ref_bukti\" value=\"" . balik_tanggal($data_post['tgl_trans']) . "\">
                            </tr>
                            <tr>
                                <td>Jml. Pokok</td>
                                <td>" . number_format($data_post['jml_trans'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_pokok\" name=\"jml_pokok\" value=\"" . $data_post['jml_trans'] . "\">
                            </tr>
                            <tr>
                                <td>Jml. Biaya Admin</td>
                                <td>" . number_format($data_post['jml_biaya_admin'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_admin\" name=\"jml_admin\" value=\"" . $data_post['jml_biaya_admin'] . "\">
                            </tr>
                            <tr>
                                <td>Bunga/Tahun (%)</td>
                                <td>" . $data_post['margin'] . "</td>
                                <input type=\"hidden\" id=\"bunga\" name=\"bunga\" value=\"" . $data_post['margin'] . "\">
                            </tr>
                            <tr>
                                <td>Bunga/Bulan (%)</td>
                                <td>" . number_format($perhitungan['bunga_bln'], 2) . "</td>
                                <input type=\"hidden\" id=\"bunga_bln\" name=\"bunga_bln\" value=\"" . $perhitungan['bunga_bln'] . "\">
                            </tr>
                            <tr>
                                <td>Jangka (Bulan)</td>
                                <td>" . $data_post['tempo_bln'] . "</td>
                                <input type=\"hidden\" id=\"tempo_bln\" name=\"tempo_bln\" value=\"" . $data_post['margin'] . "\">
                            </tr>
                            <tr>
                                <td>Sudah Diangsur</td>
                                <td>" . $perhitungan['sudah_diangsur'] . "</td>
                                <input type=\"hidden\" id=\"angsur_bln\" name=\"angsur_bln\" value=\"" . $perhitungan['sudah_diangsur'] . "\">
                                <input type=\"hidden\" id=\"sisa_bln\" name=\"sisa_bln\" value=\"" . $data_post['sisa_bln'] . "\">
                            </tr>
                            <tr>
                                <td>Jml. Angsuran/Bulan</td>
                                <td>" . number_format($data_post['angsuran'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_angsuran\" name=\"jml_angsuran\" value=\"" . $data_post['angsuran'] . "\">
                            </tr>
                        </table>
                        <br>
                        <table class=\"table table-condensed table-striped\">
                            <tr>
                                <th colspan=\"4\">Perhitungan Lama</th>
                            </tr>
                            <tr>
                                <td>Pokok Pinjaman</td>
                                <td style=\"text-align: right\">Rp " . number_format($data_post['jml_trans'] + $data_post['jml_biaya_admin'], 2) . " / " . $data_post['tempo_bln'] . " = Rp </td>
                                <td style=\"text-align: right\">" . number_format(($data_post['jml_trans'] + $data_post['jml_biaya_admin']) / $data_post['tempo_bln'], 2) . "</td>
                                <td style=\"text-align: right\"></td>
                            </tr>
                            <tr>
                                <td>Bunga</td>
                                <td style=\"text-align: right\">Rp " . number_format($data_post['jml_margin'], 2) . " / " . $data_post['tempo_bln'] . " = Rp </td>
                                <td style=\"text-align: right\">" . number_format($data_post['jml_margin'] / $data_post['tempo_bln'], 2) . "</td>
                                <td style=\"text-align: right\"></td>
                                <input type=\"hidden\" id=\"jml_bunga\" name=\"jml_bunga\" value=\"" . $data_post['jml_margin'] . "\">
                                <input type=\"hidden\" id=\"jml_bunga_bln\" name=\"jml_bunga_bln\" value=\"" . $perhitungan['jml_bunga_bln'] . "\">
                            </tr>
                            <tr>
                                <td>Jumlah</td>
                                <td style=\"text-align: right\">Rp " . number_format(($data_post['jml_trans'] + $data_post['jml_biaya_admin']) + $data_post['jml_margin'], 2) . " / " . $data_post['tempo_bln'] . " = Rp  </td>
                                <td style=\"text-align: right\">" . number_format((($data_post['jml_trans'] + $data_post['jml_biaya_admin']) + $data_post['jml_margin']) / $data_post['tempo_bln'], 2) . "</td>
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
                                <td style=\"text-align: center\">" . $perhitungan['sudah_diangsur'] . " x </td>
                                <td style=\"text-align: right\">" . number_format($data_post['angsuran'], 2) . "</td>
                                <td>= Rp " . number_format($perhitungan['sudah_diangsur'] * $data_post['angsuran'], 2) . "</td>
                            </tr>
                            <tr>
                                <td>Sisa Angsuran</td>
                                <td style=\"text-align: center\">" . $data_post['sisa_bln'] . " x </td>
                                <td style=\"text-align: right\">" . number_format($data_post['angsuran'], 2) . "</td>
                                <td>= <strong>Rp " . number_format($perhitungan['sisa_angs_lama'], 2) . "</strong></td>
                                <input type=\"hidden\" id=\"jml_sisa_angsuran_lama\" name=\"jml_sisa_angsuran_lama\" value=\"" . $perhitungan['sisa_angs_lama'] . "\">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan=\"4\">Perhitungan Baru</th>
                            </tr>
                            <tr>
                                <td>Sisa Pokok Pinjaman</td>
                                <td style=\"text-align: center\">" . $data_post['sisa_bln'] . " x </td>
                                <td style=\"text-align: right\">" . number_format($perhitungan['jml_pokok_bln'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_pokok_bln\" name=\"jml_pokok_bln\" value=\"" . $perhitungan['jml_pokok_bln'] . "\">
                                <td>= Rp " . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                                <input type=\"hidden\" id=\"jml_sisa_pokok\" name=\"jml_sisa_pokok\" value=\"" . $perhitungan['sisa_pokok'] . "\">
                            </tr>
                            <tr>
                                <td>Sisa Angsuran yg harus dibayar</td>
                                <td style=\"text-align: center\">( 1 + " . number_format($perhitungan['bunga_bln'], 2) . "% )<sup>" . $perhitungan['sudah_diangsur'] . "</sup> x </td>
                                <td style=\"text-align: right\">" . number_format($perhitungan['sisa_pokok'], 2) . "</td>
                                <td>= <strong>Rp " . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                                <input type=\"hidden\" id=\"jml_sisa_angsuran_baru\" name=\"jml_sisa_angsuran_baru\" value=\"" . $perhitungan['sisa_angs_baru'] . "\">
                                <input type=\"hidden\" id=\"jml_bayar\" name=\"jml_bayar\" value=\"" . $perhitungan['sisa_angs_baru'] . "\">
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
                                <input type=\"hidden\" id=\"jml_selisih_sisa_angsuran\" name=\"jml_selisih_sisa_angsuran\" value=\"" . $perhitungan['perhitungan_selisih'] . "\">
                            </tr>
                        </table>
                    ";

                } else {
                    $html .= "<h4>Pembayaran Spare Part</h4>
                    <table class=\"table table-condensed table-striped\">
                        <tr>
                            <td width=\"200px\">Tanggal</td>
                            <td>" . $data_post['tgl_trans'] . "</td>
                            <input type=\"hidden\" id=\"no_ref_bukti\" name=\"no_ref_bukti\" value=\"" . $data_post['no_trans'] . "\">
                            <input type=\"hidden\" id=\"tgl_ref_bukti\" name=\"tgl_ref_bukti\" value=\"" . balik_tanggal($data_post['tgl_trans']) . "\">
                        </tr>
                        <tr>
                            <td>Jml. Pokok</td>
                            <td>" . number_format($data_post['jml_trans'], 2) . "</td>
                            <input type=\"hidden\" id=\"jml_pokok\" name=\"jml_pokok\" value=\"" . $data_post['jml_trans'] . "\">
                        </tr>
                        <tr>
                            <td>Jml. Biaya Admin</td>
                            <td>" . number_format($data_post['jml_biaya_admin'], 2) . "</td>
                            <input type=\"hidden\" id=\"jml_admin\" name=\"jml_admin\" value=\"" . $data_post['jml_biaya_admin'] . "\">
                        </tr>
                        <tr>
                            <td>Bunga/Tahun (%)</td>
                            <td>" . $data_post['margin'] . "</td>
                            <input type=\"hidden\" id=\"bunga\" name=\"bunga\" value=\"" . $data_post['margin'] . "\">
                        </tr>
                        <tr>
                            <td>Bunga/Bulan (%)</td>
                            <td>" . number_format($perhitungan['bunga_bln'], 2) . "</td>
                            <input type=\"hidden\" id=\"bunga_bln\" name=\"bunga_bln\" value=\"" . $perhitungan['bunga_bln'] . "\">
                        </tr>
                        <tr>
                            <td>Jangka (Bulan)</td>
                            <td>" . $data_post['tempo_bln'] . "</td>
                            <input type=\"hidden\" id=\"tempo_bln\" name=\"tempo_bln\" value=\"" . $data_post['margin'] . "\">
                        </tr>
                        <tr>
                            <td>Sudah Diangsur</td>
                            <td>" . $perhitungan['sudah_diangsur'] . "</td>
                            <input type=\"hidden\" id=\"angsur_bln\" name=\"angsur_bln\" value=\"" . $perhitungan['sudah_diangsur'] . "\">
                            <input type=\"hidden\" id=\"sisa_bln\" name=\"sisa_bln\" value=\"" . $data_post['sisa_bln'] . "\">
                        </tr>
                        <tr>
                            <td>Masa</td>
                            <td>" . $perhitungan['jml_hari'] . "</td>
                            <input type=\"hidden\" id=\"jml_hari\" name=\"jml_hari\" value=\"" . $perhitungan['jml_hari'] . "\">
                        </tr>
                    </table>
                    <br>
                    <table class=\"table table-condensed table-striped\">
                        <tr>
                            <th colspan=\"4\">Perhitungan Lama</th>
                        </tr>
                        <tr>
                            <td>Pokok Pinjaman</td>
                            <td style=\"text-align: right\">Rp " . number_format($data_post['jml_trans'] + $data_post['jml_biaya_admin'], 2) . " / " . $data_post['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format(($data_post['jml_trans'] + $data_post['jml_biaya_admin']) / $data_post['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                        </tr>
                        <tr>
                            <td>Bunga</td>
                            <td style=\"text-align: right\">Rp " . number_format($data_post['jml_margin'], 2) . " / " . $data_post['tempo_bln'] . " = Rp </td>
                            <td style=\"text-align: right\">" . number_format($data_post['jml_margin'] / $data_post['tempo_bln'], 2) . "</td>
                            <td style=\"text-align: right\"></td>
                            <input type=\"hidden\" id=\"jml_bunga\" name=\"jml_bunga\" value=\"" . $data_post['jml_margin'] . "\">
                            <input type=\"hidden\" id=\"jml_bunga_bln\" name=\"jml_bunga_bln\" value=\"" . $perhitungan['jml_bunga_bln'] . "\">
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td style=\"text-align: right\">Rp " . number_format(($data_post['jml_trans'] + $data_post['jml_biaya_admin']) + $data_post['jml_margin'], 2) . " / " . $data_post['tempo_bln'] . " = Rp  </td>
                            <td style=\"text-align: right\">" . number_format((($data_post['jml_trans'] + $data_post['jml_biaya_admin']) + $data_post['jml_margin']) / $data_post['tempo_bln'], 2) . "</td>
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
                            <th colspan=\"4\">Perhitungan Baru</th>
                        </tr>
                        <tr>
                            <td>Pokok Pinjaman</td>
                            <td style=\"text-align: center\"></td>
                            <td style=\"text-align: right\"></td>
                            <td>= Rp " . number_format($data_post['jml_trans'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Biaya Admin</td>
                            <td style=\"text-align: center\"></td>
                            <td style=\"text-align: right\"></td>
                            <td>= Rp " . number_format($data_post['jml_biaya_admin'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Bunga Harian</td>
                            <td style=\"text-align: center;border-bottom: 1px black;\" colspan=\"2\">( " . number_format($data_post['jml_trans'], 2) . " x " . $data_post['margin'] . "% ) x " . $perhitungan['jml_hari'] . " </td>
                            <td>= Rp " . number_format($perhitungan['jml_bunga_harian'], 2) . "</td>
                            <input type=\"hidden\" id=\"jml_bunga_harian\" name=\"jml_bunga_harian\" value=\"" . $perhitungan['jml_bunga_harian'] . "\">
                        </tr>
                        <tr>
                            <td></td>
                            <td style=\"text-align: center\" colspan=\"2\">360</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan=\"3\">Angsuran yang harus dibayar</td>
                            <td style=\"text-align: left\"><strong>Rp " . number_format($perhitungan['sisa_angs_baru'], 2) . "</strong></td>
                            <input type=\"hidden\" id=\"jml_bayar\" name=\"jml_bayar\" value=\"" . $perhitungan['sisa_angs_baru'] . "\">
                        </tr>
                        <tr>
                            <td colspan=\"4\"></td>
                        </tr>
                        <tr>
                            <td><strong>Koreksi Bunga</strong></td>
                            <td>Perhitungan Lama</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_lama'], 2) . "</strong></td>
                            <td></td>
                            <input type=\"hidden\" id=\"jml_sisa_angsuran_lama\" name=\"jml_sisa_angsuran_lama\" value=\"" . $perhitungan['perhitungan_lama'] . "\">
                        </tr>
                        <tr>
                            <td></td>
                            <td>Perhitungan Baru</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_baru'], 2) . "</strong></td>
                            <td></td>
                            <input type=\"hidden\" id=\"jml_sisa_angsuran_baru\" name=\"jml_sisa_angsuran_baru\" value=\"" . $perhitungan['perhitungan_baru'] . "\">
                        </tr>
                        <tr>
                            <td></td>
                            <td>Selisih</td>
                            <td style=\"text-align: right\"><strong>" . number_format($perhitungan['perhitungan_selisih'], 2) . "</strong></td>
                            <td></td>
                            <input type=\"hidden\" id=\"jml_selisih_sisa_angsuran\" name=\"jml_selisih_sisa_angsuran\" value=\"" . $perhitungan['perhitungan_selisih'] . "\">
                        </tr>
                    </table>
                ";
                }
            } else {
                $sudah_diangsur = $data_post['tempo_bln'] - $data_post['sisa_bln'];

                $html .= "<table class=\"table table-bordered table-condensed\">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jangka</th>
                        <th>Sisa Bulan</th>
                        <th>Angsuran</th>
                        <th>Jml Sisa Angsuran/Jml Bayar</th>
                    </tr>
                    <tr>
                        <td>" . balik_tanggal($data_post['tgl_trans']) . "</td>
                        <td style=\"text-align: center\">" . $data_post['tempo_bln'] . "<input type=\"hidden\" id=\"angsur_bln\" name=\"angsur_bln\" value=\"" . $sudah_diangsur . "\"></td>
                        <td style=\"text-align: center\">" . $data_post['sisa_bln'] . "<input type=\"hidden\" id=\"sisa_bln\" name=\"sisa_bln\" value=\"" . $data_post['sisa_bln'] . "\"></td>
                        <td style=\"text-align: right\">" . number_format($data_post['angsuran'], 2) . "<input type=\"hidden\" id=\"jml_pokok\" name=\"jml_pokok\" value=\"" . $data_post['jml_trans'] . "\"><input type=\"hidden\" id=\"angsuran\" name=\"angsuran\" value=\"" . $data_post['angsuran'] . "\"></td>
                        <td style=\"text-align: right\">" . number_format($data_post['sisa_angsuran'], 2) . "<input type=\"hidden\" id=\"jml_sisa_angsuran\" name=\"jml_sisa_angsuran\" value=\"" . $data_post['sisa_angsuran'] . "\"><input type=\"hidden\" id=\"jml_bayar\" name=\"jml_bayar\" value=\"" . $data_post['sisa_angsuran'] . "\"></td>
                    </tr>
                </table>";
            }

            echo $html;
        }
    }

    public function proses_pelunasan_kredit_non_pinjaman()
    {
        $data_post = get_request('post');

        if ($data_post) {
            // $data_post['jns_pelunasan'] = "PINJAMAN";
            $data_post['kode_bukti'] = "PL";
            $data_post['tgl_lunas']  = balik_tanggal($data_post['tgl_lunas']);

            baca_array($data_post);exit();

            $query = $this->pinjaman_model->proses_pelunasan_kredit_non_pinjaman($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Diproses";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Diproses";
            }

            echo json_encode($hasil);
        }
    }

    public function hapus_pelunasan_kredit_non_pinjaman()
    {
        $data_post = get_request('post');

        if ($data_post) {
            $query = $this->pinjaman_model->hapus_pelunasan_kredit_non_pinjaman($data_post);

            if ($query) {
                $hasil['status'] = true;
                $hasil['msg']    = "Data Berhasil Dihapus";
            } else {
                $hasil['status'] = false;
                $hasil['msg']    = "Data Gagal Dihapus";
            }

            echo json_encode($hasil);
        }
    }

}
