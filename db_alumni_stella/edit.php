<?php
// File: edit.php
include 'koneksi.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Koneksi ke database gagal. Pastikan file 'koneksi.php' terhubung dengan benar.");
}

// 1. AMBIL ID DARI URL SECARA AMAN
// Gunakan intval() untuk memastikan $id adalah integer
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    die("ID Alumni tidak valid.");
}

// 2. Kueri SELECT menggunakan Prepared Statement untuk mencegah SQL Injection pada $id
$stmt_select = $conn->prepare("SELECT * FROM alumni WHERE id_alumni = ?");
$stmt_select->bind_param("i", $id); // 'i' untuk integer
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows === 0) {
    die("Data Alumni tidak ditemukan.");
}

$data = $result->fetch_assoc();
$stmt_select->close();

// --- LOGIKA UPDATE DATA ---
if (isset($_POST['update'])) {
    // Ambil data POST
    $Nama_Lengkap = $_POST['Nama_Lengkap']; // Nama diubah dari 'nama' menjadi 'nama_lengkap'
    $Tahun_Lulus = $_POST['Tahun_Lulus'];
    $Jurusan = $_POST['Jurusan'];
    $Pekerjaan_Saat_Ini = $_POST['Pekerjaan_Saat_Ini'];
    $Nomor_Telepon = $_POST['Nomor_Telepon'];
    $Email = $_POST['Email'];
    $Alamat = $_POST['Alamat'];

    // Kueri UPDATE menggunakan Prepared Statement (AMAN)
    $sql_update = "UPDATE alumni SET
        Nama_Lengkap = ?,
        Tahun_Lulus = ?,
        Jurusan = ?,
        Pekerjaan_Saat_Ini = ?,
        Nomor_Telepon = ?,
        Email = ?,
        Alamat = ?
        WHERE Id_Alumni = ?";

    $stmt_update = $conn->prepare($sql_update);

    // 'sisssssi' = String, Integer, String, String, String, String, String, Integer
    $stmt_update->bind_param(
        "sisssssi", 
        $Nama_Lengkap, 
        $Tahun_Lulus, 
        $Jurusan, 
        $Pekerjaan_Saat_Ini, 
        $Nomor_Telepon, 
        $Email, 
        $Alamat,
        $id // ID di akhir untuk klausa WHERE
    );

    if ($stmt_update->execute()) {
        echo "<p style='color:green;'>✅ Data berhasil diupdate! <a href='index.php'>Kembali ke Daftar</a></p>";
        
        // Refresh data yang ditampilkan di form setelah update berhasil
        // atau bisa diarahkan langsung ke index.php
        header("Location: index.php?update_sukses=1");
        exit;
    } else {
        echo "<p style='color:red;'>❌ Gagal mengupdate data. Error: " . $stmt_update->error . "</p>";
    }
    $stmt_update->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Alumni</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2> Edit Data Alumni: <?= htmlspecialchars($data['nama_lengkap'] ?? '') ?></h2>
    
    <form method="POST">
        <label for="Nama_Lengkap">Nama Lengkap</label>
        <input type="text" name="Nama_Lengkap" id="Nama_Lengkap" 
               value="<?= htmlspecialchars($data['Nama_Lengkap'] ?? '') ?>" required>
        
        <label for="Tahun_Lulus">Tahun Lulus</label>
        <input type="number" name="Tahun_Lulus" id="Tahun_Lulus" 
               value="<?= htmlspecialchars($data['Tahun_Lulus'] ?? '') ?>" required>
        
        <label for="Jurusan">Jurusan</label>
        <select name="Jurusan" id="Jurusan" required>
            <option value="">Pilih Jurusan</option>
            <option value="RPL" <?= ($data['Jurusan'] ?? '') == 'RPL' ? 'selected' : '' ?>>RPL</option>
            <option value="TKJ" <?= ($data['Jurusan'] ?? '') == 'TKJ' ? 'selected' : '' ?>>TKJ</option>
            <option value="TJAT" <?= ($data['Jurusan'] ?? '') == 'TJAT' ? 'selected' : '' ?>>TJAT</option>
            <option value="ANIMASI" <?= ($data['Jurusan'] ?? '') == 'ANIMASI' ? 'selected' : '' ?>>ANIMASI</option>
        </select>
        
        <label for="Pekerjaan_Saat_Ini">Pekerjaan Saat Ini</label>
        <input type="text" name="Pekerjaan_Saat_Ini" id="Pekerjaan_Saat_Ini" 
               value="<?= htmlspecialchars($data['Pekerjaan_Saat_Ini'] ?? '') ?>" required>
        
        <label for="Nomor_Telepon">Nomor Telepon</label>
        <input type="text" name="Nomor_Telepon" id="Nomor_Telepon" 
               value="<?= htmlspecialchars($data['Nomor_Telepon'] ?? '') ?>" required>
        
        <label for="Email">Email</label>
        <input type="Email" name="Email" id="Email" 
               value="<?= htmlspecialchars($data['Email'] ?? '') ?>" required>
        
        <label for="Alamat">Alamat</label>
        <textarea name="Alamat" id="Alamat" required><?= htmlspecialchars($data['Alamat'] ?? '') ?></textarea>
        
        <button type="submit" name="update">Update Data</button>
        <a href="index.php" style="margin-left: 10px;">Batal</a>
    </form>
    
</body>
</html>