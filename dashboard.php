<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: auth.php");
    exit();
}

$username = $_SESSION['username'];

// Logika sapaan & pesan NPC berdasarkan waktu (WIB)
date_default_timezone_set('Asia/Jakarta');
$hour = date('H');
if ($hour >= 5 && $hour < 11) { 
    $greeting = "Selamat Pagi"; 
    $npc_msg = "Pagi yang cerah! Siap melayani pasien hari ini?"; 
} elseif ($hour >= 11 && $hour < 15) { 
    $greeting = "Selamat Siang"; 
    $npc_msg = "Tetap semangat ya, jam sibuk nih!"; 
} elseif ($hour >= 15 && $hour < 18) { 
    $greeting = "Selamat Sore"; 
    $npc_msg = "Sore menjelang malam, jaga kesehatan ya!"; 
} else { 
    $greeting = "Selamat Malam"; 
    $npc_msg = "Waktunya santai, tapi tetap pantau data ya bos!"; 
}

// Ambil statistik data dari database
$total_pasien = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM patients"))['total'];
$selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM patients WHERE status='Selesai'"))['total'];
$diperiksa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM patients WHERE status='Diperiksa'"))['total'];
$menunggu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM patients WHERE status='Menunggu'"))['total'];

// Ambil 5 pasien terbaru
$recent_patients = mysqli_query($conn, "SELECT * FROM patients ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | E-Dokter Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background: #f8fafc; color: #1e293b; display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar Komplit */
        .sidebar { width: 260px; background: #0f172a; color: white; display: flex; flex-direction: column; padding: 20px; flex-shrink: 0; }
        .brand-logo { display: flex; align-items: center; gap: 12px; margin-bottom: 35px; padding: 5px; }
        .brand-icon { width: 42px; height: 42px; background: linear-gradient(135deg, #38bdf8, #2563eb); border-radius: 10px; display: flex; justify-content: center; align-items: center; box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3); }
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
        .content-body { padding: 30px; overflow-y: auto; flex-grow: 1; }

        /* Banner Biru Soft & Karakter NPC Animasi Orang */
        @keyframes waveHand {
            0% { transform: rotate(0deg); }
            20% { transform: rotate(14deg); }
            40% { transform: rotate(-8deg); }
            60% { transform: rotate(14deg); }
            80% { transform: rotate(-4deg); }
            100% { transform: rotate(0deg); }
        }
        
        .welcome-card { 
            background: linear-gradient(135deg, #e0f2fe, #bae6fd); /* Biru soft kalem */
            color: #0369a1; 
            padding: 25px 30px; 
            border-radius: 16px; 
            margin-bottom: 25px; 
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.15); 
            display: flex; 
            align-items: center; 
            gap: 25px; 
            border: 1px solid #7dd3fc;
        }

        /* NPC Orang (Avatar SVG + Animasi Tangan Melambai) */
        .npc-character {
            position: relative;
            width: 70px;
            height: 70px;
            background: #0284c7;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
        }
        .npc-character svg {
            width: 42px;
            height: 42px;
            fill: white;
        }
        .npc-hand {
            position: absolute;
            top: 18px;
            right: 8px;
            font-size: 24px;
            animation: waveHand 1.8s infinite ease-in-out;
            transform-origin: bottom center;
        }

        .welcome-text h2 { font-size: 24px; margin-bottom: 4px; color: #0c4a6e; font-weight: 700; }
        .welcome-text p { font-size: 14px; color: #0369a1; opacity: 0.9; }
        
        .marquee-container { 
            background: rgba(255, 255, 255, 0.6); 
            padding: 8px 15px; 
            border-radius: 6px; 
            margin-top: 12px; 
            overflow: hidden; 
            white-space: nowrap; 
            font-size: 13px; 
            color: #0369a1;
            border: 1px solid rgba(125, 211, 252, 0.5);
        }

        /* Statistics Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; display: flex; flex-direction: column; }
        .stat-card span { font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
        .stat-card h3 { font-size: 28px; color: #0f172a; font-weight: 700; }
        .stat-card.blue { border-left: 4px solid #0284c7; }
        .stat-card.green { border-left: 4px solid #10b981; }
        .stat-card.yellow { border-left: 4px solid #f59e0b; }
        .stat-card.purple { border-left: 4px solid #8b5cf6; }

        /* Section Table Terbaru */
        .section-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .section-title a { font-size: 14px; color: #0284c7; text-decoration: none; }
        .section-title a:hover { text-decoration: underline; }

        .table-container { background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        th, td { padding: 14px 18px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; color: #475569; font-weight: 600; }
        tr:hover { background: #f8fafc; }
        .badge { background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .badge-waiting { background: #fef9c3; color: #854d0e; }
        .badge-process { background: #e0f2fe; color: #0369a1; }
    </style>
</head>
<body>

    <!-- Sidebar Komplit -->
    <div class="sidebar">
        <div class="brand-logo">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
            </div>
            <div class="brand-text">
                <h2>E-Dokter</h2>
                <span>Medical System</span>
            </div>
        </div>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="patients.php">Data Pasien</a>
        <a href="jadwal_dokter.php">Jadwal Dokter</a>
        <a href="rekam_medis.php">Rekam Medis</a>
    </div>

    <!-- Main Area -->
    <div class="main-content">
        <header>
            <h1>Dashboard Utama</h1>
            <div class="user-profile">
                <span>Halo, <b><?php echo htmlspecialchars($username); ?></b></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <div class="content-body">
            
            <!-- Banner NPC Orang dengan Nuansa Biru Soft -->
            <div class="welcome-card">
                <!-- Avatar Karakter NPC -->
                <div class="npc-character">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <div class="npc-hand">👋</div>
                </div>

                <div class="welcome-text" style="flex-grow: 1;">
                    <h2><?php echo $greeting; ?>, <?php echo htmlspecialchars($username); ?>!</h2>
                    <p><?php echo $npc_msg; ?></p>
                    
                    <div class="marquee-container">
                        <marquee behavior="scroll" direction="left" scrollamount="5">
                            <b>STATUS SISTEM:</b> Total pasien terdaftar saat ini mencapai <?php echo number_format($total_pasien); ?> data. | Pastikan rekam medis selalu diupdate tepat waktu. | Tetap semangat bekerja, *bree*!
                        </marquee>
                    </div>
                </div>
            </div>

            <!-- Kartu Statistik -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <span>Total Pasien Terdaftar</span>
                    <h3><?php echo number_format($total_pasien); ?></h3>
                </div>
                <div class="stat-card green">
                    <span>Penanganan Selesai</span>
                    <h3><?php echo number_format($selesai); ?></h3>
                </div>
                <div class="stat-card purple">
                    <span>Sedang Diperiksa</span>
                    <h3><?php echo number_format($diperiksa); ?></h3>
                </div>
                <div class="stat-card yellow">
                    <span>Pasien Menunggu</span>
                    <h3><?php echo number_format($menunggu); ?></h3>
                </div>
            </div>

            <!-- Tabel Pasien Terbaru -->
            <div class="section-title">
                <span>Pasien Masuk Terbaru</span>
                <a href="patients.php">Lihat Semua (500 Data) &rarr;</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No ID</th>
                            <th>Nama Pasien</th>
                            <th>Keluhan / Gejala</th>
                            <th>Dokter Penanggung Jawab</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($recent_patients)): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><b><?php echo htmlspecialchars($row['nama_pasien']); ?></b></td>
                                <td><?php echo htmlspecialchars($row['keluhan']); ?></td>
                                <td><?php echo htmlspecialchars($row['dokter']); ?></td>
                                <td>
                                    <?php if ($row['status'] === 'Selesai'): ?>
                                        <span class="badge">Selesai</span>
                                    <?php elseif ($row['status'] === 'Diperiksa'): ?>
                                        <span class="badge badge-process">Diperiksa</span>
                                    <?php else: ?>
                                        <span class="badge badge-waiting"><?php echo htmlspecialchars($row['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>
