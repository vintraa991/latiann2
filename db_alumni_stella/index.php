<?php
// Pastikan koneksi.php mendefinisikan $conn sebagai objek koneksi MySQLi
include 'koneksi.php';

// Cek koneksi
if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Koneksi ke database gagal. Pastikan file 'koneksi.php' terhubung dengan benar.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Alumni</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2> Data Alumni </h2>
    <a href="tambah.php" id="tambahdata" style="
        display: inline-block; 
        padding: 10px 15px; 
        margin-bottom: 20px;
        background: #28a745; 
        color: white; 
        text-decoration: none; 
        border-radius: 5px;
    ">+ Tambah Data</a>

    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="cari" placeholder="Cari nama / jurusan / pekerjaan..."
            value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
        <button type="submit">Cari</button>
    </form>

    <?php if (isset($_GET['cari']) && $_GET['cari'] != ''): ?>
    <a href="index.php" style="
        display:inline-block; 
        margin-top:10px; 
        margin-bottom:20px; 
        padding:8px 12px; 
        background:#007BFF; 
        color:#fff; 
        text-decoration:none; 
        border-radius:5px;">
        Kembali (Tampilkan Semua Data)
    </a>  
    <?php endif; ?>
    
<div class="table-wrapper">
    <table>
        <tr>
            <th>ID</th>
            <th>Nama Lengkap</th>
            <th>Tahun Lulus</th>
            <th>Jurusan</th>
            <th>Pekerjaan Saat Ini</th>
            <th>Nomor Telepon</th>
            <th>Email</th>
            <th>Alamat</th>
            <th>Perubahan</th>
        </tr>

        <?php
        // --- LOGIKA PENCARIAN DENGAN PREPARED STATEMENT (AMAN) ---
        $sql = "SELECT * FROM alumni";
        
        // Cek apakah ada parameter pencarian
        if (isset($_GET['cari']) && $_GET['cari'] != '') {
            $cari = '%' . $_GET['cari'] . '%'; // Tambahkan wildcard di sini
            
            // Kueri dengan placeholder (?)
            $sql .= " WHERE nama_lengkap LIKE ? 
                    OR jurusan LIKE ? 
                    OR pekerjaan_saat_ini LIKE ? 
                    OR tahun_lulus LIKE ?";
            
            $stmt = $conn->prepare($sql);
            
            // Bind parameter: 4 string (s)
            $stmt->bind_param("ssss", $cari, $cari, $cari, $cari);
            $stmt->execute();
            $result = $stmt->get_result(); // Ambil hasil dari prepared statement

        } else {
            // Jika tidak ada pencarian, jalankan kueri standar
            $result = $conn->query($sql);
        }
        
        // Cek apakah ada hasil
        if (isset($result) && $result->num_rows > 0) {
            $nomor = 1; // Untuk penomoran jika dibutuhkan, tapi menggunakan id_alumni lebih umum
            
            // TAMPILKAN DATA
            while ($row = $result->fetch_assoc()) {
                // Perhatian: Menggunakan penulisan lowercase pada kolom ($row['nama_lengkap'])
                // Sesuai dengan konvensi penamaan yang umum dan struktur yang Anda tunjukkan
                echo "<tr>
                    <td>{$row['Id_Alumni']}</td>
                    <td>" . htmlspecialchars($row['Nama_Lengkap']) . "</td>
                    <td>" . htmlspecialchars($row['Tahun_Lulus']) . "</td>
                    <td>" . htmlspecialchars($row['Jurusan']) . "</td>
                    <td>" . htmlspecialchars($row['Pekerjaan_Saat_Ini']) . "</td>
                    <td>" . htmlspecialchars($row['Nomor_Telepon']) . "</td>
                    <td>" . htmlspecialchars($row['Email']) . "</td>
                    <td>" . htmlspecialchars($row['Alamat']) . "</td>
                    <td>
                        <a href='edit.php?id={$row['Id_Alumni']}'>Edit</a> |
                        <a href='hapus.php?id={$row['Id_Alumni']}' onclick=\"return confirm('Yakin ingin hapus data {$row['Nama_Lengkap']}?')\">Hapus</a>
                    </td>
                </tr>";
                $nomor++;
            }
        } else {
            echo "<tr><td colspan='9'>❌ Data tidak ditemukan.</td></tr>";
        }
        
        // Tutup statement jika digunakan
        if (isset($stmt)) {
            $stmt->close();
        }
        ?>
    </table>
</div>
</body>
</html>