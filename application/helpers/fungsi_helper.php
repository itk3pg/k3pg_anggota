<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Created By M. Bagus Rachmat Dianto
 */

date_default_timezone_set("Asia/Jakarta");

function host_url($uri = '', $protocol = null)
{
    if ($protocol == null) {
        $protocol = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://";
    }

    return $protocol . BASE_HOST . $uri;
}

function judul_web()
{
    return NAMA_PHP;
}

function get_request($request_mode = "")
{
    $ci = get_instance();

    $array_request = array();

    switch ($request_mode) {
        case 'post':
            $array_request = $ci->input->post();
            break;
        case 'get':
            $array_request = $ci->input->get();
            break;
        default:
            $array_request = array_merge($ci->input->get(), $ci->input->post());
            break;
    }

    return $array_request;
}

function dibulatkan($angka, $kelipatan = 5)
{
    $sisa = $angka % $kelipatan;

    if ($sisa > 0) {
        $bulat = $kelipatan - $sisa;
        $hasil = $angka + $bulat;
    } else {
        $hasil = $angka;
    }

    return $hasil;
}

function cek_tanggal_entri($tgl_entri)
{
    $str_entri               = strtotime($tgl_entri);
    $str_awal_bulan_lalu     = strtotime(date("Y-m-01", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))));
    $str_akhir_bulan_lalu    = strtotime(date("Y-m-t", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))));
    $str_awal_bulan_sekarang = mktime(0, 0, 0, date("m"), 1, date("Y"));

    $status_entri = false;

    if (date("j") <= 7 and ($str_entri >= $str_awal_bulan_lalu) and ($str_entri <= $str_akhir_bulan_lalu)) {
        $status_entri = true;
    }

    if ($str_entri >= $str_awal_bulan_sekarang) {
        $status_entri = true;
    }

    return $status_entri;
}

function get_option_tag($array, $jenis_array = "")
{
    $option = "<option value=\"\">[-PILIH-]</option>";

    foreach ($array as $key => $value) {
        $option .= "<option value=\"" . $key . "\" ";

        if ($jenis_array == "BULAN") {
            $option .= ($key == date("m")) ? "selected" : "";
        }

        $option .= ">" . $value . "</option>";
    }

    return $option;
}

function hari_diff($tgl_awal, $tgl_akhir)
{
    return round(abs(strtotime($tgl_awal) - strtotime($tgl_akhir)) / 86400);
}

function hari_diff1($tgl_awal, $tgl_akhir)
{
    $d1 = new DateTime($tgl_awal); // 20 Feb 2017
    $d2 = new DateTime($tgl_akhir); // 12 May 2017

    $diff = $d1->diff($d2);

    return $diff->format("%a");
}

function array_bulan_huruf()
{
    $arrayBulan = array(
        "JAN" => "01",
        "FEB" => "02",
        "MAR" => "03",
        "APR" => "04",
        "MAY" => "05",
        "JUN" => "06",
        "JUL" => "07",
        "AUG" => "08",
        "SEP" => "09",
        "OCT" => "10",
        "NOV" => "11",
        "DEC" => "12",
    );

    return $arrayBulan;
}

function array_bulan()
{
    $arrayBulan = array(
        "01" => "Januari",
        "02" => "Februari",
        "03" => "Maret",
        "04" => "April",
        "05" => "Mei",
        "06" => "Juni",
        "07" => "Juli",
        "08" => "Agustus",
        "09" => "September",
        "10" => "Oktober",
        "11" => "November",
        "12" => "Desember",
    );

    return $arrayBulan;
}

function array_bulan_romawi()
{
    $arrayBulan = array(
        "01" => "I",
        "02" => "II",
        "03" => "III",
        "04" => "IV",
        "05" => "V",
        "06" => "VI",
        "07" => "VII",
        "08" => "VIII",
        "09" => "IX",
        "10" => "X",
        "11" => "XI",
        "12" => "XII",
    );

    return $arrayBulan;
}

function nama_bulan($bulan)
{
    $arrayBulan = array_bulan();

    return $arrayBulan[$bulan];
}

function tanggal_lengkap($tanggal)
{
    $pecahTanggal = explode("-", $tanggal);

    $hari  = $pecahTanggal[0];
    $bulan = $pecahTanggal[1];
    $tahun = $pecahTanggal[2];

    return $hari . " " . nama_bulan($bulan) . " " . $tahun;
}

