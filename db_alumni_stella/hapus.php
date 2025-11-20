<?php
include 'koneksi.php';

// pastikan ada parameter id
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // hapus data
    mysqli_query($conn, "DELETE FROM alumni WHERE id_Alumni = $id");

    // menyusun ulang ID agar urut lagi dari 1
    mysqli_query($conn, "SET @num := 0");
    mysqli_query($conn, "UPDATE alumni SET id_Alumni = @num := @num + 1 ORDER BY id_Alumni");

    // reser auto increment agar lanjut dari ID terakhir
    mysqli_query($conn, "ALTER TABLE alumni AUTO_INCREMENT = 1");
}

// kembali ke halaman utama 
header("location: index.php");
exit;