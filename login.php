<?php
session_start();
require_once 'env.php';
require_once 'license.php';

try {
    $pdo = new PDO("mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'].";charset=utf8mb4", 
                   $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // Mã hóa SHA256
        $hashed_password = hash('sha256', $password);

        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $hashed_password]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: admin.php");
            exit;
        } else {
            $error = "❌ Sai tên đăng nhập hoặc mật khẩu!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        input {
            width: 100%;
            padding: 14px;
            margin: 12px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 14px;
            background: #e62e2e;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 17px;
            cursor: pointer;
        }
        .error { color: red; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔐 Đăng Nhập Admin</h2>
        
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Tên đăng nhập" required autofocus>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">ĐĂNG NHẬP</button>
        </form>

        <p style="margin-top:20px; font-size:14px; color:#666;">
            Mặc định: <strong>admin</strong> / <strong>admin123</strong>
        </p>
    </div>
</body>
</html>