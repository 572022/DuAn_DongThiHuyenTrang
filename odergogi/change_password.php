<?php
session_start();
include 'include/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập để đổi mật khẩu.";
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_id = $_SESSION['user_id'];

    // Lấy mật khẩu cũ từ DB
    $stmt = $conn->prepare("SELECT password FROM tbl_customer WHERE id_customer = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($old_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE tbl_customer SET password = ? WHERE id_customer = ?");
            $update->bind_param("si", $hashed_new_password, $user_id);
            if ($update->execute()) {
                $_SESSION['success'] = "Đổi mật khẩu thành công!";
                header("Location: login.php");
                exit();
            } else {
                $error = "Lỗi cập nhật mật khẩu.";
            }
        } else {
            $error = "Mật khẩu mới và xác nhận không khớp.";
        }
    } else {
        $error = "Mật khẩu cũ không chính xác.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* background: #e6f2f1; */
            background: url('/img/nendoimk.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-box {
            width: 90%;
            max-width: 400px;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .form-box h2 {
            background-color: #007c91;
            color: #ffffff;
            text-align: center;
            padding: 20px 0;
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        /* Form content */
        form {
            padding: 30px 25px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #444;
            font-weight: 500;
        }

        input[type="password"] {
            width: 90%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f7f9fc;
            font-size: 15px;
            outline: none;
            transition: border 0.3s ease;
        }

        input[type="password"]:focus {
            border: 1px solid #007c91;
        }

        /* Submit button */
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007c91;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #005f6b;
        }

        .error {
            color: #dc3545;
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="form-box">
        <h2>Đổi mật khẩu</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <label>Mật khẩu cũ:</label>
            <input type="password" name="old_password" required>

            <label>Mật khẩu mới:</label>
            <input type="password" name="new_password" required>

            <label>Nhập lại mật khẩu mới:</label>
            <input type="password" name="confirm_password" required>

            <input type="submit" value="Xác nhận đổi mật khẩu">
        </form>
    </div>
</body>

</html>