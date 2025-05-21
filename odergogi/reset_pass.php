<!-- reset_pass.php -->
<?php
include 'include/database.php';

if (isset($_GET['id_customer'])) {
    $id = $_GET['id_customer'];

    // Kiểm tra xem ID khách hàng có tồn tại không
    $stmt = $conn->prepare("SELECT fullname FROM tbl_customer WHERE id_customer = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "Link không hợp lệ!";
        exit;
    }

    // Xử lý form khi người dùng submit mật khẩu mới
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        // Kiểm tra mật khẩu xác nhận
        if ($new_pass !== $confirm_pass) {
            $error = "Mật khẩu và xác nhận mật khẩu không khớp!";
        } else {
            // Mã hóa mật khẩu mới
            $new_pass_hashed = password_hash($new_pass, PASSWORD_DEFAULT);

            // Cập nhật mật khẩu vào CSDL
            $stmt = $conn->prepare("UPDATE tbl_customer SET password = ? WHERE id_customer = ?");
            $stmt->bind_param("si", $new_pass_hashed, $id);
            if ($stmt->execute()) {
                echo "Mật khẩu đã được cập nhật thành công!";
            } else {
                echo "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    }
} else {
    echo "<p>Link không hợp lệ! </p>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
</head>
<style>
   /* Toàn bộ bố cục căn giữa */
body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #eef2f7;
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

/* Hộp chứa nội dung */
.container {
    width: 100%;
    max-width: 360px;
    padding: 40px 30px;
    background-color: #fff;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    text-align: center;
    transition: 0.3s ease;
}

/* Logo */
.container img {
    width: 90px;
    margin-bottom: 20px;
}

/* Tiêu đề */
h2 {
    font-size: 26px;
    color: #2c3e50;
}

form label {
    display: block;
    text-align: left;
    font-size: 15px;
    color: #444;
    margin-bottom: 6px;
    font-weight: 600;
}

/* Trường nhập */
form input[type="password"] {
    width: 100%;
    padding: 12px 14px;
    font-size: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-bottom: 15px;
    outline: none;
    transition: border 0.3s ease;
}

form input[type="password"]:focus {
    border-color: #3498db;
}

/* Nút xác nhận */
form input[type="submit"] {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

form input[type="submit"]:hover {
    background-color: #2980b9;
    transform: scale(1.02);
}

/* Thông báo lỗi */
form p {
    margin-top: 15px;
    font-size: 14px;
    color: #e74c3c;
}


</style>
<body>
    <div class="container">
        <img src="/img/logo_gogi.png" alt="ảnh logo gogi">
    
    <h2>Đặt lại mật khẩu</h2>
    <?php
    if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    }
    ?>

    <form method="POST">
        <label>Mật khẩu mới:</label>
        <input type="password" name="new_password" required><br>
        <label>Xác nhận mật khẩu:</label>
        <input type="password" name="confirm_password" required><br>
        <input type="submit" value="Xác nhận">
    </form>
</div>
</body>
</html>
