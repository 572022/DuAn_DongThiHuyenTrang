<?php
include 'include/database.php';
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
file_put_contents("log.txt", print_r($data, true));

// Kiểm tra dữ liệu đầu vào
if (!isset($data['id_table'], $data['total_amount'], $data['orderItems'], $data['id_billnew'])) {
    echo json_encode(["success" => false, "error" => "Dữ liệu không hợp lệ"]);
    exit;
}

$id_table = intval($data['id_table']);
$id_account = isset($data['id_account']) ? intval($data['id_account']) : null; // Có thể null nếu không có
$total_amount = floatval($data['total_amount']);
$orderItems = $data['orderItems'];
$id_bill = intval($data['id_billnew']);
$status = 0; // Mặc định hóa đơn chưa thanh toán
$date_check_in = date("Y-m-d H:i:s");

// Kiểm tra kết nối database
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Lỗi kết nối database"]);
    exit;
}

// Cập nhật total_amount trong tbl_bill
$sql_update = "UPDATE tbl_bill SET total_amount = COALESCE(total_amount, 0) + ? WHERE id_bill = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("di", $total_amount, $id_bill);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "error" => "Lỗi khi cập nhật hóa đơn: " . $stmt->error]);
    exit;
}

$stmt->close();
$_SESSION['id_bill'] = $id_bill;

// Cập nhật trạng thái bàn đã có
$updateTableSql = "UPDATE tbl_table SET status = 1 WHERE id_table = ?";
$updateStmt = $conn->prepare($updateTableSql);
$updateStmt->bind_param("i", $id_table);
if (!$updateStmt->execute()) {
    echo json_encode(["success" => false, "error" => "Lỗi khi cập nhật trạng thái bàn: " . $updateStmt->error]);
    exit;
}
$updateStmt->close();

// Thêm các món ăn vào bảng tbl_bill_info
$insertItemSql = "INSERT INTO tbl_bill_info (id_bill, id_food, quantity, price, status) VALUES (?, ?, ?, ?, ?)";
$insertStmt = $conn->prepare($insertItemSql);

foreach ($orderItems as $item) {
    $id_food = isset($item['id_food']) ? intval($item['id_food']) : 0;
    $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
    $price = isset($item['price']) ? floatval($item['price']) : 0.00;
    $status = 1; // đang chế biến
    // Nếu giá trị không hợp lệ, bỏ qua
    if ($id_food <= 0 || $quantity <= 0 || $price <= 0) {
        continue;
    }

    $insertStmt->bind_param("iiidi", $id_bill, $id_food, $quantity, $price, $status);
    if (!$insertStmt->execute()) {
        echo json_encode(["success" => false, "error" => "Lỗi khi thêm món ăn: " . $insertStmt->error]);
        exit;
    }
}

$insertStmt->close();
$conn->close();

echo json_encode(['success' => true, 'id_bill' => $id_bill]);
unset($_SESSION['cart']);

?>
