<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$BASE = rtrim(str_replace('\\', '/', $scriptName), '/');
if ($BASE === '/' || $BASE === '\\') { $BASE = ''; }

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Numpang Uji</title>
  <base href="<?= htmlspecialchars($BASE ?: '/', ENT_QUOTES) ?>/">
  <link rel="stylesheet" href="dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <link rel="icon" type="image/png" href="/favicon.png">

</head>
<body>

 <header class="topbar">
  <div class="topbar__inner">
      <div class="brand">
          <img class="brand__img" src="Logo_Kementerian_Perhubungan_Indonesia_(Kemenhub).png" alt="Kementerian Perhubungan">
          <span class="brand__text">KEMENTERIAN PERHUBUNGAN</span>
      </div>
      <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i>Logout</a>
  </div>
</header>

  <main class="page">
    <?php
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'];
        $dir    = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($dir === '/' || $dir === '\\') { $dir = ''; }
        $ACTION = $scheme . '://' . $host . $dir . '/save_uji.php';
    ?>
    <form action="<?= htmlspecialchars($ACTION, ENT_QUOTES) ?>" method="post" enctype="multipart/form-data">
        <!-- TANGGAL MASA BERLAKU -->
        <section class="section">
        <h1 class="page-title">UJI BERKALA KENDARAAN BERMOTOR</h1>
        <h2 class="page-subtitle">(Numpang Uji)</h2><br><br>
            <div class="card-wrapper">
                <h3 class="card-title">MASA BERLAKU HASIL UJI</h3>
                <div class="card card--center">
                    <div class="masa-berlaku-row">
                        <label for="masa_berlaku">Masa Berlaku Hasil Uji</label>
                        <input type="text" id="masa_berlaku" name="masa_berlaku"  placeholder="Example : 27-06-2024" >
                    </div>
                    <hr>
                </div>
            </div>


        </section>

        <!-- IDENTITAS -->
        <section class="section">
        <div class="grid g-2">
            <!-- Kiri -->
            <div class="card-wrapper">
                <h3 class="card-subtitle">IDENTITAS PEMILIK KENDARAAN</h3>
                <div class="card">

                <div class="row">
                    <div class="row__label">Nama Pemilik</div>
                    <div class="row__ctrl"><input type="text" name="owner_name"  placeholder="Enter Nama Pemilik" ></div>
                </div>

                <div class="row">
                    <div class="row__label">Alamat Pemilik</div>
                    <div class="row__ctrl"><input type="text" name="owner_address"  placeholder="Enter Alamat Pemilik" ></div>
                </div>

                <div class="row row--section">Identitas BLUE</div>

                <div class="row">
                    <div class="row__label">Nomor Kartu</div>
                    <div class="row__ctrl"><input type="text" name="blue_card"  placeholder="Enter Nomor Kartu" ></div>
                </div>

                <div class="row">
                    <div class="row__label">Nomor RFID</div>
                    <div class="row__ctrl"><input type="text" name="blue_rfid"  placeholder="Enter Nomor RFID" ></div>
                </div>
                </div>
            </div>

            <!-- Kanan -->
            <div class="card-wrapper">
                <h3 class="card-subtitle">IDENTITAS KENDARAAN BERMOTOR</h3>
                <div class="card">

                <div class="row">
                    <div class="row__label">Nomor Uji Kendaraan</div>
                    <div class="row__ctrl"><input type="text"  name="nomor_uji"  placeholder="Enter Nomor Uji Kendaraan" ></div>
                </div>

                <div class="row">
                    <div class="row__label">Nomor dan Tanggal Sertifikat Registrasi</div>
                    <div class="row__ctrl"><input type="text" name="sert_registrasi"  placeholder="Enter Nomor dan Tanggal Sertifikat Registrasi" ></div>
                </div>

                <div class="row">
                    <div class="row__label">Nomor Registrasi Kendaraan</div>
                    <div class="row__ctrl"><input type="text" name="nomor_registrasi"  placeholder="Enter Nomor Registrasi Kendaraan" ></div>
                </div>

                <div class="row">
                    <div class="row__label">Nomor Rangka Kendaraan</div>
                    <div class="row__ctrl"><input type="text" name="nomor_rangka"  placeholder="Enter Nomor Rangka Kendaraan" ></div>
                </div>

                <div class="row">
                    <div class="row__label">Nomor Motor Penggerak</div>
                    <div class="row__ctrl"><input type="text" name="nomor_penggerak"  placeholder="Enter Nomor Motor Penggerak" ></div>
                </div>
                </div>
            </div>
        </div>
        </section>

        <!-- UPLOAD FOTO -->
        <section class="section">
            <div class="card card--narrow">

                <div class="dropzones">

                <!-- Depan -->
                <label class="dz">
                    <input type="file" name="foto_depan" accept="image/*" hidden>
                    <span class="dz__text">Drop photo tampak depan or click to browse</span>
                </label>

                <!-- Kanan -->
                <label class="dz">
                    <input type="file" name="foto_kanan" accept="image/*" hidden>
                    <span class="dz__text">Drop photo tampak kanan or click to browse</span>
                </label>

                <!-- Kiri -->
                <label class="dz">
                    <input type="file" name="foto_kiri" accept="image/*" hidden>
                    <span class="dz__text">Drop photo tampak kiri or click to browse</span>
                </label>

                <!-- Belakang -->
                <label class="dz">
                    <input type="file" name="foto_belakang" accept="image/*" hidden>
                    <span class="dz__text">Drop photo tampak belakang or click to browse</span>
                </label>

                </div>

                <div class="actions">
                <small class="help">Supported file types: JPG, PNG</small>
                </div>

            </div>
        </section>

        <!-- SPESIFIKASI & KETERANGAN HASIL UJI -->
        <section class="section">
        <div class="grid g-2">
            <!-- Kolom kiri -->
            <div class="card-wrapper">
                <h3 class="card-subtitle">SPESIFIKASI TEKNIS KENDARAAN BERMOTOR</h3>
                <div class="card">

                <div class="row"><div class="row__label">Jenis</div><div class="row__ctrl"><input type="text" name="jenis_kendaraan"  placeholder="Enter Jenis" ></div></div>
                <div class="row"><div class="row__label">Merk / Tipe</div><div class="row__ctrl"><input type="text" name="merk_tipe"  placeholder="Enter Merk/Tipe" ></div></div>
                <div class="row"><div class="row__label">Tahun Pembuatan / Perakitan</div><div class="row__ctrl"><input type="text"  name="tahun_pembuatan"  placeholder="Enter Tahun Pembuatan / Perakitan" ></div></div>
                <div class="row"><div class="row__label">Bahan Bakar / Sumber Energi</div><div class="row__ctrl"><input type="text" name="bahan_bakar"   placeholder="Enter Bahan Bakar / Sumber Energi" ></div></div>
                <div class="row"><div class="row__label">Isi Silinder</div><div class="row__ctrl"><input type="text" name="isi_silinder"  placeholder="Enter Isi Silinder" ></div></div>
                <div class="row"><div class="row__label">Daya Motor</div><div class="row__ctrl"><input type="text" name="daya_motor"  placeholder="Enter Daya Motor" ></div></div>
                <div class="row"><div class="row__label">Ukuran Ban</div><div class="row__ctrl"><input type="text" name="ukuran_ban"  placeholder="Enter Ukuran Ban" ></div></div>
                <div class="row"><div class="row__label">Konfigurasi Sumbu</div><div class="row__ctrl"><input type="text" name="konfigurasi_sumbu"  placeholder="Enter Konfigurasi Sumbu" ></div></div>
                <div class="row"><div class="row__label">Berat Kosong Kendaraan</div><div class="row__ctrl"><input type="text"  name="berat_kosong"  placeholder="Enter Berat Kosong Kendaraan" ></div></div>

                <!-- Dimensi utama kendaraan bermotor -->
                    <div class="row row--section">Dimensi utama kendaraan bermotor</div>

                    <!-- Panjang + Julur Depan -->
                    <div class="row row--group">
                    <div class="label-inline"><i class="fa-regular fa-circle-check"></i> Panjang</div>
                    <input type="text" name="dim_panjang"  placeholder="Enter Panjang" >

                    <div class="label-inline"><i class="fa-regular fa-circle-check"></i> Julur Depan</div>
                    <input type="text" name="dim_julur_depan"  placeholder="Enter Julur Depan">
                    </div>

                    <!-- Lebar + Julur Belakang -->
                    <div class="row row--group">
                    <div class="label-inline"><i class="fa-regular fa-circle-check"></i> Lebar</div>
                    <input type="text" name="dim_lebar"   placeholder="Enter Lebar" >

                    <div class="label-inline"><i class="fa-regular fa-circle-check"></i> Julur Belakang</div>
                    <input type="text" name="dim_julur_belakang"  placeholder="Enter Julur Belakang" >
                    </div>

                    <!-- Tinggi (single) -->
                    <div class="row row--group ">
                    <div class="label-inline"><i class="fa-regular fa-circle-check"></i> Tinggi</div>
                    <input type="text"  name="dim_tinggi"  placeholder="Enter Tinggi" >
                    </div>


                <div class="row row--section">Jarak Sumbu</div>
                <div class="field"><label>Sumbu I – II</label><input type="text" name="sumbu_1_2"  placeholder="Enter Sumbu I - II" ></div>
                <div class="field"><label>Sumbu II – III</label><input type="text" name="sumbu_2_3"  placeholder="Enter Sumbu II - III" ></div>
                <div class="field"><label>Sumbu III – IV</label><input type="text" name="sumbu_3_4"  placeholder="Enter Sumbu III - IV" ></div>

                <div class="row row--section">Dimensi bak muatan / tangki</div>
                <div class="field"><label>Panjang x Lebar x Tinggi</label><input type="text" name="bak_pxlxt"  placeholder="Enter Panjang x Lebar x Tinggi" ></div>

                <div class="field"><label>JBB / JBKB</label><input type="text" name="jbb_jbkb"  placeholder="Enter JBB / JBKB" ></div>
                <div class="field"><label>JBI / JBKI</label><input type="text" name="jbi_jbki"   placeholder="Enter JBI / JBKI" ></div>
                <div class="field"><label>Daya angkut (orang / kg)</label><input type="text" name="daya_angkut"  placeholder="Enter Daya angkut (orang / kg)" ></div>
                <div class="field"><label>Kelas jalan terendah yang boleh dilalui</label><input type="text" name="kelas_jalan"  placeholder="Enter Kelas jalan terendah yang boleh dilalui" ></div>
                <div class="field"><label>MST</label><input type="text" name="mst"  placeholder="Enter MST" ></div>
                </div>
            </div>

            <!-- Kolom kanan -->
            <div class="card-wrapper">
                <h3 class="card-subtitle">KETERANGAN HASIL UJI</h3>
                <div class="card">

                <div class="field"><label>Rem Utama</label><input type="text" name="ef_rem_utama"  placeholder="Example: 3250 kg" ></div>
                <div class="field"><label>Rem Utama Sumbu I</label><input type="text" name="dif_rem_sumbu1"  placeholder="Example: 0.47 %" ></div>
                <div class="field"><label>Rem Utama Sumbu II</label><input type="text" name="dif_rem_sumbu2"  placeholder="Example: 0.47 %" ></div>
                <div class="field"><label>Rem Utama Sumbu III</label><input type="text" name="dif_rem_sumbu3"  placeholder="Example: 0.47 %" ></div>
                <div class="field"><label>Rem Utama Sumbu IV</label><input type="text" name="dif_rem_sumbu4"  placeholder="Example: 0.47 %" ></div>

                <div class="field"><label>Lampu Utama Kanan</label><input type="text" name="int_lampu_kanan"  placeholder="Example: 13.000 cd" ></div>
                <div class="field"><label>Lampu Utama Kiri</label><input type="text" name="int_lampu_kiri"  placeholder="Example: 13.000 cd" ></div>
                <div class="field"><label>Lampu Utama Penyimpangan Kanan</label><input type="text" name="lampu_penyimp_kanan"  placeholder="Example: 0.20" ></div>
                <div class="field"><label>Lampu Utama Penyimpangan Kiri</label><input type="text" name="lampu_penyimp_kiri"  placeholder="Example: 0.20" ></div>

                <div class="field"><label>Emisi CO</label><input type="text" name="emisi_co"  placeholder="Example: 10 %" ></div>
                <div class="field"><label>Emisi HC</label><input type="text" name="emisi_hc"  placeholder="Example: 2 ppm" ></div>
                <div class="field"><label>Ketebalan Asap</label><input type="text" name="ketebalan_asap"  placeholder="Example: 32 %" ></div>

                <div class="field"><label>KETERANGAN</label><input type="text" name="hasil_uji"  placeholder="Example: LULUS UJI BERKALA" ></div>

                <div class="field"><label>Pelaksanaan uji berkala</label><input type="text" name="pelaksanaan_berkala"  placeholder="Example: 27-06-2024" ></div>
                <div class="field"><label>Masa berlaku uji berkala</label><input type="text" name="masa_berlaku_uji"  placeholder="Example: 27-06-2024" ></div>

                <div class="field"><label>Nama Petugas Penguji</label><input type="text" name="nama_penguji"  placeholder="Example: NAMA PENGUJI" ></div>
                <div class="field"><label>NRP Petugas Penguji</label><input type="text" name="nrp_penguji"  placeholder="Example: 018.005.PT3.01.002" ></div>

                <div class="field"><label>Nama Kepala Dinas</label><input type="text" name="nama_kadis"  placeholder="Example: NAMA KADIS" ></div>
                <div class="field"><label>Pangkat Kepala Dinas</label><input type="text" name="pangkat_kadis"  placeholder="Example: Pangkat" ></div>
                <div class="field"><label>NIP Kepala Dinas</label><input type="text" name="nip_kadis"  placeholder="Example: 19691003 199703 1003" ></div>

                <div class="field"><label>Unit Pelaksana Teknis Daerah Pengujian</label><input type="text" name="uptd"  placeholder="Example: DISHUB KABUPATEN ..." ></div>

                <div class="field"><label>Nama Direktur</label><input type="text" name="nama_direktur"  placeholder="Example: Nama Direktur" ></div>
                <div class="field"><label>Pangkat Direktur</label><input type="text" name="pangkat_direktur"  placeholder="Example: Pangkat" ></div>
                <div class="field"><label>NIP Direktur</label><input type="text" name="nip_direktur"  placeholder="Example: 196408291994031003" ></div>

                <div class="field"><label>Wilayah Numpang Uji</label><input type="text" name="wilayah_numpang"  placeholder="Example: DISHUB KABUPATEN ..." ></div>
                <div class="field"><label>Wilayah Asal</label><input type="text" name="wilayah_asal"  placeholder="Example: DISHUB JAKARTA BARAT" ></div>
                </div>
            </div>
        </div>
        </section>

        <!-- TOMBOL SUBMIT (gambar 4 – kanan bawah) -->
        <section class="section">
        <div class="submit-wrap">
            <button class="btn btn-submit"><i class="fa-solid fa-paper-plane"></i> Submit</button>
        </div>
        </section>
    </form>
  </main>

  <script src="upload.js"></script>

</body>
</html>
