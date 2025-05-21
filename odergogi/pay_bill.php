<?php
session_start();
include 'include/database.php';

header('Content-Type: application/json');
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!isset($data['id_bill'])) {
    echo json_encode(["success" => false, "error" => "Thiếu mã hóa đơn"]);
    exit;
}

$id_bill = intval($data['id_bill']);
$id_customer = intval($_SESSION['user_id']);

// Kiểm tra kết nối
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Lỗi kết nối cơ sở dữ liệu"]);
    exit;
}

// Cập nhật trạng thái hóa đơn và thêm id_customer nếu có
if ($id_customer) {
    $updateBillSql = "UPDATE tbl_bill SET status = 1, date_check_out = NOW(), id_customer = ? WHERE id_bill = ?";
    $updateBillStmt = $conn->prepare($updateBillSql);
    $updateBillStmt->bind_param("ii", $id_customer, $id_bill);
} else {
    $updateBillSql = "UPDATE tbl_bill SET status = 1, date_check_out = NOW() WHERE id_bill = ?";
    $updateBillStmt = $conn->prepare($updateBillSql);
    $updateBillStmt->bind_param("i", $id_bill);
}

$billUpdated = $updateBillStmt->execute();
$updateBillStmt->close();

if (!$billUpdated) {
    echo json_encode(["success" => false, "error" => "Lỗi khi cập nhật hóa đơn"]);
    $conn->close();
    exit;
}

// Lấy id_table từ bill
$getTableSql = "SELECT id_table FROM tbl_bill WHERE id_bill = ?";
$getTableStmt = $conn->prepare($getTableSql);
$getTableStmt->bind_param("i", $id_bill);
$getTableStmt->execute();
$getTableStmt->bind_result($id_table);
$getTableStmt->fetch();
$getTableStmt->close();

// Nếu tìm được id_table thì cập nhật trạng thái bàn về 0
if ($id_table) {
    $updateTableSql = "UPDATE tbl_table SET status = 0 WHERE id_table = ?";
    $updateTableStmt = $conn->prepare($updateTableSql);
    $updateTableStmt->bind_param("i", $id_table);
    $updateTableStmt->execute();
    $updateTableStmt->close();
}

$conn->close();

echo json_encode(["success" => true, "message" => "Thanh toán thành công và đã cập nhật trạng thái bàn"]);
?>
