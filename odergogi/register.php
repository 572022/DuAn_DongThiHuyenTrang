<?php
session_start();
include 'include/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $birth = !empty($_POST['birth']) ? $_POST['birth'] : NULL;
    $gender = isset($_POST['gender']) ? (int)$_POST['gender'] : NULL;
    $phone = trim($_POST['phone']);

    $check_user = $conn->prepare("SELECT username, email, phone FROM tbl_customer WHERE username = ? OR email = ? OR phone = ?");
    $check_user->bind_param("sss", $username, $email, $phone);
    $check_user->execute();
    $result = $check_user->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['username'] === $username) {
            $_SESSION['error'] = "Tên đăng nhập đã tồn tại!";
        } elseif ($row['email'] === $email) {
            $_SESSION['error'] = "Email đã được sử dụng!";
        } elseif ($row['phone'] === $phone) {
            $_SESSION['error'] = "Số điện thoại đã được sử dụng!";
        }
        header("Location: register.php");
        exit();
    }
    
    if (!ctype_alnum($username)) {
        $_SESSION['error'] = "Tên đăng nhập chỉ chứa chữ và số!";
        header("Location: register.php");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email không hợp lệ!";
        header("Location: register.php");
        exit();
    }
    
    if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $_SESSION['error'] = "Số điện thoại không hợp lệ!";
        header("Location: register.php");
        exit();
    }
    
    // Mã hóa mật khẩu
    if (empty($password)) {
        $_SESSION['error'] = "Mật khẩu không được để trống!";
        header("Location: register.php");
        exit();
    }
    
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    
    if (!$password_hashed) {
        $_SESSION['error'] = "Lỗi mã hóa mật khẩu!";
        header("Location: register.php");
        exit();
    }
    
    // Thêm vào database
    $stmt = $conn->prepare("INSERT INTO tbl_customer (username, password, fullname, email, birth, gender, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $username, $password_hashed, $fullname, $email, $birth, $gender, $phone);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Đăng ký thành công!";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Lỗi khi đăng ký: " . $conn->error;
        header("Location: register.php");
    }

    // Đóng kết nối
    $stmt->close();
    $check_user->close();
    $check_email->close();
    $check_phone->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
</head>
<style>
       body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
    background: url('/img/gogi_bg_login.png') no-repeat center center fixed;
    background-size: cover;
}

.login-box {
    width: 400px;
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
    text-align: center;
    margin-bottom: 30px;
    font-size: 24px;
    font-weight: bold;
}

.login-box label {
    font-size: 14px;
    display: block;
}

.login-box input[type="text"],
.login-box input[type="password"],
.login-box input[type="email"],
.login-box input[type="date"],
.login-box select {
    width: 100%;
    padding: 10px;
    border: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.5);
    background: transparent;
    color: white;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s;
}

.login-box input:focus,
.login-box select:focus {
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
    margin-top: 10px;
}

.login-box button:hover {
    background-color: #f0f0f0;
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
    color: #ff6b6b;
}

.success {
    color: #9be296;
}
@media only screen and (max-width: 600px) {
    .login-box {
        width: 90%;
        padding: 20px;
    }

    .login-box h2 {
        font-size: 20px;
    }

    .login-box label {
        font-size: 12px;
    }

    .login-box input[type="text"],
    .login-box input[type="password"],
    .login-box input[type="email"],
    .login-box input[type="date"],
    .login-box select {
        font-size: 12px;
        padding: 8px;
    }

    .login-box button {
        font-size: 14px;
        padding: 12px;
    }
}

@media only screen and (max-width: 400px) {
    .login-box {
        width: 95%;
        padding: 15px;
    }

    .login-box h2 {
        font-size: 18px;
    }

    .login-box label {
        font-size: 11px;
    }

    .login-box input[type="text"],
    .login-box input[type="password"],
    .login-box input[type="email"],
    .login-box input[type="date"],
    .login-box select {
        font-size: 12px;
        padding: 7px;
    }

    .login-box button {
        font-size: 14px;
        padding: 10px;
    }
}
    </style>
<body>
<div class="login-box">
    <h2>Đăng ký tài khoản</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <label>Tên đăng nhập:</label>
        <input type="text" name="username" required>

        <label>Mật khẩu:</label>
        <input type="password" name="password" required>

        <label>Họ và tên:</label>
        <input type="text" name="fullname">

        <label>Email:</label>
        <input type="email" name="email">

        <label>Ngày sinh:</label>
        <input type="date" name="birth">

        <label>Giới tính:</label>
        <select name="gender">
            <option value="1">Nam</option>
            <option value="0">Nữ</option>
        </select>

        <label>Số điện thoại:</label>
        <input type="text" name="phone" required>

        <button type="submit">Đăng ký</button>
        Đã có tài khoản? <a href="register.php">Đăng Nhập</a>

    </form>
</div>

</body>
</html>
