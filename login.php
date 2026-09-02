<?php 
include 'db_connect.php'; 

$message = "";
$status_color = "";

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // AMAN: Menggunakan Prepared Statements untuk mencegah SQL Injection
    $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Validasi password (asumsi di database password-nya di-hash pakai password_verify, atau cek biasa untuk latihan)
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $message = "Login Berhasil! Selamat datang, " . htmlspecialchars($username);
            $status_color = "#4ade80";
        } else {
            $message = "Login Gagal! Password salah.";
            $status_color = "#f87171";
        }
    } else {
        $message = "Login Gagal! Username tidak ditemukan.";
        $status_color = "#f87171";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login E-Dokter | Secure Portal</title>
    <style>
        body { font-family: sans-serif; background: linear-gradient(135deg, #0f172a, #0284c7); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: white; }
        .login-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 320px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: none; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #0ea5e9; border: none; color: white; border-radius: 8px; cursor: pointer; font-weight: bold; }
        button:hover { background: #0284c7; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>E-Dokter Portal</h2>
        <form method="POST" autocomplete="off">
            <input type="text" name="username" placeholder="Username" autocomplete="off" required>
            <input type="password" name="password" placeholder="Password" autocomplete="new-password" required>
            <button type="submit" name="submit">Masuk Sistem</button>
        </form>

        <div style="margin-top:20px; font-size:14px;">
            <?php if (!empty($message)): ?>
                <b style="color: <?php echo $status_color; ?>;"><?php echo $message; ?></b>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