function balik_tanggal($tanggal, $tanda = "-", $tanda_out = "-")
{
    if ($tanggal) {
        $pecahTanggal = explode($tanda, $tanggal);

        $tahun = $pecahTanggal[0];
        $bulan = $pecahTanggal[1];
        $hari  = $pecahTanggal[2];

        return $hari . $tanda_out . $bulan . $tanda_out . $tahun;
    }
}

function angka_rupiah($angka, $angkaDesimal = 2)
{
    $angkaRupiah = number_format($angka, $angkaDesimal, ".", ",");

    return $angkaRupiah;
}

function angka_normal($angka)
{
    $angkaNormal = str_replace(".", "", $angka);
    $angkaNormal = str_replace(",", ".", $angkaNormal);

    return $angkaNormal;
}

function hapus_koma($str)
{
    $str_baru = str_replace(",", "", $str);

    return $str_baru;
}

function nopol_spasi($nopol)
{
    $panjang_nopol = strlen($nopol);
    $nopol_baru    = "";
    $hitung        = 0;

    for ($i = 0; $i < $panjang_nopol; $i++) {
        $huruf = substr($nopol, $i, 1);

        if (($hitung == 0 and is_numeric($huruf)) or ($hitung == 1 and !is_numeric($huruf))) {
            $nopol_baru .= " ";
            $hitung++;
        }

        $nopol_baru .= $huruf;
    }

    return $nopol_baru;
}

function baca($string)
{
    echo $string;

    if (is_cli()) {
        echo PHP_EOL . PHP_EOL;
    } else {
        echo "<br><br>";
    }
}

function baca_array($array)
{
    if (is_cli()) {
        print_r($array);

        echo PHP_EOL . PHP_EOL;
    } else {
        echo "<pre>";

        print_r($array);

        echo "</pre><br><br>";
    }
}

function ping($host)
{
    if ($_SERVER['SERVER_ADDR'] == IP_SERVER) {
        exec(sprintf('ping -c 2 -w 2 %s', escapeshellarg($host)), $res, $rval);
    } else {
        exec(sprintf('ping -n 2 -w 2 %s ', escapeshellarg($host)), $res, $rval);
    }

    return $rval === 0;
}

function cek_koneksi_mysqli($host, $username, $password, $database = "")
{
    $tesPing = ping($host);

    if ($tesPing) {
        $mysqli = @mysqli_init();

        @$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);

        return @$mysqli->real_connect($host, $username, $password, $database);
    } else {
        return false;
    }
}

function get_maxid($table, $field)
{
    $ci = get_instance();

    return $ci->db->select("ifnull(max(" . $field . "), 0) + 1 id")->get($table)->row(0)->id;
}

function get_tanda_tangan()
{
    $ci = get_instance();

    return $ci->db->select("kabag_pu, kaunit_tambang, admin_tambang")->get("s_laporan");
}

function bg_proses($cmd)
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
        pclose(popen("start " . $cmd . " /b", "r"));
    } else {
        pclose(popen("nohup " . $cmd . "> /dev/null 2>/dev/null &", "r"));
    }
}

function clear_isi_folder_cache()
{
    $array_terlarang = array(".", "..", ".htaccess", "index.html");

    $str_hari_ini = strtotime(date('Y-m-d'));

    $isi_dir = scandir(APPPATH . "/cache");

    foreach ($isi_dir as $key => $value) {
        $str_tgl_file = filemtime(APPPATH . "/cache/" . $value);

        if (!in_array($value, $array_terlarang) and $str_tgl_file < $str_hari_ini) {
            unlink(APPPATH . "/cache/" . $value);
        }
    }
}

function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp  = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}

function terbilang($nilai)
{
    if ($nilai < 0) {
        $hasil = "minus " . trim(penyebut($nilai));
    } else {
        $hasil = trim(penyebut($nilai));
    }
    return $hasil;
}

function header_excel($judul = "", $bulan = "", $tahun = "")
{
    $header = "<font size=\"2\">
            </font>
            <center>
                <font size=\"5\">
                    " . $judul . "
                </font>
                <br>
                <font size=\"3\">
                    Periode " . nama_bulan($bulan) . " " . $tahun . "
                </font>
            </center>
            <br> ";

    return $header;
}
