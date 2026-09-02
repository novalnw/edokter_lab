<?php
// Script untuk mengisi 500 data dummy pasien otomatis ke database E-Dokter (Tanpa angka di nama)
include 'db_connect.php';

$firstNames = ['Agus', 'Siti', 'Budi', 'Dewi', 'Joko', 'Rina', 'Ahmad', 'Eko', 'Fitri', 'Rahmat', 'Ani', 'Doni', 'Maya', 'Hendra', 'Siska', 'Rizki', 'Putri', 'Yudi', 'Lestari'];
$lastNames = ['Santoso', 'Rahma', 'Fauzi', 'Wahyudi', 'Pratama', 'Utami', 'Kusuma', 'Hidayat', 'Saputra', 'Lestari', 'Nugroho', 'Wijaya', 'Siregar', 'Setiawan', 'Firmansyah'];

$complaints = [
    'Demam tinggi & Sakit kepala sebelah (Migrain)',
    'Batuk berdahak disertai sesak napas ringan',
    'Nyeri dada sebelah kiri & jantung berdebar',
    'Sakit perut melilit, mual, dan asam lambung naik (GERD)',
    'Tekanan darah tinggi (Hipertensi) & Pusing',
    'Nyeri sendi lutut & pegal-pegal akut',
    'Gatal-gatal sekujur tubuh & alergi dingin',
    'Insomnia kronis & stres beban pikiran (Depresi ringan)',
    'Luka robek di tangan akibat kecelakaan kerja',
    'Sakit gigi berlubang menjalar ke rahang'
];

$doctors = [
    'Dr. Andi, Sp.PD',
    'Dr. Sarah, Sp.S',
    'Dr. Budiarto, Sp.JP',
    'Dr. Maya, Sp.KGEH',
    'Dr. Hendra, Sp.M',
    'Dr. Rika, Sp.KJ',
    'Dr. Fajar, Sp.B'
];

$statuses = ['Selesai', 'Menunggu', 'Diperiksa'];

// Kosongkan dulu data lama biar tidak menumpuk/double
mysqli_query($conn, "TRUNCATE TABLE patients");

// Generate ulang 500 data bersih
for ($i = 1; $i <= 500; $i++) {
    // Nama murni tanpa angka di belakangnya
    $randomName = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    $randomComplaint = $complaints[array_rand($complaints)];
    $randomDoctor = $doctors[array_rand($doctors)];
    $randomStatus = $statuses[array_rand($statuses)];

    $query = "INSERT INTO patients (nama_pasien, keluhan, dokter, status) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $randomName, $randomComplaint, $randomDoctor, $randomStatus);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

echo "<h2>Sukses mereset dan mengisi ulang 500 data pasien bersih!</h2>";
echo "<a href='patients.php'>Kembali ke Data Pasien</a>";
?>
