<?php
session_start();

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$BASE = rtrim(str_replace('\\', '/', $scriptName), '/');
if ($BASE === '/' || $BASE === '\\') { $BASE = ''; }

$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$BASE_URL = $scheme . '://' . $host . $BASE;

if (!isset($_SESSION['admin_id'])) {
  header('Location: ' . $BASE . '/login');
  exit;
}


/* =======================
   1) Koneksi Database
   ======================= */
$dsn = getenv('DB_DSN') ?: ''; 
$user = getenv('DB_USERNAME') ?: '';       
$pass = getenv('DB_PASSWORD') ?: '';           
try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Exception $e) { http_response_code(500); die('DB error: '. $e->getMessage()); }

/* =======================
   2) Helper
   ======================= */
function to_mysql_date($str) {
  $str = trim((string)$str);
  if ($str === '') return null;
  $str = str_replace('/', '-', $str);
  $dt = DateTime::createFromFormat('d-m-Y', $str) ?: DateTime::createFromFormat('Y-m-d', $str);
  return $dt ? $dt->format('Y-m-d') : null;
}
function filename_code_from_date_reg($tglDDMMYYYY, $nomorReg) {
  // ddmmyy dari tanggal input
  $tglDDMMYYYY = str_replace('/', '-', trim((string)$tglDDMMYYYY));
  $dt = DateTime::createFromFormat('d-m-Y', $tglDDMMYYYY) ?: DateTime::createFromFormat('Y-m-d', $tglDDMMYYYY);
  $ddmmyy = $dt ? $dt->format('dmy') : date('dmy'); // fallback hari ini

  // nomor registrasi tanpa spasi/simbol, uppercase
  $reg = strtoupper(preg_replace('~[^A-Za-z0-9]+~', '', (string)$nomorReg));

  // rakit nama file
  return "QWERTY{$ddmmyy}{$reg}";
}
// Bersihkan string untuk nama folder/file (A-Z 0-9 - _)
function slugify($s){
  return strtoupper(preg_replace('~[^A-Za-z0-9_-]+~', '', (string)$s));
}

// Simpan file ke uploads/<REG>/
function move_file($key, $regSlug) {
  if (!isset($_FILES[$key]) || $_FILES[$key]['error'] !== UPLOAD_ERR_OK) return null;

  $baseUploads = __DIR__ . '/uploads';
  $targetDir   = $baseUploads . '/' . $regSlug;   // uploads/B1234AR

  if (!is_dir($targetDir)) {
    // 0775 agar bisa dibaca web server, recursive karena kita buat nested
    mkdir($targetDir, 0775, true);
  }

  $name = $_FILES[$key]['name'];
  $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION) ?: 'jpg');
  $stem = preg_replace('~[^A-Za-z0-9_-]+~', '-', pathinfo($name, PATHINFO_FILENAME));
  $fname = $stem . '-' . time() . '.' . $ext;

  $destAbs = $targetDir . '/' . $fname;
  if (move_uploaded_file($_FILES[$key]['tmp_name'], $destAbs)) {
    // kembalikan path RELATIF dari root proyek (dishub/)
    return 'uploads/' . $regSlug . '/' . $fname;
  }
  return null;
}

/* =======================
   3) Ambil data dari form
   ======================= */
$masa_berlaku_raw = $_POST['masa_berlaku'] ?? '';          
$masa_berlaku     = to_mysql_date($masa_berlaku_raw);

$owner_name       = $_POST['owner_name'] ?? '';
$owner_address    = $_POST['owner_address'] ?? '';
$blue_card        = $_POST['blue_card'] ?? '';
$blue_rfid        = $_POST['blue_rfid'] ?? '';

$nomor_uji        = $_POST['nomor_uji'] ?? '';
$sert_registrasi  = $_POST['sert_registrasi'] ?? '';
$nomor_registrasi = $_POST['nomor_registrasi'] ?? '';
$nomor_rangka     = $_POST['nomor_rangka'] ?? '';
$nomor_penggerak  = $_POST['nomor_penggerak'] ?? '';

