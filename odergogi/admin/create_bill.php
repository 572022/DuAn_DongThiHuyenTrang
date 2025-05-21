<?php
session_start();
include '../include/database.php';

$id_admin = $_SESSION['admin_id']; // Lấy admin ID từ session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_table = $_POST['id_table'];

    // Kiểm tra dữ liệu đầu vào
    if (!is_numeric($id_table) || !is_numeric($id_admin)) {
        echo json_encode(["success" => false, "error" => "Dữ liệu không hợp lệ."]);
        exit();
    }

    // Thêm hóa đơn mới
    $sql = "INSERT INTO tbl_bill (id_table, id_admin, status, date_check_in) VALUES ($id_table, $id_admin, 0, NOW())";
    if ($conn->query($sql) === TRUE) {
        $bill_id = $conn->insert_id;

        // Cập nhật trạng thái bàn
        $update_sql = "UPDATE tbl_table SET status = 1 WHERE id_table = $id_table";
        $conn->query($update_sql);

        // In ra dữ liệu trong tbl_bill để kiểm tra
        $result = $conn->query("SELECT * FROM tbl_bill");
        $bills = [];
        while ($row = $result->fetch_assoc()) {
            $bills[] = $row;
        }

        echo json_encode([
            "success" => true,
            "bill_id" => $bill_id,
            "tbl_bill" => $bills
        ]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
}

$conn->close();
?>
