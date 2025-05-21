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
            font-family: Arial, sans-serif; 
            margin: 20px; 
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }

        h2 {
            text-align: center;
        }
    </style>
<body>
    <h2>Đăng ký tài khoản</h2>
    <?php if (isset($_SESSION['error'])): ?>
    <div style="color: red; text-align: center;"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div style="color: green; text-align: center;"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
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
    </form>
    <?php
$password_dang_ky = "matkhau"; // Mật khẩu gốc bạn đã dùng khi đăng ký
$hashed_dang_ky = password_hash($password_dang_ky, PASSWORD_DEFAULT);

echo "Mật khẩu đăng ký: " . $password_dang_ky . "<br>";
echo "Mật khẩu đã mã hóa khi đăng ký: " . $hashed_dang_ky . "<br>";
?>


</body>
</html>