$jenis_kendaraan      = $_POST['jenis_kendaraan']      ?? '';
$merk_tipe            = $_POST['merk_tipe']            ?? '';
$tahun_pembuatan      = $_POST['tahun_pembuatan']      ?? '';
$bahan_bakar          = $_POST['bahan_bakar']          ?? '';
$isi_silinder         = $_POST['isi_silinder']         ?? '';
$daya_motor           = $_POST['daya_motor']           ?? '';
$ukuran_ban           = $_POST['ukuran_ban']           ?? '';
$konfigurasi_sumbu    = $_POST['konfigurasi_sumbu']    ?? '';
$berat_kosong         = $_POST['berat_kosong']         ?? '';

$dim_panjang          = $_POST['dim_panjang']          ?? '';
$dim_julur_depan      = $_POST['dim_julur_depan']      ?? '';
$dim_lebar            = $_POST['dim_lebar']            ?? '';
$dim_julur_belakang   = $_POST['dim_julur_belakang']   ?? '';
$dim_tinggi           = $_POST['dim_tinggi']           ?? '';

$sumbu_1_2            = $_POST['sumbu_1_2']            ?? '';
$sumbu_2_3            = $_POST['sumbu_2_3']            ?? '';
$sumbu_3_4            = $_POST['sumbu_3_4']            ?? '';

$bak_pxlxt            = $_POST['bak_pxlxt']            ?? '';
$jbb_jbkb             = $_POST['jbb_jbkb']             ?? '';
$jbi_jbki             = $_POST['jbi_jbki']             ?? '';
$daya_angkut          = $_POST['daya_angkut']          ?? '';
$kelas_jalan          = $_POST['kelas_jalan']          ?? '';
$mst                  = $_POST['mst']                  ?? '';

$ef_rem_utama         = $_POST['ef_rem_utama']         ?? '';
$dif_rem_sumbu1       = $_POST['dif_rem_sumbu1']       ?? '';
$dif_rem_sumbu2       = $_POST['dif_rem_sumbu2']       ?? '';
$dif_rem_sumbu3       = $_POST['dif_rem_sumbu3']       ?? '';
$dif_rem_sumbu4       = $_POST['dif_rem_sumbu4']       ?? '';
$int_lampu_kanan      = $_POST['int_lampu_kanan']      ?? '';
$int_lampu_kiri       = $_POST['int_lampu_kiri']       ?? '';
$lampu_penyimp_kanan  = $_POST['lampu_penyimp_kanan']  ?? '';
$lampu_penyimp_kiri   = $_POST['lampu_penyimp_kiri']   ?? '';
$emisi_co             = $_POST['emisi_co']             ?? '';
$emisi_hc             = $_POST['emisi_hc']             ?? '';
$ketebalan_asap       = $_POST['ketebalan_asap']       ?? '';
$hasil_uji            = $_POST['hasil_uji']            ?? '';
$pelaksanaan_berkala  = $_POST['pelaksanaan_berkala']  ?? '';
$masa_berlaku_uji     = $_POST['masa_berlaku_uji']     ?? ''; 
$nama_penguji         = $_POST['nama_penguji']         ?? '';
$nrp_penguji          = $_POST['nrp_penguji']          ?? '';
$nama_kadis           = $_POST['nama_kadis']           ?? '';
$pangkat_kadis        = $_POST['pangkat_kadis']        ?? '';
$nip_kadis            = $_POST['nip_kadis']            ?? '';
$uptd                 = $_POST['uptd']                 ?? '';
$nama_direktur        = $_POST['nama_direktur']        ?? '';
$pangkat_direktur     = $_POST['pangkat_direktur']     ?? '';
$nip_direktur         = $_POST['nip_direktur']         ?? '';
$wilayah_numpang      = $_POST['wilayah_numpang']      ?? '';
$wilayah_asal         = $_POST['wilayah_asal']         ?? '';

$payload = $_POST;
unset($payload['masa_berlaku'],$payload['owner_name'],$payload['owner_address'],
      $payload['blue_card'],$payload['blue_rfid'],$payload['nomor_uji'],$payload['sert_registrasi'],
      $payload['nomor_registrasi'],$payload['nomor_rangka'],$payload['nomor_penggerak']);
$payload_json = json_encode($payload, JSON_UNESCAPED_UNICODE);

/* =======================
   4) Upload Foto
   ======================= */
$regSlug = slugify($nomor_registrasi ?: ('REG'.time()));
$foto_depan    = move_file('foto_depan',    $regSlug);
$foto_belakang = move_file('foto_belakang', $regSlug);
$foto_kanan    = move_file('foto_kanan',    $regSlug);
$foto_kiri     = move_file('foto_kiri',     $regSlug);


