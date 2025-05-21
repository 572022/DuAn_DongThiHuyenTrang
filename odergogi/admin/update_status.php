<?php
include '../include/database.php';

if (isset($_POST['update_status']) && isset($_POST['status'])) {
    foreach ($_POST['status'] as $id_bill_info => $status) {
        $sql = "UPDATE tbl_bill_info SET status = ? WHERE id_bill_info = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $status, $id_bill_info);
        $stmt->execute();
        $stmt->close();
    }

    // Sau khi cập nhật, quay về trang index.php
    header("Location: index.php");
    exit(); // Dừng script để đảm bảo chuyển hướng hoạt động
}

$conn->close();
?>
