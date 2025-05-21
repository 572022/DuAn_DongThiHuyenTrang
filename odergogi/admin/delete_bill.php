<?php
include '../include/database.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Lấy trạng thái và id_table từ hóa đơn
    $checkStatusSql = "SELECT status, id_table FROM tbl_bill WHERE id_bill = $id";
    $result = $conn->query($checkStatusSql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row['status'] == 1) {
            echo "Hóa đơn đã thanh toán không thể xóa.";
            exit;
        }

        $id_table = $row['id_table']; // Lấy id bàn

        // Xóa chi tiết hóa đơn
        $conn->query("DELETE FROM tbl_bill_info WHERE id_bill = $id");

        // Xóa hóa đơn
        $sql = "DELETE FROM tbl_bill WHERE id_bill = $id";

        if ($conn->query($sql)) {
            // Cập nhật trạng thái bàn về 0 (chưa có người)
            $updateTableSql = "UPDATE tbl_table SET status = 0 WHERE id_table = $id_table";
            $conn->query($updateTableSql);

            echo "Xóa hóa đơn và cập nhật trạng thái bàn thành công.";
        } else {
            echo "Lỗi khi xóa hóa đơn.";
        }
    } else {
        echo "Không tìm thấy hóa đơn.";
    }
} else {
    echo "Thiếu ID hóa đơn.";
}
?>
