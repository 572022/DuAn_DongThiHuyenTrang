<?php
session_start();

$id_table = $_SESSION['id_table'] ?? 0;
$bill_id = $_SESSION['bill_id'] ?? 0;
unset($_SESSION['cart']); // Xóa trạng thái sau khi hiển thị
session_destroy();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán thành công</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- <link rel="stylesheet" href="styles.css"> -->
</head>
<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
    background: url('/img/background_gogi_suc.png') no-repeat center center fixed;
    background-size: cover;        /* Phủ kín toàn bộ màn hình */
    background-attachment: fixed;  /* Cố định hình nền khi cuộn */
    height: 100vh;                 
}

.success-box {
    width: 450px;
    padding: 40px;
    border-radius: 15px;
    color: white;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.success-icon {
    font-size: 50px;
    color: #28a745;
    margin-bottom: 20px;
}

.success-box h2 {
    font-size: 24px;
    margin-bottom: 15px;
}

.success-box p {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.6;
}
@media (max-width: 480px) {
    body {
        background-image: url('/img/thanks.png')!important; 
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
    }
    .success-box {
        width: 100%;
        padding: 25px;
    }

    .success-icon {
        font-size: 40px;
    }

    .success-box h2 {
        font-size: 20px;
    }

    .success-box p {
        font-size: 14px;
    }
}

@media (min-width: 481px) and (max-width: 768px) {
    body {
        background-image: url('/img/thanks.png')!important; 
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
    }
    .success-box {
        width: 80%;
        padding: 30px;
    }

    .success-icon {
        font-size: 45px;
    }

    .success-box h2 {
        font-size: 22px;
    }

    .success-box p {
        font-size: 15px;
    }
}
</style>
<body>
<div class="container success-box">
    <div class="flex">
        <i class="fa-solid fa-circle-check success-icon"></i>
    </div>

    <h2>Thanh toán thành công!</h2>
    <p>
        Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được xác nhận.<br>
        Mã hóa đơn: <strong><?php echo $bill_id; ?></strong>, 
        Số bàn: <strong><?php echo $id_table; ?></strong>
    </p>
</div>

</body>
</html>
