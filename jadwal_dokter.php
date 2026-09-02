<?php
// Koneksi database (menggunakan db_connect.php)
include 'db_connect.php';

// Handle Tambah Jadwal jika form disubmit
$pesan = "";
if (isset($_POST['tambah_jadwal'])) {
    $nama_dokter = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
    $spesialis   = mysqli_real_escape_string($conn, $_POST['spesialis']);
    $hari        = mysqli_real_escape_string($conn, $_POST['hari']);
    $jam_mulai   = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "INSERT INTO jadwal_dokter (nama_dokter, spesialis, hari, jam_mulai, jam_selesai, status) 
              VALUES ('$nama_dokter', '$spesialis', '$hari', '$jam_mulai', '$jam_selesai', '$status')";
    
    if (mysqli_query($conn, $query)) {
        $pesan = "<div class='alert alert-success alert-dismissible fade show' role='alert'>Jadwal dokter berhasil ditambahkan!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Gagal menambah jadwal: " . mysqli_error($conn) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// Ambil data jadwal dari database
$result = mysqli_query($conn, "SELECT * FROM jadwal_dokter ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Dokter | Jadwal Praktik Dokter</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .badge-tersedia { background-color: #d1e7dd; color: #0f5132; }
        .badge-cuti { background-color: #f8d7da; color: #842029; }
        .badge-penuh { background-color: #fff3cd; color: #664d03; }
    </style>
</head>
<body>

    <div class="container py-5">
        <!-- Header Title -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-primary"><i class="fa-solid fa-calendar-days me-2"></i>Jadwal Praktik Dokter</h2>
                <p class="text-muted">Kelola dan pantau jadwal dokter yang bertugas di E-Dokter Lab.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
            </div>
        </div>

        <?= $pesan; ?>

        <!-- Form Input Jadwal -->
        <div class="card mb-5">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Tambah Jadwal Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Nama Dokter</label>
                            <input type="text" class="form-control" name="nama_dokter" placeholder="Contoh: Dr. Andi Sp.A" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Spesialisasi</label>
                            <input type="text" class="form-control" name="spesialis" placeholder="Contoh: Spesialis Anak" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Hari Praktik</label>
                            <select class="form-select" name="hari" required>
                                <option value="" selected disabled>Pilih Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Jam Mulai</label>
                            <input type="time" class="form-control" name="jam_mulai" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Jam Selesai</label>
                            <input type="time" class="form-control" name="jam_selesai" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Cuti">Cuti</option>
                                <option value="Penuh">Penuh</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" name="tambah_jadwal" class="btn btn-primary w-100 rounded-pill"><i class="fa-solid fa-save me-1"></i> Simpan Jadwal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Jadwal -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-list me-2 text-primary"></i>Daftar Jadwal Dokter Aktif</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Dokter</th>
                                <th>Spesialis</th>
                                <th>Hari</th>
                                <th>Jam Praktik</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><?= $no++; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-light-primary text-primary rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    <i class="fa-solid fa-user-doctor"></i>
                                                </div>
                                                <span class="fw-medium"><?= htmlspecialchars($row['nama_dokter']); ?></span>
                                            </div>
                                        </td>
                                        <td><span class="text-muted"><?= htmlspecialchars($row['spesialis']); ?></span></td>
                                        <td><span class="badge bg-secondary"><?= $row['hari']; ?></span></td>
                                        <td><i class="fa-regular fa-clock text-muted me-1"></i> <?= date('H:i', strtotime($row['jam_mulai'])); ?> - <?= date('H:i', strtotime($row['jam_selesai'])); ?> WIB</td>
                                        <td>
                                            <?php 
                                                $statusClass = 'badge-tersedia';
                                                if($row['status'] == 'Cuti') $statusClass = 'badge-cuti';
                                                if($row['status'] == 'Penuh') $statusClass = 'badge-penuh';
                                            ?>
                                            <span class="badge <?= $statusClass; ?> px-3 py-2 rounded-pill"><?= $row['status']; ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data jadwal dokter. Silakan tambahkan melalui form di atas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
