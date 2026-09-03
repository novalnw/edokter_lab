<?php
include 'db_connect.php';
if ($conn) {
    echo "Koneksi ke database MySQL BERHASIL!";
} else {
    echo "Koneksi GAGAL!";
}
?>
EOF
