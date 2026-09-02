<?php
session_start();
include 'db_connect.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah, bree!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E-Dokter Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        /* Background Blue Dongker / Navy Elegan & Adem di Mata */
        body { 
            background: linear-gradient(135deg, #0f172a, #1e293b); 
            color: #f1f5f9; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }

        /* Card Login Semi-Dark & Lembut */
        .login-card { 
            background: #111827; 
            padding: 45px 40px; 
            border-radius: 20px; 
            border: 1px solid #1f2937; 
            width: 100%; 
            max-width: 400px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4); 
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 { 
            font-size: 24px; 
            color: #38bdf8; 
            font-weight: 700;
            margin-bottom: 6px;
        }

        .login-header p {
            font-size: 13px;
            color: #94a3b8;
        }

        .form-group { 
            margin-bottom: 20px; 
        }

        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-size: 13px; 
            color: #cbd5e1; 
            font-weight: 600;
        }

        .form-group input { 
            width: 100%; 
            padding: 12px 16px; 
            border-radius: 10px; 
            border: 1px solid #334155; 
            background: #0b0f19; 
            color: #f1f5f9; 
            font-size: 14px; 
            outline: none;
            transition: all 0.2s;
        }

        .form-group input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        .btn-login { 
            width: 100%; 
            padding: 12px; 
            background: #0284c7; 
            color: white; 
            border: none; 
            border-radius: 10px; 
            font-weight: 600; 
            font-size: 15px;
            cursor: pointer; 
            margin-top: 10px; 
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
            transition: background 0.2s;
        }

        .btn-login:hover { 
            background: #026d9c; 
        }

        .error-msg { 
            background: rgba(239, 68, 68, 0.15); 
            color: #f87171; 
            padding: 10px 14px; 
            border-radius: 8px; 
            font-size: 13px; 
            margin-bottom: 20px; 
            border: 1px solid rgba(239, 68, 68, 0.3);
            text-align: center;
        }

        /* Bagian Link Register */
        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #94a3b8;
        }

        .register-link a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <h2>Selamat Datang Di E-Dokter</h2>
            <p>Silakan masuk menggunakan akun Anda</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autocomplete="off" placeholder="Masukkan username...">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password...">
            </div>
            <button type="submit" class="btn-login">Masuk Sistem</button>
        </form>

        <!-- Tombol / Tautan ke Halaman Register -->
        <div class="register-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>

</body>
</html>