/* =======================
   5) Insert DB
   ======================= */
$sql="INSERT INTO numpang_uji
(admin_id, masa_berlaku, owner_name, nomor_uji, payload,
 foto_depan, foto_kanan, foto_kiri, foto_belakang)
VALUES
(:admin_id,:masa_berlaku,:owner_name,:nomor_uji,:payload,
 :foto_depan,:foto_kanan,:foto_kiri,:foto_belakang)";
$pdo->prepare($sql)->execute([
  ':admin_id'=>$_SESSION['admin_id'],
  ':masa_berlaku'=>$masa_berlaku,
  ':owner_name'=>$owner_name,
  ':nomor_uji'=>$nomor_uji,
  ':payload'=>$payload_json,
  ':foto_depan'=>$foto_depan, ':foto_kanan'=>$foto_kanan,
  ':foto_kiri'=>$foto_kiri,   ':foto_belakang'=>$foto_belakang,
]);

$insert_id = (int)$pdo->lastInsertId();

/* =======================
   5.1) Versi tampilan untuk template HTML
   ======================= */
function val($prim, $payload, $key) {
  $v = trim((string)$prim);
  if ($v !== '') return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
  if (isset($payload[$key]) && $payload[$key] !== '') {
    return htmlspecialchars((string)$payload[$key], ENT_QUOTES, 'UTF-8');
  }
  return '';
}

$V = [];
/* Identitas & BLUe */
$V['owner_name']    = val($owner_name,    $payload, 'owner_name');
$V['owner_address'] = val($owner_address, $payload, 'owner_address');
$V['blue_card']     = val($blue_card,     $payload, 'blue_card');
$V['blue_rfid']     = val($blue_rfid,     $payload, 'blue_rfid');

/* Identitas kendaraan */
$V['nomor_uji']        = val($nomor_uji,        $payload, 'nomor_uji');
$V['sert_registrasi']  = val($sert_registrasi,  $payload, 'sert_registrasi');
$V['nomor_registrasi'] = val($nomor_registrasi, $payload, 'nomor_registrasi');
$V['nomor_rangka']     = val($nomor_rangka,     $payload, 'nomor_rangka');
$V['nomor_penggerak']  = val($nomor_penggerak,  $payload, 'nomor_penggerak');

/* Spesifikasi teknis */
$V['jenis_kendaraan']   = val($jenis_kendaraan,   $payload, 'jenis_kendaraan');
$V['merk_tipe']         = val($merk_tipe,         $payload, 'merk_tipe');
$V['tahun_pembuatan']   = val($tahun_pembuatan,   $payload, 'tahun_pembuatan');
$V['bahan_bakar']       = val($bahan_bakar,       $payload, 'bahan_bakar');
$V['isi_silinder']      = val($isi_silinder,      $payload, 'isi_silinder');
$V['daya_motor']        = val($daya_motor,        $payload, 'daya_motor');
$V['ukuran_ban']        = val($ukuran_ban,        $payload, 'ukuran_ban');
$V['konfigurasi_sumbu'] = val($konfigurasi_sumbu, $payload, 'konfigurasi_sumbu');
$V['berat_kosong']      = val($berat_kosong,      $payload, 'berat_kosong');

/* Dimensi, sumbu, bak */
$V['dim_panjang']        = val($dim_panjang,        $payload, 'dim_panjang');
$V['dim_lebar']          = val($dim_lebar,          $payload, 'dim_lebar');
$V['dim_tinggi']         = val($dim_tinggi,         $payload, 'dim_tinggi');
$V['dim_julur_depan']    = val($dim_julur_depan,    $payload, 'dim_julur_depan');
$V['dim_julur_belakang'] = val($dim_julur_belakang, $payload, 'dim_julur_belakang');

$V['sumbu_1_2'] = val($sumbu_1_2, $payload, 'sumbu_1_2');
$V['sumbu_2_3'] = val($sumbu_2_3, $payload, 'sumbu_2_3');
$V['sumbu_3_4'] = val($sumbu_3_4, $payload, 'sumbu_3_4');

