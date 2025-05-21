<?php
session_start();
include 'include/database.php';

ini_set('display_errors', 1);
header('Content-Type: application/json'); // Đảm bảo phản hồi JSON

// Nhận dữ liệu JSON từ request
$input = file_get_contents("php://input");
$data = json_decode($input, true);
$_SESSION['id_bill'] = $id_bill;

// Kiểm tra dữ liệu hợp lệ
if (!isset($data['id_table'], $data['id_account'], $data['total_amount'], $data['orderItems'])) {
    echo json_encode(["success" => false, "error" => "Dữ liệu không hợp lệ"]);
    exit;
}

$id_table = intval($data['id_table']);
$id_account = intval($data['id_account']);
$total_amount = floatval($data['total_amount']);
$orderItems = $data['orderItems'];
$status = 0; // Mặc định hóa đơn chưa thanh toán
$date_check_in = date("Y-m-d H:i:s"); // Lấy thời gian hiện tại

// Kiểm tra kết nối database
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Lỗi kết nối cơ sở dữ liệu"]);
    exit;
}
if($id_bill==0){
// Thêm hóa đơn vào bảng tbl_bill
$sql = "INSERT INTO tbl_bill (id_table, date_check_in, id_account, status, total_amount) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issid", $id_table, $date_check_in, $id_account, $status, $total_amount);

if ($stmt->execute()) {
    $id_bill = $stmt->insert_id; // Lấy id_bill vừa được tạo
    $updateTableSql = "UPDATE tbl_table SET status = 0 WHERE id_table = ?";
    $updateStmt = $conn->prepare($updateTableSql);
    $updateStmt->bind_param("i", $id_table);
    $updateStmt->execute();
    $updateStmt->close();

    // Thêm các món ăn vào bảng tbl_bill_info
    $insertItemSql = "INSERT INTO tbl_bill_info (id_bill, id_food, quantity, price, status) 
                      VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertItemSql);

    foreach ($orderItems as $item) {
        $id_food = intval($item['id_food']);
        $quantity = intval($item['quantity']);
        $price = floatval($item['price']);
        $order_status = 0; // Món ăn đang chế biến

        $insertStmt->bind_param("iiidi", $id_bill, $id_food, $quantity, $price, $order_status);
        $insertStmt->execute();
    }

    // Đóng kết nối
    $insertStmt->close();
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => true, 'id_bill' => $id_bill]);  

} else {
    echo json_encode(['success' => false, 'error' => 'Lỗi khi thêm dữ liệu']);
}
}else{
    
}
unset( $_SESSION['cart']);
?>
