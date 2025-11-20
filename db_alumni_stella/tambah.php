<?php
// File: tambah_alumni_final.php

// Pastikan koneksi.php mendefinisikan $conn sebagai objek koneksi MySQLi
include 'koneksi.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Koneksi ke database gagal. Pastikan file 'koneksi.php' terhubung dengan benar.");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Tambah Alumni</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2> Tambah Data Alumni </h2>
    
    <form method="POST" action="">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required> 
        <input type="number" name="tahun_lulus" placeholder="Tahun Lulus (Contoh: 2023)" required>
        <select name="jurusan" required>
            <option value="">Pilih Jurusan</option>
            <option value="RPL">RPL</option>
            <option value="TKJ">TKJ</option>
            <option value="TJAT">TJAT</option>
            <option value="ANIMASI">ANIMASI</option>
        </select>
        
        <input type="text" name="pekerjaan_saat_ini" placeholder="Pekerjaan Saat Ini">
        <input type="text" name="nomor_telepon" placeholder="Nomor Telepon">
        <input type="email" name="email" placeholder="Email">
        <textarea name="alamat" placeholder="Alamat"></textarea>
        
        <button type="submit" name="submit">Simpan Data Alumni</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        // Ambil data dari form
        $nama_lengkap = $_POST['nama_lengkap'];
        $tahun_lulus = $_POST['tahun_lulus'];
        $jurusan = $_POST['jurusan'];
        $pekerjaan_saat_ini = $_POST['pekerjaan_saat_ini'];
        $nomor_telepon = $_POST['nomor_telepon'];
        $email = $_POST['email'];
        $alamat = $_POST['alamat'];

        // --- Proses Penyimpanan Data (Prepared Statement) ---
        
        // Query INSERT disesuaikan dengan 7 kolom (id_alumni AUTO_INCREMENT)
        $sql = "INSERT INTO alumni (nama_lengkap, tahun_lulus, jurusan, pekerjaan_saat_ini, nomor_telepon, email, alamat)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_insert = $conn->prepare($sql);

        // 'sisssss' = String, Integer, String, String, String, String, String
        $stmt_insert->bind_param(
            "sisssss", 
            $nama_lengkap, 
            $tahun_lulus, 
            $jurusan, 
            $pekerjaan_saat_ini, 
            $nomor_telepon, 
            $email, 
            $alamat
        );

        if ($stmt_insert->execute()) {
            echo "<p style='color:green;'>✅ Data alumni **$nama_lengkap** berhasil disimpan! <a href='index.php'>Lihat Data</a></p>";
        } else {
            echo "<p style='color:red;'>❌ Gagal menyimpan data! Error: " . $stmt_insert->error . "</p>";
        }
        $stmt_insert->close();
    }
    ?>
</body>

</html>