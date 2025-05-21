<?php
session_start();
include 'include/database.php';
$id_table = isset($_SESSION['id_table']) ? $_SESSION['id_table'] : null;
$bill_id = isset($_SESSION['bill_id']) ? $_SESSION['bill_id'] : null;
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập để xem thông tin cá nhân.";
    header("Location: login.php");
    exit();
}

// Lấy ID người dùng từ session
$user_id = $_SESSION['user_id'];

// Truy vấn thông tin người dùng
$stmt = $conn->prepare("SELECT username, fullname, email, birth, gender, phone FROM tbl_customer WHERE id_customer = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #e0eafc, #cfdef3);
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.container {
    width: 90%;
    max-width: 600px;
    background: rgba(255, 255, 255, 0.85);
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(10px);
}

h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 25px;
    font-size: 26px;
    font-weight: 600;
}

.back-btn {
    text-align: left;
    margin-bottom: 20px;
}

.back-btn a {
    text-decoration: none;
    color: #007BFF;
    font-size: 16px;
    font-weight: 500;
    transition: color 0.3s;
}

.back-btn a:hover {
    color: #0056b3;
}

.info-box {
    margin-top: 20px;
}

.info-item {
    margin: 12px 0;
    padding: 12px 0;
    border-bottom: 1px solid #ddd;
    color: #333;
    font-size: 16px;
}

.info-label {
    font-weight: 600;
    display: inline-block;
    width: 150px;
    color: #555;
}

.action-buttons {
    text-align: center;
    margin-top: 35px;
}

.btn {
    display: inline-block;
    padding: 12px 25px;
    margin: 8px;
    background: linear-gradient(135deg, #007BFF, #0056b3);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
}

.btn:hover {
    background: linear-gradient(135deg, #0056b3, #003d80);
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0, 123, 255, 0.4);
}

.logout {
    background: linear-gradient(135deg, #dc3545, #a71d2a);
    box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
}

.logout:hover {
    background: linear-gradient(135deg, #a71d2a, #7c1320);
    box-shadow: 0 6px 14px rgba(220, 53, 69, 0.4);
}

</style>
<body>
    <div class="container">
        <div class="back-btn">
            <a href="order_menu.php?id_table=<?= $id_table ?>&bill_id=<?= $bill_id ?>">← Quay lại</a>
        </div>

        <h2>Thông tin cá nhân</h2>

        <?php if ($user): ?>
            <div class="info-box">
                <div class="info-item"><span class="info-label">Tên đăng nhập:</span> <?= htmlspecialchars($user['username']) ?></div>
                <div class="info-item"><span class="info-label">Họ và tên:</span> <?= htmlspecialchars($user['fullname']) ?></div>
                <div class="info-item"><span class="info-label">Email:</span> <?= htmlspecialchars($user['email']) ?></div>
                <div class="info-item"><span class="info-label">Ngày sinh:</span> <?= $user['birth'] ? date('d/m/Y', strtotime($user['birth'])) : 'Chưa cập nhật' ?></div>
                <div class="info-item"><span class="info-label">Giới tính:</span>
                    <?= is_null($user['gender']) ? 'Chưa rõ' : ($user['gender'] ? 'Nam' : 'Nữ') ?>
                </div>
                <div class="info-item"><span class="info-label">Số điện thoại:</span> <?= htmlspecialchars($user['phone']) ?></div>
            </div>

            <div class="action-buttons">
                <a href="change_password.php" class="btn">Đổi mật khẩu</a>
                <a href="update_infor.php" class="btn">Cập nhật thông tin</a>
                <a href="logout.php?id_table=<?= $id_table ?>&bill_id=<?= $bill_id ?>" class="btn logout">Đăng xuất</a>
            </div>
        <?php else: ?>
            <p>Không thể hiển thị thông tin người dùng.</p>
        <?php endif; ?>
    </div>
</body>
</html>