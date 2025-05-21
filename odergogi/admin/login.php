<?php
session_start();
include '../include/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Truy vấn thông tin admin theo username
    $stmt = $conn->prepare("SELECT id_admin, admin_name, password_admin FROM tbl_admin WHERE username_admin = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        // So sánh mật khẩu plain text
        if ($password === $admin['password_admin']) {
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_name'] = $admin['admin_name']; // Thêm dòng này
            $_SESSION['success'] = "Đăng nhập thành công!";
            header("Location: index.php"); // Trang sau khi đăng nhập thành công
            exit();
        } else {
            $_SESSION['error'] = "Mật khẩu không chính xác!";
        }
    } else {
        $_SESSION['error'] = "Tên đăng nhập không tồn tại!";
    }

    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang đăng nhập của admin </title>
    <style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
background: linear-gradient(135deg, #f1e8e6, #d99865), url(/img/nenlogin_admin.png) no-repeat center center fixed;}

.login-form {
    max-width: 35%;
    margin: 60px auto;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 4px 0px 6px rgb(0 0 0 / 47%);
    background: #fff;
}
.form-header {
    background-color: #164d89;
    padding: 20px;
    text-align: center;
}

.form-header h2 {
    color: white;
    margin: 0;
    font-size: 24px;
}

form {
    padding: 30px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
    color: #333;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

button[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #164d89;
    color: white;
    border: none;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

.register-link {
    text-align: center;
    padding: 0 30px 30px;
    font-size: 14px;
}

.register-link a {
    color: #007bff;
    text-decoration: none;
}

.register-link a:hover {
    text-decoration: underline;
}

.message {
    padding: 10px 20px;
    margin: 10px 30px;
    border-radius: 6px;
    font-size: 14px;
}

.message.error {
    background-color: #f8d7da;
    color: #721c24;
}

.message.success {
    background-color: #d4edda;
    color: #155724;
}

    </style>
</head>
<body>
<div class="login-form">
    <div class="form-header">
        <h2>Đăng nhập nhân viên</h2>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="username">Tên đăng nhập:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Mật khẩu:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Đăng nhập</button>
    </form>
    
    <p class="register-link">
    Nếu chưa có tài khoản hãy liên hệ với quản lý để được cấp.
    </p>
</div>


</body>
</html>
