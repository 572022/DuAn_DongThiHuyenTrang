<?php
session_start();
include 'include/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
$success = '';
$error = '';

// Lấy thông tin hiện tại
$sql = "SELECT * FROM tbl_customer WHERE id_customer = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $birth    = $_POST['birth'];
    $gender   = $_POST['gender'];
    $phone    = trim($_POST['phone']);

    $update_sql = "UPDATE tbl_customer 
                   SET username = ?, fullname = ?, email = ?, birth = ?, gender = ?, phone = ?
                   WHERE id_customer = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssi", $username, $fullname, $email, $birth, $gender, $phone, $id);

    if ($stmt->execute()) {
        // Cập nhật lại dữ liệu session hoặc biến nếu cần
        header("Location: user_info.php");
        exit(); // Dừng script ngay sau khi chuyển hướng
    } else {
        $error = "Lỗi khi cập nhật: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

 <style>
body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            backdrop-filter: brightness(0.8);
        }

        /* Overlay để làm nền tối hơn */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .glass-card {
            background: rgba(30, 30, 30, 0.55);
            border-radius: 16px;
            padding: 30px 40px;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            max-width: 420px;
            width: 100%;
            margin: 20px;
        }

        h3 {
            margin-bottom: 20px;
            color: #ffffff;
            font-size: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            outline: none;
        }

        .form-control::placeholder {
            color: #ccc;
        }

        .form-check-label {
            color: #ffffff;
        }

        .btn-submit {
            width: 100%;
            background-color: #3d7eff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #255be0;
        }

        .alert {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.8);
            color: #fff;
        }

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.8);
            color: #fff;
        }
    </style>

<div class="glass-card">
    <h3>Cập nhật thông tin cá nhân</h3>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <input type="text" class="form-control" name="username" placeholder="Tên đăng nhập"
                   value="<?= htmlspecialchars($customer['username']) ?>" required>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="fullname" placeholder="Họ và tên"
                   value="<?= htmlspecialchars($customer['fullname']) ?>" required>
        </div>
        <div class="form-group">
            <input type="email" class="form-control" name="email" placeholder="Email"
                   value="<?= htmlspecialchars($customer['email']) ?>" required>
        </div>
        <div class="form-group">
            <input type="date" class="form-control" name="birth"
                   value="<?= htmlspecialchars($customer['birth']) ?>" required>
        </div>
        <div class="form-group">
            <select class="form-control" name="gender" required>
                <option value="Nam" <?= $customer['gender'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                <option value="Nữ" <?= $customer['gender'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
            </select>
        </div>
        <div class="form-group">
            <input type="tel" class="form-control" name="phone" placeholder="Số điện thoại"
                   value="<?= htmlspecialchars($customer['phone']) ?>" required>
        </div>

        <button type="submit" class="btn-submit">Cập nhật thông tin</button>
    </form>
</div>
</body>
</html>
