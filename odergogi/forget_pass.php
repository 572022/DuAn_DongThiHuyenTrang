<?php
session_start();
include 'include/database.php'; // Kết nối cơ sở dữ liệu
$config = include('config.php'); // Tải cấu hình email

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    // Kiểm tra email có tồn tại trong hệ thống
    $stmt = $conn->prepare("SELECT id_customer, fullname FROM tbl_customer WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Tạo link reset mật khẩu
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset_pass.php?id_customer=" . $user['id_customer'];

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['email_user']; // Lấy email từ config
            $mail->Password   = $config['email_pass']; // Lấy mật khẩu từ config
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
           
            // Người gửi và người nhận
            $mail->setFrom($config['email_user'], 'Hệ thống hỗ trợ');
            $mail->addAddress($email, $user['fullname']);
            echo "Gửi đến: " . $email . " với tên: " . $user['fullname'] . "<br>";


            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Khôi phục mật khẩu';
            $mail->Body    = "
                Xin chào <strong>{$user['fullname']}</strong>,<br><br>
                Bạn đã yêu cầu đặt lại mật khẩu. Vui lòng bấm vào link bên dưới để đặt lại mật khẩu:<br>
                <a href='{$resetLink}'>Đặt lại mật khẩu</a><br><br>
                Nếu bạn không yêu cầu, hãy bỏ qua email này.
            ";

            // Gửi email
            if (!$mail->send()) {
                echo "Lỗi gửi email: " . $mail->ErrorInfo;
            } else {
                echo "Đã gửi email khôi phục mật khẩu tới {$email}";
            }
        } catch (Exception $e) {
            echo "Lỗi gửi email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email không tồn tại trong hệ thống!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu</title>
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css"> <!-- Optional if you need icons -->
    <style>
        body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f4f4f4;
    padding: 30px;
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
    max-width: 500px;
    width: 100%;
    background-color: #fff;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    text-align: center;
    background: rgba(255, 255, 255, 0.9);
}

h2 {
    color: #333;
    margin-bottom: 20px;
    font-size: 28px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    color: #333;
    margin-bottom: 8px;
}

.form-group input {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: #4CAF50;
    outline: none;
}

.form-group input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    font-size: 16px;
    padding: 12px;
    border-radius: 5px;
    
}
    </style>
</head>
<body>

    <div class="container">
        <h2>Quên Mật Khẩu</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Nhập địa chỉ email của bạn:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="Gửi liên kết khôi phục">
            </div>
        </form>

        <?php
        if (isset($user) && !$user) {
            echo "<div class='message'>Email không tồn tại trong hệ thống!</div>";
        }
        ?>
    </div>

</body>
</html>
