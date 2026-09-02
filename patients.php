<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: auth.php");
    exit();
}

$username = $_SESSION['username'];

// Ambil kata kunci pencarian dan arah sorting (ASC / DESC)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) && $_GET['sort'] === 'ASC' ? 'ASC' : 'DESC';

// Toggle sorting untuk link panah
$next_sort = ($sort === 'DESC') ? 'ASC' : 'DESC';
$arrow_icon = ($sort === 'DESC') ? '▼' : '▲';

// Query data pasien dengan filter pencarian dan dynamic sorting (Aman dari SQL Injection)
if ($search !== '') {
    $query = "SELECT * FROM patients WHERE nama_pasien LIKE ? OR keluhan LIKE ? OR dokter LIKE ? ORDER BY id $sort";
    $stmt = mysqli_prepare($conn, $query);
    $searchTerm = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "sss", $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $query = "SELECT * FROM patients ORDER BY id $sort";
    $result = mysqli_query($conn, $query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pasien | E-Dokter Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background: #f8fafc; color: #1e293b; display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: #0f172a; color: white; display: flex; flex-direction: column; padding: 20px; flex-shrink: 0; }
        .brand-logo { display: flex; align-items: center; gap: 12px; margin-bottom: 35px; padding: 5px; }
        .brand-icon { width: 42px; height: 42px; background: linear-gradient(135deg, #0ea5e9, #2563eb); border-radius: 10px; display: flex; justify-content: center; align-items: center; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4); }
        .brand-icon svg { width: 26px; height: 26px; fill: white; }
        .brand-text h2 { font-size: 18px; font-weight: 700; color: #fff; }
        .brand-text span { font-size: 11px; color: #38bdf8; text-transform: uppercase; font-weight: 600; }

        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 8px; margin-bottom: 8px; transition: 0.2s; display: block; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background: #1e3a8a; color: white; }
        
        /* Main Content */
        .main-content { flex: 1; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
        
        /* Header */
        header { background: white; padding: 20px 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
        header h1 { font-size: 20px; color: #0f172a; }
        .user-profile { display: flex; align-items: center; gap: 15px; }
        .logout-btn { background: #ef4444; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s; }
        .logout-btn:hover { background: #dc2626; }
        
        /* Content Body */
        .content-body { padding: 30px; display: flex; flex-direction: column; height: calc(100vh - 81px); overflow: hidden; }

        /* Toolbar Pencarian */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap; flex-shrink: 0; }
        .search-box { display: flex; gap: 10px; width: 100%; max-width: 400px; }
        .search-box input { width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: 0.2s; background: white; }
        .search-box input:focus { border-color: #0284c7; box-shadow: 0 0 5px rgba(2, 132, 199, 0.2); }
        .search-box button { background: #0284c7; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .search-box button:hover { background: #0369a1; }
        .reset-btn { background: #64748b; color: white; text-decoration: none; padding: 10px 15px; border-radius: 8px; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; }

        /* Table Container */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-grow: 1; overflow-y: auto; position: relative; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        th, td { padding: 14px 18px; border-bottom: 1px solid #e2e8f0; }
        
        /* Sticky Header */
        th { 
            background: #f1f5f9; 
            color: #475569; 
            font-weight: 600; 
            position: sticky; 
            top: 0; 
            z-index: 10; 
            box-shadow: inset 0 -2px 0 #cbd5e1;
        }

        /* Sorting Link Style */
        .sort-link {
            color: #475569;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
        }
        .sort-link:hover { color: #0284c7; }
        
        tr:hover { background: #f8fafc; }
        .badge { background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .badge-waiting { background: #fef9c3; color: #854d0e; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand-logo">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
            </div>
            <div class="brand-text">
                <h2>App E-Dokter</h2>
                <span>BY Vall Dev</span>
            </div>
        </div>

        <a href="dashboard.php">Dashboard</a>
        <a href="patients.php" class="active">Data Pasien</a>
        <a href="jadwal_dokter.php">Jadwal Dokter</a>
        <a href="rekam_medis.php">Rekam Medis</a>
    </div>

    <!-- Main Area -->
    <div class="main-content">
        <header>
            <h1>Selamat Datang Di Data Pasien</h1>
            <div class="user-profile">
                <span>Halo, <b><?php echo htmlspecialchars($username); ?></b></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <div class="content-body">
            
            <!-- Toolbar & Filter Pencarian -->
            <div class="toolbar">
                <form method="GET" class="search-box">
                    <input type="text" name="search" placeholder="Cari nama pasien, keluhan, atau dokter..." value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <button type="submit">Cari</button>
                    <?php if ($search !== ''): ?>
                        <a href="patients.php" class="reset-btn">Reset</a>
                    <?php endif; ?>
                </form>
                <div style="font-size: 14px; color: #64748b;">
                    Urutan ID: <b><?php echo ($sort === 'ASC') ? 'Terkecil ke Terbesar (1 - 500)' : 'Terbesar ke Terkecil (500 - 1)'; ?></b>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%;">
                                <!-- Tombol Sorting ID dengan Panah Atas/Bawah -->
                                <a href="patients.php?search=<?php echo urlencode($search); ?>&sort=<?php echo $next_sort; ?>" class="sort-link">
                                    No ID <span><?php echo $arrow_icon; ?></span>
                                </a>
                            </th>
                            <th style="width: 25%;">Nama Pasien</th>
                            <th style="width: 35%;">Keluhan / Gejala</th>
                            <th style="width: 15%;">Dokter Penanggung Jawab</th>
                            <th style="width: 10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><b><?php echo htmlspecialchars($row['nama_pasien']); ?></b></td>
                                    <td><?php echo htmlspecialchars($row['keluhan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['dokter']); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'Selesai'): ?>
                                            <span class="badge">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge badge-waiting"><?php echo htmlspecialchars($row['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">Data pasien yang dicari tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>