$V['bak_pxlxt']    = val($bak_pxlxt,    $payload, 'bak_pxlxt');
$V['jbb_jbkb']     = val($jbb_jbkb,     $payload, 'jbb_jbkb');
$V['jbi_jbki']     = val($jbi_jbki,     $payload, 'jbi_jbki');
$V['daya_angkut']  = val($daya_angkut,  $payload, 'daya_angkut');
$V['kelas_jalan']  = val($kelas_jalan,  $payload, 'kelas_jalan');
$V['mst']          = val($mst,          $payload, 'mst');

/* Hasil uji: rem, lampu, emisi, keterangan */
$V['ef_rem_utama']   = val($ef_rem_utama,   $payload, 'ef_rem_utama');
$V['dif_rem_sumbu1'] = val($dif_rem_sumbu1, $payload, 'dif_rem_sumbu1');
$V['dif_rem_sumbu2'] = val($dif_rem_sumbu2, $payload, 'dif_rem_sumbu2');
$V['dif_rem_sumbu3'] = val($dif_rem_sumbu3, $payload, 'dif_rem_sumbu3');
$V['dif_rem_sumbu4'] = val($dif_rem_sumbu4, $payload, 'dif_rem_sumbu4');

$V['int_lampu_kanan']    = val($int_lampu_kanan,    $payload, 'int_lampu_kanan');
$V['int_lampu_kiri']     = val($int_lampu_kiri,     $payload, 'int_lampu_kiri');
$V['lampu_penyimp_kanan']= val($lampu_penyimp_kanan,$payload, 'lampu_penyimp_kanan');
$V['lampu_penyimp_kiri'] = val($lampu_penyimp_kiri, $payload, 'lampu_penyimp_kiri');

$V['emisi_co']        = val($emisi_co,        $payload, 'emisi_co');
$V['emisi_hc']        = val($emisi_hc,        $payload, 'emisi_hc');
$V['ketebalan_asap']  = val($ketebalan_asap,  $payload, 'ketebalan_asap');

$V['hasil_uji']            = val($hasil_uji,            $payload, 'hasil_uji');
$V['pelaksanaan_berkala']  = val($pelaksanaan_berkala,  $payload, 'pelaksanaan_berkala');
$V['masa_berlaku_uji']     = val($masa_berlaku_uji,     $payload, 'masa_berlaku_uji');
$V['nama_penguji']         = val($nama_penguji,         $payload, 'nama_penguji');
$V['nrp_penguji']          = val($nrp_penguji,          $payload, 'nrp_penguji');
$V['nama_kadis']           = val($nama_kadis,           $payload, 'nama_kadis');
$V['pangkat_kadis']        = val($pangkat_kadis,        $payload, 'pangkat_kadis');
$V['nip_kadis']            = val($nip_kadis,            $payload, 'nip_kadis');
$V['uptd']                 = val($uptd,                 $payload, 'uptd');
$V['nama_direktur']        = val($nama_direktur,        $payload, 'nama_direktur');
$V['pangkat_direktur']     = val($pangkat_direktur,     $payload, 'pangkat_direktur');
$V['nip_direktur']         = val($nip_direktur,         $payload, 'nip_direktur');
$V['wilayah_numpang']      = val($wilayah_numpang,      $payload, 'wilayah_numpang');
$V['wilayah_asal']         = val($wilayah_asal,         $payload, 'wilayah_asal');


/* =======================
   6) Siapkan data tampilan & buat FILE HTML: 
   ======================= */

$code = filename_code_from_date_reg($masa_berlaku_raw, $nomor_registrasi);
if (empty($code)) {
  $code = 'QWERTY' . date('dmyHis');
}

$today  = new DateTime('today');
$expiry = $masa_berlaku ? DateTime::createFromFormat('Y-m-d', $masa_berlaku) : null;

if ($expiry && $expiry < $today) {
  $status_text  = 'Hasil Uji Sudah Tidak Berlaku';
  $status_color = '#d93025'; 
} else {
  $status_text  = 'Hasil Uji Masih Berlaku';
  $status_color = '#49b64f'; 
}

function indo_date_long($ymd) {
  if (!$ymd) return '-';
  $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  $dt = DateTime::createFromFormat('Y-m-d', $ymd);
  if (!$dt) return $ymd;
  $d = (int)$dt->format('d'); $m = (int)$dt->format('m'); $y = $dt->format('Y');
  return sprintf('%02d %s %s', $d, $bulan[$m], $y);
}
$masa_berlaku_nice = indo_date_long($masa_berlaku);

