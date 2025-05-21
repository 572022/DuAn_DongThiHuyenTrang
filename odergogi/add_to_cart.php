<?php
session_start();
include 'include/database.php';

// ini_set('display_errors', 1);
// error_reporting(E_ALL);
header('Content-Type: application/json'); 

$input = file_get_contents("php://input");
$data = json_decode($input, true);

file_put_contents("debug_log.txt", "Dữ liệu nhận: " . json_encode($data, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

// Kiểm tra dữ liệu hợp lệ
if (!isset($data['id_food']) || !is_numeric($data['id_food'])) {
    echo json_encode(["status" => "error", "message" => "id_food không hợp lệ"]);
    exit;
}

$id_food = intval($data['id_food']);
$id_bill = isset($data['id_bill']) ? intval($data['id_bill']) : (isset($_SESSION['bill_id']) ? $_SESSION['bill_id'] : null);
if ($id_bill === null) {
    echo json_encode(["status" => "error", "message" => "id_bill không hợp lệ"]);
    exit;
}

// Kiểm tra kết nối database
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Lỗi kết nối cơ sở dữ liệu"]);
    exit;
}

// Truy vấn sản phẩm từ database
$sql = "SELECT id_food, food_name, price, image FROM tbl_food WHERE id_food = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_food);
$stmt->execute();
$result = $stmt->get_result();
$food = $result->fetch_assoc();
$stmt->close();

if (!$food) {
    echo json_encode(["status" => "error", "message" => "Sản phẩm không tồn tại"]);
    exit;
}

// Xử lý thêm vào `tbl_bill_info` nếu có orderItems
if (isset($data['orderItems']) && is_array($data['orderItems'])) {
    foreach ($data['orderItems'] as $item) {
        if (
            isset($item['id_food'], $item['quantity'], $item['price']) &&
            is_numeric($item['id_food']) && is_numeric($item['quantity']) && is_numeric($item['price'])
        ) {
            $id_food_item = intval($item['id_food']);
            $quantity = intval($item['quantity']);
            $price = floatval($item['price']);
            $status = 0; // Đang chế biến

            $insert_inf = "INSERT INTO tbl_bill_info (id_bill, id_food, quantity, price, status) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insert_inf);
            $insertStmt->bind_param("iiidi", $id_bill, $id_food_item, $quantity, $price, $status);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }
}

// Khởi tạo giỏ hàng theo bill nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Đảm bảo giỏ hàng cho bill_id tồn tại trong session
if (!isset($_SESSION['cart'][$id_bill])) {
    $_SESSION['cart'][$id_bill] = [];
}

// Thêm sản phẩm vào giỏ hàng tương ứng với bill hiện tại
if (isset($_SESSION['cart'][$id_bill][$id_food])) {
    $_SESSION['cart'][$id_bill][$id_food]['quantity'] += 1;
} else {
    $_SESSION['cart'][$id_bill][$id_food] = [
        'id_food' => $food['id_food'],
        'food_name' => $food['food_name'],
        'price' => $food['price'],
        'image' => $food['image'],
        'quantity' => 1
    ];
}

// Đóng kết nối database
$conn->close();

// Trả về giỏ hàng của bill hiện tại
echo json_encode([
    "status" => "success",
    "cart" => array_values($_SESSION['cart'][$id_bill])
]);
?>
