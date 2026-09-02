<?php
include 'db_connect.php';

// Password baru yang ingin di-hash untuk user admin
$password_baru = 'kali'; // atau password lain yang diinginkan
$hashed = password_hash($password_baru, PASSWORD_DEFAULT);

// Update ke database menggunakan prepared statement
$stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = 'admin'");
mysqli_stmt_bind_param($stmt, "s", $hashed);

if (mysqli_stmt_execute($stmt)) {
    echo "Berhasil! Password admin sudah di-hash menjadi: " . $hashed;
} else {
    echo "Gagal update: " . mysqli_error($conn);
}
mysqli_stmt_close($stmt);
?>
