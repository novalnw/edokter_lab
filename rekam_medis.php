<?php
// Koneksi database
include 'db_connect.php';
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$pesan = "";
// Tangkap notif dari redirect setelah sukses input (mencegah duplikat saat refresh)
if (isset($_GET['status']) && $_GET['status'] == 'sukses') {
    $pesan = "<div class='alert alert-success alert-dismissible fade show' role='alert'>Data rekam medis berhasil ditambahkan!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// Handle Tambah Rekam Medis jika form disubmit
if (isset($_POST['tambah_rm'])) {
    $tanggal_periksa = mysqli_real_escape_string($conn, $_POST['tanggal_periksa']);
    $nama_pasien     = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
    $nama_dokter     = mysqli_real_escape_string($conn, $_POST['nama_dokter']);
    $keluhan         = mysqli_real_escape_string($conn, $_POST['keluhan']);
    $diagnosa        = mysqli_real_escape_string($conn, $_POST['diagnosa']);
    $resep_obat      = mysqli_real_escape_string($conn, $_POST['resep_obat']);

    $query = "INSERT INTO rekam_medis (tanggal_periksa, nama_pasien, nama_dokter, keluhan, diagnosa, resep_obat) 
              VALUES ('$tanggal_periksa', '$nama_pasien', '$nama_dokter', '$keluhan', '$diagnosa', '$resep_obat')";
    
    if (mysqli_query($conn, $query)) {
        // Redirect supaya aman dari duplikat pas di-refresh
        header("Location: rekam_medis.php?status=sukses");
        exit();
    } else {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Gagal menambah data: " . mysqli_error($conn) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// Ambil data rekam medis dari database (urut dari terbaru)
$result = mysqli_query($conn, "SELECT * FROM rekam_medis ORDER BY tanggal_periksa DESC, id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Dokter | Rekam Medis Pasien</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .badge-rm { background-color: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>

    <div class="container py-5">
        <!-- Header Title -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-primary"><i class="fa-solid fa-notes-medical me-2"></i>Rekam Medis Pasien</h2>
                <p class="text-muted">Catat dan pantau riwayat pemeriksaan, diagnosa, serta resep obat pasien.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
            </div>
        </div>

        <?= $pesan; ?>

        <!-- Form Input Rekam Medis -->
        <div class="card mb-5">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-circle-plus me-2 text-primary"></i>Tambah Catatan Rekam Medis</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Tanggal Periksa</label>
                            <input type="date" class="form-control" name="tanggal_periksa" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Nama Pasien</label>
                            <input type="text" class="form-control" name="nama_pasien" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Dokter Pemeriksa</label>
                            <input type="text" class="form-control" name="nama_dokter" placeholder="Contoh: Dr. Rika, Sp.KJ" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Keluhan Utama</label>
                            <textarea class="form-control" name="keluhan" rows="3" placeholder="Tuliskan keluhan pasien..." required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Diagnosa Dokter</label>
                            <textarea class="form-control" name="diagnosa" rows="3" placeholder="Tuliskan hasil diagnosa..." required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Resep Obat / Tindakan</label>
                            <textarea class="form-control" name="resep_obat" rows="3" placeholder="Tuliskan resep obat..." required></textarea>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" name="tambah_rm" class="btn btn-primary px-5 rounded-pill"><i class="fa-solid fa-save me-1"></i> Simpan Rekam Medis</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Rekam Medis -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold text-dark"><i class="fa-solid fa-table-list me-2 text-primary"></i>Riwayat Rekam Medis</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Tanggal</th>
                                <th>Pasien</th>
                                <th>Dokter</th>
                                <th>Keluhan</th>
                                <th>Diagnosa</th>
                                <th>Resep Obat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><?= $no++; ?></td>
                                        <td><span class="badge badge-rm px-2 py-1"><?= date('d/m/Y', strtotime($row['tanggal_periksa'])); ?></span></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_pasien']); ?></div>
                                        </td>
                                        <td><span class="text-muted"><i class="fa-solid fa-user-doctor me-1 text-primary"></i><?= htmlspecialchars($row['nama_dokter']); ?></span></td>
                                        <td><small><?= nl2br(htmlspecialchars($row['keluhan'])); ?></small></td>
                                        <td><span class="text-success fw-medium"><small><?= htmlspecialchars($row['diagnosa']); ?></small></span></td>
                                        <td><span class="badge bg-light text-dark border"><small><?= htmlspecialchars($row['resep_obat']); ?></small></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data rekam medis. Silakan tambahkan melalui form di atas.</td>
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
