<?php
session_start();
header("Content-Type: application/json");

// Đọc dữ liệu từ request
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu đầu vào
if (!isset($data['bill_id']) || !isset($data['id_food']) || !isset($data['quantity'])) {
    echo json_encode(["success" => false, "error" => "Thiếu dữ liệu cần thiết"]);
    exit;
}

$bill_id = $data['bill_id'];
$id_food = $data['id_food'];
$quantity = intval($data['quantity']);

// Giới hạn tối thiểu
if ($quantity < 1) {
    $quantity = 1;
}

// Kiểm tra giỏ hàng theo bill_id
if (!isset($_SESSION['cart'][$bill_id])) {
    echo json_encode(["success" => false, "error" => "Giỏ hàng không tồn tại cho hóa đơn này"]);
    exit;
}

// Tìm và cập nhật sản phẩm trong giỏ hàng
$found = false;
foreach ($_SESSION['cart'][$bill_id] as &$item) {
    if ($item['id_food'] == $id_food) {
        $item['quantity'] = $quantity;
        $found = true;
        break;
    }
}

if ($found) {
    echo json_encode(["success" => true, "cart" => $_SESSION['cart'][$bill_id]]);
} else {
    echo json_encode(["success" => false, "error" => "Sản phẩm không tồn tại trong giỏ hàng"]);
}
exit;
?>