$base      = '../../../'; 
$logo_path = $base . 'Logo_Kementerian_Perhubungan_Indonesia_(Kemenhub).png';

/* HTML template minimal – mandiri (inline CSS) */
$html = <<<HTML
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hasil Uji Berkala Kendaraan Bermotor</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/png" href="/favicon.png">
<style>
  body{margin:0;font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;background:#f2f3f9;color:#6b6f82}
  .topbar{background:linear-gradient(180deg,#041246,#0b2977);color:#fff;position: fixed;width: 100vw;top:0px;}
  .topbar__inner{height:80px;display:flex;align-items:center;gap:12px;justify-content:center}
  .topbar__inner img{width:34px;height:34px;object-fit:contain}
  .topbar__inner .brand{font-family: 'Quicksand', 'Segoe UI', Arial, sans-serif;font-size:18.5px;font-weight:800;letter-spacing:.6px;text-transform:uppercase}

  .wrap{max-width:100%;margin:16px auto;padding:0 16px}
  .title{font-family: 'Quicksand', 'Segoe UI', Arial, sans-serif;font-size:21px;font-weight:400;text-align:center;color:#464855;margin:110px 0 7px}
  .status{font-family: 'Quicksand', 'Segoe UI', Arial, sans-serif;font-size:21px;font-weight:700;text-align:center;margin:0px 0 7px;color:{$status_color};}
  .subtitle{font-family: 'Quicksand', 'Segoe UI', Arial, sans-serif;font-size:21px;font-weight:700;text-align:center;color:#673ab7;margin:0px 0 7px;}
  .info{font-family: 'Quicksand', 'Segoe UI', Arial, sans-serif;font-size:21px;text-align:center;color:#464855;margin:6px 0 7px}

  .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  @media(max-width:900px){.grid-2{grid-template-columns:1fr;}   table {display: block;width: 100%;overflow-x: auto;}
  td, th {word-break: break-word;white-space: normal;}}

  .section-title{font-size:14px;font-weight:700;color:#6b6f82;margin:14px 0;letter-spacing:.4px;}
  .card{background:#fff;border:1px solid #e7e7ef;border-radius:2px;box-shadow:0 2px 6px rgba(0,0,0,.05)}
  .table{width:100%;border-collapse:collapse;border-radius:2px;overflow:hidden}
  .table td{padding:14px;border-bottom:1px solid #ececf5;font-size:14px;}
  .table tr:last-child td{border-bottom:none}
  .label{width:38%;white-space:normal;color:rgba(0, 0, 0, .87);font-weight:500}
  .sep{width:2%;text-align:center;color:rgba(0, 0, 0, .87);}
  .val{width:60%;color:rgba(0, 0, 0, .87);}

  .photos{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:10px}
  @media(max-width:1000px){.photos{grid-template-columns:repeat(2,1fr)}}
  @media(max-width:560px){.photos{grid-template-columns:1fr}}
  .photo-card{background:#fff;border:1px solid #e7e7ef;border-radius:2px;box-shadow:0 2px 6px rgba(0,0,0,.05);padding:14px;text-align:center}
  .photo-title{font-weight:700;color:#7a7f92;margin-bottom:10px}
  .photo-card img{width:100%;max-height: 400px;object-fit:cover;border:8px solid #fff;display: block;box-sizing: border-box; margin: 0 auto;}
  .table tr:hover {
  background-color: #eeeeee; 
  transition: background-color 0.2s ease; 
}
.row--double {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
</style>

</head>
<body>
<header class="topbar">
  <div class="topbar__inner">
    <img src="{$logo_path}" alt="Kemenhub">
    <div class="brand">KEMENTERIAN PERHUBUNGAN</div>
  </div>
</header>

<div class="wrap">
  <div class="title">UJI BERKALA KENDARAAN BERMOTOR</div>
  <div class="status">{$status_text}</div>
  <div class="subtitle">(Numpang Uji)</div>
  <div class="info">Masa Berlaku Hasil Uji : {$masa_berlaku_nice}</div>

  <script>
    (function(){
      var expiry = "{$masa_berlaku}"; // format: YYYY-MM-DD
      var statusEl = document.querySelector('.status');
      if (!expiry || !statusEl) return;

      var now = new Date(); now.setHours(0,0,0,0);
      var exp = new Date(expiry + "T00:00:00");

      if (isNaN(exp.getTime())) return;

      if (exp < now) {
        statusEl.textContent = "Hasil Uji Sudah Tidak Berlaku";
        statusEl.style.color = "#d93025";
      } else {
        statusEl.textContent = "Hasil Uji Masih Berlaku";
        statusEl.style.color = "#8bc34a";
      }
    })();
  </script>

<div class="grid-2">
  <!-- Identitas Pemilik -->
  <div>
    <div class="section-title">IDENTITAS PEMILIK KENDARAAN DAN BLUe</div>
    <div class="card">
      <table class="table">
        <tr><td class="label">Nama Pemilik</td><td class="sep">:</td><td class="val">{$V['owner_name']}</td></tr>
        <tr><td class="label">Alamat Pemilik</td><td class="sep">:</td><td class="val">{$V['owner_address']}</td></tr>
        <tr><td class="label" style="font-weight:700">Identitas BLUe</td><td class="sep"></td><td class="val"></td></tr>
        <tr><td class="label">Nomor Kartu</td><td class="sep">:</td><td class="val">{$V['blue_card']}</td></tr>
        <tr><td class="label">Nomor RFID</td><td class="sep">:</td><td class="val">{$V['blue_rfid']}</td></tr>
      </table>
    </div>
  </div>

  <!-- Identitas Kendaraan -->
  <div>
    <div class="section-title">IDENTITAS KENDARAAN BERMOTOR</div>
    <div class="card">
      <table class="table">
        <tr><td class="label">Nomor Uji Kendaraan</td><td class="sep">:</td><td class="val">{$V['nomor_uji']}</td></tr>
        <tr><td class="label">Nomor dan Tanggal SRUT</td><td class="sep">:</td><td class="val">{$V['sert_registrasi']}</td></tr>
        <tr><td class="label">Nomor Registrasi Kendaraan</td><td class="sep">:</td><td class="val">{$V['nomor_registrasi']}</td></tr>
        <tr><td class="label">Nomor Rangka Kendaraan</td><td class="sep">:</td><td class="val">{$V['nomor_rangka']}</td></tr>
        <tr><td class="label">Nomor Motor Penggerak</td><td class="sep">:</td><td class="val">{$V['nomor_penggerak']}</td></tr>
      </table>
    </div>
  </div>
</div>

<div class="section-title">FOTO KENDARAAN</div>
<div class="photos">
  <div class="photo-card">
    <div class="photo-title">Tampak Depan</div>
    <img src="{$base}{$foto_depan}" alt="Depan">
  </div>
  <div class="photo-card">
    <div class="photo-title">Tampak Belakang</div>
    <img src="{$base}{$foto_belakang}" alt="Belakang">
  </div>
  <div class="photo-card">
    <div class="photo-title">Tampak Kanan</div>
    <img src="{$base}{$foto_kanan}" alt="Kanan">
  </div>
  <div class="photo-card">
    <div class="photo-title">Tampak Kiri</div>
    <img src="{$base}{$foto_kiri}" alt="Kiri">
  </div>
</div>

<style>
  .table .subhead td{
    background:#f7f7fb;
    font-weight:700;
    color:#5d6172;
  }
</style>

<div class="grid-2" style="margin-top:18px">
  <!-- KIRI: Spesifikasi Teknis -->
  <div>
    <div class="section-title">SPESIFIKASI TEKNIS KENDARAAN BERMOTOR</div>
    <div class="card">
      <table class="table">
        <tr><td class="label">Jenis Kendaraan</td><td class="sep">:</td><td class="val">{$V['jenis_kendaraan']}</td></tr>
        <tr><td class="label">Merk / Tipe Kendaraan</td><td class="sep">:</td><td class="val">{$V['merk_tipe']}</td></tr>
        <tr><td class="label">Tahun Pembuatan / Perakitan</td><td class="sep">:</td><td class="val">{$V['tahun_pembuatan']}</td></tr>
        <tr><td class="label">Bahan Bakar / Sumber Energi</td><td class="sep">:</td><td class="val">{$V['bahan_bakar']}</td></tr>
        <tr><td class="label">Isi Silinder</td><td class="sep">:</td><td class="val">{$V['isi_silinder']}</td></tr>
        <tr><td class="label">Daya Motor</td><td class="sep">:</td><td class="val">{$V['daya_motor']}</td></tr>
        <tr><td class="label">Ukuran Ban</td><td class="sep">:</td><td class="val">{$V['ukuran_ban']}</td></tr>
        <tr><td class="label">Konfigurasi Sumbu</td><td class="sep">:</td><td class="val">{$V['konfigurasi_sumbu']}</td></tr>
        <tr><td class="label">Berat Kosong Kendaraan</td><td class="sep">:</td><td class="val">{$V['berat_kosong']}</td></tr>

        <tr><td class="label" style="font-weight:700">Dimensi utama kendaraan bermotor</td><td class="sep"></td><td class="val"></td></tr>

        <tr><td class="label"><i class="fa-regular fa-circle-check"></i> Panjang</td><td class="sep">:</td><td class="val">{$V['dim_panjang']}</td></tr>
        <tr><td class="label"><i class="fa-regular fa-circle-check"></i> Lebar</td><td class="sep">:</td><td class="val">{$V['dim_lebar']}</td></tr>
        <tr><td class="label"><i class="fa-regular fa-circle-check"></i> Tinggi</td><td class="sep">:</td><td class="val">{$V['dim_panjang']}</td></tr>
        <tr><td class="label"><i class="fa-regular fa-circle-check"></i> Julur Depan</td><td class="sep">:</td><td class="val">{$V['dim_panjang']}</td></tr>
        <tr><td class="label"><i class="fa-regular fa-circle-check"></i> Julur Belakang</td><td class="sep">:</td><td class="val">{$V['dim_panjang']}</td></tr>

        <tr><td class="label" style="font-weight:700">Jarak Sumbu</td><td class="sep"></td><td class="val"></td></tr>

        <tr><td class="label"><i class="fa-regular fa-circle-check"></i> Sumbu I - II</td><td class="sep">:</td><td class="val">{$V['sumbu_1_2']}</td></tr>
        <tr><td class="label"><i class="fa-regular fa-circle-check"></i> Sumbu II - III</td><td class="sep">:</td><td class="val">{$V['sumbu_2_3']}</td></tr>
        <tr><td class="label"><i class="fa-regular fa-circle-check"></i> Sumbu III - IV</td><td class="sep">:</td><td class="val">{$V['sumbu_3_4']}</td></tr>

        <tr><td class="label" style="font-weight:700">Dimensi bak muatan / tangki</td><td class="sep"></td><td class="val"></td></tr>

        <tr><td class="label"><i class="fa-regular fa-circle-check"></i> Panjang x Lebar x Tinggi</td><td class="sep">:</td><td class="val">{$V['bak_pxlxt']}</td></tr>
        <tr><td class="label">JBB / JBKB</td><td class="sep">:</td><td class="val">{$V['jbb_jbkb']}</td></tr>
        <tr><td class="label">JBI / JBKI</td><td class="sep">:</td><td class="val">{$V['jbi_jbki']}</td></tr>
        <tr><td class="label">Daya angkut (orang / barang)</td><td class="sep">:</td><td class="val">{$V['daya_angkut']}</td></tr>
        <tr><td class="label">Kelas jalan</td><td class="sep">:</td><td class="val">{$V['kelas_jalan']}</td></tr>
        <tr><td class="label">MST</td><td class="sep">:</td><td class="val">{$V['mst']}</td></tr>
      </table>
    </div>
  </div>

  <div>
    <div class="section-title">RINCIAN HASIL UJI</div>
    <div class="card">
      <table class="table">
        <tr><td class="label" style="font-weight:700">Hasil Uji Rem</td><td class="sep"></td><td class="val"></td></tr>

        <tr><td class="label">Efisiensi Rem Utama</td><td class="sep">:</td><td class="val">{$V['ef_rem_utama']}</td></tr>
        <tr><td class="label">Dif. Rem Utama Sumbu I</td><td class="sep">:</td><td class="val">{$V['dif_rem_sumbu1']}</td></tr>
        <tr><td class="label">Dif. Rem Utama Sumbu II</td><td class="sep">:</td><td class="val">{$V['dif_rem_sumbu2']}</td></tr>
        <tr><td class="label">Dif. Rem Utama Sumbu III</td><td class="sep">:</td><td class="val">{$V['dif_rem_sumbu3']}</td></tr>
        <tr><td class="label">Dif. Rem Utama Sumbu IV</td><td class="sep">:</td><td class="val">{$V['dif_rem_sumbu4']}</td></tr>

        <tr><td class="label" style="font-weight:700">Hasil Uji Lampu</td><td class="sep"></td><td class="val"></td></tr>

        <tr><td class="label">Intensitas Lampu Utama Kanan</td><td class="sep">:</td><td class="val">{$V['int_lampu_kanan']}</td></tr>
        <tr><td class="label">Intensitas Lampu Utama Kiri</td><td class="sep">:</td><td class="val">{$V['int_lampu_kiri']}</td></tr>
        <tr><td class="label">Lampu Utama Penyimpangan Kanan</td><td class="sep">:</td><td class="val">{$V['lampu_penyimp_kanan']}</td></tr>
        <tr><td class="label">Lampu Utama Penyimpangan Kiri</td><td class="sep">:</td><td class="val">{$V['lampu_penyimp_kiri']}</td></tr>

        <tr><td class="label" style="font-weight:700">Hasil Uji Emisi</td><td class="sep"></td><td class="val"></td></tr>

        <tr><td class="label">Emisi CO</td><td class="sep">:</td><td class="val">{$V['emisi_co']}</td></tr>
        <tr><td class="label">Emisi HC</td><td class="sep">:</td><td class="val">{$V['emisi_hc']}</td></tr>
        <tr><td class="label">Ketebalan Asap</td><td class="sep">:</td><td class="val">{$V['ketebalan_asap']}</td></tr>

      </table>
    </div>

    <div class="section-title">KETERANGAN HASIL UJI</div>
    <div class="card">
      <table class="table">
        <tr><td class="label">HASIL UJI</td><td class="sep">:</td><td class="val">{$V['hasil_uji']}</td></tr>
        <tr><td class="label">Pelaksanaan uji berkala</td><td class="sep">:</td><td class="val">{$V['pelaksanaan_berkala']}</td></tr>
        <tr><td class="label">Masa berlaku uji berkala</td><td class="sep">:</td><td class="val">{$V['masa_berlaku_uji']}</td></tr>

        <tr><td class="label">Nama Petugas Penguji</td><td class="sep">:</td><td class="val">{$V['nama_penguji']}</td></tr>
        <tr><td class="label">NRP (Nomor Registrasi Penguji)</td><td class="sep">:</td><td class="val">{$V['nrp_penguji']}</td></tr>

        <tr><td class="label">Nama Kepala Dinas</td><td class="sep">:</td><td class="val">{$V['nama_kadis']}</td></tr>
        <tr><td class="label">Pangkat Kepala Dinas</td><td class="sep">:</td><td class="val">{$V['pangkat_kadis']}</td></tr>
        <tr><td class="label">NIP Kepala Dinas</td><td class="sep">:</td><td class="val">{$V['nip_kadis']}</td></tr>

        <tr><td class="label">Unit Pelaksana Uji Berkala Kendaraan Bermotor</td><td class="sep">:</td><td class="val">{$V['uptd']}</td></tr>

        <tr><td class="label" style="font-weight:700">Asal Kendaraan Wajib Uji</td><td class="sep"></td><td class="val"></td></tr>

        <tr><td class="label">Wilayah Numpang Uji</td><td class="sep">:</td><td class="val">{$V['wilayah_numpang']}</td></tr>
        <tr><td class="label">Wilayah Asal</td><td class="sep">:</td><td class="val">{$V['wilayah_asal']}</td></tr>

        </table>
    </div>
  </div>


</div> <!-- /wrap -->

</body>
</html>
HTML;

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$BASE = rtrim(str_replace('\\', '/', $scriptName), '/');
if ($BASE === '/' || $BASE === '\\') { $BASE = ''; }

$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$BASE_URL = $scheme . '://' . $host . $BASE;

$outFile = __DIR__ . '/qr/v1/rfid/' . rawurlencode($code) . '.html';
file_put_contents($outFile, $html);

require_once __DIR__ . '/generate_sitemap.php';
if (function_exists('generate_sitemap_rfid')) {
  generate_sitemap_rfid('qr/v1/rfid');
}

$target = $BASE_URL . '/qr/v1/rfid/' . rawurlencode($code) . '.html';
header('Location: ' . $target);
exit;

