<?php
session_start();
include 'include/database.php';
$id_table = isset($_SESSION['id_table']) ? $_SESSION['id_table'] : null;
$id_bill = isset($_SESSION['bill_id']) ? $_SESSION['bill_id'] : null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Truy vấn lấy thông tin người dùng
    $stmt = $conn->prepare("SELECT id_customer, password FROM tbl_customer WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo "DEBUG: Mật khẩu bạn nhập: " . $password;
        echo "<br>DEBUG: Mật khẩu đã hash trong DB: " . $user['password'];

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_customer'];
            $_SESSION['username']=$username;
            $_SESSION['success'] = "Đăng nhập thành công!";
            header("Location: order_menu.php?id_table=$id_table&bill_id=$id_bill");
            exit();
        } else {
            $_SESSION['error'] = "Mật khẩu không chính xác!";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Tên đăng nhập không tồn tại!";
        header("Location: login.php");
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
 body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
    background: url('/img/gogi_bg_login.png') no-repeat center center fixed;
    background-size: cover;
}

.login-box {
    width: 350px;
    padding: 40px;
    border-radius: 15px;
    color: white;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.login-box h2 {
    margin-bottom: 30px;
    font-size: 24px;
    font-weight: bold;
}

.login-box label {
    font-size: 14px;
    margin-bottom: 5px;
    display: block;
}

.login-box input[type="text"],
.login-box input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0 20px;
    border: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.5);
    background: transparent;
    color: white;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s;
}

.login-box input[type="text"]:focus,
.login-box input[type="password"]:focus {
    border-bottom: 1px solid #fff;
}

.login-box input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.login-box button {
    width: 100%;
    padding: 10px;
    border: none;
    background-color: white;
    color: black;
    font-weight: bold;
    cursor: pointer;
    border-radius: 5px;
}

.login-box button:hover {
    background-color: #f0f0f0;
}

.login-box p {
    margin-top: 20px;
    font-size: 14px;
    text-align: center;
}

.login-box a {
    color: #eee;
    text-decoration: none;
    margin: 0 5px;
}

.login-box a:hover {
    text-decoration: underline;
}

.message {
    text-align: center;
    margin-bottom: 15px;
}

.error {
    color: #270702;
    font-weight: bolder;
}

.success {
    color: #9be296;
}

    </style>
</head>
<body>
<div class="login-box">
    <h2 style="text-align: center;">Đăng nhập</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label>Tên đăng nhập:</label>
        <input type="text" name="username" required>

        <label>Mật khẩu:</label>
        <input type="password" name="password" required>

        <button type="submit">Đăng nhập</button>
    </form>
    
    <p style="text-align: center; margin-top: 10px;">
        Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        <a href="/forget_pass.php">Quên mật khẩu</a>
    </p>
</div>

</body>
</html>
